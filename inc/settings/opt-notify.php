<?php
if (!defined('ABSPATH')) exit;

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
    ]
]);
