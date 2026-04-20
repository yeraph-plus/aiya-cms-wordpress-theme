<?php

$posts = is_array($posts ?? null) ? $posts : [];
$loop_title = (string) ($loop_title ?? '文章列表');
$show_separator = (bool) ($show_separator ?? true);
$page_type = (string) ($page_type ?? '');

if ($posts === []) :
?>
    <div class="my-4 space-y-6">
        <div class="mx-auto my-8 border border-dashed rounded-xl p-8 text-center">
            <div class="text-lg font-semibold">暂无内容</div>
            <div class="text-sm text-muted-foreground mt-2">当前列表没有任何文章可显示</div>
        </div>
    </div>
    <?php
    return;
endif;

$loop_grid_id = function_exists('wp_unique_id') ? wp_unique_id('post-grid-') : uniqid('post-grid-');

$render_title_marker = static function ($page_type) {
    if ($page_type === 'index') {
        echo '<span class="text-primary font-semibold">首页</span>';
        return;
    }

    if ($page_type === 'archive') {
        echo '<span class="text-primary font-semibold">归档</span>';
        return;
    }

    if ($page_type === 'author') {
        echo '<span class="text-primary font-semibold">作者</span>';
        return;
    }

    if ($page_type === 'search') {
        echo '<span class="text-primary font-semibold">搜索</span>';
        return;
    }

    echo '<span class="w-2 h-5 bg-primary rounded-full"></span>';
};
?>
<div class="my-4 space-y-6">
    <?php if ($show_separator) : ?>
        <div class="h-2 my-6 shadow-sm transition-all hover:shadow-md bg-border/60 rounded-full"></div>
    <?php endif; ?>

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <?php $render_title_marker($page_type); ?>
            <h2 class="text-xl font-bold tracking-tight"><?php echo esc_html($loop_title); ?></h2>
        </div>
        <div class="hidden md:flex items-center gap-2 bg-muted/50 p-1 rounded-lg border" data-post-grid-controls data-target-id="<?php echo esc_attr($loop_grid_id); ?>">
            <button type="button" data-post-grid-toggle="grid" data-active="true" aria-pressed="true" class="inline-flex h-8 items-center justify-center rounded-md px-3 text-sm transition-colors bg-secondary text-foreground">
                网格
            </button>
            <button type="button" data-post-grid-toggle="list" aria-pressed="false" class="inline-flex h-8 items-center justify-center rounded-md px-3 text-sm transition-colors text-muted-foreground hover:bg-accent">
                列表
            </button>
        </div>
    </div>

    <div id="<?php echo esc_attr($loop_grid_id); ?>" data-post-grid-root data-layout="grid" class="grid gap-2 md:gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5">
        <?php foreach ($posts as $post) :
            $post_url = (string) ($post['url'] ?? '#');
            $post_title = (string) ($post['title'] ?? '');
            $post_attr_title = (string) ($post['attr_title'] ?? $post_title);
            $thumbnail = (string) ($post['thumbnail'] ?? '');
            $preview = (string) ($post['preview'] ?? '');
            $date = (string) ($post['date'] ?? '');
            $views = (string) ($post['views'] ?? '');
            $comments = (string) ($post['comments'] ?? '');
            $likes = (string) ($post['likes'] ?? '');
            $author = is_array($post['author'] ?? null) ? $post['author'] : [];
            $categories = is_array($post['cat_list'] ?? null) ? $post['cat_list'] : [];
        ?>
            <article data-post-grid-card class="group relative flex flex-col shadow-sm transition-all hover:shadow-md overflow-hidden py-0 gap-0 border-0 ring-1 ring-border rounded-xl bg-card">
                <div data-post-grid-media class="relative aspect-video w-full bg-muted overflow-hidden rounded-t-xl">
                    <?php if ($thumbnail !== '') : ?>
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post_attr_title); ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                    <?php else : ?>
                        <div class="flex h-full w-full items-center justify-center text-muted-foreground">No Image</div>
                    <?php endif; ?>
                </div>

                <div data-post-grid-body class="flex flex-1 flex-col p-4">
                    <div class="flex items-center overflow-hidden whitespace-nowrap gap-2 text-xs text-muted-foreground mb-2">
                        <span class="flex items-center gap-1">
                            <span class="icon-slot shrink-0" data-icon="calendar" data-icon-class="h-3 w-3"></span>
                            <span><?php echo esc_html($date); ?></span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <span class="icon-slot shrink-0" data-icon="views" data-icon-class="h-3 w-3"></span>
                            <span><?php echo esc_html($views); ?> 阅读</span>
                        </span>
                    </div>

                    <div data-post-grid-categories class="hidden md:flex gap-1 mb-2 overflow-hidden whitespace-nowrap">
                        <?php foreach ($categories as $cat) : ?>
                            <a href="<?php echo esc_url((string) ($cat['url'] ?? '#')); ?>" class="no-underline flex-shrink-0">
                                <span class="inline-flex items-center rounded-md border bg-secondary px-1.5 py-0 text-xs h-5 hover:bg-secondary/80">
                                    <?php echo esc_html((string) ($cat['name'] ?? '')); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <h3 data-post-grid-title class="line-clamp-2 text-md md:text-lg mb-2 font-semibold">
                        <a href="<?php echo esc_url($post_url); ?>" class="hover:underline focus:outline-none" title="<?php echo esc_attr($post_attr_title); ?>">
                            <?php echo esc_html($post_title); ?>
                        </a>
                    </h3>

                    <div data-post-grid-preview class="hidden md:line-clamp-2 text-sm text-muted-foreground"><?php echo wp_kses_post($preview); ?></div>

                    <div data-post-grid-footer class="mt-auto flex items-center justify-between pt-4 text-xs text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <?php if (!empty($author['avatar'])) : ?>
                                <img src="<?php echo esc_url((string) $author['avatar']); ?>" alt="<?php echo esc_attr((string) ($author['name'] ?? '')); ?>" class="h-6 w-6 rounded-full" />
                            <?php endif; ?>
                            <span class="hidden md:block font-medium"><?php echo esc_html((string) ($author['name'] ?? '')); ?></span>
                        </div>

                        <div class="flex gap-2">
                            <span class="flex items-center gap-1">
                                <span class="icon-slot shrink-0" data-icon="comments" data-icon-class="h-3 w-3"></span>
                                <span><?php echo esc_html($comments); ?></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="icon-slot shrink-0" data-icon="heart" data-icon-class="h-3 w-3"></span>
                                <span><?php echo esc_html($likes); ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
