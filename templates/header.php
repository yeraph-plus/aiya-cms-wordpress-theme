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

//dark
aya_home_open();

?>
<div id="vue-app" class="mx-auto flex flex-col bg-gray-100 dark:bg-gray-900 dark:text-gray-100">
    <header id="page-header" class="z-1 flex flex-none items-center bg-white shadow-xs dark:bg-gray-800">
        <div class="container mx-auto px-4 lg:px-8 xl:max-w-7xl">
            <div class="flex justify-between py-4">
                <!-- Left Section -->
                <div class="flex items-center gap-2 lg:gap-6">
                    <!-- Logo -->
                    <?php aya_blog_logo('header-logo group', 'h-8 w-auto hi-mini hi-cube-transparent'); ?>
                    <!-- Navigation -->
                    <?php aya_blog_nav_menu('primary-menu', 'header-menu hidden gap-2 lg:flex items-center'); ?>
                </div>
                <!-- Right Section -->
                <div class="flex items-center gap-2">
                    <!-- Search From -->
                    <div class="lg:flex hidden">
                        <?php aya_vue_load('search-form'); ?>
                    </div>
                    <!-- Notifications -->
                    <?php aya_vue_notify_component(); ?>
                    <!-- User Dorpdown Menu -->
                    <?php aya_vue_user_nemu_component(); ?>
                    <!-- Toggle Mobile Navigation -->
                    <div class="lg:hidden">
                        <button @click="mobileNavOpen = !mobileNavOpen" type="button" class="header-menu-btn">
                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" class="hi-solid hi-menu inline-block size-5">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!--Mobile Navigation -->
            <div class="lg:hidden" :class="{ hidden: !mobileNavOpen}">
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700"></div>
                <?php aya_vue_load('search-form'); ?>
                <?php aya_blog_nav_menu('primary-menu', 'header-menu-in-mobile'); ?>
            </div>
        </div>
    </header>