<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 激活码逻辑
 * ------------------------------------------------------------------------------
 */

//收集可用的赞助者激活码来源
function aya_payment_sponsor_activation_code()
{
    $order_code_from = [];
    $order_code_from = apply_filters('aya_add_sponsor_from', $order_code_from);

    //清理重复
    $order_code_from = array_unique($order_code_from);

    return $order_code_from;
}

//收集可用的赞助方案列表
function aya_payment_sponsor_order_plan()
{
    $order_plan = [];

    $order_plan = apply_filters('aya_add_sponsor_plan', $order_plan);

    //防止过滤器操作出错，返回空数组阻止报错
    if (!is_array($order_plan)) {
        $order_plan = [];
    }

    return $order_plan;
}

//写入日志文件
function aya_payment_callback_save_log($log_name, $log_data)
{
    //日志目录
    $log_dir = aya_local_mkdir('/webhook_logs');
    $today = date('Y-m-d');

    //文件名加盐
    $rand_name = substr(md5($today . AUTH_SALT), 0, 6);

    $log_file = $log_dir . '/webhook-' . $today . '-' . $rand_name . '.log';

    $log_content = date('[Y-m-d H:i:s]') . ' ' . $log_name . PHP_EOL . $log_data . PHP_EOL . PHP_EOL;

    file_put_contents($log_file, $log_content, FILE_APPEND);
}

/*
 * ------------------------------------------------------------------------------
 * 爱发电接口逻辑
 * ------------------------------------------------------------------------------
 */

//在REST-API中响应爱发电的 WebHook 接口
add_action('rest_api_init', 'aya_register_afdian_api_routes', 10, 0);
//在系统中添加爱发电赞助计划
add_filter('aya_add_sponsor_from', 'aya_add_afdian_order_activate', 10);
add_filter('aya_add_sponsor_plan', 'aya_add_afdian_sponsor_plans', 10);

//爱发电链接
function aya_get_afdian_link()
{
    return 'https://afdian.com/';
}

//爱发电主页链接
function aya_get_afdian_home_link()
{
    //获取爱发电主页 URL
    $afdian_home_slug = aya_opt('stie_afdian_homepage_text', 'access');

    //验证URL结构是否符合预期
    if (empty($afdian_home_slug)) {
        return false;
    }

    return aya_get_afdian_link() . 'a/' . $afdian_home_slug;
}

//爱发电API实例
function aya_inst_afdian_api()
{
    //获取爱发电用户ID和Token
    $afdian_userid = aya_opt('stie_afdian_userid_text', 'access');
    $afdian_token = aya_opt('stie_afdian_token_text', 'access');

    //未设置
    if (empty($afdian_userid) || empty($afdian_token)) {
        return false;
    }

    //创建接口实例
    return new Afdian_API($afdian_userid, $afdian_token);
}

//测试爱发电接口可用性
function aya_afdian_ping_server()
{
    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    return $afdian_api->ping_server();
}

//爱发电即时查询（订单号）
function aya_afdian_query_order($order = '')
{
    $order = trim($order);

    //为空&不是数字时返回
    if (empty($order) || !preg_match('/^[0-9]+$/', $order)) {
        return false;
    }

    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取订单信息
    $result = $afdian_api->query_order($order);

    if (empty($result['data']['list'])) {
        return false;
    }

    //返回查询订单详情
    return $result['data']['list'][0];
}

//爱发电即时查询（用户）
function aya_afdian_query_sponsor($sponsor = '')
{
    //为空时返回
    if (empty($sponsor)) {
        return false;
    }

    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取用户信息
    $result = $afdian_api->query_sponsor($sponsor);

    if (empty($result['data']['list'])) {
        return false;
    }

    //返回查询结果
    return $result['data']['list'][0];
}

//爱发电即时查询（订单列表）
function aya_afdian_query_order_list($page = 1, $per_page = 50)
{
    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取订单列表
    $result = $afdian_api->get_orders($page, $per_page);

    return $result;
}

//爱发电即时查询（赞助者列表）
function aya_afdian_query_sponsor_list($page = 1, $per_page = 50)
{
    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取赞助者列表
    $result = $afdian_api->get_sponsors($page, $per_page);

    return $result;
}

//用于回调爱发电 WebHook 数据的 REST-API 接口
function aya_register_afdian_api_routes()
{
    register_rest_route('afdian', 'callbacks', [
        'methods' => 'POST',
        'callback' => function ($request) {
            // 接收POST数据
            $post_data = $request->get_body();
            //是否配置日志
            $need_save_log = aya_opt('site_afdian_savelog_bool', 'access', true);

            //记录接收到的数据以便调试
            if ($need_save_log) {
                //日志目录
                aya_payment_callback_save_log('Afdian API received data:', $post_data);
            }

            //处理数据
            if (!empty($post_data)) {
                //解析JSON数据
                $json_decode = json_decode($post_data, true);
                $order_data = $json_decode['data']['order'] ?? [];

                //如果设置了订单回调ID，处理激活
                if (isset($order_data['custom_order_id'])) {
                    //order id
                    $activate_order = 'afd_' . $order_data['out_trade_no'];
                    //days
                    $activate_days = intval($order_data['month']) * 31;
                    //user
                    $activate_user_token = aya_token_decode($order_data['custom_order_id'], 8);
                    $activate_user = intval($activate_user_token);

                    //检查订单是否已存在
                    if (!aya_sponsor_order_exists($activate_order)) {
                        //查询成功，创建系统内赞助者激活
                        $activate_result = aya_sponsor_add_order($activate_user, $activate_order, $activate_days, 'paid', 'afdian');
                    }

                    if ($need_save_log) {
                        //处理完成
                        $activation_to = 'The order:"' . $activate_order . '" User id:' . $activate_user;

                        if ($activate_result === true) {
                            $activation_to .= ' activation completed .';
                        } else {
                            $activation_to .= ' activation failed .';
                        }
                        //日志目录
                        aya_payment_callback_save_log('', $activation_to);
                    }
                }
            }

            //任何情况下都返回成功响应
            return new WP_REST_Response([
                'ec' => 200,
                'em' => 'done'
            ], 200);
        },
        'permission_callback' => function () {
            return true;
        }
    ]);
}

//取消爱发电回调接口的Cookie验证
add_filter('rest_authentication_errors', function ($errors) {
    //获取当前请求的路径
    $request_uri = $_SERVER['REQUEST_URI'];
    //需要跳过的请求路径
    $pos_uri = '/' . aya_rewrite_rest_api_url_prefix() . '/afdian/callbacks';
    //检查请求
    if (strpos($request_uri, $pos_uri) !== false) {
        //始终响应爱发电发回的webhook请求，跳过Cookie认证
        return true;
    }

    return $errors;
}, 99);

//允许爱发电赞助订单号激活
function aya_add_afdian_order_activate($code_from)
{
    if (aya_opt('site_afdian_convert_bool', 'access') !== '') {

        $code_from[] = 'afdian';
    }

    return $code_from;
}

//爱发电赞助方案数据
function aya_add_afdian_sponsor_plans($order_plan)
{
    //启用爱发电订阅接入
    if (aya_opt('site_afdian_convert_bool', 'access')) {
        //爱发电主页
        $afd_home = aya_get_afdian_home_link();

        //爱发电主页方案
        $order_plan['afdian'] = [
            'color' => '#946ce6',
            'title' => __('支持创作，用爱发电', 'aiya-cms'),
            'desc' => __('查看爱发电创作者主页', 'aiya-cms'),
            'price' => '',
            'href' => $afd_home,
            'href_title' => __('爱发电主页', 'aiya-cms'),
            'triggered_msg' => __('在当前页面输入爱发电平台订单号激活本站订阅即可查看专属内容', 'aiya-cms'),
            'refresh' => false,
        ];

        //自动回调时预设方案
        $afd_plan_type = aya_opt('site_afdian_plan_type', 'access');

        //配置回调时需求的数据
        $site_name = get_bloginfo('name');
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $user_name = $current_user->display_name;
        //生成自定义订单ID
        $custom_id = aya_token_encode($user_id, 8);
        //方案详情页
        $afd_order_create = aya_get_afdian_link() . 'order/create?';
        //创建留言消息
        $remark_format = __('来自 「%s」 用户 %s 发送的赞助订单~', 'aiya-cms');

        $remark_msg = urlencode(sprintf($remark_format, $site_name, $user_name));

        //跳转自选金额页面
        if ($afd_plan_type === 'optional') {
            $afd_user_id = aya_opt('stie_afdian_userid_text', 'access');
            //拼接回调链接
            $plan_url = "{$afd_order_create}user_id={$afd_user_id}&custom_order_id={$custom_id}&remark={$remark_msg}";

            $order_plan['afdian_optional'] = [
                'color' => '#946ce6',
                'title' => __('自选金额赞助', 'aiya-cms'),
                'desc' => __('在爱发电支持我', 'aiya-cms'),
                'price' => __('自选金额', 'aiya-cms'),
                'href' => $plan_url,
                'href_title' => __('爱发电订阅', 'aiya-cms'),
                'triggered_msg' => __('已经支持，稍后并刷新页面即可查看赞助记录', 'aiya-cms'),
                'refresh' => true,
            ];
        }
        //跳转预设方案页面
        else if ($afd_plan_type === 'preset') {
            $afd_preset_url = aya_opt('site_afdian_preset_plan_url', 'access');
            //提取方案ID
            $afd_plan_id = aya_extract_url_query($afd_preset_url, 'plan_id');

            if (!empty($afd_plan_id)) {
                //拼接回调链接
                $plan_url = "{$afd_order_create}plan_id={$afd_plan_id}&custom_order_id={$custom_id}&remark={$remark_msg}";

                $order_plan['afdian_preset'] = [
                    'color' => '#946ce6',
                    'title' => __('赞助方案订阅', 'aiya-cms'),
                    'desc' => __('订阅我的赞助方案', 'aiya-cms'),
                    'price' => '',
                    'href' => $plan_url,
                    'href_title' => __('爱发电订阅', 'aiya-cms'),
                    'triggered_msg' => __('已经支持，稍后并刷新页面即可查看专属内容', 'aiya-cms'),
                    'refresh' => true,
                ];
            }
        }
    }

    return $order_plan;
}

//爱发电接口订单查询逻辑
function aya_verify_code_by_afdian($order_id)
{
    //接口地址不可用
    if (aya_afdian_ping_server() === false) {
        return [
            'status' => false,
            'detail' => __('爱发电接口不可用，请联系管理员', 'aiya-cms'),
        ];
    }

    //检查订单是否存在
    if (aya_sponsor_order_exists('afd_' . $order_id)) {
        return [
            'status' => false,
            'detail' => __('此订单已被激活过了，请查看订单记录', 'aiya-cms'),
        ];
    }

    //发起查询
    $afd_order = aya_afdian_query_order($order_id);

    //查询失败
    if ($afd_order === false) {
        return [
            'status' => false,
            'detail' => __('没有查询到订单，请确认订单号是否正确，或于爱发电平台私信作者询问', 'aiya-cms'),
        ];
    }

    //查询成功
    $activate_order = 'afd_' . $afd_order['out_trade_no']; //order id

    //创建系统内赞助者激活
    $activate_days = intval($afd_order['month']) * 31; //days

    $result = aya_sponsor_key_activation($activate_order, $activate_days, 'afdian');

    //合并订单信息
    $order_info = PHP_EOL;
    $order_info .= __('订单号：', 'aiya-cms') . $afd_order['out_trade_no'] . PHP_EOL;
    $order_info .= __('赞助方案：', 'aiya-cms') . $afd_order['plan_title'] . PHP_EOL;
    $order_info .= __('赞助周期：', 'aiya-cms') . $afd_order['month'] . __('个月', 'aiya-cms') . PHP_EOL;
    $order_info .= __('金额：', 'aiya-cms') . $afd_order['show_amount'] . __('（折后', 'aiya-cms') . $afd_order['total_amount'] . __('）', 'aiya-cms') . PHP_EOL;
    $order_info .= __('留言：', 'aiya-cms') . __('无留言', 'aiya-cms') . (!empty($afd_order['remark']) ? $afd_order['remark'] : __('无', 'aiya-cms')) . PHP_EOL;
    $order_info .= __('兑换码：', 'aiya-cms') . __('无', 'aiya-cms') . (!empty($afd_order['redeem_id']) ? $afd_order['redeem_id'] : __('无', 'aiya-cms')) . PHP_EOL;

    $success = ($result) ? __('刷新页面即可查看激活记录', 'aiya-cms') : __('当前无法创建激活，稍后重试', 'aiya-cms');

    //返回订单信息
    return [
        'status' => $result,
        'detail' => $success . $order_info,
    ];
}

/*
 * ------------------------------------------------------------------------------
 * 兑换码接口逻辑
 * ------------------------------------------------------------------------------
 */

//自定义的兑换码数据表结构
add_action('aya_install', 'aya_install_convert_activate_code_db');
//在订阅系统中添加激活码接口
add_filter('aya_add_sponsor_from', 'aya_add_convert_code_activate', 20);

function aya_install_convert_activate_code_db()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'aya_convert_codes';

    $charset_collate = $wpdb->get_charset_collate();

    //dbDelta
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // 检查表是否存在
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {

        //表结构定义
        $sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(64) NOT NULL UNIQUE COMMENT 'convert code',
            used_to VARCHAR(20) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            user_id BIGINT UNSIGNED DEFAULT NULL,
            duration INT UNSIGNED DEFAULT 30,
            status BOOLEAN NOT NULL DEFAULT 0,
            UNIQUE KEY (code)
        ) $charset_collate;";

        dbDelta($sql);
    }
}

//添加激活码接口
function aya_add_convert_code_activate($code_from)
{
    if (aya_opt('site_sponsor_convert_bool', 'access')) {

        $code_from[] = 'code';
    }

    return $code_from;
}

//激活码接口查询逻辑
function aya_verify_code_by_code($code)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'aya_convert_codes';

    $current_user_id = get_current_user_id();
    if (!$current_user_id) {
        return ['status' => false, 'detail' => __('请先登录', 'aiya-cms')];
    }

    // 先查询兑换码获取时长信息
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE code = %s", $code));

    if (!$row) {
        return ['status' => false, 'detail' => __('无效的兑换码', 'aiya-cms')];
    }

    if ($row->status == 1 || !empty($row->user_id)) {
        return ['status' => false, 'detail' => __('兑换码已被使用', 'aiya-cms')];
    }

    // 原子更新：防止并发竞态条件下同一激活码被重复兑换
    $affected = $wpdb->query($wpdb->prepare(
        "UPDATE $table_name SET status = 1, user_id = %d, used_to = %s WHERE code = %s AND status = 0 AND user_id IS NULL",
        $current_user_id,
        current_time('mysql'),
        $code
    ));

    if ($affected === 0) {
        return ['status' => false, 'detail' => __('兑换码已被使用', 'aiya-cms')];
    }

    // 尝试激活权限
    $result = aya_sponsor_key_activation($code, $row->duration, 'code');

    if ($result) {
        return ['status' => true, 'detail' => __('兑换成功，权限已激活', 'aiya-cms')];
    } else {
        // 激活失败，回滚激活码状态
        $wpdb->update(
            $table_name,
            ['user_id' => null, 'status' => 0, 'used_to' => null],
            ['code' => $code]
        );
        return ['status' => false, 'detail' => __('激活失败，可能是您已拥有该时段的权限或系统错误', 'aiya-cms')];
    }
}

//注册管理菜单
add_action('admin_menu', function () {
    //启用时加载
    if (aya_opt('site_sponsor_convert_bool', 'access')) {
        add_menu_page(
            __('兑换码管理', 'aiya-cms'),
            __('兑换码管理', 'aiya-cms'),
            'manage_options',
            'convert-management',
            'render_convert_order_page',
            'dashicons-admin-site-alt3',
            99
        );
    }
});

//激活码管理页面
function render_convert_order_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'aya_convert_codes';

    // 处理删除所有请求
    if (isset($_POST['aya_delete_all_codes']) && check_admin_referer('aya_delete_all_codes_action')) {
        $wpdb->query("TRUNCATE TABLE $table_name");
        echo '<div class="updated notice is-dismissible"><p>' . __('所有兑换码已删除', 'aiya-cms') . '</p></div>';
    }

    // 处理生成请求
    if (isset($_POST['aya_generate_codes']) && check_admin_referer('aya_generate_codes_action')) {
        $quantity = intval($_POST['quantity']);
        $days = intval($_POST['days']);
        $prefix = sanitize_text_field($_POST['prefix']);

        if ($quantity > 0 && $days > 0) {
            $generated = 0;
            for ($i = 0; $i < $quantity; $i++) {
                $code = strtoupper($prefix . wp_generate_password(16, false));
                $res = $wpdb->insert($table_name, [
                    'code' => $code,
                    'duration' => $days,
                    'created_at' => current_time('mysql'),
                    'status' => 0
                ]);
                if ($res) $generated++;
            }
            echo '<div class="updated notice is-dismissible"><p>' . sprintf(__('成功生成 %d 个兑换码', 'aiya-cms'), $generated) . '</p></div>';
        } else {
            echo '<div class="error notice is-dismissible"><p>' . __('请输入有效的数量和天数', 'aiya-cms') . '</p></div>';
        }
    }

    // 分页参数
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    // 获取总数
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);

    // 获取列表
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php __('激活码管理', 'aiya-cms'); ?></h1>
        <hr class="wp-header-end">

        <div class="card" style="max-width: 100%; margin-top: 20px;">
            <h2><?php __('批量生成兑换码', 'aiya-cms'); ?></h2>
            <form method="post" action="" class="layout-form">
                <?php wp_nonce_field('aya_generate_codes_action'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="quantity"><?php __('生成数量', 'aiya-cms'); ?></label></th>
                        <td><input name="quantity" type="number" id="quantity" value="1" class="small-text" min="1" max="100"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="days"><?php __('有效期天数', 'aiya-cms'); ?></label></th>
                        <td><input name="days" type="number" id="days" value="7" class="small-text" min="1"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="prefix"><?php __('前缀 (可选)', 'aiya-cms'); ?></label></th>
                        <td><input name="prefix" type="text" id="prefix" value="" class="regular-text" placeholder="PREFIX-"></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="aya_generate_codes" id="submit" class="button button-primary" value="<?php __('生成兑换码', 'aiya-cms'); ?>">
                </p>
            </form>

            <form method="post" action="" onsubmit="return confirm('<?php __('确定要删除所有兑换码吗？此操作不可恢复！', 'aiya-cms'); ?>');">
                <?php wp_nonce_field('aya_delete_all_codes_action'); ?>
                <input type="submit" name="aya_delete_all_codes" class="button button-link-delete" value="<?php __('删除所有已创建的激活码', 'aiya-cms'); ?>">
            </form>

            <div style="margin-top: 20px; padding-top: 20px;"></div>

            <h2 class="screen-reader-text"><?php __('兑换码列表', 'aiya-cms'); ?></h2>

            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php __('兑换码', 'aiya-cms'); ?></th>
                        <th><?php __('天数', 'aiya-cms'); ?></th>
                        <th><?php __('状态', 'aiya-cms'); ?></th>
                        <th><?php __('使用者ID', 'aiya-cms'); ?></th>
                        <th><?php __('使用时间', 'aiya-cms'); ?></th>
                        <th><?php __('创建时间', 'aiya-cms'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results) : ?>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row->id); ?></td>
                                <td><code><?php echo esc_html($row->code); ?></code></td>
                                <td><?php echo esc_html($row->duration); ?></td>
                                <td>
                                    <?php if ($row->status == 1) : ?>
                                        <span class="badge badge-success" style="color:green"><?php __('已使用', 'aiya-cms'); ?></span>
                                    <?php else : ?>
                                        <span class="badge" style="color:gray"><?php __('未使用', 'aiya-cms'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row->user_id) : ?>
                                        <a href="<?php echo esc_url(get_edit_user_link($row->user_id)); ?>" target="_blank">
                                            <?php echo esc_html(get_the_author_meta('display_name', $row->user_id)); ?>
                                        </a>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row->used_to ? esc_html($row->used_to) : '-'; ?></td>
                                <td><?php echo esc_html($row->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7"><?php __('暂无数据', 'aiya-cms'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php printf(__('%s 个项目', 'aiya-cms'), $total_items); ?></span>
                    <?php
                    echo paginate_links([
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ]);
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php
}
