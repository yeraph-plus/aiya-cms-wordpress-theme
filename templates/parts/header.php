<header class="z-40" class="shadow-sm" :class="{'dark' : $store.app.colorSemidark && $store.app.navbarMenu === 'horizontal'}">
    <div>
        <div class="relative bg-white flex w-full items-center px-5 py-2.5 dark:bg-[#0e1726]">
            <div class="horizontal-logo flex lg:hidden justify-between items-center ltr:mr-2 rtl:ml-2">
                <!-- Logo -->
                <a href="/" class="main-logo flex items-center shrink-0">
                    <?php if (aya_opt('site_logo_text_bool', 'basic')): ?>
                        <img class="w-8 h-8 ltr:-ml-1 rtl:-mr-1 inline" src="<?php aya_echo(aya_opt('site_logo_image_upload', 'basic')); ?>" alt="<?php aya_echo(get_bloginfo('name')); ?>" />
                        <span class="text-2xl ltr:ml-1.5 rtl:mr-1.5 font-bold align-middle hidden md:inline dark:text-white-light transition-all duration-300"><?php aya_echo(get_bloginfo('name')); ?></span>
                    <?php else: ?>
                        <img class="w-full h-8 ltr:-ml-1 rtl:-mr-1 inline" src="<?php aya_echo(aya_opt('site_logo_image_upload', 'basic')); ?>" alt="<?php aya_echo(get_bloginfo('name')); ?>" />
                    <?php endif; ?>
                </a>
                <!-- Sidebar toggle button -->
                <a href="javascript:;" class="collapse-icon flex-none dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex lg:hidden ltr:ml-2 rtl:mr-2 p-2 rounded-md bg-white-light/40 dark:bg-dark/40 hover:bg-white-light/90 dark:hover:bg-dark/60" @click="$store.app.toggleMenuBar()">
                    <i data-feather="more-vertical" width="20" height="20" stroke-width="2"></i>
                </a>
            </div>
            <div x-data="header" class="sm:flex-1 ltr:sm:ml-0 ltr:ml-auto sm:rtl:mr-0 rtl:mr-auto flex items-center space-x-1.5 lg:space-x-2 rtl:space-x-reverse dark:text-[#d0d2d6]">
                <!-- Search form -->
                <div class="sm:ltr:mr-auto sm:rtl:ml-auto" x-data="{ search: false }" @click.outside="search = false">
                    <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="sm:relative absolute inset-x-0 sm:top-0 top-1/2 sm:translate-y-0 -translate-y-1/2 sm:mx-0 mx-4 z-10 sm:block hidden" :class="{'!block' : search}" @submit="search = false">
                        <div class="relative">
                            <input type="text" name="s" class="form-input ltr:pl-9 rtl:pr-9 ltr:sm:pr-4 rtl:sm:pl-4 ltr:pr-9 rtl:pl-9 peer sm:bg-transparent bg-gray-100 placeholder:tracking-widest" placeholder="Search..." />
                            <button type="submit" class="absolute w-9 h-9 inset-0 ltr:right-2 rtl:left-2 appearance-none peer-focus:text-primary flex items-center justify-center">
                                <i data-feather="search" width="20" height="20" stroke-width="2"></i>
                            </button>
                            <button type="button" class="hover:opacity-80 sm:hidden block absolute top-1/2 -translate-y-1/2 ltr:right-2 rtl:left-2" @click="search = false">
                                <i data-feather="x-circle" width="20" height="20" stroke-width="2"></i>
                            </button>
                        </div>
                    </form>
                    <button type="button" class="search_btn sm:hidden p-2 rounded-md bg-white-light/40 dark:bg-dark/40 hover:bg-white-light/90 dark:hover:bg-dark/60 flex items-center justify-center" @click="search = ! search">
                        <i data-feather="search" width="20" height="20" stroke-width="2"></i>
                    </button>
                </div>
                <!-- Color Scheme -->
                <div class="space-x-1.5">
                    <a href="javascript:;" x-cloak x-show="$store.app.colorScheme === 'light'" href="javascript:;"
                        class="flex items-center p-2 rounded-md bg-white-light/40 dark:bg-dark/40 hover:text-primary hover:bg-white-light/90 dark:hover:bg-dark/60" @click="$store.app.toggleDarkMode('dark')">
                        <i data-feather="sun" width="20" height="20" stroke-width="2"></i>
                    </a>
                    <a href="javascript:;" x-cloak x-show="$store.app.colorScheme === 'dark'" href="javascript:;"
                        class="flex items-center p-2 rounded-md bg-white-light/40 dark:bg-dark/40 hover:text-primary hover:bg-white-light/90 dark:hover:bg-dark/60" @click="$store.app.toggleDarkMode('system')">
                        <i data-feather="moon" width="20" height="20" stroke-width="2"></i>
                    </a>
                    <a href="javascript:;" x-cloak x-show="$store.app.colorScheme === 'system'" href="javascript:;"
                        class="flex items-center p-2 rounded-md bg-white-light/40 dark:bg-dark/40 hover:text-primary hover:bg-white-light/90 dark:hover:bg-dark/60" @click="$store.app.toggleDarkMode('light')">
                        <i data-feather="monitor" width="20" height="20" stroke-width="2"></i>
                    </a>
                </div>
                <!-- Notification -->
                <div class="dropdown" x-data="dropdown" @click.outside="open = false">
                    <a href="javascript:;" class="block p-2 rounded-md bg-white-light/40 dark:bg-dark/40 hover:text-primary hover:bg-white-light/90 dark:hover:bg-dark/60" @click="toggle">
                        <i data-feather="bell" width="20" height="20" stroke-width="2"></i>

                        <template x-if="notificationList.length">
                            <span class="flex absolute w-3 h-3 ltr:right-0 rtl:left-0 top-0">
                                <span class="animate-ping absolute ltr:-left-[3px] rtl:-right-[3px] -top-[3px] inline-flex h-full w-full rounded-full bg-success/50 opacity-75"></span>
                                <span class="relative inline-flex rounded-full w-[6px] h-[6px] bg-success"></span>
                            </span>
                        </template>
                    </a>
                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms class="top-11 !py-0 text-dark dark:text-white-dark w-[300px] ltr:-right-10 rtl:-left-10 text-xs">
                        <li>
                            <div class="flex items-center px-4 py-2 justify-between font-semibold hover:!bg-transparent">
                                <h4 class="text-lg">
                                    <?php aya_echo(__('通知盒子', 'AIYA')); ?>
                                </h4>
                                <template x-if="notificationList.length">
                                    <span class="badge bg-primary/80" x-text="notificationList.length"></span>
                                </template>
                            </div>
                        </li>
                        <template x-for="note in notificationList">
                            <li>
                                <div class="flex items-center px-5 py-3" @click.self="toggle">
                                    <div x-html="note.icon"></div>
                                    <span class="px-3 dark:text-gray-500">
                                        <div class="font-semibold text-sm dark:text-white-light/90" x-text="note.title"></div>
                                        <div x-text="note.message"></div>
                                    </span>
                                    <span class="font-semibold bg-white-dark/20 rounded text-dark/60 px-1 ltr:ml-auto rtl:mr-auto whitespace-pre dark:text-white-dark ltr:mr-2 rtl:ml-2" x-text="note.time"></span>
                                </div>
                            </li>
                        </template>
                        <template x-if="!notificationList.length">
                            <li class="mb-5">
                                <div class="!grid place-content-center hover:!bg-transparent text-lg min-h-[200px]">
                                    <?php aya_echo(__('没有新的消息', 'AIYA')); ?>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
                <?php if (aya_user_can_register()): ?>
                    <!-- Login Button -->
                    <div class="sm:flex items-center">
                        <a href="#" class="block p-2 rounded-md btn btn-outline-primary" @click="toggle"><?php aya_echo(__('登录', 'AIYA')); ?></a>
                    </div>
                    <div class="sm:flex items-center">
                        <a href="#" class="block p-2 rounded-md btn btn-primary" @click="toggle"><?php aya_echo(__('注册', 'AIYA')); ?></a>
                    </div>
                <?php else: ?>
                    <!-- User Profile -->
                    <div class="dropdown" x-show="userInfo.logged" x-data="dropdown" @click.outside="open = false">
                        <a href="javascript:;" class="block p-2 rounded-md bg-white-light/40 dark:bg-dark/40 hover:text-primary hover:bg-white-light/90 dark:hover:bg-dark/60" @click="toggle()">
                            <i data-feather="user" width="20" height="20" stroke-width="2"></i>
                        </a>
                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms class="ltr:right-0 rtl:left-0 text-dark dark:text-white-dark top-11 !py-0 w-[230px] font-semibold dark:text-white-light/90">
                            <li class="border-b border-white-light dark:border-white-light/10">
                                <div class="flex items-center px-4 py-4">
                                    <div class="flex-none">
                                        <span>
                                            <img class=" rounded-md w-10 h-10 object-cover" :src="userInfo.avatar" alt="avatar" />
                                        </span>
                                    </div>
                                    <div class="ltr:pl-4 rtl:pr-4 truncate">
                                        <h4 class="text-base">
                                            <span x-text="userInfo.name"></span>
                                            <span class="text-xs rounded" x-html="userInfo.badge"></span>
                                        </h4>

                                        <p class="text-black/60  hover:text-primary dark:text-dark-light/60 dark:hover:text-white" x-text="userInfo.email"></p>
                                    </div>
                                </div>
                            </li>
                            <template x-for="item in userInfo.menus">
                                <li>
                                    <a :href="item.url" class="dark:hover:text-white" x-text="item.label"></a>
                                </li>
                            </template>
                            <li class="border-t border-white-light dark:border-white-light/10">
                                <a href="#" class=" text-danger !py-3" @click="toggle">
                                    <i data-feather="log-out" width="16" height="16" class="mr-2"></i>
                                    <span><?php aya_echo(__('退出登录', 'AIYA')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- horizontal menu -->
        <ul class="horizontal-menu hidden py-1.5 font-semibold px-6 lg:space-x-1.5 xl:space-x-8 rtl:space-x-reverse bg-white border-t border-[#ebedf2] dark:border-[#191e3a] dark:bg-[#0e1726] text-black dark:text-white-dark">
            <?php foreach (aya_menu_array_get('secondary-menu') as $key => $menu): ?>
                <?php if (!empty($menu['child'])): ?>
                    <li class="menu nav-item relative">
                        <a href="javascript:;" class="nav-link">
                            <div class="flex items-center">
                                <span class="px-1"><?php aya_echo($menu['label']); ?></span>
                            </div>
                            <div class="right_arrow">
                                <i data-feather="chevron-down" width="20" height="20" stroke-width="2"></i>
                            </div>
                        </a>
                        <ul class="sub-menu">
                            <?php foreach ($menu['child'] as $key => $child): ?>
                                <li>
                                    <a href="<?php aya_echo($child['url']); ?>"><?php aya_echo($child['label']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="menu nav-item relative">
                        <a class="nav-link" href="<?php aya_echo($menu['url']); ?>"><?php aya_echo($menu['label']); ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</header>