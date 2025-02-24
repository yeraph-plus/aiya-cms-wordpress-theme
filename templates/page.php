<?php

//面包屑
aya_template_load('parts/breadcrumb');

?>
<article class="content-loop w-full p-4">
    <div class="flex flex-wrap">
        <div class="w-full lg:w-3/4 lg:pr-4 animate__animated" :class="[$store.app.animation]">
            <!-- content grid -->
            <div class="relative">
                <?php
                //文章内容
                aya_while_have_content();
                //评论
                aya_comments_template();
                ?>
            </div>
        </div>
        <div class="w-full mt-4 lg:w-1/4 lg:mt-0 animate__animated" :class="[$store.app.animation]">
            <?php
            //小工具栏
            aya_template_load('parts/sidebar');
            ?>
        </div>
    </div>
</article>