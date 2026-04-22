<?php

if (!defined('ABSPATH')) {
    exit;
}

//获取文章数据
$post_obj = new AYA_Post_In_While();

?>
<article>
    <?php
    aya_template_part_load('content-detail', [
        'post_id' => $post_obj->id,
        'title' => $post_obj->title,
        'author' => [
            'name' => $post_obj->author_name,
            'url' => $post_obj->author_url,
            'avatar' => $post_obj->author_avatar_x32,
        ],
        'date' => $post_obj->date,
        'date_iso' => $post_obj->date_iso,
        'status' => $post_obj->status,
        'modified_ago' => $post_obj->modified_ago,
        'views' => $post_obj->views,
        'comments' => $post_obj->comments_text,
        'likes' => $post_obj->likes,
        'thumbnail_url' => $post_obj->thumbnail_url,
        'categories' => $post_obj->cat_list,
        'tags' => $post_obj->tag_list,
        'alert_tips' => aya_get_post_tips($post_obj->id),
    ]);
    ?>
    <!-- Content Start -->
    <div id="article-content" class=" prose prose-base lg:prose-lg max-w-none">
        <?php echo $post_obj->content; ?>
    </div>
    <!-- Content End -->
    <?php
    //加载文件列表组件
    if (aya_is_oplist_cli_ready($post_obj->id)) {
        aya_react_island('open-list-client', ['postId' => $post_obj->id]);
    }

    //获取上下篇文章
    $prev_post = $post_obj->prev_post();
    $next_post = $post_obj->next_post();

    aya_template_part_load('content-footer', [
        'use_interaction' => true,
        'post_id' => $post_obj->id,
        'likes' => $post_obj->likes,
        'is_favorite' => aya_user_get_favorite_posts_check($post_obj->id),
        'prev_post' => !empty($prev_post) ? [
            'title' => $prev_post->title,
            'url' => $prev_post->url,
        ] : null,
        'next_post' => !empty($next_post) ? [
            'title' => $next_post->title,
            'url' => $next_post->url,
        ] : null,
    ]);
    ?>
</article>