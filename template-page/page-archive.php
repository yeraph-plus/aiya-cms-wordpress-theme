<?php

/**
 * Template Name: 归档页面
 */
?>

<?php aya_header(); ?>
<!-- Archive Page -->
<section id="home" class="site-home">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-12">
                <?php aya_breadcrumbs(); ?>
                <div class="post-content mx-2 my-4">
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
                                <?php

                                $year = '1970';
                                $mon = '01';

                                query_posts('posts_per_page=-1&ignore_sticky_posts=1');

                                while (have_posts()) : the_post();

                                    $post_year = get_the_time('Y');
                                    $post_mon = get_the_time('m');

                                    if ($year != $post_year) {
                                        $year = $post_year;
                                        echo '<h3 class="year">' . $year . __('年', 'AIYA') . '</h3>';
                                    }
                                    if ($mon != $post_mon) {
                                        $mon = $post_mon;
                                        echo '<h5 class="mon">' . $mon . __('月：', 'AIYA') . '</h5>';
                                    }
                                    echo '<p>' . get_the_time('m-d') . ' - <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
                                endwhile;

                                wp_reset_query();

                                ?>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
            <div class="col-lg-3 col-md-12">
                <?php aya_sidebar(); ?>
            </div>
        </div>
    </div>
</section>
<?php aya_footer(); ?>