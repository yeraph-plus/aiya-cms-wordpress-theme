<?php
if (!defined('ABSPATH')) exit;

//主题外观设置
AYF::new_opt(array(
    'title' => '格式设置',
    'parent' => 'theme',
    'slug' => 'single',
    'desc' => 'AIYA-CMS 主题，文章和页面样式设置',
    'fields' => array(
        array(
            'desc' => '正文格式',
            'type' => 'title_2',
        ),
        array(
            'title' => '中文格式化排版',
            'desc' => '执行统一排版纠正，使用<code>Chinese-Typesetting</code>项目',
            'id' => 'site_post_chinese_format',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'desc' => '外部链接跳转',
            'type' => 'title_2',
        ),
        array(
            'title' => '外部链接跳转设置',
            'desc' => '文章正文内部&lt;a&gt;标签自动添加"nofollow"和"_blank"属性，选择链接自动跳转还是提示外部页面',
            'id' => 'site_single_link_jump_page',
            'type' => 'radio',
            'sub'  => array(
                'false' => '直接跳转（不处理）',
                'go' => '内链跳转',
                'link' => '提示外部页面',
            ),
            'default' => 'go',
        ),
        array(
            'desc' => '格式模板',
            'type' => 'title_2',
        ),
        array(
            'title' => '标题附加锚点',
            'desc' => '文章正文内部&lt;h1&gt;、&lt;h2&gt;、&lt;h3&gt;标签自动添加锚点ID（用于生成文章目录）',
            'id' => 'site_post_menu_id',
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'desc' => '垃圾评论防护',
            'type' => 'title_2',
        ),
    ),
));
