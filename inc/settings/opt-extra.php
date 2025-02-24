<?php
if (!defined('ABSPATH')) exit;

//创建主题设置
AYF::new_opt([
    'title' => '拓展包',
    'parent' => 'basic',
    'slug' => 'extra',
    'desc' => 'AIYA-CMS 主题，拓展包',
    'fields' => [
        [
            'desc' => '拓展组件管理',
            'type' => 'title_1',
        ],
        [
            'title' => '经典编辑器增强',
            'desc' => '重排经典编辑器工具栏，增加表格插入等功能',
            'id' => 'site_plugin_editor_modify',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '简码图床插件',
            'desc' => '绕过WP媒体库上传，直接保存图片到当前服务器中',
            'id' => 'site_plugin_sc_picbed',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '代码高亮',
            'desc' => '前台加载 Highlight.js 组件，并对文章启用短代码功能',
            'id' => 'site_plugin_code_highlight',
            'type' => 'switch',
            'default' => false,
        ],
    ]
]);
