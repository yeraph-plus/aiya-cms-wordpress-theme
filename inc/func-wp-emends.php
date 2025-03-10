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
//add_action('add_meta_boxes', 'aya_theme_add_tweet_meta_box');

//强制排除评论表单站点字段
add_filter('comment_form_default_fields', function ($fields) {
    if (isset($fields['url'])) {
        unset($fields['url']);
    }
    return $fields;
});

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

/*
 * ------------------------------------------------------------------------------
 * 自动拼音化别名
 * ------------------------------------------------------------------------------
 */

add_filter('wp_insert_term_data', 'aya_insert_term_data_slug', 10, 3);
add_filter('wp_update_term_data', 'aya_update_term_data_slug', 10, 4);
add_filter('wp_insert_post_data', 'aya_insert_post_data_slug', 10, 2);

//添加分类时替换分类slug为拼音
function aya_insert_term_data_slug($data, $taxonomy, $term_arr)
{
    if (aya_opt('site_term_auto_pinyin_slug_bool', 'postpage')) {
        //已存在，跳过
        if (!empty($term_arr['slug'])) return $data;

        $data['slug'] = wp_unique_term_slug(sanitize_title(aya_pinyin_permalink($data['name'], true)), (object) $term_arr);
    }

    return $data;
}

//更新分类时替换分类slug为拼音
function aya_update_term_data_slug($data, $term_id, $taxonomy, $term_arr)
{
    if (aya_opt('site_term_auto_pinyin_slug_bool', 'postpage')) {
        //已存在，跳过
        if (!empty($term_arr['slug'])) return $data;

        $data['slug'] = wp_unique_term_slug(sanitize_title(aya_pinyin_permalink($data['name'], true)), (object) $term_arr);
    }

    return $data;
}

//保存文章时替换文章slug为拼音
function aya_insert_post_data_slug($data, $post_arr)
{
    //跳过自动草稿
    if ('auto-draft' === $post_arr['post_status']) return $data;

    if (aya_opt('site_post_auto_pinyin_slug_bool', 'postpage')) {
        //已存在，跳过
        if (!empty($post_arr['post_name'])) return $data;
        //检查标题是否为空
        if (empty($post_arr['post_title'])) return $data;


        $formatted_sulg = sanitize_title(aya_pinyin_permalink($post_arr['post_title'], true));

        $data['post_name'] = wp_unique_term_slug($formatted_sulg, (object) $post_arr);
    }

    return $data;
}
