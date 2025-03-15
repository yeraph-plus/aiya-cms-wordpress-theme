<?php

$post_data = new AYA_Post_Content(0, 128);
$post_outdated_days = get_post_meta($post_data->id, 'days_is_outdated', true);
$post_is_outdated = aya_get_post_is_outdated($post_data->id, $post_outdated_days);
$post_disclaimer_text = aya_opt('site_single_disclaimer_text', 'postpage');

?>
<div class="panel w-full py-4 px-6">
    <?php
    //特色图片
    if ($post_data->thumbnail != NUll) : ?>
        <div class="-mt-4 -mx-6 rounded-tl rounded-tr h-[200px] overflow-hidden mb-4">
            <img src="<?php aya_echo($post_data->thumbnail); ?>" alt="thumbnail" class="w-full h-full object-cover object-center transition duration-300 group-hover:scale-105 ">
        </div>
    <?php endif; ?>
    <div class="relative flex items-center pb-6">
        <h1 class="text-2xl font-bold line-clamp-2 whitespace-normal break-words">
            <?php aya_echo($post_data->title); ?>
        </h1>
        <span class=" flex items-center ml-4 text-2xl font-normal">
            <?php aya_single_badge_starts($post_data->id); ?>
        </span>
    </div>
    <div class="relative flex-2 lg:flex items-center justify-start lg:justify-between border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-4 mb-4">
        <div class="hidden lg:flex items-center justify-start text-1xl font-bold mr-3">
            <div class="w-6 h-6 rounded-full overflow-hidden inline-block ltr:mr-2 rtl:ml-2.5">
                <img src="<?php aya_echo($post_data->author['avatar']); ?>" alt="avatar" class="flex w-full h-full items-center justify-center bg-[#515365] text-[#e0e6ed]" />
            </div>
            <span class="text-[#515365] dark:text-white-dark whitespace-nowrap">
                <?php aya_echo(__('由 ', 'AIYA') . '<a href="' . $post_data->author['url'] . '">' . $post_data->author['name'] . '</a>' . (($post_data->author['is_submit']) ? __(' 发布', 'AIYA') : __(' 投稿', 'AIYA'))); ?>
            </span>
        </div>
        <div class="flex-2 lg:flex items-center text-1xl text-white-dark justify-between overflow-hidden whitespace-nowrap">
            <p class="flex items-center overflow-hidden whitespace-nowrap lg:mr-2">
                <i data-feather="clock" width="16" height="16" class="mr-1"></i><?php aya_echo($post_data->date); ?>
            </p>
            <p class="flex items-center mt-2 lg:mt-0">
                <i data-feather="eye" width="16" height="16" class="mr-2"></i><?php aya_echo($post_data->views); ?>
                <i data-feather="heart" width="16" height="16" class="mx-2"></i><?php aya_echo($post_data->likes); ?>
                <i data-feather="message-circle" width="16" height="16" class="mx-2"></i><?php aya_echo($post_data->comments); ?>
            </p>
        </div>
    </div>
    <?php
    //文章过时提示
    if ($post_is_outdated) : ?>
        <div class="relative flex items-center text-dark bg-dark-light rounded border-l-4 border-dark dark:bg-dark-dark-light dark:text-white-light dark:border-white-light/20 p-4 mb-4">
            <i data-feather="alert-octagon" width="20" height="20" class="mr-2"></i>
            <span><?php aya_echo(__('这篇文章的发布时间已经超过 ', 'AIYA') . $post_outdated_days . __(' 天，部分信息可能已过时。', 'AIYA')); ?></span>
        </div>
    <?php endif; ?>
    <!-- The content start -->
    <div x-data="editorComponent" class="editor-modality">
        <?php
        //摘要
        if ($post_data->excerpt != '') : ?>
            <p class="text-dark p-4"><?php aya_echo($post_data->excerpt); ?></p>
        <?php endif; ?>
        <?php aya_echo($post_data->content); ?>
    </div>
    <!-- The content end -->
    <div class="relative flex items-center justify-center py-4" x-data="ajaxClickLikes" data-initial-likes="<?php aya_echo($post_data->likes); ?>" data-post-id="<?php aya_echo($post_data->id); ?>">
        <button class="btn btn-outline-primary btn-lg rounded-full flex items-center" @click="sendClickLikes(<?php aya_echo($post_data->id); ?>)">
            <i data-feather="heart" width="16" height="16" class="mr-2"></i>
            <span x-text="responseLikes"></span>
        </button>
    </div>
    <?php
    //文末声明
    if ($post_disclaimer_text != ''): ?>
        <div class="relative flex flex-col items-center justify-center mb-4">
            <i data-feather="feather" width="20" height="20" class="m-4"></i>
            <p class="text-sm text-white-dark px-4"><?php aya_echo($post_disclaimer_text); ?></p>
        </div>
    <?php endif; ?>
    <div class="relative flex items-center justify-start overflow-hidden whitespace-nowrap border-t border-[#e0e6ed] dark:border-[#1b2e4b] pt-4">
        <?php aya_single_badge_tags(); ?>
    </div>
</div>

<?php
//上/下篇文章
aya_template_load('units/single-next-post');
