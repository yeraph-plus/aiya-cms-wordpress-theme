<?php

if (!defined('ABSPATH')) {
    exit;
}

//创建主题设置
AYF::new_opt([
    'title' => __('OpenList 客户端', 'aiya-cms'),
    'parent' => 'basic',
    'slug' => 'oplist',
    'desc' => __('AIYA-CMS 主题，页面组件设置', 'aiya-cms'),
    'fields' => [
        [
            'desc' => __('OpenList 客户端模块设置', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'desc' => __('OpenList 是一个开源的网盘列表程序，此模块用于直接调用 OpenList 的文件列表显示在文章页面上', 'aiya-cms'),
            'type' => 'content',
        ],
        aya_oplist_server_option_test_request(),
        [
            'title' => __('服务器地址', 'aiya-cms'),
            'desc' => __('OpenList 的服务器地址', 'aiya-cms'),
            'id' => 'site_oplist_server_url',
            'type' => 'text',
            'default' => 'https://your.openlist.server',
        ],
        [
            'title' => __('用户名', 'aiya-cms'),
            'desc' => __('用于请求 OpenList API 登录的用户', 'aiya-cms'),
            'id' => 'site_oplist_server_user',
            'type' => 'text',
            'default' => 'username',
        ],
        [
            'title' => __('密码', 'aiya-cms'),
            'desc' => __('用于请求 OpenList API 登录的用户', 'aiya-cms'),
            'id' => 'site_oplist_server_pswd',
            'type' => 'text',
            'default' => 'password',
        ],
        [
            'title' => __('令牌缓存时间', 'aiya-cms'),
            'desc' => __('OpenList 的 JWt Token 缓存时间（小时），设置为 [code]0[/code] 则每次都重新请求令牌（*取决于 OpenList 站点配置，默认为 48 小时）', 'aiya-cms'),
            'id' => 'site_oplist_server_token_hours',
            'type' => 'text',
            'default' => '48',
        ],
        [
            'title' => __('启用文件图标匹配', 'aiya-cms'),
            'desc' => __('在 OpenList 返回文件列表时为文件匹配图标', 'aiya-cms'),
            'id' => 'site_oplist_fs_icon_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => __('文件跳转设置', 'aiya-cms'),
            'desc' => __('设置从文件列表跳转到 OpenList 的方法
                [br/]文件页面：直接跳转到 OpenList 的文件/文件夹详情页面
                [br/]直接下载：下载文件（由 OpenList 完成 302 跳转）
                [br/]代理下载：下载文件（由 OpenList 本机代理下载）
                [br/]直链下载：循环请求尝试直接取出真实文件地址，会大幅增加加载时间
                ', 'aiya-cms'),
            'id' => 'site_oplist_fs_link_type',
            'type' => 'radio',
            'sub' => [
                'f' => __('文件页面', 'aiya-cms'),
                'd' => __('直接下载', 'aiya-cms'),
                'p' => __('代理下载', 'aiya-cms'),
                'r' => __('直链下载', 'aiya-cms'),
            ],
            'default' => 'd',
        ],
        [
            'title' => __('默认列表描述', 'aiya-cms'),
            'desc' => __('设置 OpenList 的文件列表底部默认的描述文本', 'aiya-cms'),
            'id' => 'site_oplist_file_desc',
            'type' => 'textarea',
            'default' => '文件下载由 your.openlist.server 提供支持',
        ],
    ]
]);
