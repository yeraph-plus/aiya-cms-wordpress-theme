<?php
//获取文章数据
$post_obj = new AYA_Post_In_While();
?>
<article data-id="<?php aya_echo($post_obj->id); ?>" class="bg-base-100 border border-base-300 rounded-lg mb-8">
    <div class="relative p-4 border-b border-base-300">
        <h1 class="text-2xl font-bold mb-4 ">
            <?php aya_post_status_badge($post_obj->status); ?>
            <?php aya_echo($post_obj->title); ?>
        </h1>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <!-- Meta -->
            <div class="flex items-center">
                <div class="flex items-center mr-1.5">
                    <div class="w-6 h-6 rounded-full overflow-hidden inline-block mr-1.5">
                        <img src="<?php aya_echo($post_obj->author_avatar_x32); ?>" alt="<?php aya_echo($post_obj->author_name); ?>" class="w-full h-full object-cover" />
                    </div>
                    <span class="whitespace-nowrap">
                        <?php aya_echo(__('由&nbsp;', 'AIYA') . '<a href="' . $post_obj->author_url . '" class="hover:underline">' . $post_obj->author_name . '</a>' . ($post_obj->is_post_author ? __('&nbsp;发布', 'AIYA') : __('&nbsp;投稿', 'AIYA'))); ?>
                    </span>
                </div>
            </div>
            <div class="flex items-center">
                <icon name="calendar" class="size-4 mr-1"></icon>
                <time datetime="<?php aya_echo($post_obj->date_iso); ?>">
                    <?php aya_echo($post_obj->date); ?>
                    <span class="hidden sm:inline-block"><?php aya_echo(__('&nbsp;[', 'AIYA') . $post_obj->modified_ago . __('更新]', 'AIYA')); ?></span>
                </time>
            </div>
            <div class="flex items-center">
                <icon name="chat-bubble-oval-left-ellipsis" class="size-4 mr-1"></icon>
                <span><?php aya_echo($post_obj->comments_text); ?></span>
            </div>
        </div>
    </div>
    <!-- Content Start -->
    <div class="article-content p-6 prose prose-base lg:prose-lg max-w-none">
        <?php aya_echo($post_obj->content); ?>
    </div>
    <!-- Content End -->
</article>

<?php
// 加载评论模板
aya_comments_template();
?>