<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 静态文件注册列表
 * ------------------------------------------------------------------------------
 */

add_action('admin_enqueue_scripts', 'aya_theme_enqueue_admin_scripts');
add_action('login_enqueue_scripts', 'aya_theme_enqueue_login_scripts');
add_action('wp_enqueue_scripts', 'aya_theme_localize_default_config', 1);

//向前端传递配置参数
function aya_theme_localize_default_config()
{
    $app_config = [
        'homeUrl' => untrailingslashit(get_home_url()),
        'apiUrl' => untrailingslashit(rest_url()),
        'apiNonce' => wp_create_nonce('wp_rest'),
        'defaultColorTheme' => aya_opt('site_default_color_mode_type', 'basic'),
    ];

    if (!wp_script_is('aya-theme-config', 'registered')) {
        wp_register_script('aya-theme-config', '', [], null, false);
    }

    wp_enqueue_script('aya-theme-config');
    wp_localize_script('aya-theme-config', 'AIYACMS_CONFIG', $app_config);
}

//静态文件加载（后台）
function aya_theme_enqueue_admin_scripts()
{
    $theme_version = aya_theme_version();

    wp_register_style('aya-admin-style', get_template_directory_uri() . '/assets/admin/css/admin.style.css', [], $theme_version, 'all');
    wp_enqueue_style('aya-admin-style');
}

//静态文件加载（登录页）
function aya_theme_enqueue_login_scripts()
{
    $theme_version = aya_theme_version();

    wp_register_style('aya-login-style', get_template_directory_uri() . '/assets/admin/css/admin.login.css', [], $theme_version, 'all');
    wp_enqueue_style('aya-login-style');
}
