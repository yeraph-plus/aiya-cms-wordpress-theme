<?php

if (!defined('ABSPATH')) {
    exit;
}

//创建主题设置
AYF::new_opt([
    'title' => '页面组件',
    'parent' => 'basic',
    'slug' => 'land',
    'desc' => 'AIYA-CMS 主题，页面组件设置',
    'fields' => [
        [
            'desc' => '社交登录设置',
            'type' => 'title_1',
        ],
        [
            'desc' => 'To be continued ...',
            'type' => 'content',
        ],
        /*
        [
            'title' => 'OpenID 登录',
            'desc' => '启用 OpenID 登录接入',
            'id' => 'site_oauth_openid_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => 'Github 登录',
            'desc' => '启用 Github 登录接入',
            'id' => 'site_oauth_github_bool',
            'type' => 'switch',
            'default' => false,
        ],
        */
        [
            'desc' => '页面广告位（AD）',
            'type' => 'title_1',
        ],
        [
            'title' => '全局顶部',
            'desc' => '广告位在页面顶部显示',
            'id' => 'site_ad_home_before_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '广告视图',
                    'desc' => '广告的图片链接 ',
                    'id' => 'view',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => '全局底部',
            'desc' => '广告位在页面底部显示',
            'id' => 'site_ad_home_after_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '广告视图',
                    'desc' => '广告的图片链接 ',
                    'id' => 'view',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => '文内顶部',
            'desc' => '广告位在文章顶部显示（仅文本链接）',
            'id' => 'site_ad_post_before_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => '文内底部',
            'desc' => '广告位在文章底部显示（仅文本链接）',
            'id' => 'site_ad_post_after_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'desc' => ' OpenList 客户端模块设置',
            'type' => 'title_1',
        ],
        [
            'desc' => ' OpenList 是一个开源的网盘列表程序，此模块用于直接调用 OpenList 的文件列表显示在文章页面上',
            'type' => 'content',
        ],
        aya_oplist_server_option_test_request(),
        [
            'title' => '服务器地址',
            'desc' => ' OpenList 的服务器地址',
            'id' => 'site_oplist_server_url',
            'type' => 'text',
            'default' => 'https://your.openlist.server',
        ],
        [
            'title' => '用户名',
            'desc' => '用于请求 OpenList API 登录的用户',
            'id' => 'site_oplist_server_user',
            'type' => 'text',
            'default' => 'username',
        ],
        [
            'title' => '密码',
            'desc' => '用于请求 OpenList API 登录的用户',
            'id' => 'site_oplist_server_pswd',
            'type' => 'text',
            'default' => 'password',
        ],
        [
            'title' => '令牌缓存时间',
            'desc' => ' OpenList 的 JWt Token 缓存时间（小时），设置为 [code]0[/code] 则每次都重新请求令牌（*取决于 OpenList 站点配置，默认为 48 小时）',
            'id' => 'site_oplist_server_token_hours',
            'type' => 'text',
            'default' => '48',
        ],
        [
            'title' => '启用文件图标匹配',
            'desc' => '在 OpenList 返回文件列表时为文件匹配图标',
            'id' => 'site_oplist_fs_icon_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '文件跳转设置',
            'desc' => '设置从文件列表跳转到 OpenList 的方法
                [br/]文件页面：直接跳转到 OpenList 的文件/文件夹详情页面
                [br/]直接下载：下载文件（由 OpenList 完成 302 跳转）
                [br/]代理下载：下载文件（由 OpenList 本机代理下载）
                [br/]直链下载：循环请求尝试直接取出真实文件地址，会大幅增加加载时间
                ',
            'id' => 'site_oplist_fs_link_type',
            'type' => 'radio',
            'sub' => [
                'f' => '详情页面',
                'd' => '直接下载',
                'p' => '代理下载',
                'r' => '直链下载',
            ],
            'default' => 'd',
        ],
        [
            'title' => '默认列表描述',
            'desc' => '设置 OpenList 的文件列表底部默认的描述文本',
            'id' => 'site_oplist_file_desc',
            'type' => 'textarea',
            'default' => '文件下载由 your.openlist.server 提供支持',
        ],
    ]
]);
