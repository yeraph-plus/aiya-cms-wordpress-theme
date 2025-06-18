<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 订阅权限功能
 * ------------------------------------------------------------------------------
 */

//自定义的赞助订单数据表结构
add_action('aya_install', 'aya_install_sponsor_order_db');

function aya_install_sponsor_order_db()
{
    global $wpdb;
    //表名
    $table_name = $wpdb->prefix . 'aya_sponsor_orders';
    //获取字符集
    $charset_collate = $wpdb->get_charset_collate();

    //检查表是否存在
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {
        //引入WP升级API
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        //SQL语句
        $sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            order_id VARCHAR(64) NOT NULL,
            start_time INT UNSIGNED NOT NULL,
            duration_days INT UNSIGNED NOT NULL,
            source VARCHAR(32) DEFAULT '',
            status VARCHAR(16) DEFAULT 'paid',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY (order_id)
        ) $charset_collate;";

        dbDelta($sql);
    }
}

//添加赞助者订单
function aya_sponsor_add_order($user_id, $order_id, $duration_days, $status, $source = '')
{
    //验证用户ID存在
    if (!is_numeric($user_id) || !get_userdata($user_id)) {
        return new WP_Error('invalid_user', 'User ID is invalid');
    }

    //验证天数有效性
    $duration_days = intval($duration_days);

    if ($duration_days <= 0) {
        return new WP_Error('invalid_duration', 'Duration must be positive');
    }

    global $wpdb;

    //表名
    $table = $wpdb->prefix . 'aya_sponsor_orders';
    // 计算最新有效期
    $now = current_time('timestamp');
    $current_expiration = aya_sponsor_get_expiration_time($user_id);

    $start_time = ($current_expiration > $now) ? $current_expiration : $now;

    //直接尝试插入（依赖唯一索引捕获重复）
    $result = $wpdb->insert($table, [
        'user_id' => $user_id,
        'order_id' => $order_id,
        'start_time' => $start_time,
        'duration_days' => $duration_days,
        'status' => $status, //cancelled, pending, paid, unpaid
        'source' => sanitize_text_field($source),
        'created_at' => current_time('mysql', 1),
    ]);

    //插入失败
    if ($result === false) {

        if ($wpdb->last_error === 'Duplicate entry') {
            return new WP_Error('duplicate_order', 'Order ID exists');
        }

        return new WP_Error('db_error', $wpdb->last_error);
    }

    //重新计算到期日
    $new_expiration = aya_sponsor_get_expiration_time($user_id);
    //缓存一条记录到用户Meta
    update_user_meta($user_id, 'sponsor_expiration', $new_expiration);

    return true;
}

//简单查询订单是否存在，阻止重复插入
function aya_sponsor_order_exists($order_id)
{
    global $wpdb;

    //表名
    $table = $wpdb->prefix . 'aya_sponsor_orders';

    //查询订单号
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE order_id = %s LIMIT 1",
        $order_id
    ));

    return !empty($exists);
}

//订单状态更新
function aya_sponsor_update_order_status($order_id, $new_status)
{
    global $wpdb;
    $table = $wpdb->prefix . 'aya_sponsor_orders';

    // 获取订单信息以获取用户ID
    $order = $wpdb->get_row($wpdb->prepare(
        "SELECT user_id FROM $table WHERE order_id = %s",
        $order_id
    ));

    if (!$order) {
        return new WP_Error('not_found', 'Order not found');
    }

    $result = $wpdb->update(
        $table,
        ['status' => $new_status],
        ['order_id' => $order_id],
        ['%s'],
        ['%s']
    );

    if ($result === false) {
        return new WP_Error('db_error', $wpdb->last_error);
    }

    // 重新计算到期时间并更新缓存
    $user_id = $order->user_id;
    $new_expiration = aya_sponsor_get_expiration_time($user_id);
    update_user_meta($user_id, 'sponsor_expiration', $new_expiration);

    return true;
}

//从兑换码等自助来源的订单激活（对当前用户）
function aya_sponsor_key_activation($order_id, $order_days, $order_from)
{
    //查询当前用户
    if (!is_user_logged_in()) {
        return new WP_Error('not_logged_in', 'Please log in first');
    }

    $user_id = get_current_user_id();

    $result = aya_sponsor_add_order($user_id, $order_id, intval($order_days), 'paid', $order_from);

    //捕捉报错
    if (is_wp_error($result)) {
        //检查是否是订单重复错误
        // if ($result->get_error_code() === 'duplicate_order') {
        //     return false;
        // }
        return false;
    }

    return true;
}

//获取赞助者到期时间（遍历）
function aya_sponsor_get_expiration_time($user_id)
{
    global $wpdb;

    $table = $wpdb->prefix . 'aya_sponsor_orders';

    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT start_time, duration_days FROM $table WHERE user_id = %d AND status = 'paid' ORDER BY start_time ASC",
        $user_id
    ), ARRAY_A);

    $expiration = 0;

    foreach ($orders as $order) {
        $start = intval($order['start_time']);
        $duration = intval($order['duration_days']) * 86400;
        $expiration = ($start > $expiration) ? ($start + $duration) : ($expiration + $duration);
    }

    return $expiration;
}

//获取用户历史赞助订单
function aya_sponsor_get_user_orders($user_id = 0)
{
    if (!is_user_logged_in()) {
        return false;
    }

    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    global $wpdb;

    $table = $wpdb->prefix . 'aya_sponsor_orders';

    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE user_id = %d ORDER BY start_time DESC",
        $user_id
    ));

    //如果没有订单，返回空数组
    if (empty($orders)) {
        return [];
    }

    //获取当前禁用状态
    $force_cancel = get_user_meta($user_id, 'aya_force_cancel_sponsor', true);
    //获取到期时间
    $expiration = get_user_meta($user_id, 'sponsor_expiration', true);

    //计算期时间和剩余天数
    $now = current_time('timestamp');

    $left_days = 0;
    $is_valid = false;
    $total_days = 0;

    if ($expiration && $expiration > $now && $force_cancel != '1') {
        $left_days = ceil(($expiration - $now) / 86400);
        $is_valid = true;
    }

    //计算累计已赞助天数
    foreach ($orders as $order) {
        $total_days += intval($order->duration_days);
    }

    return [
        'orders' => $orders,
        'force_cancel' => $force_cancel,
        'expiration' => $expiration,
        'left_days' => $left_days,
        'is_valid' => $is_valid,
        'total_days' => $total_days,
    ];
}

//获取用户赞助有效性
function aya_is_sponsor($user_id = 0)
{
    //如果未指定用户ID，则使用当前用户
    if (empty($user_id)) {
        if (!is_user_logged_in()) {
            return false;
        }

        $user_id = get_current_user_id();
    }

    //获取当前禁用状态
    $force_cancel = get_user_meta($user_id, 'aya_force_cancel_sponsor', true);
    //获取到期时间
    $expiration = get_user_meta($user_id, 'sponsor_expiration', true);

    $now = current_time('timestamp');

    //如果到期时间大于当前时间，并且没有强制取消
    return ($expiration && $expiration > $now && $force_cancel != '1');
}

//尝试修复用户赞助数据
function aya_sponsor_fix_user_data($user_id = 0, $review_cancel = false)
{
    if (empty($user_id)) {
        return false;
    }

    //重新计算到期时间
    $new_expiration = aya_sponsor_get_expiration_time($user_id);

    //更新用户元数据
    update_user_meta($user_id, 'sponsor_expiration', $new_expiration);

    //移除强制取消标记
    $force_cancel = get_user_meta($user_id, 'aya_force_cancel_sponsor', true);

    if ($force_cancel == '1' && $new_expiration > current_time('timestamp')) {
        if ($review_cancel) {
            //取消强制取消标记
            delete_user_meta($user_id, 'aya_force_cancel_sponsor');
        } else {
            //返回提示消息，用于后续处理
            return 'The user id:' . $user_id . ' status has still valid. But has been forced to cancel.';
        }
    }

    return true;
}

//在用户后台页面添加自定义区块
add_action('show_user_profile', 'aya_sponsor_show_orders_user_profile');
add_action('edit_user_profile', 'aya_sponsor_show_orders_user_profile');
add_action('personal_options_update', 'aya_sponsor_save_orders_user_profile');
add_action('edit_user_profile_update', 'aya_sponsor_save_orders_user_profile');

//用户后台页面区块表单
function aya_sponsor_show_orders_user_profile($user)
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $order_query = aya_sponsor_get_user_orders($user->ID);

    $orders = $order_query['orders'];
    $force_cancel = $order_query['force_cancel'];
    $left_days = $order_query['left_days'];
    $is_valid = $order_query['is_valid'];
    $total_days = $order_query['total_days'];

    // Get the current page URL to handle form submissions
    $current_url = add_query_arg(array('user_id' => $user->ID), self_admin_url('user-edit.php'));

    if (isset($_GET['fix_sponsor_data']) && $_GET['fix_sponsor_data'] == 1 && isset($_GET['user_id']) && $_GET['user_id'] == $user->ID) {
        //恢复订阅
        $review_cancel = (isset($_GET['review_cancel']) && $_GET['review_cancel'] == 1);

        $fix_result = aya_sponsor_fix_user_data($user->ID, $review_cancel);

        if ($fix_result === true) {
            echo '<div class="updated"><p>' . __('用户订阅数据已修复', 'AIYA') . '</p></div>';
        } elseif (is_string($fix_result)) {
            echo '<div class="notice notice-warning"><p>' . esc_html($fix_result) . '</p></div>';
        }
    }
    //开始输出区块表单
    ?>
    <h2><?php _e('赞助者管理', 'AIYA'); ?></h2>
    <p>
        <?php _e('当前订阅状态：', 'AIYA'); ?>
        <?php if ($force_cancel == '1'): ?>
            <span style="color:red;"><?php _e('已被管理员强制取消', 'AIYA'); ?></span>
        <?php elseif ($is_valid): ?>
            <span style="color:green;"><?php _e('有效', 'AIYA'); ?>&nbsp;&nbsp;</span>
            <?php printf(__('剩余 <strong>%d</strong> 天', 'AIYA'), $left_days); ?>
        <?php else: ?>
            <span style="color:gray;"><?php _e('无效', 'AIYA'); ?></span>
        <?php endif; ?>
    </p>
    <table class="form-table">
        <tr>
            <th><label><?php _e('重新验证订阅有效性', 'AIYA'); ?></label></th>
            <td>
                <a href="<?php echo esc_url(add_query_arg(array('fix_sponsor_data' => 1), $current_url)); ?>" class="button">
                    <?php _e('重新计算到期日', 'AIYA'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg(array('fix_sponsor_data' => 1, 'review_cancel' => 1), $current_url)); ?>" class="button" style="margin-left: 10px;">
                    <?php _e('恢复赞助者权限', 'AIYA'); ?>
                </a>
            </td>
        </tr>
        <tr>
            <th><label for="aya_force_cancel_sponsor"><?php _e('取消此用户赞助者权限', 'AIYA'); ?></label></th>
            <td>
                <input type="checkbox" name="aya_force_cancel_sponsor" id="aya_force_cancel_sponsor" value="1" <?php checked($force_cancel, '1'); ?> />
                <span class="description"><?php _e('勾选后，该用户即使有赞助订单生效也视为非赞助者。', 'AIYA'); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="aya_admin_add_order_days"><?php _e('手动添加赞助订单', 'AIYA'); ?></label></th>
            <td>
                <input type="number" name="aya_admin_add_order_days" id="aya_admin_add_order_days" min="1" step="1" value="" />
                <p class="description"><?php _e('输入要添加的赞助天数（必填）', 'AIYA'); ?></p>
            </td>
        </tr>
    </table>
    <h3><?php _e('赞助订单记录', 'AIYA'); ?></h3>
    <p>
        <?php printf(__('累计已赞助 <strong>%d</strong> 天', 'AIYA'), $total_days); ?>
    </p>
    <div class="sponsor-orders-list" style="margin-top: 15px;">
        <table class="widefat" style="width: 100%;">
            <thead>
                <tr>
                    <th><?php _e('订单号', 'AIYA'); ?></th>
                    <th><?php _e('开始时间', 'AIYA'); ?></th>
                    <th><?php _e('天数', 'AIYA'); ?></th>
                    <th><?php _e('来源', 'AIYA'); ?></th>
                    <th><?php _e('状态', 'AIYA'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo esc_html($order->order_id); ?></td>
                        <td><?php echo date('Y-m-d H:i', intval($order->start_time)); ?></td>
                        <td><?php echo intval($order->duration_days); ?></td>
                        <td><?php echo esc_html($order->source); ?></td>
                        <td><?php echo esc_html($order->status); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

//用户后台页面区块数据保存
function aya_sponsor_save_orders_user_profile($user_id)
{
    if (!current_user_can('manage_options')) {
        return;
    }

    //禁用用户
    if (isset($_POST['aya_force_cancel_sponsor'])) {
        update_user_meta($user_id, 'aya_force_cancel_sponsor', '1');
    } else {
        delete_user_meta($user_id, 'aya_force_cancel_sponsor');
    }
    //手动添加赞助订单
    if (!empty($_POST['aya_admin_add_order_days'])) {
        $days = intval($_POST['aya_admin_add_order_days']);

        //生成订单号
        $order_id = 'sys_' . time() . random_int(1000, 9999);

        $result = aya_sponsor_add_order($user_id, $order_id, $days, 'paid', 'admin');
    }
    //捕捉报错
    if (is_wp_error($result)) {
        return new WP_Error('admin_notices', $result->get_error_message());
    }
}

/*
 * ------------------------------------------------------------------------------
 * 用户收藏夹功能
 * ------------------------------------------------------------------------------
 */

//取回收藏列表
function aya_user_get_favorite_posts($user_id = 0)
{
    if (!is_user_logged_in()) {
        return false;
    }
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    $favorites = get_user_meta($user_id, 'favorite_posts', true);

    //数据错误
    if (empty($favorites) || !is_array($favorites)) {
        return [];
    }

    $query = new AYA_Query_Post();

    return $query->list_posts($favorites, ['post']);
}

//收藏列表数据查询结果
function aya_user_favorite_posts_data()
{
    $favorites = aya_user_get_favorite_posts();
    $the_posts = [];
    $store_nonce = '';

    if ($favorites !== false && is_array($favorites)) {
        //循环查询结果
        $the_posts = [];
        foreach ($favorites as $post) {
            $post = new AYA_Post_In_While($post);

            $the_posts[$post->id] = [
                'id' => $post->id,
                'date' => $post->date,
                'modified' => $post->modified,
                'title' => $post->title,
                'status' => $post->status,
                'url' => $post->url,
            ];
        }
        //交互功能安全参数
        $store_nonce = aya_nonce_active_store();
    }

    return [
        'ajax_url' => AYA_AJAX_URI,
        'posts' => $the_posts,
        'ajax_nonce' => $store_nonce
    ];
}

//在用户后台页面添加自定义区块
add_action('show_user_profile', 'aya_user_show_favorites_profile');

function aya_user_show_favorites_profile($user)
{
    $user_id = get_current_user_id();
    // 只对管理员显示此区块，或者当前用户查看自己的资料
    if (!current_user_can('manage_options') && $user_id != $user->ID) {
        return;
    }

    // 获取收藏文章
    $favorites = aya_user_get_favorite_posts($user_id);

    // 开始输出区块
    ?>
    <h2><?php _e('收藏夹', 'AIYA'); ?></h2>

    <?php if (empty($favorites)): ?>
        <p><?php _e('暂无收藏文章', 'AIYA'); ?></p>
    <?php else: ?>
        <p><?php printf(__('共有 <strong>%d</strong> 篇收藏文章', 'AIYA'), count($favorites)); ?></p>
        <div class="favorite-posts-list" style="margin-top: 15px;">
            <table class="widefat" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 60%;"><?php _e('文章标题', 'AIYA'); ?></th>
                        <th><?php _e('作者', 'AIYA'); ?></th>
                        <th><?php _e('发布日期', 'AIYA'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($favorites as $post):
                        //循环查询结果
                        $post = new AYA_Post_In_While($post);
                        ?>
                        <tr>
                            <td><a href="<?php echo $post->url; ?>" target="_blank"><?php echo $post->title; ?></a></td>
                            <td><?php echo $post->author_name; ?></td>
                            <td><?php echo $post->date; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php
}

/*
 * ------------------------------------------------------------------------------
 * 用户菜单数据
 * ------------------------------------------------------------------------------
 */

//处理用户级别
function aya_user_toggle_level($user_id = 0)
{
    //是游客
    if (!is_user_logged_in()) {
        return 'guest';
    }

    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    //编辑及以上统一认为是管理员
    if (user_can($user_id, 'edit_pages')) {
        return 'administrator';
    }
    //有投稿权限
    else if (user_can($user_id, 'publish_posts')) {
        return 'author';
    }
    //是赞助用户
    else if (aya_is_sponsor($user_id)) {
        return 'sponsor';
    }
    //是普通用户
    else {
        return 'subscriber';
    }
}

//生成用户菜单数据
function aya_user_get_login_data($logged_in = false)
{
    $user_menu = [];

    if ($logged_in) {
        //获取用户对象
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        //用户详细信息
        $user_menu['data'] = [
            'id' => $user_id,
            'avatar' => get_avatar_url($user_id, ['size' => 64]),
            'role' => aya_user_toggle_level($user_id),
            'name' => $current_user->display_name,
            'email' => $current_user->user_email,
        ];

        //判断用户权限更新选项菜单
        $dorpdown_menus = [];

        //仪表盘链接
        if (user_can($user_id, 'edit_pages')) {
            $dorpdown_menus[] = [
                'label' => __('管理后台', 'AIYA'),
                'icon' => 'dashboard',
                'url' => admin_url(),
                'targe_blank' => true
            ];
        }
        $dorpdown_menus[] = [
            'label' => __('个人中心', 'AIYA'),
            'icon' => 'profile',
            'url' => get_author_posts_url($user_id),
        ];
        $dorpdown_menus[] = [
            'label' => __('获取订阅', 'AIYA'),
            'icon' => 'sponsor',
            'url' => home_url('sponsor'),
        ];
        $dorpdown_menus[] = [
            'label' => __('我的收藏', 'AIYA'),
            'icon' => 'inbox',
            'url' => home_url('favlist'),
        ];

        $user_menu['menus'] = $dorpdown_menus;
        $user_menu['rest_nonce'] = wp_create_nonce('wp_rest');
    } else {
        //获取站点设置是否允许注册
        $user_menu['enable_register'] = get_option('users_can_register') ? true : false;
        //TODO 社交登录
        $user_menu['enable_sso_register'] = false; //aya_opt('allow_sso_register_bool', 'access');
        $user_menu['rest_nonce'] = wp_create_nonce('wp_rest');
    }

    return $user_menu;
}

//收集可用的赞助方案列表
function aya_sponsor_get_user_plan()
{
    $order_plan = [];

    $order_plan = apply_filters('aya_sponsor_plan_add', $order_plan);

    //防止过滤器操作出错，返回空数组阻止报错
    if (!is_array($order_plan)) {
        $order_plan = [];
    }

    return $order_plan;
}

//获取用户赞助方案数据
function aya_user_sponsor_plan_data()
{
    //获取用户的赞助订单
    $order_query = aya_sponsor_get_user_orders();

    //未登录时取消数据加载
    if ($order_query === false) {
        //返回空的查询
        return [
            'from_source' => [],
            'order_plan' => [],
            'order_history' => [],
            'rest_nonce' => '',
        ];
    }

    //获取订阅支付方案列表
    $order_plan = aya_sponsor_get_user_plan();

    if (empty($order_query)) {
    } else {
        //计算到期时间戳为可读时间
        $order_query['expiration'] = date_i18n('Y-m-d', $order_query['expiration']);
        //被强制取消状态
        $order_query['force_cancel'] = ($order_query['force_cancel'] === '1') ? true : false;
    }

    //系统可用的激活码来源
    $code_from = aya_payment_allowable_activation_code();

    return [
        'from_source' => $code_from,
        'order_plan' => $order_plan,
        'order_history' => $order_query,
        'rest_nonce' => wp_create_nonce('wp_rest'),
    ];
}