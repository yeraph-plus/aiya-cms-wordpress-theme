<?php aya_header(); ?>
<!-- Search page -->
<section id="search" class="site-search">
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