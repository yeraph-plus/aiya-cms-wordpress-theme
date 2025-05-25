<?php
if (!defined('ABSPATH')) exit;

//创建主题设置
AYF::new_opt([
    'title' => '广告位',
    'parent' => 'basic',
    'slug' => 'ads',
    'desc' => 'AIYA-CMS 主题，功能和必要组件设置',
    'fields' => [
        [
            'desc' => '顶部广告位',
            'type' => 'title_1',
        ],
        
    ]
]);
