<aside :class="{'dark text-white-dark' : $store.app.colorSemidark}">
    <nav x-data="navbar" class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-50 transition-all duration-300">
        <div class="bg-white dark:bg-[#0e1726] h-full">
            <div class="flex justify-between items-center px-4 py-3">
                <!-- Logo -->
                <a href="/" class="main-logo flex items-center shrink-0">
                    <?php if (aya_opt('site_logo_text_bool', 'basic')): ?>
                        <img class="w-8 h-8 ml-2 flex-none" src="<?php aya_echo(aya_opt('site_logo_image_upload', 'basic')); ?>" alt="<?php aya_echo(get_bloginfo('name')); ?>" />
                        <span class="text-2xl ltr:ml-1.5 rtl:mr-1.5 font-bold align-middle lg:inline dark:text-white-light"><?php aya_echo(get_bloginfo('name')); ?></span>
                    <?php else: ?>
                        <img class="w-full h-8 ml-2 flex-none" src="<?php aya_echo(aya_opt('site_logo_image_upload', 'basic')); ?>" alt="<?php aya_echo(get_bloginfo('name')); ?>" />
                    <?php endif; ?>
                </a>
                <!-- Sidebar toggle button -->
                <a href="javascript:;" class="collapse-icon w-8 h-8 rounded-md p-2 flex items-center hover:bg-gray-500/10 dark:hover:bg-dark-light/10 dark:text-white-light transition duration-300 rtl:rotate-180" @click="$store.app.toggleMenuBar()">
                    <i data-feather="chevrons-left" width="20" height="20" stroke-width="2"></i>
                </a>
            </div>
            <!-- Menu -->
            <ul class="perfect-scrollbar relative font-semibold space-y-0.5 h-[calc(100vh-80px)] overflow-y-auto overflow-x-hidden p-4 py-0" x-data="{ activeDropdown: null }">
                <?php foreach (aya_menu_array_get('primary-menu') as $key => $menu): ?>
                    <?php if (!empty($menu['child'])):
                        $menu_id = 'menu-' . $key; ?>
                        <li class="menu nav-item">
                            <button type="button" class="nav-link group" :class="{'active' : activeDropdown === '<?php aya_echo($menu_id); ?>'}" @click="activeDropdown === '<?php aya_echo($menu_id); ?>' ? activeDropdown = null : activeDropdown = '<?php aya_echo($menu_id); ?>'">
                                <div class="flex items-center">
                                    <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark"><?php aya_echo($menu['label']); ?></span>
                                </div>
                                <div class="rtl:rotate-180" :class="{'!rotate-90' : activeDropdown === '<?php aya_echo($menu_id); ?>'}">
                                    <i data-feather="chevron-right" width="20" height="20" stroke-width="2"></i>
                                </div>
                            </button>
                            <ul x-cloak x-show="activeDropdown === '<?php aya_echo($menu_id); ?>'" x-collapse class="sub-menu text-gray-500">
                                <?php foreach ($menu['child'] as $key => $child): ?>
                                    <li>
                                        <a href="<?php aya_echo($child['url']); ?>"><?php aya_echo($child['label']); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="menu nav-item">
                            <a href="<?php aya_echo($menu['url']); ?>" class="nav-link group flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark"><?php aya_echo($menu['label']); ?></span>
                                </div>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        //小广告
        //aya_template_load('units/navbar-info-box');
        ?>
    </nav>
</aside>