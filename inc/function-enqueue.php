<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 静态文件注册和排队
 * ------------------------------------------------------------------------------
 */

//排队前台静态文件
add_action('wp_enqueue_scripts', 'aya_theme_register_js_css');
//排队前台主题静态文件
add_action('wp_enqueue_scripts', 'aya_theme_self_js_css');
//排队后台静态文件
add_action('admin_enqueue_scripts', 'aya_admin_register_js_css');
//排队后台静态文件
add_action('login_enqueue_scripts', 'aya_login_register_js_css');
//主题样式控件
add_action('wp_head', 'aya_theme_head_css_custom');
add_action('login_head', 'aya_theme_head_css_custom');
add_action('admin_head', 'aya_theme_head_css_custom');
//操作<body>的样式
add_filter('body_class', 'aya_theme_body_class_name');

//静态文件加载
function aya_theme_register_js_css()
{
    //获取主题设置
    $enqueue_type = aya_opt('site_load_script', 'theme');

    //排除版本号
    function remove_css_js_ver($src)
    {
        if (strpos($src, 'ver='))
            $src = remove_query_arg('ver', $src);
        return $src;
    }

    //local
    if ($enqueue_type == 'local') {
        //本地文件路径
        $url_path = AYA_URI . '/assets/build';

        wp_register_script('aya-lib-merged', $url_path . '/lib.merged.js', array(), aya_theme_version(), true);
        wp_register_style('aya-lib-merged', $url_path . '/lib.merged.css', array(), aya_theme_version(), 'all');

        wp_enqueue_script('aya-lib-merged');
        wp_enqueue_style('aya-lib-merged');

        //加载JQ组件
        if (aya_opt('site_jquery_type', 'theme', true)) {
            wp_enqueue_script('jquery');
        }
    } else {
        //从CDN加载
        switch ($enqueue_type) {
            case 'cdnjs':
                $url_cdn = '//cdnjs.cloudflare.com/ajax/libs';
                break;
            case 'zstatic':
                $url_cdn = '//s4.zstatic.net/ajax/libs';
                break;
            case 'bootcdn':
                $url_cdn = '//cdn.bootcdn.net/ajax/libs';
                break;
            default:
                $url_cdn = '//cdnjs.cloudflare.com/ajax/libs';
                break;
        }

        $load_script = array(
            'jquery-cdn' => array(
                'pack' => 'jquery',
                'file' => 'jquery.min.js',
                'ver' => '3.7.1',
            ),
            'bootstrap' => array(
                'pack' => 'bootstrap',
                'file' => 'js/bootstrap.min.js',
                'ver' => '5.3.3',
            ),
            'bootstrap-bundle' => array(
                'pack' => 'bootstrap',
                'file' => 'js/bootstrap.bundle.min.js',
                'ver' => '5.3.3',
            ),
            'lozad' => array(
                'pack' => 'lozad.js',
                'file' => 'lozad.min.js',
                'ver' => '1.16.0',
            ),
            'pjax' => array(
                'pack' => 'pjax',
                'file' => 'pjax.min.js',
                'ver' => '0.2.8',
            ),
            'masonry-pkgd' => array(
                'pack' => 'masonry',
                'file' => 'masonry.pkgd.min.js',
                'ver' => '4.2.2',
            ),
            'viewer' => array(
                'pack' => 'viewerjs',
                'file' => 'viewer.min.js',
                'ver' => '1.11.6',
            ),
            'highlight' => array(
                'pack' => 'highlight.js',
                'file' => 'highlight.min.js',
                'ver' => '11.10.0',
            ),
            'highlight-line' => array(
                'pack' => 'highlightjs-line-numbers.js',
                'file' => 'highlightjs-line-numbers.min.js',
                'ver' => '2.8.0',
            ),
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
            'flv.js' => array(
                'pack' => 'flv.js',
                'file' => 'flv.min.js',
                'ver' => '1.6.2',
            ),
            'hls.js' => array(
                'pack' => 'hls.js',
                'file' => 'hls.min.js',
                'ver' => '1.5.1',
            ),
            'hls.light.js' => array(
                'pack' => 'hls.js',
                'file' => 'hls.light.min.js',
                'ver' => '1.5.1',
            ),
            'clipboard.js' => array(
                'pack' => 'clipboard.js',
                'file' => 'clipboard.min.js',
                'ver' => '2.0.11',
            ),
            'marked' => array(
                'pack' => 'marked',
                'file' => 'marked.min.js',
                'ver' => '13.0.3',
            ),
        );
        $load_style = array(
            'bootstrap' => array(
                'pack' => 'bootstrap',
                'file' => 'css/bootstrap.min.css',
                'ver' => '5.3.3',
            ),
            'bootstrap-icons' => array(
                'pack' => 'bootstrap-icons',
                'file' => 'font/bootstrap-icons.min.css',
                'ver' => '1.11.3',
            ),
            'viewer' => array(
                'pack' => 'viewerjs',
                'file' => 'viewer.min.css',
                'ver' => '1.11.6',
            ),
            'highlight' => array(
                'pack' => 'highlight.js',
                'file' => 'styles/' . aya_opt('site_highlight_style', 'format'),
                'ver' => '11.9.0',
            ),
            'aplayer' => array(
                'pack' => 'aplayer',
                'file' => 'APlayer.min.css',
                'ver' => '1.10.1',
            ),
        );

        //注册JS
        foreach ($load_script as $handle => $value) {
            wp_register_script($handle, $url_cdn . '/' . $value['pack'] . '/' . $value['ver'] . '/' . $value['file'], array(), $value['ver'], true);
        }
        //注册CSS
        foreach ($load_style as $handle => $value) {
            wp_register_style($handle, $url_cdn . '/' . $value['pack'] . '/' . $value['ver'] . '/' . $value['file'], array(), $value['ver'], 'all');
        }

        //排除静态文件路径
        add_filter('style_loader_src', 'remove_css_js_ver', 99);
        add_filter('script_loader_src', 'remove_css_js_ver', 99);

        //加载Bootstrasp组件
        wp_enqueue_script('bootstrap-bundle');
        wp_enqueue_script('masonry-pkgd');
        wp_enqueue_style('bootstrap');
        wp_enqueue_style('bootstrap-icons');
        //加载LozadJS
        wp_enqueue_script('lozad');
        //加载ViewerJS
        wp_enqueue_style('viewer');
        wp_enqueue_script('viewer');
        //加载Pjax组件
        wp_enqueue_script('pjax');
        //加载JQ组件
        if (aya_opt('site_jquery_type', 'theme', true)) {
            wp_enqueue_script('jquery-cdn');
        }
        //加载HighlightJS
        if (is_singular() && aya_opt('site_highlight_type', 'format', true)) {
            wp_enqueue_style('highlight');
            wp_enqueue_script('highlight');
            wp_enqueue_script('highlight-line');
        }
        //加载Aplayer
        if (is_singular() && aya_opt('site_aplayer_type', 'format', true)) {
            wp_enqueue_style('aplayer');
            wp_enqueue_script('aplayer');
            wp_enqueue_script('meting');
            wp_enqueue_script('hls.light.js');
        }
        //加载Dplayer
        if (is_singular() && aya_opt('site_dplayer_type', 'format', true)) {
            wp_enqueue_style('dplayer');
            wp_enqueue_script('dplayer');
            wp_enqueue_script('flv.js');
            wp_enqueue_script('hls.light.js');
        }
    }
}
//静态文件加载（后台）
function aya_admin_register_js_css()
{
    //主题CSS文件
    wp_register_style('aya-admin-style', AYA_URI . '/assets/admin/admin-style.css', array(), aya_theme_version(), 'all');

    wp_enqueue_style('aya-admin-style');
}
//静态文件加载（登录）
function aya_login_register_js_css()
{
    //主题CSS文件
    wp_register_style('aya-admin-login', AYA_URI . '/assets/admin/admin-login.css', array(), aya_theme_version(), 'all');

    wp_enqueue_style('aya-admin-login');
}
//主题自身
function aya_theme_self_js_css()
{
    //主题JS文件
    wp_register_script('aya-main-action', AYA_URI . '/assets/index/main.firing.js', array(), aya_theme_version(), false); //false就在页头加载
    wp_register_script('aya-main-bulid', AYA_URI . '/assets/index/main.bulid.js', array(), aya_theme_version(), true);
    wp_register_script('aya-main-child', AYA_URI . '/assets/index/main.child.js', array(), aya_theme_version(), true);
    //主题CSS文件
    wp_register_style('aya-main-style', AYA_URI . '/assets/index/main.style.css', array(), aya_theme_version(), 'all');

    //加载JQ组件
    if (aya_opt('site_jquery_type', 'theme', true)) {
        wp_enqueue_script('jquery');
    }
    //如果WP_DEBUG启用
    if (defined('WP_DEBUG')) {
        //调试用
        $css_list = array(
            'body',
            'aside',
            'header',
            'footer',
            'home',
            'loop',
            'post',
            'editor',
            'comment',
            'sidebar',
        );
        foreach ($css_list as $css) {
            wp_enqueue_style('aya-theme-' . $css . '', AYA_URI . '/assets/index/unit/' . $css . '.css', array(), time(), 'all');
        }
    }
    //加载主题静态文件
    wp_enqueue_style('aya-main-style');
    wp_enqueue_script('aya-main-action');
    wp_enqueue_script('aya-main-bulid');
    //wp_enqueue_script('aya-main-child'); //jQuery组件

    //加载AJAX位置
    wp_localize_script('jquery', 'aya_home', array(
        'home_url' => home_url(),
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
//配置可选CSS样式
function aya_theme_head_css_custom()
{
    $theme_css = '';

    //加载动画
    if (aya_opt('site_loading_animation', 'layout', true)) {
        $theme_css .= ':root { --aya-loading-animation: url(' . aya_get_page_loading_animation() . '); }' . PHP_EOL;
    }
    //背景图
    if (aya_opt('site_background_type', 'layout', true)) {
        $theme_css .= ':root { --aya-bg-image: url(' . aya_opt('site_background_upload', 'layout') . '); }' . PHP_EOL;
    }
    //自定义样式表
    if (aya_opt('site_color_custom_type', 'layout', true)) {
        $theme_css .= aya_opt('site_color_custom', 'layout');
    } else {
        $theme_css .= aya_theme_head_css_custom_rule();
    }

    //注册一个过滤器
    $theme_css = apply_filters('aya_theme_style_filter', $theme_css);

    //组装自定义样式表
    return e_html('<style type="text/css">' . $theme_css . '</style>' . PHP_EOL);
}
//自定义样式表规则
function aya_theme_head_css_custom_rule($opt_echo = false)
{
    $color_el = aya_opt('site_color_element', 'layout');

    //添加样式
    $css = '';
    //主题样式
    $css .= ':root {';
    $css .= '--aya-bg-color: ' . aya_opt('site_background_color', 'layout') . ';';
    $css .= '--aya-header-bar-bg: #1f1f1f;';
    $css .= '--aya-footer-bar-bg: #1f1f1f;';
    $css .= '--aya-aside-bar-bg: #f8f8f5;';
    $css .= '--aya-aside-width: ' . aya_opt('site_aside_bar_width', 'layout') . ';';
    $css .= '--aya-banner-image: url(' . aya_opt('site_banner_upload', 'layout') . '); ';
    $css .= '--aya-banner-height: ' . aya_opt('site_banner_height', 'layout') . '; ';
    //主题配色
    $css .= '--aya-element-color: ' . $color_el . ';';
    $css .= '--aya-less-color: #7a7a7a;';
    $css .= '--aya-border-color: #f0f0f0;';
    $css .= '--aya-title-color: #1f1f1f;';
    $css .= '--aya-link-color: #212529;';
    $css .= '--aya-link-hover-color: ' . $color_el . ';';
    $css .= '--aya-btn-bg-color: ' . $color_el . ';';
    $css .= '--aya-btn-hover-color: #fff;';
    $css .= '--aya-btn-hover-bg: ' . $color_el . ';';
    $css .= '--aya-btn-focus-color: #fff;';
    $css .= '--aya-btn-focus-bg: ' . $color_el . ';';
    $css .= '--aya-card-box-color: #505050;';
    $css .= '--aya-card-box-bg: #fff;';
    $css .= '--aya-card-box-shadow: 2px 2px 4px 2px #0000000f;';
    $css .= '}' . PHP_EOL;
    //正文样式
    $css .= ':root {';
    $css .= '--aya-main-color: ' . aya_opt('site_main_color', 'format') . ';';
    $css .= '--aya-main-link-color: ' . $color_el . ';';
    $css .= '--aya-main-font-size: ' . aya_opt('site_main_size', 'format') . ';';
    $css .= '--aya-main-margin-width: ' . aya_opt('site_main_width', 'format') . ';';
    $css .= '--aya-main-block-bg: #f5f5f5;';
    $css .= '}';

    //需要回显时添加换行
    if ($opt_echo) $css = str_replace(';', ';' . PHP_EOL, $css);

    return $css;
}
//添加样式名到<body>标签
function aya_theme_body_class_name($class_array)
{
    //获取主题设置
    $add_array = array(
        'clearfix',
        //'default',
        (aya_opt('site_dark_mode_type', 'layout')) ? 'dark' : '',
        (aya_opt('site_gray_mode_type', 'layout')) ? 'gray-mode' : '',
        (aya_opt('site_background_center', 'layout')) ? 'bg-center' : '',
        (aya_opt('site_background_after', 'layout')) ? 'bg-shade' : '',
    );

    return array_merge($class_array, $add_array);
}
//获取主题加载动画
function aya_get_page_loading_animation()
{
    //获取主题设置
    $svg_selector = aya_opt('site_page_default_selector', 'layout');

    //判断切换
    if ($svg_selector == 'custom') {
        //获取主题设置
        return aya_opt('site_page_custom_selector', 'layout');
    } else {
        return AYA_URI . '/assets/image/load/' . $svg_selector . '.svg';
    }
}
//获取主题空列表图片
function aya_get_loaded_empty_img($class = '')
{
    $page_img = aya_opt('site_none_page_img', 'theme');

    return aya_lazy_img_tags($page_img, $class, 'NONE CONTENT');
}
//获取主题404页面占位图片
function aya_get_404_page_img($class = '')
{
    $page_img = aya_opt('site_404_page_img', 'theme');

    return aya_lazy_img_tags($page_img, $class, '404 NOT FOUND');
}
//获取主题默认图片
function aya_get_default_thumbnail()
{
    return aya_opt('site_thumb_default', 'theme');
}
