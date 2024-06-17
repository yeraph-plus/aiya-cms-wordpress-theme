<?php

/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 */

?>
<?php aya_header(); ?>
<!-- Archive -->
<section id="archive" class="site-archive">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-12">
                <?php aya_breadcrumbs(); ?>
                <div class="loop-content mx-2 my-4">
                    <?php //aya_loop_section_title('最新文章', '#'); 
                    ?>
                    <div id="post-loop-content" class="row <?php e_html(aya_loop_row_class()); ?>">
                        <?php aya_while_loop_template(); ?>
                    </div>
                </div>
                <div class="load-page-nav mx-2 my-4">
                    <?php aya_loop_pagination(); ?>
                </div>
            </div>
            <div class="col-lg-3 col-md-12">
                <?php aya_sidebar(); ?>
            </div>
        </div>
    </div>
</section>
<?php aya_footer(); ?>