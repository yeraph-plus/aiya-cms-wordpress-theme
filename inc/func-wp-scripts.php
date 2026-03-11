<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 静态文件注册列表
 * ------------------------------------------------------------------------------
 */

add_action('wp_enqueue_scripts', 'aya_theme_localize_default_config');
add_action('wp_enqueue_scripts', 'aya_theme_register_scripts_assets');

add_action('admin_enqueue_scripts', 'aya_theme_enqueue_admin_scripts');
add_action('login_enqueue_scripts', 'aya_theme_enqueue_login_scripts');

add_action('wp_head', 'aya_theme_enqueue_header_scripts');
add_action('wp_footer', 'aya_theme_enqueue_footer_scripts');

//复制CDN资源到本地
//wget -x -nH --cut-dirs=2 -P ./ https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.8/cdn.min.js

//传递配置参数
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

//切换CDN位置
function aya_theme_static_scripts_cdn()
{
    $load_type = aya_opt('site_scripts_load_type', 'basic');

    switch ($load_type) {
        case 'cdnjs':
            $url_cdn = 'https://cdnjs.cloudflare.com/ajax/libs/';
            break;
        case 'zstatic':
            $url_cdn = 'https://s4.zstatic.net/ajax/libs/';
            break;
        case 'bootcdn':
            $url_cdn = 'https://cdn.bootcdn.net/ajax/libs/';
            break;
        case 'local':
        default:
            $url_cdn = get_template_directory_uri() . '/assets/libs/';
            break;
    }


    return $url_cdn;
}

//注册镜头文件列表
function aya_theme_register_scripts_assets()
{
    $theme_version = aya_theme_version();

    wp_register_style('aya-admin-style', get_template_directory_uri() . '/assets/admin/css/admin.style.css', [], $theme_version, 'all');
    wp_register_style('aya-login-style', get_template_directory_uri() . '/assets/admin/css/admin.login.css', [], $theme_version, 'all');

    return;


    $load_css = array(
        'aplayer' => [
            'pack' => 'aplayer',
            'deps' => [],
            'file' => 'APlayer.min.css',
            'ver' => '1.10.1',
        ],
    );

    $load_scripts = array(
        'aplayer' => [
            'pack' => 'aplayer',
            'deps' => [],
            'file' => 'APlayer.min.js',
            'ver' => '1.10.1',
        ],
        'meting' => [
            'pack' => 'meting',
            'deps' => [],
            'file' => 'Meting.min.js',
            'ver' => '2.0.1',
        ],
        'dplayer' => [
            'pack' => 'dplayer',
            'deps' => [],
            'file' => 'DPlayer.min.js',
            'ver' => '1.27.1',
        ],
        'flv' => [
            'pack' => 'flv.js',
            'deps' => [],
            'file' => 'flv.min.js',
            'ver' => '1.6.2',
        ],
        'hls' => [
            'pack' => 'hls.js',
            'deps' => [],
            'file' => 'hls.min.js',
            'ver' => '1.5.1',
        ],
        'hls-light' => [
            'pack' => 'hls.js',
            'deps' => [],
            'file' => 'hls.light.min.js',
            'ver' => '1.5.1',
        ],
    );

    $js_cdn_url = aya_theme_static_scripts_cdn();
    //循环注册
    foreach ($load_css as $key => $value) {
        wp_register_style($key, $js_cdn_url . '/' . $value['pack'] . '/' . $value['ver'] . '/' . $value['file'], $value['deps'], $value['ver'], 'all');
    }

    foreach ($load_scripts as $key => $value) {
        wp_register_script($key, $js_cdn_url . '/' . $value['pack'] . '/' . $value['ver'] . '/' . $value['file'], $value['deps'], $value['ver'], true);
    }
}

//静态文件加载（后台）
function aya_theme_enqueue_admin_scripts()
{
    wp_enqueue_style('aya-admin-style');
}

//静态文件加载（登录页）
function aya_theme_enqueue_login_scripts()
{
    wp_enqueue_style('aya-login-style');
}

//静态文件加载（页面样式）
function aya_theme_enqueue_header_scripts()
{
    //...
}

//静态文件加载（页面脚本）
function aya_theme_enqueue_footer_scripts()
{
    //...
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

/*
 * ------------------------------------------------------------------------------
 * 加载Composer实例中的方法
 * ------------------------------------------------------------------------------
 */

//加载库
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;
use Overtrue\Pinyin\Pinyin;
use Overtrue\PHPOpenCC\OpenCC;
//use Overtrue\PHPOpenCC\Strategy;

//应用中文格式化实例
function aya_chs_type_setting($content, $correct_array)
{
    $typesetting = new ChineseTypesetting();

    //$formatted_content = $typesetting->correct($content, ['insertSpace', 'removeSpace', 'full2Half', 'fixPunctuation', 'properNoun']);

    $formatted_content = $typesetting->correct($content, $correct_array);

    //返回格式化后的内容
    return $formatted_content;
}

//应用拼音转换 SLUG 实例
function aya_pinyin_permalink($slug, $abbr = false)
{
    //传入如果不是字符串
    $slug = strval($slug);
    //设置最大词长
    $length = 60;
    //设置字符
    $divider = '-'; //可用参数 '_', '-', '.', ''

    $pinyin = new Pinyin();

    //是否使用索引模式
    if ($abbr === true) {
        $slug = $pinyin->permalink($slug, $divider);
    } else {
        $slug = $pinyin->abbr($slug);
    }
    //截取最大长度
    $slug = aya_trim_slug($slug, $length, $divider);
    //返回格式化后的内容 //格式为：'带着希望去旅行' -> 'dai-zhe-xi-wang-qu-lyu-xing'
    return $slug;
}

//应用通用拼音转换实例
function aya_pinyin_setting($content, $tone = true)
{
    //传入如果不是字符串
    $content = strval($content);

    $pinyin = new Pinyin();

    //是否添加声调
    $tone = ($tone) ? 'none' : '';

    //返回格式化后的内容
    return $pinyin->sentence($content, $tone);
}

//应用繁体转换实例
function aya_opencc_setting($content, $drive = 's2t')
{
    //传入如果不是字符串
    $content = strval($content);

    //创建转换器实例
    $converter = new OpenCC();

    //进行转换
    return $converter->convert($content, 'T2JP');
}
