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

    $highlight_style = aya_opt('site_highlight_style', 'theme');

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
        $dir_path = AYA_URI . '/assets/dist/';
        //主要文件
        wp_register_script('jquery-self', $dir_path . 'jquery/jquery.min.js', array(), '3.7.1', true);
        wp_register_script('bootstrap', $dir_path . 'bootstrap/js/bootstrap.min.js', array(), '5.3.2', true);
        wp_register_script('bootstrap-bundle', $dir_path . 'bootstrap/js/bootstrap.bundle.min.js', array(), '5.3.2', true);
        wp_register_style('bootstrap', $dir_path . 'bootstrap/css/bootstrap.min.css', array(), '5.3.2', 'all');
        wp_register_style('bootstrap-icons', $dir_path . 'bootstrap-icons/font/bootstrap-icons.min.css', array(), '1.11.3', 'all');
        //Lazyload
        wp_register_script('lozad', $dir_path . 'lozad/lozad.min.js', array(), '1.16.0', true);
        //Pjax
        wp_register_script('pjax', $dir_path . 'pjax/pjax.min.js', array(), '0.2.8', true);
        //masonry
        wp_register_script('masonry-self', $dir_path . 'masonry/masonry.pkgd.min.js', array(), '4.2.2', true);
        //viewer
        wp_register_style('viewer', $dir_path . 'viewer/viewer.min.css', array(), '1.11.6', 'all');
        wp_register_script('viewer', $dir_path . 'viewer/viewer.min.js', array(), '1.11.6', true);
        //highlight
        wp_register_script('highlight', $dir_path . 'highlight/highlight.min.js', array(), '11.9.0', true);
        wp_register_style('highlight', $dir_path . 'highlight/styles/' . $highlight_style . '.css', array(), '11.9.0', 'all');
        //aplayer
        wp_register_script('aplayer', $dir_path . 'aplayer/APlayer.min.js', array(), '1.10.1', true);
        wp_register_style('aplayer', $dir_path . 'aplayer/APlayer.min.css', array(), '1.10.1', 'all');
        //dplayer
        wp_register_script('dplayer', $dir_path . 'dplayer/DPlayer.min.js', array(), '1.27.0', true);
        //flv
        wp_register_script('flv', $dir_path . 'flv/flv.min.js', array(), '1.6.2', true);
        //hls
        wp_register_script('hls', $dir_path . 'hls/hls.min.js', array(), '1.4.12', true);
        wp_register_script('hls-light', $dir_path . 'hls/hls.light.min.js', array(), '1.4.12', true);
        //meting
        wp_register_script('meting-api', $dir_path . 'meting/Meting.min.js', array(), '1.2.0', true);
        //clipboard
        wp_register_script('clipboard', $dir_path . 'clipboard/clipboard.min.js', array(), '2.0.11', true);
        //marked
        wp_register_script('marked', $dir_path . 'marked/marked.min.js', array(), '10.0.0', true);
    }
    //staticfile
    else if ($enqueue_type == 'staticfile') {
        $url_cdn = '//cdn.staticfile.net/';
        //jquery
        wp_register_script('jquery-self', $url_cdn . 'jquery/3.7.1/jquery.min.js', array(), '3.7.1', true);
        //bootstrap
        wp_register_script('bootstrap', $url_cdn . 'bootstrap/5.3.2/js/bootstrap.min.js', array(), '5.3.2', true);
        wp_register_script('bootstrap-bundle', $url_cdn . 'bootstrap/5.3.2/js/bootstrap.bundle.min.js', array(), '5.3.2', true);
        wp_register_style('bootstrap', $url_cdn . 'bootstrap/5.3.2/css/bootstrap.min.css', array(), '5.3.2', 'all');
        wp_register_style('bootstrap-icons', $url_cdn . 'bootstrap-icons/1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3', 'all');
        //Lazyload
        wp_register_script('lozad', $url_cdn . 'lozad.js/1.16.0/lozad.min.js', array(), '1.16.0', true);
        //Pjax
        wp_register_script('pjax', $url_cdn . 'pjax/0.2.8/pjax.min.js', array(), '0.2.8', true);
        //masonry
        wp_register_script('masonry-self', $url_cdn . 'masonry/4.2.2/masonry.pkgd.min.js', array(), '4.2.2', true);
        //viewer
        wp_register_style('viewer', $url_cdn . 'viewerjs/1.11.6/viewer.min.css', array(), '1.11.6', 'all');
        wp_register_script('viewer', $url_cdn . 'viewerjs/1.11.6/viewer.min.js', array(), '1.11.6', true);
        //highlight
        wp_register_script('highlight', $url_cdn . 'highlight.js/11.9.0/highlight.min.js', array(), '11.9.0', true);
        wp_register_style('highlight', $url_cdn . 'highlight.js/11.9.0/styles/' . $highlight_style . '.css', array(), '11.9.0', 'all');
        //aplayer
        wp_register_script('aplayer', $url_cdn . 'aplayer/1.10.1/APlayer.min.js', array(), '1.10.1', true);
        wp_register_style('aplayer', $url_cdn . 'aplayer/1.10.1/APlayer.min.css', array(), '1.10.1', 'all');
        //dplayer
        wp_register_script('dplayer', $url_cdn . 'dplayer/1.27.1/DPlayer.min.js', array(), '1.27.1', true);
        //flv
        wp_register_script('flv', $url_cdn . 'flv.js/1.6.2/flv.min.js', array(), '1.6.2', true);
        //hls
        wp_register_script('hls', $url_cdn . 'hls.js/1.5.1/hls.min.js', array(), '1.5.1', true);
        wp_register_script('hls-light', $url_cdn . 'hls.js/1.5.1/hls.light.min.js', array(), '1.5.1', true);
        //meting
        wp_register_script('meting-api', $url_cdn . 'meting/2.0.1/Meting.min.js', array(), '2.0.1', true);
        //clipboard
        wp_register_script('clipboard', $url_cdn . 'clipboard.js/2.0.11/clipboard.min.js', array(), '2.0.11', true);
        //marked
        wp_register_script('marked', $url_cdn . 'marked/11.1.1/marked.min.js', array(), '11.1.1', true);
        //排除静态文件路径
        add_filter('style_loader_src', 'remove_css_js_ver', 99);
        add_filter('script_loader_src', 'remove_css_js_ver', 99);
    }
    //jsdelivr
    else if ($enqueue_type == 'jsdelivr') {
        $url_cdn = '//cdn.jsdelivr.net/npm/';
        //jquery
        wp_register_script('jquery-self', $url_cdn . 'jquery@3.7.1/dist/jquery.min.js', array(), '3.7.1', true);
        //bootstrap
        wp_register_script('bootstrap', $url_cdn . 'bootstrap@5.3.2/dist/js/bootstrap.min.js', array(), '5.3.2', true);
        wp_register_script('bootstrap-bundle', $url_cdn . 'bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), '5.3.2', true);
        wp_register_style('bootstrap', $url_cdn . 'bootstrap@5.3.2/dist/css/bootstrap.min.css', array(), '5.3.2', 'all');
        wp_register_style('bootstrap-icons', $url_cdn . 'bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3', 'all');
        //Lazyload
        wp_register_script('lozad', $url_cdn . 'lozad@1.16.0/dist/lozad.min.js', array(), '1.16.0', true);
        //Pjax
        wp_register_script('pjax', $url_cdn . 'pjax@0.2.8/pjax.min.js', array(), '0.2.8', true);
        //masonry
        wp_register_script('masonry-self', $url_cdn . 'masonry-layout@4.2.2/dist/masonry.pkgd.min.js', array(), '4.2.2', true);
        //viewer
        wp_register_style('viewer', $url_cdn . 'viewerjs@1.11.6/dist/viewer.min.css', array(), '1.11.6', 'all');
        wp_register_script('viewer', $url_cdn . 'viewerjs@1.11.6/dist/viewer.min.js', array(), '1.11.6', true);
        //highlight
        wp_register_script('highlight', $url_cdn . 'highlight.js@11.9.0/lib/index.min.js', array(), '11.9.0', true);
        wp_register_style('highlight', $url_cdn . 'highlight.js@11.9.0/styles/' . $highlight_style . '.min.css', array(), '11.9.0', 'all');
        //aplayer
        wp_register_script('aplayer', $url_cdn . 'aplayer@1.10.1/dist/APlayer.min.js', array(), '1.10.1', true);
        wp_register_style('aplayer', $url_cdn . 'aplayer@1.10.1/dist/APlayer.min.css', array(), '1.10.1', 'all');
        //dplayer
        wp_register_script('dplayer', $url_cdn . 'dplayer@1.27.1/dist/DPlayer.min.js', array(), '1.27.1', true);
        //flv
        wp_register_script('flv', $url_cdn . 'flv.js@1.6.2/dist/flv.min.js', array(), '1.6.2', true);
        //hls
        wp_register_script('hls', $url_cdn . 'hls.js@1.5.4/dist/hls.min.js', array(), '1.5.4', true);
        wp_register_script('hls-light', $url_cdn . 'hls.js@1.5.4/dist/hls.light.min.js', array(), '1.5.4', true);
        //meting
        wp_register_script('meting-api', $url_cdn . 'meting@2.0.1/dist/Meting.min.js', array(), '2.0.1', true);
        //clipboard
        wp_register_script('clipboard', $url_cdn . 'clipboard@2.0.11/dist/clipboard.min.js', array(), '2.0.11', true);
        //marked
        wp_register_script('marked', $url_cdn . 'marked@12.0.0/lib/marked.umd.min.js', array(), '12.0.0', true);
        //排除静态文件路径
        add_filter('style_loader_src', 'remove_css_js_ver', 99);
        add_filter('script_loader_src', 'remove_css_js_ver', 99);
    }

    //-----加载动作-----//

    //加载Bootstrasp组件
    wp_enqueue_script('bootstrap-bundle');
    wp_enqueue_style('bootstrap');
    wp_enqueue_style('bootstrap-icons');

    //加载JQ组件
    if (aya_opt('site_jquery_type', 'theme', true)) {
        wp_enqueue_script('jquery-self');
        wp_enqueue_script('aya-main-child'); //jQuery组件
        //加载AJAX位置
        wp_localize_script('jquery-self', 'aya_home', array(
            'home_url' => home_url(),
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
    //加载LozadJS
    if (aya_opt('site_lozad_type', 'theme', true)) {
        wp_enqueue_script('lozad');
    }
    //加载MasonryJS
    if (aya_opt('site_masonry_type', 'theme', true)) {
        wp_enqueue_script('masonry-self');
    }
    //加载ViewerJS
    if (aya_opt('site_viewer_type', 'theme', true)) {
        wp_enqueue_style('viewer');
        wp_enqueue_script('viewer');
    }
    //加载Pjax组件
    if (aya_opt('site_pjax_type', 'theme', true)) {
        wp_enqueue_script('pjax');
    }
    //加载HighlightJS
    if (aya_opt('site_highlight_type', 'theme', true)) {
        wp_enqueue_style('highlight');
        wp_enqueue_script('highlight');
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
        wp_enqueue_style('aya-theme-' . $css . '', AYA_URI . '/assets/build/unit/' . $css . '.css', array(), time(), 'all');
    }
    /*
    //主题CSS文件
    wp_register_style('aya-main-style', AYA_URI . '/assets/build/main.style.css', array(), aya_theme_version(), 'all');
    */
    //主题JS文件
    wp_register_script('aya-main-action', AYA_URI . '/assets/build/main.firing.js', array(), aya_theme_version(), false); //false就在页头加载
    wp_register_script('aya-main-bulid', AYA_URI . '/assets/build/main.bulid.js', array(), aya_theme_version(), true);
    wp_register_script('aya-main-child', AYA_URI . '/assets/build/main.child.js', array(), aya_theme_version(), true);
    //加载主题静态文件
    wp_enqueue_style('aya-main-style');
    wp_enqueue_script('aya-main-action');
    wp_enqueue_script('aya-main-bulid');
}
//配置可选CSS样式
function aya_theme_head_css_custom()
{
    $theme_css = '';

    //加载动画
    $theme_css .= '--aya-loading-animation: url(' . aya_get_loading_img() . ');';
    //背景图
    if (aya_opt('site_background_type', 'color', true)) {
        $theme_css .= '--aya-bg-image: url(' . aya_opt('site_background_upload', 'color') . ');';
    }
    //自定义样式表
    if (aya_opt('site_color_custom_type', 'color', true)) {
        $theme_css .= aya_opt('site_color_custom', 'color');
    } else {
        $theme_css .= aya_theme_head_css_custom_rule();
    }

    //注册一个过滤器
    $theme_css = apply_filters('aya_theme_style_filter', $theme_css);

    //组装自定义样式表
    e_html('<style type="text/css">:root {' . $theme_css . '}</style>');
}
//自定义样式表规则
function aya_theme_head_css_custom_rule($re_echo = false)
{
    $color_el = aya_opt('site_color_element', 'color');

    //添加样式
    $css = '';
    //主题样式
    $css .= '--aya-bg-color: ' . aya_opt('site_background_color', 'color') . ';';
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
    //正文样式
    $css .= '--aya-main-color: ' . aya_opt('site_main_color', 'color') . ';';
    $css .= '--aya-main-link-color: ' . $color_el . ';';
    $css .= '--aya-main-margin-width: ' . aya_opt('site_main_width', 'color') . ';';
    $css .= '--aya-main-font-size: ' . aya_opt('site_main_size', 'color') . ';';
    $css .= '--aya-main-block-bg: #f5f5f5;';

    //需要回显时添加换行
    if ($re_echo) $css = str_replace(';', ';' . PHP_EOL, $css);

    return $css;
}
//添加样式名到<body>标签
function aya_theme_body_class_name($class_array)
{
    //获取主题设置
    $add_array = array(
        'clearfix',
        (aya_get_loading_img() === false) ? 'default' : '',
        (aya_opt('site_dark_mode_type', 'color')) ? 'dark' : '',
        (aya_opt('site_gray_mode_type', 'color')) ? 'gray-mode' : '',
        (aya_opt('site_background_center', 'color')) ? 'bg-center' : '',
        (aya_opt('site_background_after', 'color')) ? 'bg-shade' : '',
    );

    return array_merge($class_array, $add_array);
}
//获取主题加载动画
function aya_get_loading_img()
{
    //获取主题设置
    $svg_selector = aya_opt('site_default_selector', 'theme');

    //如果未设置直接返回一个false给下一步函数
    if ($svg_selector) return false;

    //判断是否存在
    if ($svg_selector == 'custom') {
        //获取主题设置
        return aya_opt('site_custom_selector', 'theme');
    } else {
        return AYA_URI . '/assets/image/load/' . $svg_selector . '.svg';
    }
}
//获取主题空列表图片
function aya_get_loaded_empty_img($class = '')
{
    $page_img = aya_opt('site_none_page_img', 'theme');

    return aya_lazy_img_tags($page_img, $class, 'NONE CONTENT', false);
}
//获取主题404页面占位图片
function aya_get_404_page_img($class = '')
{
    $page_img = aya_opt('site_404_page_img', 'theme');

    return aya_lazy_img_tags($page_img, $class, '404 NOT FOUND', false);
}
//获取主题默认图片
function aya_get_default_thumbnail()
{
    return aya_opt('site_thumb_default', 'theme');
}
