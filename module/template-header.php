<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * header组件
 * ------------------------------------------------------------------------------
 */

//加载LOGO
function aya_header_logo()
{
    $logo_image = aya_opt('site_logo_image', 'theme');
    //输出
    if (aya_opt('site_logo_text', 'theme')) {
        return e_html('<a class="logo-with-text" href="' . esc_url(home_url()) . '"><img src="' . $logo_image . '" alt="logo" />' . get_bloginfo('name') . '</a>');
    } else {
        return e_html('<a class="logo" href="' . esc_url(home_url()) . '"><img src="' . $logo_image . '" alt="logo" /></a>');
    }
}
//首页时额外输出
function aya_header_home_extra()
{
    //判断是否为首页
    if (aya_page_type('home')) {

        $format_description = (get_bloginfo('description') == '') ?: ' - ' . get_bloginfo('description');
        //输出
        return e_html('<h1 class="d-none">' . get_bloginfo('name') . $format_description . '</h1>');
    }
}
//侧栏菜单切换
function aya_aside_menu_toggle()
{
    //获取主题设置
    $menu_name = 'main-menu';
    //执行过滤器
    $menu_name = apply_filters('aya_main_menu_toggle', $menu_name);
    //输出
    return aya_nav_menu($menu_name, 'nav nav-pills flex-column mb-auto', 2);
}
//顶栏菜单切换
function aya_header_menu_toggle()
{
    //获取主题设置
    if (aya_opt('site_header_menu_drawer', 'layout')) {
        $html_before = '<div class="navbar-nav navbar-drawer trans-200 me-auto mb-0">';
        $html_before .= '<li class="menu-item"><a id="nav-drawer-btn" href="javascript:void(0);" class="nav-link"><i class="bi bi-three-dots"></i></a></li>';

        return e_html($html_before) . aya_nav_menu('header-menu', 'nav-drawer-menu me-auto mb-0', 1) . e_html('</div>');
    }

    return e_html('<div class="trans-200 me-auto mb-0">') . aya_nav_menu('header-menu', 'navbar-nav me-auto mb-0', 1) . e_html('</div>');
}
//加载Banner组件
function aya_banner_section()
{
    //判断是否为文章页或移动端
    if (aya_page_type('singular') || aya_is_mobile()) {
        $banner_type = 'off';
    } else {
        //获取主题设置
        $banner_type = aya_opt('site_banner_type', 'layout');

        $banner_type = ($banner_type) ? 'default' : 'off';
    }

    return aya_template_part('section/banner', $banner_type);
}
//加载Banner内容
function aya_banner_content()
{
    //获取主题设置
    $banner_text = aya_opt('site_banner_text_type', 'layout');

    switch ($banner_text) {
        case 'custom':
            $banner_content = aya_opt('site_banner_content', 'layout');
            break;
        case 'hitokoto':
            $banner_content = aya_get_hitokoto();
            break;
        case 'false':
            $banner_content = '';
            break;
        default:
            $banner_content = aya_get_hitokoto();
            break;
    }

    if (empty($banner_content)) return;

    return e_html('<p class="banner-title">' . $banner_content . '</p>');
}
//加载用户组件
function aya_header_user_login()
{
    $html = '';

    if (is_user_logged_in()) {
        global $current_user;

        $html .= '<div class="user-controls dropdown">';
        $html .= '<a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">' . get_avatar($current_user->user_email, 32) . '<b class="mx-2">' . $current_user->display_name . '</b></a>';
        $html .= '<ul class="dropdown-menu text-small shadow">';

        if (current_user_can('edit_posts')) {
            $html .= '<li><a class="dropdown-item" href="' . home_url('/wp-admin') . '" target="_blank">' . __('仪表盘', 'AIYA') . '</a></li>';
            $html .= '<li><hr class="dropdown-divider" /></li>';
        }

        $html .= '<li><a class="dropdown-item" href="' . home_url('/wp-admin/post-new.php') . '" target="_blank">' . __('新增文章', 'AIYA') . '</a></li>';
        $html .= '<li><a class="dropdown-item" href="' . home_url('/wp-admin/profile.php') . '" target="_blank">' . __('个人资料', 'AIYA') . '</a></li>';
        $html .= '<li><hr class="dropdown-divider" /></li>';
        $html .= '<li><a class="dropdown-item" href="' . home_url('/wp-login.php?action=logout') . '" target="_blank">' . __('登出', 'AIYA') . '</a></li>';
        $html .= '</ul></div>';

        return e_html($html);
    } else {

        if (get_option('users_can_register') == false) return;

        $html .= '<div class="user-login">';
        $html .= '<a class="btn btn-color-solid mx-1" role="button" href="' . home_url('/wp-login.php') . '" target="_blank"><i class="bi bi-person-fill"></i> ' . __('登录', 'AIYA') . '</a>';
        $html .= '<a class="btn btn-color-hollow mx-1" role="button" href="' . home_url('/wp-login.php?action=register') . '" target="_blank"><i class="bi bi-person-plus-fill"></i> ' . __('注册', 'AIYA') . '</a>';
        $html .= '</div>';

        return e_html($html);
    }
}
