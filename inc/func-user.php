<?php

if (!defined('ABSPATH'))
    exit;

/*
 * ------------------------------------------------------------------------------
 * 用户中心方法
 * ------------------------------------------------------------------------------
 */

// 修改作者基为'user'
function change_author_base()
{
    global $wp_rewrite;
    $wp_rewrite->author_base = 'user';
    $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
}
add_action('init', 'change_author_base');

// 添加重写规则，将/user/数字映射到用户ID
function add_user_rewrite_rules()
{
    add_rewrite_rule('^user/(\d+)/?$', 'index.php?author=$matches[1]', 'top');
}
add_action('init', 'add_user_rewrite_rules');

// 刷新重写规则以确保生效（仅在需要时运行一次）
function flush_rewrites()
{
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'flush_rewrites');

// 生成/user/{ID}格式的作者链接
function custom_author_link($link, $user_id)
{
    return home_url('/user/' . $user_id . '/');
}
add_filter('author_link', 'custom_author_link', 10, 2);

// 移除REST API中的用户敏感信息
function prevent_user_login_exposure($response, $user, $request)
{
    $data = $response->get_data();
    unset($data['slug'], $data['name']); // 根据需要移除其他字段
    $response->set_data($data);
    return $response;
}
add_filter('rest_prepare_user', 'prevent_user_login_exposure', 10, 3);

// 重定向旧的作者链接到新路径
function redirect_old_author_urls()
{
    if (is_author() && get_query_var('author_name')) {
        $user = get_user_by('slug', get_query_var('author_name'));
        if ($user) {
            wp_redirect(get_author_posts_url($user->ID), 301);
            exit;
        }
    }
}
add_action('template_redirect', 'redirect_old_author_urls');