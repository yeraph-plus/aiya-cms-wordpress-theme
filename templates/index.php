<?php

aya_home_open();
?>
<!-- Screen Loading -->
<div id="page-loading-mask" class="flex items-center justify-center fixed inset-0 bg-base-100 z-[9999] transition-opacity duration-300 ease-in-out">
    <div class="flex flex-col items-center">
        <div class="loading loading-spinner loading-lg text-primary"></div>
        <p class="mt-4 text-base-content/70 animate-pulse">加载中...</p>
    </div>
</div>

<div id="vue-app" class="min-h-screen overflow-hidden" style="visibility: hidden">
    <!-- Topbar -->
    <header class="navbar fixed bg-base-100 shadow-md z-20 w-full transition-all duration-300 ease-in-out">
        <div class="flex container mx-auto justify-between items-center">
            <div class="navbar-start">
                <!-- Logo -->
                <?php aya_blog_logo('max-w-[160px] overflow-hidden whitespace-nowrap text-xl font-bold transition-all duration-300 ease-in-out', 'h-8 w-auto'); ?>
                <!-- Sidebar Toggle -->
                <button @click="sidebarToggle = !sidebarToggle" class="btn btn-square btn-ghost ml-2 transition-all duration-300 ease-in-out">
                    <svg v-if="!sidebarToggle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                    </svg>
                </button>
                <!-- Nav Menu -->
                <div class="hidden lg:flex">
                    <?php aya_vue_load('nav-menu', ['menu' => aya_get_menu('primary-menu'), 'dorpdown' => true]); ?>
                </div>
            </div>
            <!-- Button Group -->
            <div class="navbar-end gap-2">
                <!-- Search Form -->
                <div class="hidden lg:flex">
                    <?php aya_vue_load('search-form'); ?>
                </div>
                <!-- Theme Switcher -->
                <?php aya_vue_load('theme-switcher'); ?>
                <!-- Mobile Menu Button-->
                <button @click="appMenuToggle = !appMenuToggle" class="btn btn-square btn-ghost "><!-- lg:hidden -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </button>
                <!-- Notifications -->
                <?php aya_vue_notify_component(); ?>
                <!-- User Menu -->
                <?php aya_vue_user_nemu_component(); ?>
            </div>
        </div>
        <!-- Mobile Menu-->
        <div v-if="appMenuToggle" class="origin-top-right absolute right-4 top-16 w-80 rounded shadow-lg bg-base-100 transition-all duration-300 ease-in-out transform z-50">
            <div class="p-4">
                <h3 class="text-base font-medium mb-3">应用菜单</h3>
                <div class="grid grid-cols-3 gap-4">
                    <!-- 应用菜单项 -->
                    <a href="#" class="group flex flex-col items-center p-3 rounded-lg hover:bg-base-200 transition-all duration-300 ease-in-out">
                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center mb-2 group-hover:bg-primary/20 transition-all duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium">文档</span>
                    </a>

                    <!-- 更多应用菜单项... -->
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar Mask -->
    <div v-if="sidebarToggle && isMobile" @click="sidebarToggle = false" class="fixed md:hidden inset-0 bg-base-300/30 backdrop-blur-sm transition-all duration-300 ease-in-out z-10"></div>

    <!-- Left Sidebar -->
    <aside class="fixed top-16 bottom-0 flex flex-col overflow-hidden bg-base-100 border-r border-base-300 transition-all duration-300 ease-in-out z-20" :class="[sidebarToggle ? 'w-64 left-0' : 'w-64 -translate-x-full']">
        <!-- Main Menu -->
        <div class="flex-grow overflow-y-auto custom-scrollbar">
            <div class="relative transition-all duration-300 ease-in-out">
                <?php aya_vue_load('left-sitebar-menu', ['menu' => aya_get_menu('primary-menu')]); ?>
            </div>
        </div>
        <!-- Info Box -->
        <div class="flex-shrink-0 transition-all duration-300 ease-in-out border-t border-base-200">
            <div class="p-4 w-auto">
                <div class="p-4 border border-base-200 rounded-md bg-base-200/50 transition-all duration-300 ease-in-out">
                    <div class="flex items-center gap-2 text-primary mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <h5 class="text-md font-bold">AIYA-CMS PRO</h5>
                    </div>
                    <p class="mb-2 text-xs text-base-content/70">
                        一种很新的旧 WordPress 主题
                    </p>
                    <a href="#" class="text-primary hover:underline text-sm transition-all duration-300 ease-in-out">了解更多</a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="relative flex-1 flex flex-col overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out pt-16" :class="[sidebarToggle ? 'md:ml-64' : 'ml-0']">
        <main class="flex-1 flex flex-col">
            <!-- Content Container -->
            <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
                <!-- 主内容区域 -->
                <!-- #region 面包屑导航 -->
                <div class="text-sm breadcrumbs mb-3">
                    <ul>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 mr-1 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>首页</a>
                        </li>
                        <li><a>管理功能</a></li>
                        <li class="text-primary">当前页面</li>
                    </ul>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <footer class="footer md:footer-horizontal bg-base-200 text-base-content transition-all duration-300 ease-in-out">
            <div class="container mx-auto px-4 py-6">
                <div class="grid grid-flow-col gap-4">
                    <a class="link link-hover">About us</a>
                    <a class="link link-hover">Contact</a>
                    <a class="link link-hover">Jobs</a>
                    <a class="link link-hover">Press kit</a>
                </div>
                <div class="text-base-content mt-4">
                    <p>Copyright © {{new Date().getFullYear()}} Aiya-CMS All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</div>