<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 自定义推文文章类型
 * ------------------------------------------------------------------------------
 */

//注册文章类型
add_action('after_setup_theme', 'aya_post_type_tweet_action');
//MetaBox注册
add_action('add_meta_boxes', 'aya_post_type_tweet_add_meta_box');
//前台发帖处理
add_action('admin_post_aya_submit_tweet', 'aya_tweet_handle_front_submit');
add_action('admin_post_nopriv_aya_submit_tweet', 'aya_tweet_handle_front_submit_guest');
//归档筛选
add_action('pre_get_posts', 'aya_tweet_archive_filter_by_tag');

function aya_post_type_tweet_action()
{
    //注册文章类型
    AYF::module(
        //'文章类型' => array('name' => '文章类型名','slug' => '别名','icon' => '图标','in_homepage' => 允许显示在首页),
        'Register_Post_Type',
        [
            'tweet' => [
                'name' => __('推文', 'aiya-cms'),
                'slug' => 'tweet',
                'icon' => 'dashicons-format-quote',
                'in_homepage' => false,
            ],
        ]
    );
    //注册推文标签分类法（非层级，类似标签）
    AYF::module(
        'Register_Tax_Type',
        [
            'tweet_tag' => [
                'name' => __('推文标签', 'aiya-cms'),
                'slug' => 'tweet_tag',
                'post_type' => ['tweet'],
                'tag_mode' => true,
            ],
        ]
    );
}

//使自定义文章类型可以操作置顶
function aya_post_type_tweet_add_meta_box()
{
    add_meta_box('aya_tweet_product_sticky', __('置顶', 'aiya-cms'), 'aya_tweet_product_sticky', 'tweet', 'side', 'high');
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
        esc_html__('置顶这篇文章', 'aiya-cms')
    );
}

//前台发帖未登录处理
function aya_tweet_handle_front_submit_guest()
{
    wp_safe_redirect(wp_login_url(get_post_type_archive_link('tweet')));
    exit;
}

//前台发帖处理
function aya_tweet_handle_front_submit()
{
    if (!is_user_logged_in()) {
        aya_tweet_handle_front_submit_guest();
    }

    check_admin_referer('aya_submit_tweet_action', 'aya_submit_tweet_nonce');

    $title = sanitize_text_field(wp_unslash($_POST['tweet_title'] ?? ''));
    $content = wp_kses_post(wp_unslash($_POST['tweet_content'] ?? ''));
    $tags_text = sanitize_text_field(wp_unslash($_POST['tweet_tags'] ?? ''));

    if ($content === '') {
        wp_safe_redirect(add_query_arg('tweet_status', 'empty', get_post_type_archive_link('tweet')));
        exit;
    }

    if ($title === '') {
        $title = mb_substr(wp_strip_all_tags($content), 0, 24);
    }

    $post_status = current_user_can('publish_posts') ? 'publish' : 'pending';

    $post_id = wp_insert_post([
        'post_type' => 'tweet',
        'post_status' => $post_status,
        'post_author' => get_current_user_id(),
        'post_title' => $title,
        'post_content' => $content,
    ], true);

    if (is_wp_error($post_id) || !$post_id) {
        wp_safe_redirect(add_query_arg('tweet_status', 'error', get_post_type_archive_link('tweet')));
        exit;
    }

    if ($tags_text !== '') {
        $tags = preg_split('/[，,\s]+/u', $tags_text);
        $tags = array_values(array_filter(array_unique(array_map('sanitize_text_field', (array) $tags))));
        if (!empty($tags)) {
            wp_set_object_terms($post_id, $tags, 'tweet_tag', false);
        }
    }

    $status = ($post_status === 'publish') ? 'published' : 'pending';
    wp_safe_redirect(add_query_arg('tweet_status', $status, get_post_type_archive_link('tweet')));
    exit;
}

//推文归档按标签筛选
function aya_tweet_archive_filter_by_tag($query)
{
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('tweet')) {
        return;
    }

    $tag_slug = sanitize_title(wp_unslash($_GET['tweet_tag'] ?? ''));
    if ($tag_slug === '') {
        return;
    }

    $query->set('tax_query', [
        [
            'taxonomy' => 'tweet_tag',
            'field' => 'slug',
            'terms' => $tag_slug,
        ]
    ]);
}