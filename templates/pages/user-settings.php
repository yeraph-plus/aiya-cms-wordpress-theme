<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    return wp_redirect(home_url());
}

$current_user = wp_get_current_user();

aya_react_island('user-settings', [
    'initialUser' => [
        'email' => $current_user->user_email,
        'first_name' => $current_user->first_name,
        'last_name' => $current_user->last_name,
        'nickname' => $current_user->nickname,
        'description' => $current_user->description,
        'locale' => get_user_locale($current_user),
        'user_url' => $current_user->user_url,
    ],
]);
