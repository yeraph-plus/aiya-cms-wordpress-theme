<?php 
/**
 * Template Name: 友情链接
 */

$args = array(
    'title_li' => '',
    'show_images' => 1,
    'show_name' => 1,
    'show_description' => 1,
);
get_header(); ?>
<section class="index-single py-3">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-9">
                <!--page-->
                <div class="content-area card p-3 mb-2">
                    <h1 class="single-title pb-2">
                        <?php the_post_title(); ?>
                    </h1>
                    <div class="single-article border-top">
                        <article class="entry-main">
                        <?php
                            while (have_posts()) : the_post();
                                the_content();
                            endwhile;
                        ?>
                        </article>
                    </div>
                    <div class="single-bookmarks">
                        <?php wp_list_bookmarks($args); ?>
                    </div>
                </div>
                <!--comments-->
                <?php 
                    if (comments_open() || get_comments_number()){
                        //输出
                        comments_template();
                    } 
                ?>
            </div>
            <div class="col-lg-3">
                <!--sidebar-->
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</section>
<?php get_footer();?>