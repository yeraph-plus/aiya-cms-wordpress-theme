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
            $url_cdn = get_template_directory_uri() . '/assets/static/';
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

//在head中加载
function aya_head_include()
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
        echo '<script src="' . htmlspecialchars(aya_static_scripts_cdn() .  $value['pack'] . '/' . $value['ver'] . '/' . $value['file']) . '"></script>' . PHP_EOL;
    }
    foreach ($load_css as $value) {
        echo '<link rel="stylesheet" href="' . htmlspecialchars(aya_static_scripts_cdn() .  $value['pack'] . '/' . $value['ver'] . '/' . $value['file']) . '">' . PHP_EOL;
    }

    //主题样式表
    $main_pack_ver = (defined('AYA_RELEASE')) ? aya_theme_version() : time();
    $main_theme_uri = get_template_directory_uri() . '/assets/css/';

    aya_echo('<link rel="stylesheet" href="' . esc_url($main_theme_uri . 'main.style.css?ver=' . $main_pack_ver) . '">' . PHP_EOL);
}

//直接嵌入alpinejs和其他esm模块的标签结构
function aya_scripts_include()
{
    $load_js = array(
        [
            'pack' => 'alpinejs-collapse',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'attr' => '',
        ],
        [
            'pack' => 'alpinejs-persist',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'attr' => '',
        ],
        [
            'pack' => 'alpinejs-ui',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'attr' => 'defer',
        ],
        [
            'pack' => 'alpinejs-focus',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'attr' => 'defer',
        ],
        [
            'pack' => 'alpinejs-anchor',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'attr' => 'defer',
        ],
        [
            'pack' => 'alpinejs',
            'ver' => '3.14.8',
            'file' => 'cdn.min.js',
            'attr' => 'defer',
        ],
        [
            'pack' => 'pjax',
            'ver' => '0.2.8',
            'file' => 'pjax.min.js',
            'attr' => '',
        ],
        [
            'pack' => 'feather-icons',
            'ver' => '4.29.2',
            'file' => 'feather.min.js',
            'attr' => '',
        ],
        [
            'pack' => 'splidejs',
            'ver' => '4.1.4',
            'file' => 'js/splide.min.js',
            'attr' => '',
        ],
        /*
        [
            'pack' => 'ionicons',
            'ver' => '7.4.0',
            'file' => 'ionicons.min.js',
            'attr' => '',
        ],
        */
        [
            'pack' => 'viewerjs',
            'ver' => '1.11.7',
            'file' => 'viewer.min.js',
            'attr' => '',
        ],
        /*
        [
            'pack' => 'sweetalert',
            'ver' => '2.1.2',
            'file' => 'sweetalert.min.js',
            'attr' => 'defer',
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
            'attr' => 'defer',
        ],
        [
            'pack' => 'highlight.js',
            'ver' => '11.11.1',
            'file' => 'highlight.min.js',
            'attr' => 'defer',
        ],
    );
    foreach ($load_js as $value) {
        echo '<script ' . $value['attr'] . ' src="' . htmlspecialchars(aya_static_scripts_cdn() .  $value['pack'] . '/' . $value['ver'] . '/' . $value['file']) . '"></script>' . PHP_EOL;
    }

    //主题脚本
    $main_pack_ver = (defined('AYA_RELEASE')) ? aya_theme_version() : time();
    aya_echo('<script src="' . esc_url(get_template_directory_uri() . '/assets/js/main.init.js?ver=' . $main_pack_ver) . '"></script>' . PHP_EOL);
}

//排队后台静态文件
//add_action('admin_enqueue_scripts', 'aya_theme_register_admin_scripts');
//add_action('login_enqueue_scripts', 'aya_theme_register_admin_scripts');

//静态文件加载（后台）
function aya_theme_register_admin_scripts()
{
    //wp_enqueue_style('aya-login-style', AYA_URI . '/assets/css/admin.login.css', array(), aya_theme_version(), 'all');
}
