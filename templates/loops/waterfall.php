<?php

$post_obj = new AYA_Post_In_While();

$post_thumb = aya_post_thumb($post_obj->thumbnail_url, $post_obj->content, 400, 0);

?>
<!-- POST ID: <?php aya_echo($post_obj->id); ?> -->
<article class="card bg-base-100 hover:shadow-md transition-shadow border border-base-300">
    <figure class="overflow-hidden">
        <img src="<?php aya_echo($post_thumb); ?>" alt="<?php aya_echo($post_obj->attr_title); ?>" class="w-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
    </figure>
    <div class="card-body p-4">
        <div class="card-actions justify-start my-1 flex flex-nowrap overflow-hidden whitespace-nowrap">
            <div class="flex space-x-1 overflow-hidden">
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <time datetime="<?php aya_echo($post_obj->date_iso); ?>">
                    <?php aya_echo($post_obj->date); ?>
                </time>
            </div>
            <div class="flex items-center">
                <span class="flex items-center mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <?php aya_echo($post_obj->views); ?>
                </span>
                <span class="flex items-center mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <?php aya_echo($post_obj->comments); ?>
                </span>
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a4 4 0 014-4c1.657 0 3.157.686 4 1.757C12.843 3.686 14.343 3 16 3a4 4 0 014 4c0 .88-.348 1.68-.9 2.25l-7.1 7.1a2.5 2.5 0 01-3.5 0l-7.1-7.1A3.999 3.999 0 013 7z" />
                    </svg>
                    <?php aya_echo($post_obj->likes); ?>
                </span>
            </div>
        </div>
    </div>
</article>