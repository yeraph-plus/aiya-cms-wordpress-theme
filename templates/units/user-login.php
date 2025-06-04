<?php

if (!defined('ABSPATH')) {
    exit;
}

if (is_user_logged_in()) {
    aya_vue_load('user-menu', aya_user_get_login_data(true));
} else {
    aya_vue_load('login-action', aya_user_get_login_data(false));
}
