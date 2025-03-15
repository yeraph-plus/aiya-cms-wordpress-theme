<?php

$post_type = aya_post_type();
$post_data = new AYA_Post_Meta(0, 'timeago', 32, 67);
$post_thumb = aya_post_thumb($post_data->thumb_url, $post_data->content, 400, 300);

?>
<!-- POST ID: <?php aya_echo($post_data->id); ?> -->
<div class="panel col group w-auto py-7 px-6">
    <div class="-mt-7 -mx-6 rounded-tl rounded-tr h-[200px] overflow-hidden">
        <img src="<?php aya_echo($post_thumb); ?>" alt="content-thumb" class="w-full h-full object-cover object-center transition duration-300 group-hover:scale-105 " />
    </div>
    <div class="relative flex overflow-hidden whitespace-nowrap mt-4 mb-2">
        <?php aya_single_badge_starts($post_data->id); ?>
        <?php aya_single_badge_category($post_data->id); ?>
    </div>
    <h5 class="h-[40px] line-clamp-2 whitespace-normal break-words text-[#3b3f5c] text-[17px] font-bold mb-4 dark:text-white-light">
        <a href="<?php aya_echo($post_data->url); ?>" title="<?php aya_echo($post_data->attr_title); ?>" class="inset-0 hover:text-primary transition-colors duration-300">
            <?php aya_echo($post_data->title); ?>
        </a>
    </h5>
    <p class="h-[60px] text-white-dark line-clamp-3">
        <?php aya_echo($post_data->preview); ?>
    </p>
    <div class="relative flex items-center justify-between overflow-hidden whitespace-nowrap mt-4">
        <div class="flex items-center justify-start text-xs font-base mr-3">
            <div class="w-6 h-6 rounded-full overflow-hidden inline-block ltr:mr-2 rtl:ml-2.5">
                <img src="<?php aya_echo($post_data->author['avatar']); ?>" alt="avatar" class="flex w-full h-full items-center justify-center bg-[#515365] text-[#e0e6ed]" />
            </div>
            <span class="text-[#515365] dark:text-white-dark">
                <a href="<?php aya_echo($post_data->author['url']); ?>"><?php aya_echo($post_data->author['name']); ?></a>
            </span>
        </div>
        <div class="flex items-center text-xs text-white-dark">
            <p class="flex items-center mr-2">
                <i data-feather="eye" width="16" height="16" class="mr-1"></i><?php aya_echo($post_data->views); ?>
            </p>
            <p class="flex items-center mr-2">
                <i data-feather="heart" width="16" height="16" class="mr-1"></i><?php aya_echo($post_data->likes); ?>
            </p>
            <p class="flex items-center mr-2">
                <i data-feather="message-circle" width="16" height="16" class="mr-1"></i><?php aya_echo($post_data->comments); ?>
            </p>
        </div>
    </div>
</div>