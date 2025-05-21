<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 订阅功能组件方法
 * ------------------------------------------------------------------------------
 */

//自定义的赞助订单数据表结构
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
    global $wpdb;
    //表名
    $table = $wpdb->prefix . 'aya_sponsor_orders';

    //验证天数有效性
    $duration_days = intval($duration_days);

    if ($duration_days <= 0) {
        return new WP_Error('invalid_duration', 'Duration must be positive');
    }

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

    //强制重新计算
    $new_expiration = aya_sponsor_get_expiration_time($user_id);
    //缓存一条记录到用户Meta
    update_user_meta($user_id, 'sponsor_expiration', $new_expiration);

    return true;
}

//获取赞助者到期时间（遍历）
function aya_sponsor_get_expiration_time($user_id)
{
    global $wpdb;

    $table = $wpdb->prefix . 'aya_sponsor_orders';

    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT start_time, duration_days FROM $table WHERE user_id = %d ORDER BY start_time ASC",
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

    global $wpdb;

    $table = $wpdb->prefix . 'aya_sponsor_orders';

    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE user_id = %d ORDER BY start_time DESC",
        $user->ID
    ));

    //获取当前禁用状态
    $force_cancel = get_user_meta($user->ID, 'aya_force_cancel_sponsor', true);
    //获取到期时间
    $expiration = get_user_meta($user->ID, 'sponsor_expiration', true);

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

    //开始输出区块表单
    ?>
    <h2><?php _e('赞助者管理', 'AIYA'); ?></h2>
    <p>
        <?php _e('当前订阅状态：', 'AIYA'); ?>
        <?php if ($force_cancel == '1'): ?>
            <span style="color:red;"><?php _e('已被管理员强制取消', 'AIYA'); ?></span>
        <?php elseif ($is_valid): ?>
            <span style="color:green;"><?php _e('有效', 'AIYA'); ?>&nbsp;&nbsp;</span>
            <strong><?php _e('剩余', 'AIYA'); ?>         <?php echo $left_days; ?>         <?php _e('天', 'AIYA'); ?></strong>
        <?php else: ?>
            <span style="color:gray;"><?php _e('无效', 'AIYA'); ?></span>
        <?php endif; ?>
    </p>
    <table class="form-table">
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
    <p><?php printf(__('累计已赞助: <strong>%d</strong> 天', 'AIYA'), $total_days); ?></p>
    <table class="form-table">
        <tr>
            <th><?php _e('订单号', 'AIYA'); ?></th>
            <th><?php _e('开始时间', 'AIYA'); ?></th>
            <th><?php _e('天数', 'AIYA'); ?></th>
            <th><?php _e('来源', 'AIYA'); ?></th>
            <th><?php _e('状态', 'AIYA'); ?></th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo esc_html($order->order_id); ?></td>
                <td><?php echo date('Y-m-d H:i', intval($order->start_time)); ?></td>
                <td><?php echo intval($order->duration_days); ?></td>
                <td><?php echo esc_html($order->source); ?></td>
                <td><?php echo esc_html($order->status); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
}

//用户后台页面区块数据保存
function aya_sponsor_save_orders_user_profile($user_id)
{
    //if (!current_user_can('edit_user', $user_id)) return;

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
        $order_id = 'system_' . wp_generate_password(4, false) . '_' . time();

        $result = aya_sponsor_add_order($user_id, $order_id, $days, 'unpaid', 'admin');
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

add_action('wp_ajax_toggle_post_save', 'handle_toggle_post_save');

function handle_toggle_post_save()
{
    //验证nonce字段
    check_ajax_referer('save_post_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('error' => __('请先登录', 'AIYA')));
    }

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();
    $saved_posts = get_user_meta($user_id, 'saved_posts', true) ?: array();

    if (in_array($post_id, $saved_posts)) {
        // 取消收藏
        $saved_posts = array_diff($saved_posts, array($post_id));
        $is_saved = false;
    } else {
        // 添加收藏
        $saved_posts[] = $post_id;
        $is_saved = true;
    }

    update_user_meta($user_id, 'saved_posts', $saved_posts);

    wp_send_json_success(array('is_saved' => $is_saved));
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
    //是赞助者
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
            'label' => __('查看收藏夹', 'AIYA'),
            'icon' => 'inbox',
            'url' => home_url('inbox'),
        ];

        $user_menu['menus'] = $dorpdown_menus;
        $user_menu['rest_nonce'] = wp_create_nonce('wp_rest');
    } else {
        //获取站点设置是否允许注册
        $user_menu['enable_register'] = get_option('users_can_register') ? true : false;
        //获取找回密码链接
        $user_menu['lost_password_url'] = wp_lostpassword_url();
        //TODO 社交登录
        $user_menu['enable_sso_register'] = false; //aya_opt('allow_sso_register_switch');
        $user_menu['rest_nonce'] = wp_create_nonce('wp_rest');
    }

    return $user_menu;
}

//登录和用户菜单组件
function aya_vue_user_nemu_component()
{
    if (is_user_logged_in()) {
        return aya_vue_load('user-menu', aya_user_get_login_data(true));
    } else {
        return aya_vue_load('login-action', aya_user_get_login_data(false));
    }
}
