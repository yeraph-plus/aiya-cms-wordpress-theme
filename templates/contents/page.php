<?php

if (!defined('ABSPATH')) {
    exit;
}

//获取文章数据
$post_obj = new AYA_Post_In_While();

?>
<article>
    <?php
    echo aya_render_hydrate_template(
        'hy-content-detail',
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
            'disallowToggle' => true,
        ]
    );
    ?>

    <!-- Content Start -->
    <div id="article-content" class="prose prose-base lg:prose-lg max-w-none">
        <?php aya_echo($post_obj->content); ?>
    </div>
    <!-- Content End -->

    <?php
    aya_render_hydrated_island(
        'hy-content-end',
        ['endDivider' => true]
    );
    ?>
</article>
