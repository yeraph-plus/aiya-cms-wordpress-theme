<?php

//检查是否是首页
if (is_paged()) {
    //面包屑
    aya_template_load('parts/breadcrumb');
}

?>
<div class="main-homepage w-full p-4">
    <?php if (aya_opt('site_home_widget_bool', 'basic')): ?>
        <div class="flex flex-wrap">
            <div class="w-full lg:w-3/4 lg:pr-4 animate__animated" :class="[$store.app.animation]">
                <?php
                //轮播
                //aya_template_load('parts/carousel');
                //主循环
                aya_template_load('parts/main-loop');
                ?>
            </div>
            <div class="w-full mt-4 lg:w-1/4 lg:mt-0 animate__animated" :class="[$store.app.animation]">
                <?php
                //小工具栏
                aya_template_load('parts/sidebar');
                ?>
            </div>
        </div>
    <?php else: ?>
        <?php
        //主循环
        aya_template_load('parts/main-loop');
        ?>
    <?php endif; ?>
</div>