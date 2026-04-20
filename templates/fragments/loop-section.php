<?php

$posts = is_array($posts ?? null) ? $posts : [];
$loop_title = (string) ($loop_title ?? '文章列表');

$render_status_badges = static function ($status_list) {
    if (!is_array($status_list) || $status_list === []) {
        return;
    }
    ?>
    <div class="absolute top-2 left-2 flex flex-col gap-1 z-10 pointer-events-none">
        <?php foreach ($status_list as $key => $label) : ?>
            <span class="badge-slot px-2 py-1 shadow-sm backdrop-blur-sm" data-badge-alias="<?php echo esc_attr((string) $key); ?>">
                <span><?php echo esc_html((string) $label); ?></span>
            </span>
        <?php endforeach; ?>
    </div>
    <?php
};

if ($posts === []) :
?>
    <div class="mt-6 mb-4 space-y-6">
        <div class="mx-auto my-8 border border-dashed rounded-xl p-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                <span class="icon-slot" data-icon="inbox" data-icon-class="h-8 w-8"></span>
            </div>
            <div class="text-lg font-semibold">暂无内容</div>
            <div class="text-sm text-muted-foreground mt-2">当前列表没有任何文章可显示</div>
        </div>
    </div>
    <?php
    return;
endif;
?>
<div class="mt-6 mb-4 space-y-6">
    <div class="flex items-center justify-start gap-2">
        <span class="icon-slot text-primary" data-icon="layers-2" data-icon-class="h-6 w-6 text-primary"></span>
        <h2 class="text-xl font-bold tracking-tight"><?php echo esc_html($loop_title); ?></h2>
    </div>

    <div class="grid gap-2 md:gap-4 transition-all duration-300 ease-in-out grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5">
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
            $status = is_array($post['status'] ?? null) ? $post['status'] : [];
            $categories = is_array($post['cat_list'] ?? null) ? $post['cat_list'] : [];
            $author = is_array($post['author'] ?? null) ? $post['author'] : [];
        ?>
            <article class="group relative flex flex-col shadow-sm transition-all hover:shadow-md overflow-hidden py-0 gap-0 border-0 ring-1 ring-border rounded-xl bg-card">
                <div class="relative aspect-video w-full bg-muted overflow-hidden rounded-t-xl">
                    <?php $render_status_badges($status); ?>
                    <?php if ($thumbnail !== '') : ?>
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post_attr_title); ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                    <?php else : ?>
                        <div class="flex h-full w-full items-center justify-center text-muted-foreground">No Image</div>
                    <?php endif; ?>
                </div>

                <div class="flex flex-1 flex-col p-4">
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

                    <?php if ($categories !== []) : ?>
                        <div class="flex gap-1 mb-2 overflow-hidden whitespace-nowrap hidden md:block">
                            <?php foreach ($categories as $cat) : ?>
                                <a href="<?php echo esc_url((string) ($cat['url'] ?? '#')); ?>" class="no-underline flex-shrink-0">
                                    <span class="inline-flex items-center rounded-md border bg-secondary px-1.5 py-0 text-xs h-5 hover:bg-secondary/80">
                                        <?php echo esc_html((string) ($cat['name'] ?? '')); ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <h3 class="line-clamp-2 text-md md:text-lg mb-2 font-semibold">
                        <a href="<?php echo esc_url($post_url); ?>" class="hover:underline focus:outline-none" title="<?php echo esc_attr($post_attr_title); ?>">
                            <?php echo esc_html($post_title); ?>
                        </a>
                    </h3>

                    <div class="hidden md:line-clamp-2 text-sm text-muted-foreground"><?php echo wp_kses_post($preview); ?></div>

                    <div class="mt-auto flex items-center justify-between pt-4 text-xs text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <?php if (!empty($author['avatar'])) : ?>
                                <img src="<?php echo esc_url((string) $author['avatar']); ?>" alt="<?php echo esc_attr((string) ($author['name'] ?? '')); ?>" class="h-6 w-6 rounded-full" />
                            <?php endif; ?>
                            <span class="hidden md:block font-medium"><?php echo esc_html((string) ($author['name'] ?? '')); ?></span>
                        </div>

                        <div class="flex gap-2 text-xs text-muted-foreground">
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
