<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 配置一些WordPress过滤器和动作
 * ------------------------------------------------------------------------------
 */

//添加钩子 显示一言
add_action('admin_notices', 'aya_theme_admin_hello_hitokoto');
//添加钩子 移除密码保护和私密文章标题前缀文本
add_filter('protected_title_format', 'aya_theme_remove_protected_title_format');
add_filter('private_title_format', 'aya_theme_remove_protected_title_format');
//添加钩子 修改阅读更多文本
add_filter('excerpt_more', 'aya_theme_excerpt_more_filter');
//编辑器中 给文章添加一个置顶选项
add_action('add_meta_boxes', 'aya_theme_add_tweet_meta_box');

//静态文件加载（编辑器）
//add_action('admin_print_scripts', 'aya_theme_add_tinymce_buttons');
function aya_theme_add_tinymce_buttons()
{
    //wp_enqueue_script('aya-tinymce-quick-tags', AYA_URI . '/assets/js/tinymce.quicktags.js', array('quicktags'), aya_theme_version(), true);
    /*
    add_filter('mce_buttons', function ($buttons) {
        array_push($buttons, 'separator', 'add_button');
        return $buttons;
    });
    add_filter('mce_css', function ($mce_css) {
        $url = '';
        if (empty($mce_css)) {
            return $url;
        } else {
            return $mce_css . ',' . $url;
        }
    });
    add_filter('mce_external_plugins', function ($plugin_array) {
        $plugin_array['add_button'] = get_template_directory_uri() . '/js/mybutton.js';
        return $plugin_array;
    });
    */
}

//一言
function aya_theme_admin_hello_hitokoto()
{
    echo '<p id="hello-hitokoto"><span dir="ltr">' . aya_curl_get_hitokoto() . '</span></p>';
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
