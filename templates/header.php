<?php

/**
 * 
 * NOTE: All wp-head info tag:
 * 
 * bloginfo('html_type');
 * bloginfo('charset');
 * bloginfo('description');
 * bloginfo('version');
 * bloginfo('pingback_url');
 * 
 * NOTE: 模板文件加载结构
 * cards / loop卡片组件
 * contents / 正文内容组件
 * parts / 页面复用组件
 * units / 可选组件
 * 其余为页面模板
 * 
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name='viewport' content='width=device-width, initial-scale=1' />
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no, email=no">
    <meta http-equiv="content-language" content="<?php aya_echo(get_locale()); ?>" />
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta http-equiv="Cache-Control" content="private">
    <?php wp_head(); ?>
</head>

<body <?php //body_class(); ?>>
    <?php wp_body_open(); ?>