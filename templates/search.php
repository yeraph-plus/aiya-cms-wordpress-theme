<?php

//面包屑
aya_template_load('parts/breadcrumb');

?>
<section class="search-loop w-full p-4">
    <div class="flex flex-wrap">
        <div class="w-full lg:w-3/4 lg:pr-4 animate__animated" :class="[$store.app.animation]">
            <!-- Loop post grid -->
            <div class="re grid grid-cols-1 gap-4" :class="[$store.app.loopGridClass]">
                <?php aya_while_have_post(); ?>
            </div>
            <!-- Loop post pagination -->
            <?php
            //分页
            aya_template_load('parts/pagination');
            ?>
        </div>
        <div class="w-full mt-4 lg:w-1/4 lg:mt-0">
            <?php
            //小工具栏
            aya_template_load('parts/sidebar');
            ?>
        </div>
    </div>
</section>