<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 简单消息调度器
 * ------------------------------------------------------------------------------
 */

//消息列表钩子
add_filter('aya_notify_add', 'aya_notify_collect_hook', 10, 1);
add_filter('aya_notify_scope_filter', 'aya_notify_check_scope_wrapper');
//调度自定义的消息列表
add_action('aya_home_open', 'aya_notify_join_custom_notification');

//收集所有原始消息
function aya_notify_collect_hook($note)
{
    //存储变量
    static $stored_notes = array();

    if (empty($note)) {
        return $stored_notes;
    }

    //合并到数组中
    $stored_notes[] = $note;

    return $stored_notes;
}

//消息权限过滤器内逻辑
function aya_notify_check_scope($scope, $is_logged)
{
    /**
     * 防脑死用权限检查鱼骨图
     * 
     * 全局（guest） -> yes -> true
     *   ->no
     * 检查已登录 -> no -> false
     *    -> yes
     * 是已登录的用户（user） -> yes -> true
     *   ->no
     * 是作者（author） -> yes -> 检查 edit_posts 权限
     *   ->no
     * 管理员（administrator） -> yes -> 检查 manage_options 权限
     *   ->no
     * false
     */

    //所有用户可见
    if ($scope === 'guest') {
        return true;
    }
    //否则其他类型均需要已登录
    if (!$is_logged) {
        return false;
    }
    //无需额外权限
    if ($scope === 'user') {
        return true;
    }
    //需编辑文章权限
    if ($scope === 'author') {
        return current_user_can('edit_posts');
    }
    //需管理权限
    if ($scope === 'administrator') {
        return current_user_can('publish_pages');
    }

    return false;
}

//消息权限过滤器
function aya_notify_check_scope_wrapper($notes)
{
    $is_logged = is_user_logged_in();

    //遍历
    foreach ($notes as $key => $note) {

        if (aya_notify_check_scope($note['scope'], $is_logged)) {
            //踢出列表
            unset($notes[$key]);
        }
    }

    return $notes;
}

//新增消息
function aya_notify_create($once_note = array())
{
    //消息数据结构
    $default_note = array(
        'level' => 'message',
        'scope' => 'guest',
        'title' => '',
        'content' => '',
        'time' => 'now',
    );
    //合并参数
    $note = wp_parse_args($once_note, $default_note);

    //允许的图标
    $allowed_levels = array('success', 'info', 'warning', 'error', 'message');
    if (!in_array($note['level'], $allowed_levels)) {
        $note['level'] = 'message';
    }

    //允许的通知范围
    $allowed_scopes = array('guest', 'user', 'author', 'administrator');
    if (!in_array($note['scope'], $allowed_scopes)) {
        $note['scope'] = 'guest';
    }

    //传入值不是整数
    if (!is_numeric($note['time'])) {
        //尝试计算为时间戳
        $timestamp = strtotime($note['time']);
        $note['time'] = $timestamp ?: time();
    }

    $note['title'] = wp_kses_post($note['title']);
    $note['content'] = wp_kses_post($note['content']);
    $note['time'] = date_i18n(get_option('date_format'), $note['time']);

    //添加到消息列表
    return apply_filters('aya_notify_add', $note);
}

//内部创建消息动作触发
function aya_notify_join_custom_notification()
{
    //加载设置表单中自定义消息列表
    $custom_notes = aya_opt('site_custom_notify_list', 'notify');

    if (!empty($custom_notes)) {

        foreach ($custom_notes as $note) {
            //合并到通知列表
            aya_notify_create($note);
        }
    }
    //加载系统生成的消息列表

    //TODO 系统消息

    return true;
}

//收集消息
function aya_notify_list()
{
    //获取消息列表
    $notes = apply_filters('aya_notify_add', []);
    //应用通知过滤器
    $notes = apply_filters('aya_notify_check_scope_wrapper', $notes);
    //返回数据
    return $notes;
}

/*
 * ------------------------------------------------------------------------------
 * WP内置消息加载
 * ------------------------------------------------------------------------------
 */

//TODO 检测到新版本、插件、主题更新
// 检测到核心更新时触发通知
add_action('wp_version_check', function () {
    $update_data = get_site_transient('update_core');
    if (!empty($update_data->updates)) {
        aya_notify_create([
            'level' => 'warning',
            'scope' => 'administrator',
            'title' => '系统更新',
            'content' => '检测到新的 WordPress 版本可用',
            'time' => time()
        ]);
    }
});

//TODO 新评论待审、文章审核状态变更、用户提及
/*
add_action('comment_post', function ($comment_ID, $comment_approved) {
    if ($comment_approved === 0) {
        $comment = get_comment($comment_ID);
        aya_notify_create([
            'level' => 'info',
            'scope' => 'author', // 或根据文章作者动态设置
            'title' => '新评论待审',
            'content' => sprintf('文章《%s》有新评论等待审核', get_the_title($comment->comment_post_ID)),
            'time' => time()
        ]);
    }
}, 10, 2);
*/

// 新用户注册通知 (管理员)、密码重置请求（用户）
/*
add_action('user_register', function ($user_id) {
    $user = get_userdata($user_id);
    aya_notify_create([
        'level' => 'success',
        'scope' => 'administrator',
        'title' => '新用户加入',
        'content' => sprintf('新用户 %s 已注册', $user->display_name),
        'time' => time()
    ]);
});
*/

// WooCommerce 新订单通知
/*
add_action('woocommerce_new_order', function ($order_id) {
    aya_notify_create([
        'level' => 'info',
        'scope' => 'administrator',
        'title' => '新订单',
        'content' => sprintf('新订单 #%s 已创建', $order_id),
        'time' => time()
    ]);
});
*/

// 登录失败告警
/*
add_action('wp_login_failed', function ($username) {
    aya_notify_create([
        'level' => 'error',
        'scope' => 'administrator',
        'title' => '登录异常',
        'content' => sprintf('用户 %s 登录失败', $username),
        'time' => time()
    ]);
});
*/

//IDEA 在用户META中设置允许的通知类型
//IDEA 邮件通知联动
//IDEA 在用户META中标记已读状态时间