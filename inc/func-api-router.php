<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * REST-API方法组件
 * ------------------------------------------------------------------------------
 */

// 修改REST API路由前缀
add_filter('rest_url_prefix', function () {
    return 'api'; // 自定义路径（如：/api）
});

// 可选：兼容旧路径的重定向（从/wp-json重定向到/api）
/*
add_action('template_redirect', function () {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-json') !== false) {
        wp_redirect(site_url(str_replace('wp-json', 'api', $_SERVER['REQUEST_URI'])), 301);
        exit;
    }
});
*/