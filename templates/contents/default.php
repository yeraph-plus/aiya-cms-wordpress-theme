<?php

if (!defined('ABSPATH')) {
    exit;
}

//获取文章数据
$post_obj = new AYA_Post_In_While();

//获取特色图片生成裁剪
$thumbnail = aya_the_post_thumb($post_obj->thumbnail_url, '', 1200, 0);
$has_thumbnail = ($thumbnail !== false);
//获取过期状态
$post_deadline = aya_opt('site_single_outdate_text', 'basic');
$post_is_outdated = $post_obj->the_post_is_outdated($post_deadline);
//获取声明文本
$statement_content = aya_opt('site_single_statement_text', 'basic');
//获取上下篇文章
$prev_post = $post_obj->prev_post();
$next_post = $post_obj->next_post();
?>
<article data-id="<?php aya_echo($post_obj->id); ?>" class="bg-base-100 border border-base-300 rounded-lg mb-8">
    <div class="relative border-b border-base-300">
        <?php if ($has_thumbnail): ?>
            <!-- Thumbnail -->
            <div class="relative w-full h-60 md:h-72 lg:h-80 rounded-t-lg overflow-hidden">
                <img src="<?php aya_echo($thumbnail); ?>" alt="<?php aya_echo($post_obj->title); ?>" class="absolute inset-0 w-full h-full object-cover object-center" />
                <!-- Mask -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
            </div>
        <?php endif; ?>
        <div class="<?php aya_echo($has_thumbnail ? 'absolute bottom-0 left-0 right-0 p-4 z-10' : 'p-4'); ?>">
            <h1 class="text-2xl font-bold mb-4 flex items-center gap-2 <?php aya_echo($has_thumbnail ? 'text-white drop-shadow-lg' : 'text-base-content'); ?>">
                <?php aya_the_post_status_badge($post_obj->status); ?>
                <?php aya_echo($post_obj->title); ?>
            </h1>
            <div class="flex flex-wrap items-center gap-3 text-sm <?php aya_echo($has_thumbnail ? 'text-white/90' : 'text-base-content/70'); ?>">
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
                    <icon name="eye" class="size-4 mr-1"></icon>
                    <span><?php aya_echo($post_obj->views_text); ?></span>
                </div>
                <div class="flex items-center">
                    <icon name="chat-bubble-oval-left-ellipsis" class="size-4 mr-1"></icon>
                    <span><?php aya_echo($post_obj->comments_text); ?></span>
                </div>
                <div class="flex items-center">
                    <icon name="heart" class="size-4 mr-1"></icon>
                    <span><?php aya_echo($post_obj->likes_text); ?></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Start -->
    <div class="article-content p-6 prose prose-base max-w-none">
        <?php if ($post_is_outdated): ?>
            <div class="alert alert-soft my-4">
                <icon name="exclamation-triangle" class="size-4 mr-1"></icon>
                <span><?php aya_echo(__('这篇文章的发布于&nbsp;', 'AIYA') . $post_obj->date_ago . __('&nbsp;，部分信息可能已过时。', 'AIYA')); ?></span>
            </div>
        <?php endif; ?>

        <?php aya_the_post_tips($post_obj->id); ?>

        <?php aya_echo($post_obj->content); ?>

        <?php aya_vue_load('post-button-group', [
            'ajax-url' => admin_url('admin-ajax.php'),
            'post-id' => $post_obj->id,
            'like-count' => $post_obj->likes,
            'nonce' => aya_nonce_active_store(),
        ]); ?>
    </div>
    <!-- Content End -->
    <div class="divider px-8 lg:px-48 font-semibold text-base-content/50">THE END</div>
    <div class="flex items-center justify-center p-6 text-sm text-base-content/50">
        <?php aya_echo($statement_content); ?>
    </div>
    <div class="flex flex-wrap gap-2 p-6">
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
    <div class="flex justify-between p-6 border-t border-base-300">
        <div class="prev-post max-w-[49%]">
            <?php if (!empty($prev_post)): ?>
                <a class="btn btn-md md:btn-lg gap-2 lg:gap-3 text-sm" href="<?php aya_echo($prev_post->url); ?>">
                    <icon name="chevron-left" class="size-9"></icon>
                    <div class="flex flex-col items-start gap-1 leading-[1.1] overflow-hidden">
                        <span class="text-base-content/50 text-[0.7rem] font-semibold"><?php aya_echo(__('上一篇', 'AIYA')); ?></span>
                        <span class=" hidden md:flex"><?php aya_echo($prev_post->title); ?></span>
                    </div>
                </a>
            <?php endif; ?>
        </div>
        <div class="next-post max-w-[49%]">
            <?php if (!empty($next_post)): ?>
                <a class="btn btn-md md:btn-lg gap-2 lg:gap-3 text-sm" href="<?php aya_echo($next_post->url); ?>">
                    <div class="flex flex-col items-start gap-1 leading-[1.1] overflow-hidden">
                        <span class="text-base-content/50 text-[0.7rem] font-semibold"><?php aya_echo(__('下一篇', 'AIYA')); ?></span>
                        <span class=" hidden md:flex"><?php aya_echo($next_post->title); ?></span>
                    </div>
                    <icon name="chevron-right" class="size-9"></icon>
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php
/*
$related = aya_opt('site_single_related_bool', 'basic');
// 显示相关文章
$related_posts = get_posts(array(
    'category__in' => wp_get_post_categories($post_obj->id),
    'numberposts' => 3,
    'post__not_in' => array($post_obj->id)
));

if (!empty($related_posts)):
    ?>
    <div class="related-posts mt-8 bg-base-100 border border-base-300 rounded-lg shadow-sm p-6">
        <h3 class="text-xl font-bold mb-4"><?php _e('相关文章', 'AIYA'); ?></h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($related_posts as $related_post): ?>
                <a href="<?php echo get_permalink($related_post->ID); ?>" class="card bg-base-200 hover:shadow-md transition-shadow">
                    <div class="card-body p-4">
                        <h4 class="card-title text-base line-clamp-2"><?php echo esc_html($related_post->post_title); ?></h4>
                        <p class="text-xs text-base-content/70">
                            <?php echo get_the_date('', $related_post->ID); ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
*/
?>

<?php
// 加载评论模板
aya_comments_template();
?>