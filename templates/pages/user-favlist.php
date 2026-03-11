<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    return wp_redirect(home_url());
}

//创建查询
$query_obj = new AYA_WP_Query_Object();

$favorites = aya_user_get_favorite_posts();

$fav_posts = $query_obj->list_posts($favorites, ['post', 'page', 'tweet']);

//循环查询结果
$posts_data = [];

foreach ($fav_posts as $fav_post) {
    $post_obj = new AYA_Post_In_While($fav_post);

    $post_thumb = aya_get_post_thumb($post_obj->thumbnail_url, $post_obj->content, 300, 200);

    $posts_data[] = [
        'id' => $post_obj->id,
        'url' => (string) $post_obj->url,
        'date' => (string) $post_obj->date,
        'date_iso' => (string) $post_obj->date_iso,
        'modified' => (string) $post_obj->modified,
        'thumbnail' => (string) $post_thumb,
        'title' => (string) $post_obj->title,
        'attr_title' => (string) $post_obj->attr_title,
        'author_name' => (string) $post_obj->author_name,
    ];
}
aya_react_island('user-favorites', [
    'initialPosts' => $posts_data
]);
