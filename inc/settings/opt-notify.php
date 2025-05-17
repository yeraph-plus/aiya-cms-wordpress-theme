<?php
if (!defined('ABSPATH'))
    exit;

//创建主题设置
AYF::new_opt([
    'title' => '消息通知',
    'parent' => 'basic',
    'slug' => 'notify',
    'desc' => 'AIYA-CMS 主题，功能和必要组件设置',
    'fields' => [
        [
            'desc' => '通知列表设置',
            'type' => 'title_2',
        ],
        [
            'title' => '添加自定义通知',
            'desc' => '添加新通知消息',
            'id' => 'site_custom_notify_list',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '通知标题',
                    'desc' => '添加新通知消息',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '通知内容',
                    'desc' => '消息内容',
                    'id' => 'content',
                    'type' => 'textarea',
                    'default' => '',
                ],
                [
                    'title' => '通知图标',
                    'desc' => '消息提示图标',
                    'id' => 'level',
                    'type' => 'radio',
                    'sub' => [
                        'message' => '信息',
                        'info' => '提示',
                        'success' => '成功',
                        'warning' => '警告',
                        'danger' => '错误',
                    ],
                    'default' => 'message',
                ],
                [
                    'title' => '通知级别',
                    'desc' => '接收此消息的用户权限级别
                    [br/]公开：全局消息，游客和所有用户可见
                    [br/]注册用户：已登录的订阅者及以上权限用户可见
                    [br/]作者用户：已登录的投稿者、作者及以上权限用户可见
                    [br/]管理员用户：已登录的编辑权限用户、管理员可见',
                    'id' => 'scope',
                    'type' => 'radio',
                    'sub' => [
                        'guest' => '公开',
                        'user' => '注册用户',
                        'author' => '作者用户',
                        'administrator' => '管理员用户',
                    ],
                    'default' => 'user',
                ],
                [
                    'id' => 'time',
                    'type' => 'hidden',
                    'default' => time(),
                ],
            ],
        ],

    ]
]);
