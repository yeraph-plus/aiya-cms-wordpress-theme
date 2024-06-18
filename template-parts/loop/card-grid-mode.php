<?php

/**
 * 卡片布局
 */

$post_type = aya_post_type();
$post_data = aya_the_loop_meta_data(0, 47);
$post_thumb = aya_the_loop_thumb($post_data['id'], 400, 300, true);
?>
<div class="loop-grid col">
    <div class="card">
        <div class="card-img-border">
            <?php aya_lazy_img_tags($post_thumb, 'card-img-border-top', $post_data['attr_title'], 400, 300, true); ?>
        </div>
        <div class="card-body">
            <h5 class="card-title">
                <a href="<?php e_html($post_data['url']); ?>" class="stretched-link" title="<?php e_html($post_data['attr_title']); ?>">
                    <?php e_html($post_data['title']); ?>
                </a>
            </h5>
            <p class="card-text">
                <?php e_html($post_data['preview']); ?>
            </p>
            <p class="loop-meta mb-0">
                <span class="views px-1"><i class="bi bi-eye"></i> <?php e_html($post_data['views']); ?></span>
                <span class="likes px-1"><i class="bi bi-heart"></i> <?php e_html($post_data['likes']); ?></span>
                <span class="comments px-1"><i class="bi bi-chat-dots"> </i><?php e_html($post_data['comments']); ?></span>
            </p>
        </div>
    </div>
</div>