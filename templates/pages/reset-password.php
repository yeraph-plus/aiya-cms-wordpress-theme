<?php

if (!defined('ABSPATH')) {
    exit;
}

$reset_token = isset($_GET['token']) ? sanitize_text_field(wp_unslash((string) $_GET['token'])) : '';

aya_react_island('user-reset-password', [
    'resetToken' => $reset_token,
]);
