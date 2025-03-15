<?php
/**
 * Template Name: 全宽页面
 *  
 **/

//页头
aya_template_load('header');
//面包屑
aya_template_load('parts/breadcrumb');

?>
<article class="main-boxed full-width-page w-full p-4">
    <div class="flex flex-wrap">
        <div class="w-full animate__animated" :class="[$store.app.animation]">
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
    </div>
</article>

<?php
//页脚
aya_template_load('footer');
?>