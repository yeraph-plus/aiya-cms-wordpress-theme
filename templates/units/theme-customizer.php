<?php

if (!aya_opt('site_theme_customizer_bool', 'basic')) return;

?>
<div x-data="customizer">
    <div class="fixed inset-0 bg-[black]/60 z-[51] px-4 hidden transition-[display]" :class="{'!block': showCustomizer}" @click="showCustomizer = false"></div>

    <nav class="bg-white fixed ltr:-right-[400px] rtl:-left-[400px] top-0 bottom-0 w-full max-w-[400px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-[right] duration-300 z-[51] dark:bg-[#0e1726] p-4" :class="{'ltr:!right-0 rtl:!left-0' : showCustomizer}">
        <a href="javascript:;" class="bg-primary ltr:rounded-tl-full rtl:rounded-tr-full ltr:rounded-bl-full rtl:rounded-br-full absolute ltr:-left-12 rtl:-right-12 top-0 bottom-0 my-auto w-12 h-10 flex justify-center items-center text-white cursor-pointer" @click="showCustomizer = !showCustomizer">
            <span x-show="showCustomizer">
                <i data-feather="x" width="20" height="20" stroke-width="2"></i>
            </span>
            <span x-show="!showCustomizer">
                <i data-feather="settings" width="20" height="20" stroke-width="2"></i>
            </span>
        </a>
        <div class="overflow-y-auto overflow-x-hidden perfect-scrollbar h-full">
            <div class="text-center relative pb-5">
                <h4 class="mb-1 dark:text-white">TEMPLATE CUSTOMIZER</h4>
                <p class="text-white-dark">实时调整外观首选项。</p>
            </div>
            <div class="border border-dashed border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md mb-3 p-3">
                <h5 class="mb-1 text-base dark:text-white leading-none">暗色主题</h5>
                <p class="text-white-dark text-xs">设置站点首次加载时的配色模式。</p>
                <div class="grid grid-cols-3 gap-2 mt-3">
                    <button type="button" class="btn" :class="[$store.app.colorScheme === 'light' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleDarkMode('light')">
                        亮色
                    </button>
                    <button type="button" class="btn" :class="[$store.app.colorScheme === 'dark' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleDarkMode('dark')">
                        暗色
                    </button>
                    <button type="button" class="btn" :class="[$store.app.colorScheme === 'system' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleDarkMode('system')">
                        跟随系统
                    </button>
                </div>
            </div>

            <div class="border border-dashed border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md mb-3 p-3">
                <h5 class="mb-1 text-base dark:text-white leading-none">导航栏布局</h5>
                <p class="text-white-dark text-xs">设置站点导航栏样式。</p>
                <div class="grid grid-cols-3 gap-2 mt-3">
                    <button type="button" class="btn" :class="[$store.app.navbarMenu === 'horizontal' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleMenu('horizontal')">
                        顶部夹层导航
                    </button>
                    <button type="button" class="btn" :class="[$store.app.navbarMenu === 'vertical' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleMenu('vertical')">
                        左侧边栏导航
                    </button>
                    <button type="button" class="btn" :class="[$store.app.navbarMenu === 'collapsible-vertical' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleMenu('collapsible-vertical')">
                        左侧边栏抽屉
                    </button>
                </div>
                <!--<div class="mt-5 text-primary">
                    <label class="inline-flex mb-0">
                        <input x-model="$store.app.colorSemidark" type="checkbox" :value="true" class="form-checkbox" @change="$store.app.toggleSemidark()" />
                        <span>Semi Dark (Sidebar & Header)</span>
                    </label>
                </div>-->
            </div>
            <div class="border border-dashed border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md mb-3 p-3">
                <h5 class="mb-1 text-base dark:text-white leading-none">布局盒子</h5>
                <p class="text-white-dark text-xs">设置站点强制使用CSS盒子进行布局，忽略页面宽度自适应。</p>
                <div class="flex gap-2 mt-3">
                    <button type="button" class="btn flex-auto" :class="[$store.app.bodyLayout === 'boxed-layout' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleLayout('boxed-layout')">
                        Box
                    </button>
                    <button type="button" class="btn flex-auto" :class="[$store.app.bodyLayout === 'full' ? 'btn-primary' :'btn-outline-primary']" @click="$store.app.toggleLayout('full')">
                        Full
                    </button>
                </div>
            </div>
            <div class="border border-dashed border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md mb-3 p-3">
                <h5 class="mb-1 text-base dark:text-white leading-none">文章列表</h5>
                <p class="text-white-dark text-xs">文章列表卡片宽度。</p>
                <div class="mt-3 flex items-center gap-3 text-primary">
                    <label class="inline-flex mb-0">
                        <input x-model="$store.app.loopGridCol" type="radio" value="col-3" class="form-radio" @change="$store.app.toggleGridColumn()" />
                        <span>3列</span>
                    </label>
                    <label class="inline-flex mb-0">
                        <input x-model="$store.app.loopGridCol" type="radio" value="col-4" class="form-radio" @change="$store.app.toggleGridColumn()" />
                        <span>4列</span>
                    </label>
                    <label class="inline-flex mb-0">
                        <input x-model="$store.app.loopGridCol" type="radio" value="col-5" class="form-radio" @change="$store.app.toggleGridColumn()" />
                        <span>5列</span>
                    </label>
                </div>
            </div>
            <div class="border border-dashed border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md mb-3 p-3">
                <h5 class="mb-1 text-base dark:text-white leading-none">导航栏浮动</h5>
                <p class="text-white-dark text-xs">设置站点导航栏是否跟随滚动。</p>
                <div class="mt-3 flex items-center gap-3 text-primary">
                    <label class="inline-flex mb-0">
                        <input x-model="$store.app.navbarSticky" type="radio" value="navbar-sticky" class="form-radio" @change="$store.app.toggleNavbar()" />
                        <span>锁定</span>
                    </label>
                    <label class="inline-flex mb-0">
                        <input x-model="$store.app.navbarSticky" type="radio" value="navbar-floating" class="form-radio" @change="$store.app.toggleNavbar()" />
                        <span>浮动</span>
                    </label>
                    <label class="inline-flex mb-0">
                        <input x-model="$store.app.navbarSticky" type="radio" value="navbar-static" class="form-radio" @change="$store.app.toggleNavbar()" />
                        <span>关闭</span>
                    </label>
                </div>
            </div>


            <div class="border border-dashed border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md mb-3 p-3">
                <h5 class="mb-1 text-base dark:text-white leading-none">页面动画</h5>
                <p class="text-white-dark text-xs">设置站点主要内容区域切换时的动画。</p>
                <div class="mt-3">
                    <select x-model="$store.app.animation" class="form-select border-primary text-primary" @change="$store.app.toggleAnimation()">
                        <option value="">None</option>
                        <option value="animate__fadeIn">Fade</option>
                        <option value="animate__fadeInDown">Fade Down</option>
                        <option value="animate__fadeInUp">Fade Up</option>
                        <option value="animate__fadeInLeft">Fade Left</option>
                        <option value="animate__fadeInRight">Fade Right</option>
                        <option value="animate__slideInDown">Slide Down</option>
                        <option value="animate__slideInLeft">Slide Left</option>
                        <option value="animate__slideInRight">Slide Right</option>
                        <option value="animate__zoomIn">Zoom In</option>
                    </select>
                </div>
            </div>
        </div>
    </nav>
</div>
<script>
    document.addEventListener("alpine:init", () => {
        Alpine.data("customizer", () => ({
            showCustomizer: false,
        }));
    });
</script>