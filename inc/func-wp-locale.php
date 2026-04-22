<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 主题语言包加载方法
 * ------------------------------------------------------------------------------
 */

add_filter('determine_locale', 'ayar_locale_auto_convert_filter', 1);
//add_action('after_setup_theme', 'aya_theme_textdomain_load');

// 在当前文件中加载主题语言包。
function aya_theme_textdomain_load()
{
    load_theme_textdomain(AYA_THEME_TEXTDOMAIN, AYA_PATH . '/languages');
}

// 获取浏览器语言
function aya_get_browser_locale()
{
    $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower((string) $_SERVER['HTTP_ACCEPT_LANGUAGE']) : '';

    if ($accept_language === '') {
        return false;
    }

    if (strpos($accept_language, 'zh-hk') !== false || strpos($accept_language, 'zh-mo') !== false || strpos($accept_language, 'zh-hant-hk') !== false) {
        return 'zh_HK';
    }

    if (strpos($accept_language, 'zh-tw') !== false || strpos($accept_language, 'zh-hant') !== false) {
        return 'zh_TW';
    }

    if (strpos($accept_language, 'zh-cn') !== false || strpos($accept_language, 'zh-sg') !== false || strpos($accept_language, 'zh-hans') !== false) {
        return 'zh_CN';
    }

    if (strpos($accept_language, 'en') !== false) {
        return 'en_US';
    }

    return false;
}

// 约束语言标记字段
function aya_normalize_locale($locale)
{
    $locale = str_replace('-', '_', (string) $locale);

    if (stripos($locale, 'zh_HK') === 0 || stripos($locale, 'zh_MO') === 0) {
        return 'zh_HK';
    }

    if (stripos($locale, 'zh_TW') === 0) {
        return 'zh_TW';
    }

    if (stripos($locale, 'en') === 0) {
        return 'en_US';
    }

    return 'zh_CN';
}

// 为用户判断主题的语言
function aya_get_user_locale()
{
    if (is_user_logged_in()) {
        return aya_normalize_locale(get_user_locale(get_current_user_id()));
    }

    $browser_locale = aya_get_browser_locale();

    if ($browser_locale !== false) {
        return aya_normalize_locale($browser_locale);
    }

    return aya_normalize_locale(get_locale());
}

// 过滤前台请求用于切换语言
function ayar_locale_auto_convert_filter($locale)
{
    if (is_admin()) {
        return $locale;
    }

    return aya_get_user_locale();
}
