<?php

/**
 * 列表布局
 */
//<span class="likes px-1"><i class="bi bi-heart"></i>e_html($post_data['likes']); </span>
$post_type = aya_post_type();
if ($post_type == 'tweet') :
    $post_data = aya_the_loop_meta_data(0, 'short', 255);
?>
    <div class="loop-list col">
        <div class="card">
            <div class="row">
                <div class="col">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?php e_html($post_data['url']); ?>" title="<?php e_html($post_data['attr_title']); ?>">
                                <?php aya_loop_title_badge() . e_html($post_data['title']); ?>
                            </a>
                        </h5>
                        <p class="card-tweet">
                            <?php e_html($post_data['preview']); ?>
                        </p>
                        <p class="loop-meta mb-0">
                            <a href="<?php e_html($post_data['url']); ?>" class="stretched-link" title="<?php e_html($post_data['attr_title']); ?>">
                                <i class="bi bi-arrow-return-right"></i><?php e_html(__('查看', 'AIYA')); ?>
                            </a>
                            <span class="date px-1"><i class="bi bi-clock"></i> <?php e_html($post_data['date']); ?></span>
                            <span class="comments px-1"><i class="bi bi-chat-dots"> </i><?php e_html($post_data['comments']); ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php

else :
    $post_data = aya_the_loop_meta_data(0, 'short', 147);
    $post_thumb = aya_the_loop_image($post_data['id'], 400, 300);

?>
    <div class="loop-list col">
        <div class="card">
            <div class="row">
                <div class="col-4">
                    <div class="card-img-border">
                        <?php aya_lazy_img_tags($post_thumb, 'card-img-border-left', $post_data['attr_title'], 400, 300, true); ?>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card-body with-img">
                        <h5 class="card-title">
                            <a href="<?php e_html($post_data['url']); ?>" title="<?php e_html($post_data['attr_title']); ?>">
                                <?php aya_loop_title_badge() . e_html($post_data['title']); ?>
                            </a>
                        </h5>
                        <p class="loop-tags">
                            <?php aya_loop_tags_list($post_data['id']); ?>
                        </p>
                        <p class="card-text">
                            <?php e_html($post_data['preview']); ?>
                        </p>
                        <p class="loop-meta mb-0">
                            <span class="date pr-1"><i class="bi bi-clock"></i> <?php e_html($post_data['date']); ?></span>
                            <span class="views px-1"><i class="bi bi-eye"></i> <?php e_html($post_data['views']); ?></span>
                            <span class="comments px-1"><i class="bi bi-chat-dots"> </i><?php e_html($post_data['comments']); ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php

endif;
