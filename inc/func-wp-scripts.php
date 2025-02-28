<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 静态文件注册和排队
 * ------------------------------------------------------------------------------
 */

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
        default:
            $url_cdn = get_template_directory_uri() . '/assets/ajax/libs/';
            break;
    }

    return $url_cdn;

    $load_script = array(
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

    $load_style = array(
        'aplayer' => array(
            'pack' => 'aplayer',
            'file' => 'APlayer.min.css',
            'ver' => '1.10.1',
        ),
    );
}

//模板script标签
function aya_int_script($pack, $ver, $file, $defer)
{
    $defer = ($defer) ? 'defer ' : '';

    aya_echo('<script ' . $defer . 'src="' . aya_static_scripts_cdn() .  $pack . '/' . $ver . '/' . $file . '"></script>' . PHP_EOL);
}
//模板css标签
function aya_int_style($pack, $ver, $file)
{
    aya_echo('<link rel="stylesheet" href="' . aya_static_scripts_cdn() .  $pack . '/' . $ver . '/' . $file . '">' . PHP_EOL);
}

//在head中加载
function aya_head_inc()
{
    $load_js = array(
        [
            'pack' => 'perfect-scrollbar',
            'ver' => '1.5.5',
            'file' => 'perfect-scrollbar.min.js',
        ],
        [
            'pack' => 'lozad.js',
            'ver' => '1.16.0',
            'file' => 'lozad.min.js',
        ],
        [
            'pack' => 'swup',
            'ver' => '4.8.1',
            'file' => 'Swup.umd.min.js',
        ],
    );
    $load_css = array(
        [
            'pack' => 'perfect-scrollbar',
            'ver' => '1.5.5',
            'file' => 'css/perfect-scrollbar.min.css',
        ],
        [
            'pack' => 'viewerjs',
            'ver' => '1.11.7',
            'file' => 'viewer.min.css',
        ],
        [
            'pack' => 'splidejs',
            'ver' => '4.1.4',
            'file' => 'css/splide.min.css',
        ],
        [
            'pack' => 'animate.css',
            'ver' => '4.1.1',
            'file' => 'animate.min.css',
        ],
    );

    foreach ($load_js as $value) {
        aya_int_script($value['pack'], $value['ver'], $value['file'], false);
    }
    foreach ($load_css as $value) {
        aya_int_style($value['pack'], $value['ver'], $value['file']);
    }

    $main_pack_ver = (defined('AYA_RELEASE')) ? aya_theme_version() : time();
    $main_theme_uri = get_template_directory_uri() . '/assets/css/';
    //主题样式表
    aya_echo('<link rel="stylesheet" href="' . esc_url($main_theme_uri . 'main.style.css?ver=' . $main_pack_ver) . '">' . PHP_EOL);
}

//直接嵌入alpinejs和其他esm模块的标签结构
function aya_footer_inc()
{
    $load_js = array(
        [
            'pack' => 'alpinejs-collapse',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'alpinejs-persist',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'defer' => false,
        ],
        [
            'pack' => 'alpinejs-ui',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'alpinejs-focus',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'alpinejs-anchor',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'alpinejs',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'defer' => true,
        ],
        [
            'pack' => 'feather-icons',
            'ver' => '4.29.2',
            'file' => 'feather.min.js',
            'defer' => false,
        ],
        /*
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
        */
        [
            'pack' => 'viewerjs',
            'ver' => '1.11.7',
            'file' => 'viewer.min.js',
            'defer' => false,
        ],
        /*
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
            'attr' => '',
        ],
        */
        [
            'pack' => 'clipboard.js',
            'ver' => '2.0.11',
            'file' => 'clipboard.min.js',
            'defer' => false,
        ],
    );

    foreach ($load_js as $value) {
        aya_int_script($value['pack'], $value['ver'], $value['file'], $value['defer']);
    }

    $main_pack_ver = (defined('AYA_RELEASE')) ? aya_theme_version() : time();
    $main_theme_uri = get_template_directory_uri() . '/assets/js/';
    //主题脚本
    aya_echo('<script src="' . esc_url($main_theme_uri . 'main.init.js?ver=' . $main_pack_ver) . '"></script>' . PHP_EOL);
}

//排队后台静态文件
//add_action('admin_enqueue_scripts', 'aya_theme_register_admin_scripts');
//add_action('login_enqueue_scripts', 'aya_theme_register_admin_scripts');

//静态文件加载（后台）
function aya_theme_register_admin_scripts()
{
    //wp_enqueue_style('aya-login-style', AYA_URI . '/assets/css/admin.login.css', array(), aya_theme_version(), 'all');
}
