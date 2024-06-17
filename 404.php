<?php

/**
 * The template for displaying 404 pages (not found)
 * 
 */

?>
<?php aya_header(); ?>
<section class="404-not-found">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-12">
                <?php
                //输出
                aya_breadcrumbs();

                e_html('<div class="post-content mx-2 my-4">');

                aya_template_part('article/content', 'none');

                e_html('</div>'); ?>
            </div>
            <div class="col-lg-3 col-md-12">
                <?php aya_sidebar(); ?>
            </div>
        </div>
    </div>
</section>
<?php aya_footer(); ?>