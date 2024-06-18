<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 配置一些WordPress过滤器和动作
 * ------------------------------------------------------------------------------
 */

//添加钩子 显示一言
add_action('admin_notices', 'aya_theme_admin_hello_hitokoto');
//添加钩子 排除评论表单站点字段
//add_filter('comment_form_default_fields', 'aya_theme_filter_comment_form_unset_url_field');
//添加钩子 过滤评论和body输出html的 "comment-author-" 和 "author-"css
add_filter('body_class', 'aya_theme_filter_body_class');
add_filter('comment_class', 'aya_theme_filter_body_class');
//添加钩子 过滤菜单和页面输出html的无用css
//add_filter('nav_menu_css_class', 'aya_theme_filter_menu_class');
//add_filter('nav_menu_item_id', 'aya_theme_filter_menu_class');
//add_filter('page_css_class', 'aya_theme_filter_menu_class');
//添加钩子 移除密码保护和私密文章标题前缀文本
add_filter('protected_title_format', 'aya_theme_remove_protected_title_format');
add_filter('private_title_format', 'aya_theme_remove_protected_title_format');
//添加钩子 修改阅读更多文本
add_filter('excerpt_more', 'aya_theme_excerpt_more_filter');
//编辑器中 给文章添加一个置顶选项
add_action('add_meta_boxes', 'aya_theme_add_tweet_meta_box');

//一言
function aya_theme_admin_hello_hitokoto()
{
    echo '<p id="hello-hitokoto"><span dir="ltr">' . aya_get_hitokoto() . '</span></p>';
}
//排除评论表单站点字段
function aya_theme_filter_comment_form_unset_url_field($fields)
{
    if (isset($fields['url'])) unset($fields['url']);

    return $fields;
}
//过滤"author"的css
function aya_theme_filter_body_class($content)
{
    $content = preg_replace("/(.*?)([^>]*)author-([^>]*)(.*?)/i", '$1$4', $content);
    return $content;
}
//过滤菜单的css
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
//取消前缀文本格式
function aya_theme_remove_protected_title_format($format)
{
    return '';
}
//修改阅读更多文本
function aya_theme_excerpt_more_filter()
{
    return '...';
}
//MetaBox注册
function aya_theme_add_tweet_meta_box()
{
    add_meta_box('aya_tweet_product_sticky', __('置顶', 'AIYA'), 'aya_tweet_product_sticky', 'tweet', 'side', 'high');
}
//MetaBox内容
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
