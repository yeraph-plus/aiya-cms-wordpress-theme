<?php

if (!defined('ABSPATH')) {
    exit;
}


//创建主题设置
AYF::new_opt([
    'title' => '广告位',
    'parent' => 'basic',
    'slug' => 'ads',
    'desc' => 'AIYA-CMS 主题，功能和必要组件设置',
    'fields' => [
        [
            'desc' => '页面广告位',
            'type' => 'title_1',
        ],
        [
            'title' => '全局顶部',
            'desc' => '站点顶部广告位，支持 HTML ',
            'id' => 'site_ad_home_before',
            'type' => 'code_editor',
            'settings' => [
                'lineNumbers' => true,
                'tabSize' => 2,
                'theme' => 'monokai',
                'mode' => 'javascript',
            ],
            'default' => '',
        ],
        [
            'title' => '全局底部',
            'desc' => '站点底部广告位，支持 HTML ',
            'id' => 'site_ad_home_after',
            'type' => 'code_editor',
            'settings' => [
                'lineNumbers' => true,
                'tabSize' => 2,
                'theme' => 'monokai',
                'mode' => 'javascript',
            ],
            'default' => '',
        ],
        [
            'desc' => '文章广告位',
            'type' => 'title_1',
        ],
        [
            'title' => '文章顶部',
            'desc' => '插入到文章开头的广告位，支持 HTML ',
            'id' => 'site_ad_post_before',
            'type' => 'code_editor',
            'settings' => [
                'lineNumbers' => true,
                'tabSize' => 2,
                'theme' => 'monokai',
                'mode' => 'javascript',
            ],
            'default' => '',
        ],
        [
            'title' => '文章底部',
            'desc' => '插入到文章结尾的广告位，支持 HTML ',
            'id' => 'site_ad_post_after',
            'type' => 'code_editor',
            'settings' => [
                'lineNumbers' => true,
                'tabSize' => 2,
                'theme' => 'monokai',
                'mode' => 'javascript',
            ],
            'default' => '',
        ],
    ]
]);
