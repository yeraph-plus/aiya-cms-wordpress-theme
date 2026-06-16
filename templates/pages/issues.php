<?php

if (!defined('ABSPATH')) {
    exit;
}

$login_data = function_exists('aya_user_get_login_data') ? aya_user_get_login_data() : [];

// 如果问题设置为非公开，检查用户是否登录且拥有足够权限
if (!aya_opt('site_issue_public_bool', 'land')) {

    if (!is_user_logged_in() || !current_user_can('edit_posts')) {
        wp_redirect(home_url('/'));
        exit;
    }
}

aya_react_island('issues', [
    'currentUser' => $login_data['data'] ?? null,
    'allowedTypes' => array_values(aya_issue_get_allowed_types()),
    'allowedStatuses' => array_values(aya_issue_get_allowed_statuses()),
    'pageUrl' => home_url('/issues/'),
]);
