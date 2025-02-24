<?php

$post_data = new AYA_Post_Content(0, 128);

?>
<div class="panel w-full py-4 px-6">
    <div class="relative flex items-center pb-6">
        <h1 class="text-2xl font-bold">
            <?php aya_echo($post_data->title); ?>
        </h1>
    </div>
    <div class="relative flex-2 lg:flex items-center justify-start lg:justify-between border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-4 mb-4">
        <div class="hidden lg:flex items-center justify-start text-1xl font-bold mr-3">
            <div class="w-6 h-6 rounded-full overflow-hidden inline-block ltr:mr-2 rtl:ml-2.5">
                <img src="<?php aya_echo($post_data->author_avatar); ?>" alt="avatar" class="flex w-full h-full items-center justify-center bg-[#515365] text-[#e0e6ed]" />
            </div>
            <span class="text-[#515365] dark:text-white-dark whitespace-nowrap">
                <?php aya_echo(__('由 ', 'AIYA') . '<a href="' . $post_data->author['url'] . '">' . $post_data->author['name'] . '</a>' . (($post_data->author['is_submit']) ? __(' 发布', 'AIYA') : __(' 投稿', 'AIYA'))); ?>
            </span>
        </div>
        <div class="flex-2 lg:flex items-center text-1xl text-white-dark justify-between overflow-hidden whitespace-nowrap">
            <p class="flex items-center overflow-hidden whitespace-nowrap lg:mr-2">
                <i data-feather="clock" width="16" height="16" class="mr-1"></i><?php aya_echo($post_data->date); ?>
            </p>
        </div>
    </div>
    <!-- The content start -->
    <div class="editor-modality">
        <?php
        //摘要
        if ($post_data->excerpt != '') : ?>
            <p class="text-dark p-4"><?php aya_echo($post_data->excerpt); ?></p>
        <?php endif; ?>
        <?php aya_echo($post_data->content); ?>
    </div>
    <!-- The content end -->
</div>