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
 * NOTE: Template loading structure
 * cards / the while loop post card
 * contents / the while loop post content
 * parts / the page independent component
 * units / the reuse unit of component
 * root / the page layout component
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
    <?php
    //The wp_head action
    wp_head();
    ?>
    <!-- Current the body <?php body_class(); ?> of. -->
</head>

<body x-data="main" class="antialiased relative font-nunito text-sm font-normal overflow-x-hidden" :class="[ $store.app.menuBar ? 'toggle-sidebar' : '', $store.app.colorScheme === 'dark' || $store.app.isDarkMode ? 'dark' : '', $store.app.navbarMenu, $store.app.bodyLayout, $store.app.rtlClass ]">
    <?php
    //页面组件
    aya_template_load('parts/screen-loader');
    aya_template_load('parts/overlay');
    //模板顶部动作钩子
    aya_body_start();
    ?>
    <div id="main-container" class="main-container text-black dark:text-white-dark min-h-screen" :class="[$store.app.navbarSticky]">
        <?php
        //左侧边菜单
        aya_template_load('parts/navbar');
        ?>
        <div id="swup-container" class="main-content flex flex-col min-h-screen">
            <?php
            //顶栏
            aya_template_load('parts/header');
            //The wp_body_open action
            wp_body_open();
            ?>