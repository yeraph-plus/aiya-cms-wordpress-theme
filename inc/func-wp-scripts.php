<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 后台静态文件注册
 * ------------------------------------------------------------------------------
 */

add_action('admin_enqueue_scripts', 'aya_theme_register_admin_scripts');
add_action('login_enqueue_scripts', 'aya_theme_register_login_scripts');

//静态文件加载（后台）
function aya_theme_register_admin_scripts()
{
    wp_enqueue_style('aya-login-style', AYA_URI . '/assets/admin/css/admin.style.css', [], aya_theme_version(), 'all');
}
//静态文件加载（登录页）
function aya_theme_register_login_scripts()
{
    wp_enqueue_style('aya-login-style', AYA_URI . '/assets/admin/css/admin.login.css', [], aya_theme_version(), 'all');
}

/*
 * ------------------------------------------------------------------------------
 * 前台外部静态文件排队
 * ------------------------------------------------------------------------------
 */

//注册新的资源队列
// add_action('after_setup_theme', 'aya_init_registered_assets');
//外部资源加载打印位置
// add_action('wp_head', 'aya_load_header_assets');
// add_action('wp_footer', 'aya_load_footer_assets');

//复制CDN资源到本地
//wget -x -nH --cut-dirs=2 -P ./ https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.8/cdn.min.js

//切换CDN位置
function aya_static_scripts_cdn($load_type)
{
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
            $url_cdn = AYA_URI . '/assets/libs/';
            break;
    }

    return $url_cdn;
}

//预设全局变量存储加载队列
function aya_init_registered_assets()
{
    global $aya_preload_assets;

    $aya_preload_assets = [
        'styles' => [],
        'header_scripts' => [],
        'footer_scripts' => []
    ];
}

//队列注册逻辑
//Tips: 此函数仅用于工作在 wp_enqueue_scripts 钩子（即 wp_head 之前）
function aya_register_assets($location = 'styles', $handle = 'null', $args = [])
{
    global $aya_preload_assets;

    //内部查重
    static $internal_assets = [];

    //检查是否已注册
    if (isset($internal_assets[$handle])) {
        return false;
    }

    switch ($location) {
        case 'styles':
            $aya_preload_assets['styles'][$handle] = aya_parse_style($args);
            break;
        case 'header_scripts':
            $aya_preload_assets['header_scripts'][$handle] = aya_parse_script($args);
            break;
        case 'footer_scripts':
            $aya_preload_assets['footer_scripts'][$handle] = aya_parse_script($args);
            break;
        default:
            return false; // 无效位置
    }

    $internal_assets[$handle] = true;

    return true;

}

//定义脚本资源包数据结构
function aya_parse_script($args = [])
{
    $defaults = [
        'pack' => '',
        'ver' => '',
        'file' => '',
        'integrity' => '', //SRI
        'defer' => false,
    ];

    $args = wp_parse_args($args, $defaults);

    if (empty($args['pack']) || empty($args['ver']) || empty($args['file'])) {
        return false;
    }

    return $args;
}

//定义样式表资源包数据结构
function aya_parse_style($args = [])
{
    $defaults = [
        'pack' => '',
        'ver' => '',
        'file' => '',
        'integrity' => '', //SRI
    ];

    $args = wp_parse_args($args, $defaults);

    if (empty($args['pack']) || empty($args['ver']) || empty($args['file'])) {
        return false;
    }

    return $args;
}

// /**
//  * Load assets in header (styles and scripts)
//  */
// function aya_load_header_assets()
// {
//     global $aya_preload_assets;

//     // Allow dynamic style additions via filter
//     $list_styles = apply_filters('aya_registered_styles', $aya_registered_styles);
//     // Allow dynamic script additions via filter
//     $aya_registered_scripts = apply_filters('aya_registered_scripts', $aya_registered_scripts);

//     $load_type = aya_opt('site_scripts_load_type', 'local');
//     $is_external = ($load_type !== 'local');

//     // Output styles
//     if (!empty($aya_registered_styles)) {
//         foreach ($aya_registered_styles as $style) {
//             if ($style = aya_parse_style($style)) {
//                 $integrity_attr = (!empty($style['integrity'])) ? 'integrity="' . esc_attr($style['integrity']) . '" ' : '';
//                 $crossorigin = ($is_external) ? 'crossorigin="anonymous" ' : '';
//                 $full_href = aya_static_scripts_cdn($load_type) . $style['pack'] . '/' . $style['ver'] . '/' . $style['file'];
//                 aya_echo('<link rel="stylesheet" ' . $crossorigin . $integrity_attr . 'href="' . esc_url($full_href) . '" media="all">' . PHP_EOL);
//             }
//         }
//     }

//     // Output scripts
//     if (!empty($aya_registered_scripts)) {
//         foreach ($aya_registered_scripts as $script) {
//             if ($script = aya_parse_script($script)) {
//                 $integrity_attr = (!empty($script['integrity'])) ? 'integrity="' . esc_attr($script['integrity']) . '" ' : '';
//                 $defer_attr = ($script['defer'] === true) ? 'defer ' : '';
//                 $crossorigin = ($is_external) ? 'crossorigin="anonymous" ' : '';
//                 $full_src = aya_static_scripts_cdn($load_type) . $script['pack'] . '/' . $script['ver'] . '/' . $script['file'];
//                 aya_echo('<script ' . $defer_attr . $crossorigin . $integrity_attr . 'src="' . esc_url($full_src) . '"></script>' . PHP_EOL);
//             }
//         }
//     }
// }

// /**
//  * Load assets in footer (scripts only)
//  */
// function aya_load_footer_assets()
// {
//     global $aya_registered_footer_scripts;

//     // Allow dynamic footer script additions via filter
//     $aya_registered_footer_scripts = apply_filters('aya_registered_footer_scripts', $aya_registered_footer_scripts);

//     $load_type = aya_opt('site_scripts_load_type', 'local');
//     $is_external = ($load_type !== 'local');

//     // Output footer scripts
//     if (!empty($aya_registered_footer_scripts)) {
//         foreach ($aya_registered_footer_scripts as $script) {
//             if ($script = aya_parse_script($script)) {
//                 $integrity_attr = (!empty($script['integrity'])) ? 'integrity="' . esc_attr($script['integrity']) . '" ' : '';
//                 $defer_attr = ($script['defer'] === true) ? 'defer ' : '';
//                 $crossorigin = ($is_external) ? 'crossorigin="anonymous" ' : '';
//                 $full_src = aya_static_scripts_cdn($load_type) . $script['pack'] . '/' . $script['ver'] . '/' . $script['file'];
//                 aya_echo('<script ' . $defer_attr . $crossorigin . $integrity_attr . 'src="' . esc_url($full_src) . '"></script>' . PHP_EOL);
//             }
//         }
//     }
// }

// //初始化主题默认脚本队列



// //在head中加载
// function aya_inc_load_head_scripts()
// {
//     //使用自定义的方法加载静态文件
//     $load_js = array(
//         'aplayer' => array(
//             'pack' => 'aplayer',
//             'file' => 'APlayer.min.js',
//             'ver' => '1.10.1',
//         ),
//         'meting' => array(
//             'pack' => 'meting',
//             'file' => 'Meting.min.js',
//             'ver' => '2.0.1',
//         ),
//         'dplayer' => array(
//             'pack' => 'dplayer',
//             'file' => 'DPlayer.min.js',
//             'ver' => '1.27.1',
//         ),
//         'flv' => array(
//             'pack' => 'flv.js',
//             'file' => 'flv.min.js',
//             'ver' => '1.6.2',
//         ),
//         'hls' => array(
//             'pack' => 'hls.js',
//             'file' => 'hls.min.js',
//             'ver' => '1.5.1',
//         ),
//         'hls-light' => array(
//             'pack' => 'hls.js',
//             'file' => 'hls.light.min.js',
//             'ver' => '1.5.1',
//         ),
//         'marked' => array(
//             'pack' => 'marked',
//             'file' => 'marked.min.js',
//             'ver' => '13.0.3',
//         ),
//     );
//     $load_css = array(
//         [
//             'pack' => 'aplayer',
//             'file' => 'APlayer.min.css',
//             'ver' => '1.10.1',
//         ],
//     );
//     $load_js = array(
//         [
//             'pack' => 'feather-icons',
//             'ver' => '4.29.2',
//             'file' => 'feather.min.js',
//             'defer' => false,
//         ],
//         [
//             'pack' => 'ionicons',
//             'ver' => '7.4.0',
//             'file' => 'ionicons.min.js',
//             'defer' => false,
//         ],

//     );
// }