<?php

$post_obj = new AYA_Post_In_While();

$post_thumb = aya_post_thumb($post_obj->thumbnail_url, $post_obj->content, 400, 300);

?>
<article class="card bg-base-100 hover:shadow-md transition-shadow border border-base-300 ">
    <!-- POST ID: <?php aya_echo($post_obj->id); ?> -->
    <figure class="overflow-hidden h-48">
        <img src="<?php aya_echo($post_thumb); ?>" alt="<?php aya_echo($post_obj->attr_title); ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
    </figure>
    <div class="card-body p-4">
        <div class="card-actions justify-start my-1 flex flex-nowrap overflow-hidden whitespace-nowrap">
            <div class="flex space-x-2 overflow-hidden">
                <?php aya_the_post_status_badge($post_obj->status); ?>
                <?php foreach ($post_obj->cat_list as $cat): ?>
                    <a href="<?php aya_echo($cat['url']); ?>" class="badge badge-primary badge-outline"><?php aya_echo($cat['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <h2 class="card-title line-clamp-2 hover:text-primary transition-colors">
            <a href="<?php aya_echo($post_obj->url); ?>" title="<?php aya_echo($post_obj->attr_title); ?>">
                <?php aya_echo($post_obj->title); ?>
            </a>
        </h2>
        <p class="line-clamp-3 text-sm text-base-content/70">
            <?php aya_echo($post_obj->preview); ?>
        </p>
        <div class="flex items-center justify-between mt-3 text-xs text-base-content/60">
            <div class="flex items-center">
                <icon name="clock" class="size-4 mr-1"></icon>
                <time datetime="<?php aya_echo($post_obj->date_iso); ?>">
                    <?php aya_echo($post_obj->date); ?>
                </time>
            </div>
            <div class="flex items-center">
                <span class="flex items-center mr-1">
                    <icon name="eye" class="size-4 mr-1"></icon>
                    <?php aya_echo($post_obj->views); ?>
                </span>
                <span class="flex items-center mr-1">
                    <icon name="chat-bubble-oval-left-ellipsis" class="size-4 mr-1"></icon>
                    <?php aya_echo($post_obj->comments); ?>
                </span>
                <span class="flex items-center">
                    <icon name="heart" class="size-4 mr-1"></icon>
                    <?php aya_echo($post_obj->likes); ?>
                </span>
            </div>
        </div>
    </div>
</article>