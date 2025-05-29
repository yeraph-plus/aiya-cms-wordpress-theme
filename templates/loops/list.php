<?php

$post_obj = new AYA_Post_In_While();

$post_thumb = aya_post_thumb($post_obj->thumbnail_url, $post_obj->content, 300, 200);

?>
<article class="card bg-base-100 hover:shadow-md transition-shadow border border-base-300 overflow-hidden">
    <!-- POST ID: <?php aya_echo($post_obj->id); ?> -->
    <div class="flex flex-col sm:flex-row">
        <div class="sm:w-48 md:w-56 lg:w-72 sm:h-full overflow-hidden">
            <a href="<?php aya_echo($post_obj->url); ?>" class="block h-full">
                <img src="<?php aya_echo($post_thumb); ?>" alt="<?php aya_echo($post_obj->attr_title); ?>" class="w-full h-48 sm:h-full object-cover hover:scale-105 transition-transform duration-300" />
            </a>
        </div>
        <div class="card-body flex-1 p-4 sm:pl-5 flex flex-col">
            <h2 class="card-title mb-2 hover:text-primary transition-colors">
                <a href="<?php aya_echo($post_obj->url); ?>" title="<?php aya_echo($post_obj->attr_title); ?>">
                    <?php aya_echo($post_obj->title); ?>
                </a>
            </h2>
            <div class="card-actions justify-start my-1 flex flex-wrap overflow-hidden">
                <div class="flex gap-2 overflow-hidden">
                    <?php aya_post_status_badge($post_obj->status); ?>
                    <?php foreach ($post_obj->cat_list as $cat): ?>
                        <a href="<?php aya_echo($cat['url']); ?>" class="badge badge-primary badge-outline truncate line-clamp">
                            <?php aya_echo($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                    <?php foreach ($post_obj->tag_list as $tag): ?>
                        <a href="<?php aya_echo($tag['url']); ?>" class="badge badge-outline truncate line-clamp">
                            <icon name="hashtag" class="size-3"></icon>
                            <?php aya_echo($tag['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <p class="line-clamp-2 sm:line-clamp-3 text-sm text-base-content/70 flex-grow">
                <?php aya_echo($post_obj->preview); ?>
            </p>
            <div class="flex items-center justify-between mt-auto pt-3 text-xs text-base-content/60">
                <div class="flex items-center gap-3">
                    <span class="flex items-center">
                        <icon name="clock" class="size-4 mr-1"></icon>
                        <time datetime="<?php aya_echo($post_obj->date_iso); ?>">
                            <?php aya_echo($post_obj->date); ?>
                        </time>
                    </span>
                    <span class="flex items-center">
                        <icon name="eye" class="size-4 mr-1"></icon>
                        <?php aya_echo($post_obj->views); ?>
                    </span>
                    <span class="flex items-center">
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
    </div>
</article>