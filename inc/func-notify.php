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
add_filter('aya_notify_list', 'aya_notify_collect_hook', 10, 1);
//调度自定义的消息列表
add_action('aya_home_open', 'aya_notify_custom_notification_join_action');

//收集所有原始消息
function aya_notify_collect_hook($notes)
{
    //存储变量
    static $stored_notes = array();
    static $note_count = 0;

    //合并到数组中
    $stored_notes[$note_count] = $notes;

    $note_count++;

    return $stored_notes;
}

//内置的消息权限过滤器逻辑
function aya_notify_check_scope($scope)
{
    static $is_logged = is_user_logged_in();

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

    //时间匹配为时间戳
    if (!is_numeric($note['time'])) {
        //TODO 尝试计算为时间戳
        $note['time'] = time();
    }

    $note['title'] = wp_kses_post($note['title']);
    $note['content'] = wp_kses_post($note['content']);
    $note['time'] = date_i18n(get_option('date_format'), $note['time']);

    //添加到消息列表
    return apply_filters('aya_notify_list', $note);
}

//内部创建消息动作触发
function aya_notify_custom_notification_join_action()
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

    return;
}

//收集消息
function aya_notify_list()
{
    //获取消息列表
    $notes = apply_filters('aya_notify_list', []);
    $notes = array_filter($notes);
    //应用通知过滤器
    $notes = array_filter($notes, function ($note) {
        return aya_notify_check_scope($note['scope']);
    });
    //返回数据
    return $notes;
}

//组件位置
function aya_vue_notify_component()
{
    return aya_vue_load(
        //组件名
        'notify-list',
        //传递数据
        aya_notify_list()
    );
}