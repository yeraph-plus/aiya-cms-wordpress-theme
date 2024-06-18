<?php

/**
 * Template Name: 全屏页面
 */
?>

<?php aya_header(); ?>
<!-- Full Page -->
<section id="full-page" class="site-singular">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php aya_breadcrumbs(); ?>
                <div class="post-content mx-2 my-4">
                    <?php aya_while_content_template(); ?>
                </div>
                <div class="post-comments mx-2 my-4">
                    <?php aya_comments_template(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php aya_footer(); ?>