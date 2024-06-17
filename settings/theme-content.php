<?php
if (!defined('ABSPATH')) exit;

//主题外观设置
AYF::new_opt(array(
    'title' => '文内页面',
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
            'title' => '显示分享按钮',
            'desc' => '开关分享组件，显示文章二维码和转发功能',
            'id' => 'site_share_post',
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'title' => '显示点赞按钮',
            'desc' => '开关点赞组件，显示文章点赞和点踩功能',
            'id' => 'site_belike_post',
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'title' => '标题附加锚点',
            'desc' => '文章正文内部&lt;h1&gt;、&lt;h2&gt;、&lt;h3&gt;标签自动添加锚点ID（用于生成文章目录）',
            'id' => 'site_post_menu_id',
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'title' => '外部链接跳转',
            'desc' => '文章正文内部&lt;a&gt;标签自动添加"nofollow"和"_blank"属性',
            'id' => 'site_link_nofollow',
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'title' => '外部链接跳转设置',
            'desc' => '接续上一项设置，选择链接自动跳转还是提示外部页面',
            'id' => 'site_link_jump_page',
            'type' => 'radio',
            'sub'  => array(
                'go' => '内链自动跳转',
                'link' => '提示外部页面',
            ),
            'default' => 'go',
        ),
        array(
            'desc' => '垃圾评论防护',
            'type' => 'title_2',
        ),
    ),
));
