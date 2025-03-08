<?php

if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 通知生成组件
 * ------------------------------------------------------------------------------
 */

//简单调度器
//通知列表消息钩子
function aya_notify_filter_hook($notes)
{
    //存储变量
    static $stored_notes = array();
    static $note_count = 0;

    //合并通知
    $stored_notes[$note_count] = $notes;

    $note_count++;

    return $stored_notes;
}
add_filter('aya_notify_list', 'aya_notify_filter_hook', 10, 1);

//合并方法
function aya_notify_create($icon = null, $title = null, $message = null, $time = null)
{

    //组合通知
    $note = array(
        'icon' => aya_notify_icon_selector($icon),
        'title' => ($title),
        'message' => ($message),
        'time' => ($time),
    );

    //添加到通知列表
    return apply_filters('aya_notify_list', $note);
}

//图标名转换
function aya_notify_icon_selector($icon_name)
{
    switch ($icon_name) {
        case 'info':
            $icon = '<span class="grid place-content-center w-9 h-9 rounded-full bg-info-light dark:bg-info text-info dark:text-info-light"><i data-feather="navigation"></i></span>';
            break;
        case 'success':
            $icon = '<span class="grid place-content-center w-9 h-9 rounded-full bg-success-light dark:bg-success text-success dark:text-success-light"><i data-feather="coffee"></i></span>';
            break;
        case 'warning':
            $icon = '<span class="grid place-content-center w-9 h-9 rounded-full bg-danger-light dark:bg-danger text-danger dark:text-danger-light"><i data-feather="alert-triangle"></i></span>';
            break;
        case 'danger':
            $icon = '<span class="grid place-content-center w-9 h-9 rounded-full bg-warning-light dark:bg-warning text-warning dark:text-warning-light"><i data-feather="alert-triangle">shield</i></span>';
            break;
        default:
            $icon = '';
            break;
    }
    return $icon;
}
//收集通知
function aya_notify_list_data()
{
    //获取通知列表
    $notes = apply_filters('aya_notify_list', array());
    //返回数据
    return aya_json_echo(array_filter($notes));
}

//demo
//aya_notify_create('success', 'Congratulations!', 'Your OS has been updated.', '1hr');
//aya_notify_create('info', 'Did you know?', 'You can switch between artboards.', '2hr');
//aya_notify_create('warning', 'Something went wrong!', 'Send Reposrt', '2days');
//aya_notify_create('danger', 'Warning', 'Your password strength is low.', '5days');
