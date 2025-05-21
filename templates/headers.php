<?php

aya_home_open();
?>
<!-- screen loader -->
<div id="page-loading-mask" class="fixed inset-0 bg-base-100 z-[9999] flex items-center justify-center">
    <div class="loading loading-spinner loading-lg text-primary"></div>
</div>

<div id="vue-app" class="h-screen flex overflow-hidden" style="visibility: hidden">
    <!-- 页面加载状态指示器 -->
    <div v-if="isLoading" class="fixed inset-0 bg-base-100 bg-opacity-50 flex items-center justify-center z-50">
        <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>

    <!-- 移动端侧边栏遮罩层 -->
    <div v-if="sidebarNavOpen && isMobile" @click="sidebarNavOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden transition-opacity duration-300"></div><!-- 左侧边栏 -->
    <aside class="fixed lg:static h-full bg-base-100 shadow-md z-40" :class="[
            'transition-all duration-300 ease-in-out transform',
            sidebarNavOpen ? 'translate-x-0 w-64' : '-translate-x-full w-0 lg:translate-x-0 lg:w-64'
        ]">
        <!-- 侧边栏内容 -->
        <div class="h-full flex flex-col text-base-content overflow-hidden" :class="{'w-64': sidebarNavOpen || !isMobile, 'w-0': !sidebarNavOpen && isMobile}">
            <!-- Logo区域 -->
            <div class="min-h-16 h-16 flex items-center justify-center transition-opacity duration-500" :class="{'opacity-0': !sidebarNavOpen && isMobile, 'opacity-100': sidebarNavOpen || !isMobile}">
                <?php aya_blog_logo('max-w-[160px] overflow-hidden whitespace-nowrap text-xl font-bold', 'h-8 w-auto'); ?>
            </div>

            <!-- 导航菜单 -->
            <div class="flex-grow overflow-y-auto custom-scrollbar max-h-[calc(100vh_-_100px)]">
                <div class="relative transition-opacity duration-700 ease-in" :class="{'opacity-0': !sidebarNavOpen && isMobile, 'opacity-100 delay-150': sidebarNavOpen || !isMobile}">
                    <?php aya_vue_load('left-sitebar-menu', ['menu' => aya_get_menu('primary-menu')]); ?>
                </div>
            </div>

            <!-- 底部信息区域 -->
            <div class="transition-opacity duration-500 ease-in" :class="{'opacity-0': !sidebarNavOpen && isMobile, 'opacity-100 delay-200': sidebarNavOpen || !isMobile}">
                <div class="flex items-center p-4">
                    <div class="relative p-6 border border-gray-500/20 rounded-md">
                        <div class="text-primary mb-2">
                            <!-- Icon -->
                        </div>
                        <h5 class="font-bold mb-2">AIYA-CMS PRO</h5>
                        <p class="mb-2 text-sm">
                            一种很新的旧 WordPress 主题
                        </p>
                        <a href="#" class="text-primary hover:underline">link</a>
                    </div>
                </div>
            </div>
        </div>
    </aside> <!-- 主内容区域 -->
    <div class="flex-1 flex flex-col bg-base-100 min-h-screen overflow-x-hidden">
        <!-- Topbar -->
        <header class="navbar bg-base-100 shadow-b shadow-md z-10" :class="{'sticky top-0': navbarSticky}">
            <div class="flex-none flex items-center">
                <!-- Logo -->
                <div class="h-16 flex items-center justify-center mr-2 lg:hidden">
                    <?php
                    aya_blog_logo('max-w-[160px] overflow-hidden whitespace-nowrap text-xl font-bold transition-opacity duration-500', 'h-8 w-auto');
                    ?>
                </div>
                <!-- SideaBar Toggle -->
                <button @click="sidebarNavOpen = !sidebarNavOpen" class="btn btn-square btn-ghost mr-4" aria-label="SideaBar Toggle">
                    <svg v-if="!sidebarNavOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                    </svg>
                </button>

                <!-- 内容区域切换菜单 - 仅在桌面端显示 -->
                <div class="hidden lg:flex">
                    <?php aya_vue_load('nav-menu', ['menu' => aya_get_menu('primary-menu'), 'dorpdown' => true,]); ?>
                </div>
            </div>

            <!-- 挤压占位元素 -->
            <div class="flex-1 flex items-center">
            </div>
            <div class="flex-none flex items-center">
                <!-- 桌面端显示搜索框 - 贴靠在右侧 -->
                <div class="hidden lg:flex mr-2" style="width: 250px;">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" placeholder="搜索..." class="input input-bordered w-full pl-10" />
                    </div>
                </div> <!-- 移动端搜索按钮 -->
                <button @click="showMobileSearch = !showMobileSearch" class="btn btn-ghost btn-circle lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                <!-- 通知图标菜单位置 -->
                <!-- 用户菜单位置 -->
            </div>
        </header>
        <!-- Topbar #end --> <!-- MobileFunction Dorpdown  -->
        <div v-if="showMobileSearch" class="bg-base-200 p-3 lg:hidden border-t border-base-300 shadow-inner">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text" placeholder="搜索..." class="input input-bordered w-full pl-10" autofocus />
                <button @click="showMobileSearch = false" class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Main-Content -->
        <div class="container flex-1 flex flex-col relative">
            <div class="p-4 pb-24 flex-grow flex justify-center custom-scrollbar">
                <div class="w-full max-w-7xl">
                    <!-- ===================== 面包屑导航 ===================== -->
                    <!-- #region 面包屑导航 -->
                    <div class="text-sm breadcrumbs mb-3">
                        <ul>
                            <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 mr-1 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>首页</a></li>
                            <li><a>管理功能</a></li>
                            <li class="text-primary">当前页面</li>
                        </ul>
                    </div>
                    <!-- #endregion 面包屑导航 -->

                    <!-- ===================== 页面标题区域 ===================== -->
                    <!-- #region 页面标题区域 -->
                    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
                        <h1 class="text-2xl font-bold">管理后台</h1>
                        <div class="flex mt-2 md:mt-0">
                            <button class="btn btn-sm btn-primary">新建</button> <button class="btn btn-sm btn-outline ml-2">刷新</button>
                        </div>
                    </div>
                    <!-- #endregion 页面标题区域 -->

                    <!-- ===================== 页面内容区域 ===================== -->
                    <!-- #region 页面内容区域 -->
                    <!-- #endregion 页面内容区域 -->
                </div>
            </div> <!-- ========================= 页面底部区域 ======================= -->
            <!-- #region 底部Footer - 绝对定位在底部 -->
            <footer class="footer footer-center p-4 bg-base-200 text-base-content border-t absolute bottom-0 left-0 right-0">
                <div class="container mx-auto max-w-7xl flex flex-col md:flex-row justify-between items-center">
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 mb-2 md:mb-0">
                        <a href="#" class="link link-hover">关于我们</a>
                        <a href="#" class="link link-hover">联系我们</a>
                        <a href="#" class="link link-hover">隐私政策</a>
                        <a href="#" class="link link-hover">服务条款</a>
                    </div>
                    <div class="text-center md:text-right">
                        <p>© 2025 All Rights Reserved. <span class="text-sm">粤ICP备xxxxxxxx号-1</span></p>
                    </div>
                </div>
            </footer>
            <!-- #endregion 底部Footer -->
        </div>
        <!-- Main-Content #end -->
    </div>
</div>
<script type="text/javascript">
    // 设置body为加载状态
    document.body.classList.add('loading');

    // 页面完全加载后移除body的loading类并显示Vue应用
    window.addEventListener('load', function () {
        setTimeout(function () {
            document.body.classList.remove('loading');

            // 检测视窗宽度，设置初始侧边栏状态
            const app = document.getElementById('vue-app');
            app.style.visibility = 'visible';

            // 设置isMobile变量
            if (window.innerWidth < 1024) {
                window.vueApp.$data.isMobile = true;
                window.vueApp.$data.sidebarNavOpen = false;
            } else {
                window.vueApp.$data.isMobile = false;
                window.vueApp.$data.sidebarNavOpen = true;
            }
        }, 300);
    });

    // 添加窗口大小变化监听
    window.addEventListener('resize', function () {
        if (window.vueApp) {
            if (window.innerWidth < 1024) {
                window.vueApp.$data.isMobile = true;
            } else {
                window.vueApp.$data.isMobile = false;
            }
        }
    });
</script>

<div class="drawer">
    <input id="mobile-drawer" type="checkbox" class="drawer-toggle" />

    <div class="drawer-content flex flex-col">
        <header class="navbar bg-base-100 shadow-md sticky top-0 z-10">
            <div class="container mx-auto flex ">
                <div class="navbar-start">
                    <!-- Mobile Toggle -->
                    <label for="mobile-drawer" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </label>
                    <!-- Logo -->
                    <?php aya_blog_logo('max-w-[150px] overflow-hidden whitespace-nowrap', 'h-8 w-auto'); ?>
                </div>

                <!-- Desktop Navigation -->
                <div id="desktop-nav" class="navbar-center hidden lg:flex">
                    <?php aya_vue_menu_component('primary-menu', 1); ?>
                </div>
                <div class="navbar-end gap-2">
                    <!-- Theme Switcher -->
                    <?php aya_vue_load('theme-switcher'); ?>
                    <div class="hidden lg:flex">
                    <!-- Notifications -->
                        <?php aya_vue_notify_component(); ?>
                        <!-- User Menu -->
                        <?php aya_vue_user_nemu_component(); ?>
                        <!-- Search Form -->
                        <?php aya_vue_load('search-form'); ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Breadcrumb -->
        <div class="bg-base-100 border-b border-base-300">
            <div class="container mx-auto px-4 lg:px-8 xl:max-w-7xl py-2">
                <?php aya_blog_breadcrumb();

                //aya_json_print(aya_get_menu('primary-menu'));
                ?>
            </div>

            <!-- page content  -->
        </div>
    </div>
    <!-- 移动端侧边栏内容 -->
    <div class="drawer-side">
        <label for="mobile-drawer" class="drawer-overlay"></label>
        <div class="menu p-4 w-80 min-h-full bg-base-200">
            <!-- 侧边栏头部 -->
            <div class="flex items-center justify-between pb-4 border-b mb-4">
                <?php aya_blog_logo('max-w-[150px]', 'h-8 w-auto'); ?>
                <label for="mobile-drawer" class="btn btn-sm btn-circle">✕</label>
            </div>

            <!-- 移动端导航 - 将由JS动态填充 -->
            <div id="mobile-nav" class="mb-4"></div>
        </div>
    </div>
</div>