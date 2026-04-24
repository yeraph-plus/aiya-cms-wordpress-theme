<?php

if (!defined('ABSPATH')) {
    exit;
}

// 分页
$paged = aya_get_pagination();
// 推文归档URL
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

?>
<div class="layout flex flex-col lg:flex-row gap-4">
    <div class="layout hidden lg:block lg:w-1/5">
        <?php
        aya_react_island('tweet-tag', [
            'title' => __('推文标签', 'aiya-cms'),
            'tags' => is_array($tag_items) ? $tag_items : [],
            'selected' => $selected_tags,
            'archiveUrl' => $archive_url,
        ]);
        ?>
    </div>
    <div class="layout w-full lg:w-4/5">
        <?php

        aya_react_island('tweet-editor', [
            'mode' => 'create',
            'redirectUrl' => $archive_url,
            'tags' => is_array($tag_items) ? $tag_items : [],
        ]);

        if (!have_posts()) {
            return;
        } else {
            while (have_posts()) {
                the_post();
                $post_obj = new AYA_Post_In_While();

                aya_react_island('tweet-card', [
                    'post' => [
                        'id' => $post_obj->id,
                        'url' => (string) $post_obj->url,
                        'title' => (string) $post_obj->title,
                        'attr_title' => (string) $post_obj->attr_title,
                        'content' => (string) aya_tweet_post_excerpt($post_obj->content, 15, $post_obj->url),
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
                        'gallery_images' => aya_tweet_sanitize_gallery_images(get_post_meta($post_obj->id, 'gallery_images', true)),
                        'is_author' => $post_obj->is_post_author,
                    ],
                    'archiveUrl' => $archive_url,
                ]);
            }
        }

        aya_react_island('content-pagination', $paged);
        ?>
    </div>
</div>