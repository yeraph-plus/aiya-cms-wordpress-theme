<?php

/**
 * AIYA-CMS
 * 
 * 页头
 */

$header_sticky = aya_opt('site_header_sticky_top', 'layout');
$aside_bar_off = aya_opt('site_aside_bar_type', 'layout');

$layout_class = ($aside_bar_off) ?: 'aside-padding-left';
$sub_class = ($aside_bar_off) ?: 'tablet-none';
$header_class = ($header_sticky) ? 'sticky-top' . ' ' . $layout_class : 'smooth-top';
?>
<!-- Layer -->
<div id="loading" class="loading-overlay-layer"></div>
<!-- Layer-END -->
<div id="root-wrapper" class="main-position-row <?php e_html($layout_class); ?>">
    <?php if (!$aside_bar_off) : ?>
        <!-- Menu -->
        <aside id="aside-wrapper" class="aside-menu-bar mobile-none">
            <div id="aside-nav" class="aside-nav d-flex flex-column flex-shrink-0 p-3">
                <?php aya_header_logo(); ?>
                <hr />
                <?php aya_aside_menu_toggle(); ?>
                <hr />
                <?php aya_header_user_login(); ?>
            </div>
        </aside>
    <?php endif; ?>
    <!-- Header -->
    <header id="header">
        <?php aya_header_home_extra(); ?>
        <div class="header-bar trans-400 <?php e_html($header_class); ?>">
            <div class="container height-fill">
                <nav class="navbar navbar-expand-lg" aria-label="offcanvas-navbar-large">
                    <div class="container-fluid">
                        <button class="navbar-btn tablet-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menu-left" aria-controls="menu-left" aria-label="toggle-aside-navigation">
                            <i class="bi bi-grid"></i>
                        </button>
                        <div class="navbar-brand <?php e_html($sub_class); ?>"><?php aya_header_logo(); ?></div>
                        <button class="navbar-btn tablet-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menu-right" aria-controls="menu-right" aria-label="toggle-serach-navigation">
                            <i class="bi bi-search"></i>
                        </button>
                        <div class="navbar-collapse mobile-none">
                            <?php aya_header_menu_toggle(); ?>
                            <?php aya_search_form(); ?>
                        </div>
                    </div>
                </nav>
                <div class="offcanvas offcanvas-start p-3" tabindex="-1" id="menu-left" aria-labelledby="offcanvas-navbar">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvas-navbar"><?php e_html(__('菜单', 'AIYA')); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <?php aya_header_menu_toggle(); ?>
                        <hr />
                        <?php aya_aside_menu_toggle(); ?>
                    </div>
                </div>
                <div class="offcanvas offcanvas-top p-3" tabindex="-1" id="menu-right" aria-labelledby="offcanvas-navbar">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvas-navbar"><?php e_html(__('搜索', 'AIYA')); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-flex flex-column flex-shrink-0">
                        <?php aya_search_form(); ?>
                        <hr />
                        <?php aya_header_user_login(); ?>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--pjax-container-start-->
    <div id="pjax-wrapper">
        <?php aya_banner_section(); ?>