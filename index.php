<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * 
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

?>
<?php aya_header(); ?>
<!-- Home -->
<section id="home" class="site-home">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-12">
                <?php aya_home_open(); ?>
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