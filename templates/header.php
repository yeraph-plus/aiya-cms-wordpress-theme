<?php

if (!defined('ABSPATH')) {
    exit;
}

/** 
 * NOTE: All wp-head info tag:
 * 
 * bloginfo('html_type');
 * bloginfo('charset');
 * bloginfo('description');
 * bloginfo('version');
 * bloginfo('pingback_url');
 * 
 */

$logo_url = aya_opt('site_logo_image_upload', 'basic');
$site_name = get_bloginfo('name');
$is_home = aya_page_is('home');

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

<body <?php (aya_is_debug()) ? body_class() : null; ?>>
    <?php wp_body_open(); ?>
    <!-- React App -->
    <header
        class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container mx-auto flex h-16 items-center px-4">
            <div class="flex w-10 items-center justify-start md:hidden">
                <?php
                $menu_items = aya_get_menu('header-menu');
                $notify_items = aya_notify_list();
                $user_login_data = aya_user_get_login_data();
                aya_react_island('navbar-mobile', [
                    'menu' => $menu_items,
                    'notes' => $notify_items,
                ]);
                ?>
            </div>
            <div class="flex flex-1 items-center justify-center md:flex-none md:justify-start">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center justify-center space-x-2 md:mr-6 md:justify-start">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>"
                        class="h-8 w-auto" itemprop="logo" />
                    <?php
                    if (aya_opt('site_logo_text_bool', 'basic')) {
                        $text_tag = $is_home ? 'h1' : 'span';
                        $text_class = $logo_url ? 'ml-2' : '';
                        aya_echo('<' . $text_tag . ' class="' . $text_class . ' hidden font-bold sm:inline-block text-lg" itemprop="name">' . esc_html($site_name) . '</' . $text_tag . '>');
                    }
                    ?>
                </a>
            </div>
            <div class="hidden md:flex flex-1 items-center justify-center">
                <?php
                aya_react_island('navbar-menu', ['menu' => $menu_items]);
                ?>
            </div>
            <div class="flex w-10 items-center justify-end md:hidden">
                <?php
                aya_react_island('navbar-user', array_merge($user_login_data, ['compact' => true]));
                ?>
            </div>
            <div class="hidden items-center space-x-2 md:flex">
                <?php
                aya_react_island('navbar-search');
                aya_react_island('ui-mode-toggle');
                // TODO 需要将通知组件添加到store
                aya_react_island('navbar-notify', $notify_items);
                aya_react_island('navbar-user', $user_login_data);
                ?>
            </div>
        </div>
    </header>
    <div class="container mx-auto p-4 md:p-0 transition-all duration-300 ease-in-out">
        <div class="layout flex flex-col lg:flex-row gap-4">
            <main class="layout w-full lg:w-4/5">
                <!-- Breadcrumb -->
                <?php aya_template_part_load('breadcrumb', ['items' => aya_get_breadcrumb()]); ?>
                <!-- Ad Space -->
                <?php
                // 获取广告位
                $post_ads = aya_opt('site_ad_home_before_mult', 'land');

                if (!aya_is_sponsor() && !empty($post_ads) && is_array($post_ads)) {
                    aya_react_island('content-ad-space', ['ads' => array_values($post_ads)]);
                }
                ?>