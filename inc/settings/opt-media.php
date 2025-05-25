<?php
if (!defined('ABSPATH'))
    exit;

//创建主题设置
AYF::new_opt([
    'title' => '媒体功能',
    'parent' => 'basic',
    'slug' => 'meida',
    'desc' => 'AIYA-CMS 主题，功能和必要组件设置',
    'fields' => [
        [
            'desc' => '静态资源设置（CDN）',
            'type' => 'title_1',
        ],
        [
            'title' => '静态文件加载位置',
            'desc' => '设置全站静态文件加载方式，使用CDN或从本地加载',
            'id' => 'site_scripts_load_type',
            'type' => 'radio',
            'sub' => [
                'local' => '本地加载',
                'cdnjs' => 'CloudFlare',
                'zstatic' => 'ZstaticCDN',
                'bootcdn' => 'BootCDN',
            ],
            'default' => 'cdnjs',
        ],
    ]
]);
