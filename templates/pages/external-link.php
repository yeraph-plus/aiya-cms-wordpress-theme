<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 外链提示模板
 * ------------------------------------------------------------------------------
 */

$blog_name = get_bloginfo('name');
//验证来源
$checked = aya_home_url_referer_check();
//获取URL参数
$external_url = isset($_GET['target']) ? esc_url($_GET['target']) : '';

//不是网址时直接抛回404
if (!cur_is_url($external_url)) {
    return wp_redirect(home_url('/404'));
}

aya_react_island('ui-external-link', ['url' => $external_url, 'checked' => $checked]);
