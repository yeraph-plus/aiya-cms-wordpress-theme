<?php

if (!defined('ABSPATH')) {
    exit;
}

//获取文章数据
$post_obj = new AYA_Post_In_While();

?>
<article>
    <?php
    aya_react_island(
        'content-detail',
        [
            'postId' => $post_obj->id,
            'title' => $post_obj->title,
            'author' => [
                'name' => $post_obj->author_name,
                'url' => $post_obj->author_url,
                'avatar' => $post_obj->author_avatar_x32,
            ],
            'date' => $post_obj->date,
            'dateIso' => $post_obj->date_iso,
            'status' => $post_obj->status,
            'modifiedAgo' => $post_obj->modified_ago,
            'views' => $post_obj->views,
            'comments' => $post_obj->comments_text,
            'likes' => $post_obj->likes,
            'isFavorite' => (function () use ($post_obj) {
                $user_id = get_current_user_id();
                if (!$user_id) return false;
                $favorites = get_user_meta($user_id, 'aya_user_favorite_posts', true);
                return is_array($favorites) && in_array($post_obj->id, $favorites);
            }),
        ],
        '<h1 class="text-3xl font-bold text-neutral-900">' . $post_obj->title . '</h1>'
    );
    ?>

    <!-- Content Start -->
    <div id="article-content" class=" prose prose-base lg:prose-lg max-w-none">
        <?php aya_echo($post_obj->content); ?>
    </div>
    <!-- Content End -->

    <?php
    //获取声明文本
    $statement_content = aya_opt('site_post_statement_text', 'basic');
    //获取上下篇文章
    $prev_post = $post_obj->prev_post();
    $next_post = $post_obj->next_post();

    aya_react_island(
        'content-end',
        ['endDivider' => true]
    );
    ?>
</article>