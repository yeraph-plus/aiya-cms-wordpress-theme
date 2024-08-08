<?php

/**
 * 默认正文
 */

$post_data = aya_the_content_meta_data(0);

//<span class="likes px-2"><i class="bi bi-heart"></i> <?php //e_html($post_data['likes']); </span>
?>
<article id="entry-main">
    <div class="card">
        <div class="post-top-box border-bottom">
            <?php
            $title_style = '';
            //检查特色图
            if ($post_data['thumbnail'] != NULL) :
                $title_style = 'thumbnail-inner';
                e_html('<div class="thumbnail card-img-border">');
                aya_lazy_img_tags($post_data['thumbnail'], 'card-img-border-top', $post_data['title'], '100%', 'auto', true);
                e_html('</div>');
            endif;
            ?>
            <div class="card-body <?php e_html($title_style); ?>">
                <h1 class="post-title">
                    <?php aya_loop_title_badge() . e_html($post_data['title'] . (($post_data['status'] == '') ? '' : ' - ') . $post_data['status']); ?>
                </h1>
                <div class="post-meta">
                    <span class="author px-2"><i class="bi bi-person"></i> <?php e_html($post_data['author']); ?></span>
                    <span class="date pr-2"><i class="bi bi-clock"></i> <?php e_html($post_data['date']); ?></span>
                    <span class="views px-2"><i class="bi bi-eye"></i> <?php e_html($post_data['views']); ?></span>
                    <span class="comments px-2"><i class="bi bi-chat-dots"></i> <?php e_html($post_data['comments']); ?></span>
                </div>
            </div>
        </div>
        <div class="entry-content">
            <?php
            //检查摘要
            if ($post_data['excerpt'] != '') :
                e_html('<p class="card-excerpt">' . $post_data['excerpt'] . '</p>');
            endif;
            ?>
            <?php aya_single_outdated_tip(); ?>
            <?php e_html($post_data['content']); ?>
        </div>
        <div class="post-end-box">
            <div class="card-body">
                <div class="post-separator text-center">THE END</div>
                <?php aya_single_related_more(); ?>
                <?php aya_single_specs_like(); ?>
                <?php aya_single_dis_claimer_info(); ?>
            </div>
        </div>
        <div class="post-tags-list border-top">
            <div class="card-body">
                <?php aya_loop_tags_list(); ?>
            </div>
        </div>
    </div>
</article>
<?php aya_single_prev_next_post(); ?>
<?php aya_single_author_panel(); ?>