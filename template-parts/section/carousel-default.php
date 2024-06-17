<?php
$post_list = banner_add_posts();
?>
<section class="index-banner pt-3">
    <div class="container">
        <div id="carousel-banner" class="banner-card carousel slide carousel-fade card" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php banner_indicators($post_list); ?>
            </div>
            <div class="carousel-inner">
                <?php banner_inner($post_list);?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-banner" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carousel-banner" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</section>