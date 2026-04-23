<?php

if (!defined('ABSPATH')) {
    exit;
}

$archive_url = get_post_type_archive_link('tweet') ?: home_url('/tweet/');
$selected_tags = wp_unslash($_GET['t_tag'] ?? '');

if (is_array($selected_tags)) {
    $selected_tags = array_map('sanitize_title', $selected_tags);
} else {
    $selected_tags = preg_split('/[，,\s]+/u', (string) $selected_tags);
    $selected_tags = array_map('sanitize_title', $selected_tags);
}

$selected_tags = array_values(array_filter(array_unique($selected_tags)));
$tag_items = aya_tweet_post_get_tags_list();

aya_react_island('tweet-editor', [
    'mode' => 'create',
    'redirectUrl' => $archive_url,
    'className' => 'my-4',
]);

aya_react_island('tweet-tag', [
    'tags' => is_array($tag_items) ? $tag_items : [],
    'selected' => $selected_tags,
    'title' => __('推文标签', 'aiya-cms'),
    'archiveUrl' => $archive_url,
]);

if (!have_posts()) {
    aya_react_island('loop-tweet', ['posts' => []]);
    return;
}

while (have_posts()) {
    the_post();
    $post_obj = new AYA_Post_In_While();

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
            'tags' => $post_obj->tag_list,
            'author' => [
                'name' => (string) $post_obj->author_name,
                'avatar' => (string) $post_obj->author_avatar_x64,
            ],
            'gallery_images' => aya_tweet_sanitize_gallery_images(get_post_meta($post_obj->id, 'gallery_images', true)),
        ],
        'archiveUrl' => $archive_url,
        'className' => 'my-4',
    ]);
}

aya_react_island('content-pagination', aya_get_pagination());
