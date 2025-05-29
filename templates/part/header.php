<?php aya_home_open(); ?>
<?php aya_template_load('units/screen-loader'); ?>
<div id="vue-app" class="min-h-screen overflow-hidden" style="visibility: hidden">
    <!-- Mobile Sidebar Mask -->
    <div v-if="sidebarToggle && isMobile" @click="sidebarToggle = false" class="fixed md:hidden inset-0 bg-base-300/30 backdrop-blur-sm transition-all duration-300 ease-in-out z-20"></div>
    <!-- Topbar -->
    <header class="navbar fixed z-40 w-full bg-base-100 border-b border-base-300 transition-all duration-300 ease-in-out">
        <div class="navbar-start lg:w-64 flex items-center justify-center">
            <!-- Logo -->
            <?php aya_blog_logo('max-w-[180px] overflow-hidden whitespace-nowrap text-xl font-bold ', 'h-8 w-auto'); ?>
            <!-- Sidebar Toggle -->
            <button @click="sidebarToggle = !sidebarToggle" class="btn btn-square btn-ghost ml-4">
                <icon v-if="!sidebarToggle" name="bars-3" class="size-5"></icon>
                <icon v-else name="bars-3-bottom-left" class="size-5"></icon>
            </button>
        </div>
        <div class="navbar-center lg:w-1/2 hidden lg:flex">
            <?php aya_vue_load('nav-menu', ['menu' => aya_get_menu('secondary-menu'), 'dorpdown' => true]); ?>
        </div>
        <!-- Button Group -->
        <div class="navbar-end lg:w-1/4 gap-2">
            <!-- Theme Switcher -->
            <?php aya_vue_load('theme-switcher'); ?>
            <!-- Notifications -->
            <?php aya_vue_notify_component(); ?>
            <!-- User Menu -->
            <?php aya_vue_user_nemu_component(); ?>
        </div>
    </header>
    <!-- Left Sidebar -->
    <aside class="fixed z-20 top-16 bottom-0 flex flex-col overflow-hidden bg-base-100 shadow-md border-r border-base-300 transition-all duration-300 ease-in-out" :class="[sidebarToggle ? 'w-64 left-0' : 'w-64 -translate-x-full']">
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
    <div class="relative flex flex-col overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out pt-16" :class="[sidebarToggle ? 'md:ml-64' : 'ml-0']">
        <main class="flex-1 flex flex-col">
            <?php aya_template_load('part/banner'); ?>
            <!-- Content Container -->
            <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">