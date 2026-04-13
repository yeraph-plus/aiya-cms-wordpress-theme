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

/*
 * ------------------------------------------------------------------------------
 * 用于隐藏数字ID的XDeode类
 * ------------------------------------------------------------------------------
 */

//加密方法
function aya_token_encode($token, $length = 15)
{
    $obj = new XDE_code($length);

    return $obj->encode($token);
}

//解密方法
function aya_token_decode($token, $length = 15)
{
    $obj = new XDE_code($length);

    return $obj->decode($token);
}

/*
 * ------------------------------------------------------------------------------
 * 缩略图组件及相关函数
 * ------------------------------------------------------------------------------
 * 
 * BFI_Thumb.php
 * 
 * Demo1
 * $size = array( 400, 300, 'opacity' => 50, 'grayscale' => true, 'bfi_thumb' => true );
 * wp_get_attachment_image_src( $attachment_id, $size )
 * 
 * Else
 * the_post_thumbnail( array( 1024, 400, 'bfi_thumb' => true, 'grayscale' => true ) );
 * 
 * Demo2
 * $params = array( 'width' => 400, 'height' => 300, 'opacity' => 50, 'grayscale' => true, 'colorize' => '#ff0000' );
 * bfi_thumb( "URL-to-image.jpg", $params );
 * 
 */

//BFI_Thumb调用函数
function get_bfi_thumb($url, $width = 0, $height = 0, $crop_y = 0, $crop_x = 0, $crop_only = false)
{
    $url = esc_url($url);
    //判断是否是本地图片
    if (strpos($url, AYA_HOME) === false) {
        return $url;
    }

    //图片质量设置
    $thumb_quality = 96;

    //判断参数
    if ($height == 'full') {
        //仅缩放
        $params = array(
            'width' => $width,
            'quality' => $thumb_quality
        );
    } else {
        //生成缩略图
        $params = array(
            'width' => $width, //int pixels
            'height' => $height, //int pixels
            'crop' => true, //bool
            'crop_only' => $crop_only, //bool
            'crop_x' => $crop_x ? $crop_x : 0, //bool string
            'crop_y' => $crop_y ? $crop_y : 0, //bool string
            'quality' => $thumb_quality //int 1-100
        );
    }

    return bfi_thumb($url, $params);
}