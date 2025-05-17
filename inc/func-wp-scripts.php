<?php

if (!defined('ABSPATH')) {
    exit;
}

//wget -x -nH --cut-dirs=2 -P ./ https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.8/cdn.min.js

//从CDN加载
function aya_static_scripts_cdn()
{
    //获取主题设置
    $load_type = aya_opt('site_scripts_load_type', 'basic');

    //从CDN加载
    switch ($load_type) {
        case 'cdnjs':
            $url_cdn = '//cdnjs.cloudflare.com/ajax/libs/';
            break;
        case 'zstatic':
            $url_cdn = '//s4.zstatic.net/ajax/libs/';
            break;
        case 'bootcdn':
            $url_cdn = '//cdn.bootcdn.net/ajax/libs/';
            break;
        case 'local':
        default:
            $url_cdn = get_template_directory_uri() . '/assets/libs/';
            break;
    }

    return $url_cdn;
}

//模板script标签
function aya_int_script($pack, $ver, $file, $defer)
{
    $defer = ($defer) ? 'defer ' : '';

    aya_echo('<script ' . $defer . 'src="' . aya_static_scripts_cdn() . $pack . '/' . $ver . '/' . $file . '"></script>' . PHP_EOL);
}
//模板css标签
function aya_int_style($pack, $ver, $file)
{
    aya_echo('<link rel="stylesheet" href="' . aya_static_scripts_cdn() . $pack . '/' . $ver . '/' . $file . '">' . PHP_EOL);
}

/*
 * ------------------------------------------------------------------------------
 * 静态文件注册和排队
 * ------------------------------------------------------------------------------
 */

//排队后台静态文件
//add_action('admin_enqueue_scripts', 'aya_theme_register_admin_scripts');
//add_action('login_enqueue_scripts', 'aya_theme_register_admin_scripts');
//add_action('wp_head', 'aya_inc_load_head_scripts');
//add_action('wp_footer', 'aya_inc_load_footer_scripts');

//静态文件加载（后台）
function aya_theme_register_admin_scripts()
{
    //wp_enqueue_style('aya-login-style', AYA_URI . '/assets/css/admin.login.css', array(), aya_theme_version(), 'all');
}
//在head中加载
function aya_inc_load_head_scripts()
{
    //使用自定义的方法加载静态文件
    $load_js = array(
        [
            'pack' => 'perfect-scrollbar',
            'ver' => '1.5.6',
            'file' => 'perfect-scrollbar.min.js',
        ],
        [
            'pack' => 'lozad.js',
            'ver' => '1.16.0',
            'file' => 'lozad.min.js',
        ],
        [
            'pack' => 'htmx',
            'ver' => '2.0.4',
            'file' => 'Swup.umd.min.js',
        ],
        'aplayer' => array(
            'pack' => 'aplayer',
            'file' => 'APlayer.min.js',
            'ver' => '1.10.1',
        ),
        'meting' => array(
            'pack' => 'meting',
            'file' => 'Meting.min.js',
            'ver' => '2.0.1',
        ),
        'dplayer' => array(
            'pack' => 'dplayer',
            'file' => 'DPlayer.min.js',
            'ver' => '1.27.1',
        ),
        'flv' => array(
            'pack' => 'flv.js',
            'file' => 'flv.min.js',
            'ver' => '1.6.2',
        ),
        'hls' => array(
            'pack' => 'hls.js',
            'file' => 'hls.min.js',
            'ver' => '1.5.1',
        ),
        'hls-light' => array(
            'pack' => 'hls.js',
            'file' => 'hls.light.min.js',
            'ver' => '1.5.1',
        ),
        'marked' => array(
            'pack' => 'marked',
            'file' => 'marked.min.js',
            'ver' => '13.0.3',
        ),
    );
    $load_css = array(
        [
            'pack' => 'perfect-scrollbar',
            'ver' => '1.5.6',
            'file' => 'css/perfect-scrollbar.min.css',
        ],
        [
            'pack' => 'viewerjs',
            'ver' => '1.11.7',
            'file' => 'viewer.min.css',
        ],
        [
            'pack' => 'swiperjs',
            'ver' => '4.1.4',
            'file' => 'css/splide.min.css',
        ],
        [
            'pack' => 'animate.css',
            'ver' => '4.1.1',
            'file' => 'animate.min.css',
        ],
        [
            'pack' => 'aplayer',
            'file' => 'APlayer.min.css',
            'ver' => '1.10.1',
        ],
    );
    //循环打印
    foreach ($load_js as $value) {
        //aya_int_script($value['pack'], $value['ver'], $value['file'], false);
    }
    //循环打印
    foreach ($load_css as $value) {
        //aya_int_style($value['pack'], $value['ver'], $value['file']);
    }
    //设置版本
    $main_pack_ver = (defined('AYA_RELEASE')) ? aya_theme_version() : time();
    //主题样式表
    aya_echo('<link rel="stylesheet" href="' . esc_url(get_template_directory_uri() . '/assets/dist/main.css?ver=' . $main_pack_ver) . '">' . PHP_EOL);

}
//直接嵌入alpinejs和其他esm模块的标签结构
function aya_inc_load_footer_scripts()
{
    //使用自定义的方法加载静态文件
    $load_js2 = array(
        [
            'pack' => 'alpinejs-ui',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'viewerjs',
            'ver' => '1.11.7',
            'file' => 'viewer.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'tocbot',
            'ver' => '4.32.2',
            'file' => 'tocbot.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'splidejs',
            'ver' => '4.1.4',
            'file' => 'js/splide.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'ionicons',
            'ver' => '7.4.0',
            'file' => 'ionicons.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'sweetalert',
            'ver' => '2.1.2',
            'file' => 'sweetalert.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'masonry',
            'ver' => '4.2.2',
            'file' => 'masonry.pkgd.min.js',
            'defer' => false,
        ],
    );
    $load_js = array(
        [
            'pack' => 'alpinejs-persist',
            'ver' => '3.14.9',
            'file' => 'cdn.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'alpinejs-collapse',
            'ver' => '3.14.9',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'alpinejs-focus',
            'ver' => '3.14.9',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'alpinejs-intersect',
            'ver' => '3.14.9',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'alpinejs-anchor',
            'ver' => '3.14.9',
            'file' => 'cdn.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'alpinejs',
            'ver' => '3.14.9',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'feather-icons',
            'ver' => '4.29.2',
            'file' => 'feather.min.js',
            'defer' => false,
        ],

    );
    //循环打印
    foreach ($load_js as $value) {
        aya_int_script($value['pack'], $value['ver'], $value['file'], $value['defer']);
    }
    //加载额外脚本
    $add_scripts_filter = apply_filters('aya_int_add_scripts', '');
    aya_echo($add_scripts_filter);
    //主题脚本
    $main_pack_ver = (defined('AYA_RELEASE')) ? aya_theme_version() : time();
    //主题启动脚本
    aya_echo('<script src="' . esc_url(get_template_directory_uri() . '/assets/src/alpine.init.js?ver=' . $main_pack_ver) . '"></script>' . PHP_EOL);
}
