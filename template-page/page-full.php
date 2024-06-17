<?php

/**
 * Template Name: 全宽页面
 */
?>

<?php aya_header(); ?>
<!-- Archive Page -->
<section id="home" class="site-home">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php aya_breadcrumbs(); ?>
                <div class="post-content mx-2 my-4">
                    <?php aya_while_content_template('page'); ?>
                    <?php aya_content_comment(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php aya_footer(); ?>