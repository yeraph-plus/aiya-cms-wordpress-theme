<?php

if (!defined('ABSPATH')) {
    exit;
}

//创建主题设置
AYF::new_opt([
    'title' => __('消息通知', 'aiya-cms'),
    'parent' => 'basic',
    'slug' => 'notify',
    'desc' => __('AIYA-CMS 主题，功能和必要组件设置'),
    'fields' => [
        [
            'desc' => __('通知列表设置'),
            'type' => 'title_2',
        ],
        [
            'title' => __('添加自定义通知', 'aiya-cms'),
            'desc' => __('添加新通知消息'),
            'id' => 'site_custom_notify_list',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => __('通知标题', 'aiya-cms'),
                    'desc' => __('添加新通知消息'),
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('通知内容', 'aiya-cms'),
                    'desc' => __('消息内容'),
                    'id' => 'content',
                    'type' => 'textarea',
                    'default' => '',
                ],
                [
                    'title' => __('通知图标', 'aiya-cms'),
                    'desc' => __('消息提示图标'),
                    'id' => 'level',
                    'type' => 'radio',
                    'sub' => [
                        'message' => __('信息', 'aiya-cms'),
                        'info' => __('提示', 'aiya-cms'),
                        'success' => __('成功', 'aiya-cms'),
                        'warning' => __('警告', 'aiya-cms'),
                        'danger' => __('错误', 'aiya-cms'),
                    ],
                    'default' => 'message',
                ],
                [
                    'title' => __('通知级别', 'aiya-cms'),
                    'desc' => __('接收此消息的用户权限级别
                    [br/]公开：全局消息，游客和所有用户可见
                    [br/]注册用户：已登录的订阅者及以上权限用户可见
                    [br/]作者用户：已登录的投稿者、作者及以上权限用户可见
                    [br/]管理员用户：已登录的编辑权限用户、管理员可见', 'aiya-cms'),
                    'id' => 'scope',
                    'type' => 'radio',
                    'sub' => [
                        'guest' => __('公开', 'aiya-cms'),
                        'user' => __('注册用户', 'aiya-cms'),
                        'author' => __('作者用户', 'aiya-cms'),
                        'administrator' => __('管理员用户', 'aiya-cms'),
                    ],
                    'default' => 'user',
                ],
                [
                    'id' => 'time',
                    'type' => 'hidden',
                    'default' => time(),
                ],
            ]
        ],

    ]
]);
