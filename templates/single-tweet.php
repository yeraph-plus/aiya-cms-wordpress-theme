<?php

if (!defined('ABSPATH')) {
    exit;
}

// 推文归档URL
$archive_url = get_post_type_archive_link('tweet') ?: home_url('/tweet/');
// 是否编辑模式
$is_edit_mode = isset($_GET['update']) && $_GET['update'] === 'true';

if ($is_edit_mode) {
    //TODO 编辑模式下，面包屑注入晚于导航生成，需要修改调用
    aya_add_breadcrumb_item(__('编辑推文', 'aiya-cms'), '#');
}

//没有文章
if (!have_posts()) {
    //重定向到404
    aya_template_none();
}

//执行主循环
while (have_posts()) {
    the_post();
    $post_obj = new AYA_Post_In_While();
    $is_author =  $post_obj->is_post_author;
    $tag_items = aya_tweet_post_get_tags_list();

    if ($is_edit_mode && $is_author) {
        aya_react_island('tweet-editor', [
            'mode' => 'edit',
            'post' => [
                'id' => $post_obj->id,
                'url' => (string) $post_obj->url,
                'title' => (string) $post_obj->title,
                'content' => (string) $post_obj->content,
            ],
            'redirectUrl' => $archive_url,
            'tags' => is_array($tag_items) ? $tag_items : [],
        ]);
    } else {
        aya_react_island('tweet-card', [
            'post' => [
                'id' => $post_obj->id,
                'url' => (string) $post_obj->url,
                'title' => (string) $post_obj->title,
                'attr_title' => (string) $post_obj->attr_title,
                'content' => (string) $post_obj->content,
                'date' => (string) $post_obj->date,
                'date_iso' => (string) $post_obj->date_iso,
                'comments' => (string) $post_obj->comments,
                'likes' => (string) $post_obj->likes,
                'status' => (array) $post_obj->status,
                'tags' => is_array($tag_items) ? $tag_items : [],
                'author' => [
                    'name' => (string) $post_obj->author_name,
                    'avatar' => (string) $post_obj->author_avatar_x64,
                ],
                'is_author' => $is_author,
                'gallery_images' => aya_tweet_sanitize_gallery_images(get_post_meta($post_obj->id, 'gallery_images', true)),
            ],
            'archiveUrl' => $archive_url,
        ]);
    }
}

//评论模板
aya_comments_template();
