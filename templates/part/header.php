<?php

if (!defined('ABSPATH')) {
    exit;
}

$app_config = [
    'defaultDarkMode' => aya_opt('site_default_dark_mode_bool', 'basic') ? 'true' : 'false',
    'defaultSitebarClose' => aya_opt('site_default_sitebar_close_bool', 'basic') ? 'true' : 'false'
];

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

<body <?php if (aya_is_dev_mode()) {
    body_class();
} ?>>
    <?php aya_template_load('units/screen-loader'); ?>
    <?php wp_body_open(); ?>
    <?php aya_home_open(); ?>
    <div id="vue-app" class="min-h-screen overflow-hidden" style="visibility: hidden" data-config="<?php aya_echo(aya_vue_json_encode($app_config)); ?>">
        <!-- Mobile Sidebar Mask -->
        <div v-if="!sidebarToggle && isMobile" @click="sidebarToggle = false" class="fixed md:hidden inset-0 bg-base-300/30 backdrop-blur-sm transition-all duration-300 ease-in-out z-20"></div>
        <!-- Topbar -->
        <header class="flex fixed z-40 w-full bg-base-100 border-b border-base-300 min-h-16 p-2 transition-all duration-300 ease-in-out">
            <div class="inline-flex w-64 items-center justify-start px-4">
                <!-- Logo -->
                <?php aya_blog_logo('max-w-[180px] overflow-hidden whitespace-nowrap text-xl font-bold ', 'h-8 w-auto'); ?>
                <!-- Sidebar Toggle -->
                <button @click="sidebarToggle = !sidebarToggle" class="btn btn-square btn-ghost ml-4">
                    <icon v-if="!sidebarToggle" name="bars-3" class="size-5"></icon>
                    <icon v-else name="bars-3-bottom-left" class="size-5"></icon>
                </button>
            </div>
            <div class="flex-1 hidden lg:flex items-center flex-shrink-0">
                <?php aya_vue_load('nav-menu', ['menu' => aya_get_menu('secondary-menu'), 'dorpdown' => true]); ?>
            </div>
            <!-- Button Group -->
            <div class="inline-flex items-center justify-end gap-2">
                <!-- Theme Switcher -->
                <?php aya_vue_load('theme-switcher'); ?>
                <!-- Notifications -->
                <?php aya_vue_load('notify-list', aya_notify_list()); ?>
                <!-- User Menu -->
                <?php aya_template_load('units/user-login'); ?>
            </div>
        </header>
        <!-- Left Sidebar -->
        <aside class="fixed z-20 w-64 top-16 bottom-0 flex flex-col overflow-hidden bg-base-100 shadow-md border-r border-base-300 transition-all duration-300 ease-in-out" :class="[sidebarToggle ? '-translate-x-full' : 'left-0']">
            <!-- Scroll Box -->
            <div class="flex-grow overflow-y-auto custom-scrollbar">
                <!-- Search Form -->
                <div class="relative px-4 pt-6">
                    <?php aya_vue_load('search-form'); ?>
                </div>
                <!-- Mobile Menu-->
                <div class="flex lg:hidden ">
                </div>
                <!-- Main Menu -->
                <div class="relative transition-all duration-300 ease-in-out">
                    <?php aya_vue_load('left-sitebar-menu', ['menu' => aya_get_menu('primary-menu'), 'top_menu' => aya_get_menu('secondary-menu')]); ?>
                </div>
            </div>
            <?php aya_template_load('units/bar-info-box'); ?>
        </aside>
        <!-- Main Content -->
        <div class="relative flex flex-col overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out pt-16" :class="[sidebarToggle ? 'ml-0' : 'md:ml-64']">