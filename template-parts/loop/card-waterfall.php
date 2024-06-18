<?php

/**
 * 瀑布流
 */
$post_type = aya_post_type();
$grid_class = aya_grid_waterfall_class();
$post_data = aya_the_loop_meta_data(0, 400, 'full', 0, 450);

if ($post_type == 'tweet') :

?>
    <div class="loop-waterfall <?php e_html($grid_class); ?> mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="<?php e_html($post_data['url']); ?>" class="stretched-link" title="<?php e_html($post_data['attr_title']); ?>">
                        <?php e_html($post_data['title']); ?>
                    </a>
                </h5>
                <p class="card-text"><?php e_html($post_data['preview']); ?></p>
                <small class="loop-meta">
                    <span class="author pr-1"><i class="bi bi-person"></i> <?php e_html($post_data['author']); ?></span>
                    <span class="date pr-1"><i class="bi bi-clock"></i> <?php e_html($post_data['date']); ?></span>
                    <span class="likes px-1"><i class="bi bi-heart"></i> <?php e_html($post_data['likes']); ?></span>
                </small>
            </div>
        </div>
    </div>
<?php

else :

?>
    <div class="loop-waterfall <?php e_html($grid_class); ?> mb-4">
        <div class="card">
            <?php
            //if ($post_data['thumb'] != NULL) :

            //endif;
            ?>
            <div class="card-body">
                <h5 class="card-title">
                    <a href="<?php e_html($post_data['url']); ?>" class="stretched-link" title="<?php e_html($post_data['attr_title']); ?>">
                        <?php e_html($post_data['title']); ?>
                    </a>
                </h5>
                <p class="card-text"><?php e_html($post_data['preview']); ?></p>
                <small class="loop-meta">
                    <span class="date pr-1"><i class="bi bi-clock"></i> <?php e_html($post_data['date']); ?></span>
                    <span class="views px-1"><i class="bi bi-eye"></i> <?php e_html($post_data['views']); ?></span>
                    <span class="likes px-1"><i class="bi bi-heart"></i> <?php e_html($post_data['likes']); ?></span>
                </small>
            </div>
        </div>
    </div>
<?php

endif;
