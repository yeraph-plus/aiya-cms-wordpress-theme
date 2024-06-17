<?php

/*
 * ------------------------------------------------------------------------------
 * 配置一些WordPress过滤器和动作
 * ------------------------------------------------------------------------------
 */

//显示一言
add_action('admin_notices', 'aya_admin_hello_hitokoto');
//URL自动附加反斜杠
add_filter('user_trailingslashit', 'aya_auto_trailingslashit', 10, 2);
//排除评论表单站点字段
add_filter('comment_form_default_fields', 'aya_comment_form_unset_url_field');
//过滤评论和body输出html的 "comment-author-" 和 "author-"css
add_filter('body_class', 'aya_theme_filter_body_class');
add_filter('comment_class', 'aya_theme_filter_body_class');
//过滤菜单和页面输出html的无用css
add_filter('nav_menu_css_class', 'aya_theme_filter_menu_class');
add_filter('nav_menu_item_id', 'aya_theme_filter_menu_class');
add_filter('page_css_class', 'aya_theme_filter_menu_class');
//移除密码保护和私密文章标题前缀文本
add_filter('protected_title_format', 'aya_theme_remove_protected_title_format');
add_filter('private_title_format', 'aya_theme_remove_protected_title_format');
//修改阅读更多文本
add_filter('excerpt_more', 'aya_theme_excerpt_more_filter');
//WordPress编辑器中给推文类型添加一个置顶选项
add_action('add_meta_boxes', 'aya_theme_add_tweet_meta_box');

function aya_auto_trailingslashit($string, $type)
{
    //排除文章和页面
    if (get_query_var('page_type')) return $string;

    if ($type == 'single' || $type == 'page') return $string;

    //使用WP内置过滤器
    return trailingslashit($string);
}

function aya_comment_form_unset_url_field($fields)
{
    if (isset($fields['url'])) unset($fields['url']);

    return $fields;
}

function aya_theme_filter_body_class($content)
{
    $content = preg_replace("/(.*?)([^>]*)author-([^>]*)(.*?)/i", '$1$4', $content);
    return $content;
}

function aya_theme_filter_menu_class($classes)
{
    $content = array(
        'current-menu-item',
        'current-post-ancestor',
        'current-menu-ancestor',
        'current-menu-parent',
        //'menu-item-has-children',
        //'menu-item'
    );
    return is_array($classes) ? array_intersect($classes, $content) : '';
}

function aya_theme_remove_protected_title_format($format)
{
    return '';
}

function aya_theme_excerpt_more_filter()
{
    return '...';
}

//MetaBox注册
function aya_theme_add_tweet_meta_box()
{
    add_meta_box('aya_tweet_product_sticky', __('置顶', 'AIYA'), 'aya_tweet_product_sticky', 'tweet', 'side', 'high');
}

function aya_tweet_product_sticky()
{
    printf(
        '<p>
            <label for="super-sticky" class="selectit">
                <input id="super-sticky" name="sticky" type="checkbox" value="sticky" %s />
                %s
            </label>
        </p>',
        checked(is_sticky(), true, false),
        esc_html__('置顶这篇文章', 'AIYA')
    );
}

function aya_admin_hello_hitokoto()
{
    echo '<p id="hello-hitokoto"><span dir="ltr">' . aya_get_hitokoto() . '</span></p>';
}
