<?php
$post_list = banner_cms_posts(0, 3);
$post_center = banner_cms_posts(3, 2);
$post_end = banner_cms_posts(1, 1);
?>
<section class="index-banner pt-3">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-6 banner-cms-start">
                <div id="carousel-banner" class="banner-card carousel slide carousel-fade card" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php banner_indicators($post_list); ?>
                    </div>
                    <div class="carousel-inner">
                        <?php banner_inner($post_list);?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-banner" data-bs-slide="prev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-banner" data-bs-slide="next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-3 banner-cms-center">
                <?php banner_outer($post_center);?>
            </div>
            <div class="col-lg-3 banner-cms-end">
                <?php banner_outer($post_end);?>
            </div>
        </div>
    </div>
</section>