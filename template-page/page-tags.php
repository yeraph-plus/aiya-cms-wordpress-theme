<?php

/**
 * Template Name: 标签云页面
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
                            <div class="entry-content entry-content-tagcloud">
                                <?php

                                $cats = get_categories();
                                $tags = get_tags();

                                echo '<h3 class="tag">' . __('分类', 'AIYA') . '</h3>';

                                foreach ($cats as $cat) {
                                    $name = apply_filters('the_title', $cat->name);
                                    $url = esc_attr(get_tag_link($cat->term_id));

                                    echo '<a href="' . $url . '" class="tag-item" title="#' . $name . '">' . $name . '</span></a>';
                                }

                                echo '<h3 class="tag">' . __('标签', 'AIYA') . '</h3>';

                                foreach ($tags as $tag) {
                                    $count = intval($tag->count);
                                    $name = apply_filters('the_title', $tag->name);
                                    $url = esc_attr(get_tag_link($tag->term_id));

                                    echo '<a href="' . $url . '" class="tag-item" title="#' . $name . '">' . $name . '<span class="tag-plus">+' . $count . '</span></a>';
                                }

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