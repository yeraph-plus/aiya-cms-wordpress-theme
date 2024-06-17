<?php

/**
 * 推文正文
 */

$post_data = aya_get_post_meta_data(0);

?>
<article id="entry-main">
    <div class="card">
        <div class="post-top-box border-bottom">
            <div class="card-body">
                <h1 class="post-title">
                    <?php e_html($post_data['title'] . (($post_data['status'] == '') ? '' : ' - ') . $post_data['status']); ?>
                </h1>
                <div class="post-meta">
                    <span class="author px-2"><i class="bi bi-person"></i> <?php e_html($post_data['author']); ?></span>
                    <span class="date pr-2"><i class="bi bi-clock"></i> <?php e_html($post_data['date']); ?></span>
                    <span class="views px-2"><i class="bi bi-eye"></i> <?php e_html($post_data['views']); ?></span>
                    <span class="likes px-2"><i class="bi bi-heart"></i> <?php e_html($post_data['likes']); ?></span>
                    <span class="comments px-2"><i class="bi bi-chat-dots"></i> <?php e_html($post_data['comments']); ?></span>
                </div>
            </div>
        </div>
        <div class="entry-content">
            <?php e_html($post_data['content']); ?>
        </div>
        <div class="post-end-box">
            <div class="card-body">
                <?php //aya_single_specs_like(); 
                ?>
                <div class="post-share"><!-- ComingSoon --></div>
            </div>
        </div>
    </div>
</article>
<?php //aya_single_author_panel(); 
?>