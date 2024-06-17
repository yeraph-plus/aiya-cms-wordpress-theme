<?php

/**
 * 页面正文
 */

?>
<article id="entry-main">
    <div class="card">
        <div class="post-top-box border-bottom">
            <div class="card-body">
                <h1 class="post-title">
                    <?php e_html(aya_get_post_title(0, 1) . ((aya_get_post_status(0) == '') ? '' : ' - ' . aya_get_post_status(0))); ?>
                </h1>
            </div>
        </div>
        <div class="entry-content">
            <?php e_html(aya_get_post_content()); ?>
        </div>
    </div>
</article>