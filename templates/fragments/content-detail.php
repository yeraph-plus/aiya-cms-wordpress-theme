<?php

$has_thumbnail = $thumbnail !== '';

$render_action_island = static function ($variant) use ($post_id, $comments, $likes, $is_favorite, $disallow_toggle) {
    aya_react_island('content-button-group', [
        'post_id' => $post_id,
        'comments' => $comments,
        'likes' => $likes,
        'is_favorite' => $is_favorite,
        'disallow_toggle' => $disallow_toggle,
        'variant' => $variant,
    ]);
};

?>
<div class="relative mb-8">
    <?php if ($has_thumbnail) : ?>
        <div class="relative w-full h-60 md:h-72 lg:h-96 rounded-lg overflow-hidden mb-6">
            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>" class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 z-10">
                <div class="flex flex-col gap-4">
                    <h1 class="text-xl md:text-3xl lg:text-4xl font-bold leading-tight flex flex-wrap items-center gap-3 text-white drop-shadow-lg">
                        <?php echo esc_html($title); ?>
                        <?php if (!empty($status) && $status !== 'publish') : ?>
                            <span class="inline-flex items-center rounded-md border px-2 py-1 text-xs font-medium bg-background/80 text-foreground">
                                <?php echo esc_html(is_string($status) ? $status : '状态'); ?>
                            </span>
                        <?php endif; ?>
                    </h1>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-4 text-sm text-white/90">
                            <a href="<?php echo esc_url((string) ($author['url'] ?? '#')); ?>" class="flex items-center gap-2 hover:opacity-80 transition-opacity" title="<?php echo esc_attr((string) ($author['name'] ?? '')); ?>">
                                <?php if (!empty($author['avatar'])) : ?>
                                    <img src="<?php echo esc_url((string) $author['avatar']); ?>" alt="<?php echo esc_attr((string) ($author['name'] ?? '')); ?>" class="h-8 w-8 rounded-full border border-border object-cover" />
                                <?php endif; ?>
                                <span class="font-medium"><?php echo esc_html((string) ($author['name'] ?? '')); ?></span>
                            </a>
                            <div class="flex items-center gap-1.5" title="<?php echo esc_attr($date_iso); ?>">
                                <span class="icon-slot shrink-0" data-icon="calendar" data-icon-class="h-4 w-4"></span>
                                <time datetime="<?php echo esc_attr($date_iso); ?>"><?php echo esc_html($date); ?></time>
                                <?php if ($modified_ago !== '') : ?>
                                    <span class="opacity-80">Updated <?php echo esc_html($modified_ago); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($views !== '') : ?>
                                <div class="flex items-center gap-1.5">
                                    <span class="icon-slot shrink-0" data-icon="views" data-icon-class="h-4 w-4"></span>
                                    <span><?php echo esc_html($views); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php $render_action_island('overlay'); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mb-6 pb-6 border-b border-border">
            <div class="flex flex-col gap-4">
                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold leading-tight flex flex-wrap items-center gap-3 text-foreground">
                    <?php echo esc_html($title); ?>
                    <?php if (!empty($status) && $status !== 'publish') : ?>
                        <span class="inline-flex items-center rounded-md border px-2 py-1 text-xs font-medium bg-background/80 text-foreground">
                            <?php echo esc_html(is_string($status) ? $status : '状态'); ?>
                        </span>
                    <?php endif; ?>
                </h1>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                        <a href="<?php echo esc_url((string) ($author['url'] ?? '#')); ?>" class="flex items-center gap-2 hover:opacity-80 transition-opacity" title="<?php echo esc_attr((string) ($author['name'] ?? '')); ?>">
                            <?php if (!empty($author['avatar'])) : ?>
                                <img src="<?php echo esc_url((string) $author['avatar']); ?>" alt="<?php echo esc_attr((string) ($author['name'] ?? '')); ?>" class="h-8 w-8 rounded-full border border-border object-cover" />
                            <?php endif; ?>
                            <span class="font-medium text-base-content"><?php echo esc_html((string) ($author['name'] ?? '')); ?></span>
                        </a>
                        <div class="flex items-center gap-1.5" title="<?php echo esc_attr($date_iso); ?>">
                            <span class="icon-slot shrink-0" data-icon="calendar" data-icon-class="h-4 w-4"></span>
                            <time datetime="<?php echo esc_attr($date_iso); ?>"><?php echo esc_html($date); ?></time>
                            <?php if ($modified_ago !== '') : ?>
                                <span class="opacity-80">Updated <?php echo esc_html($modified_ago); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($views !== '') : ?>
                            <div class="flex items-center gap-1.5">
                                <span class="icon-slot shrink-0" data-icon="views" data-icon-class="h-4 w-4"></span>
                                <span><?php echo esc_html($views); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php $render_action_island('default'); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($categories) || !empty($tags)) : ?>
        <div class="flex flex-wrap items-center gap-2 mb-6 text-sm">
            <?php if (!empty($categories)) : ?>
                <span class="text-muted-foreground">分类：</span>
                <?php foreach ($categories as $index => $cat) : ?>
                    <a href="<?php echo esc_url((string) ($cat['url'] ?? '#')); ?>" class="bg-primary/90 text-primary-foreground hover:bg-primary/80 text-sm px-2 py-1 rounded-md"><?php echo esc_html((string) ($cat['name'] ?? '')); ?></a>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($tags)) : ?>
                <?php foreach ($tags as $index => $tag) : ?>
                    <a href="<?php echo esc_url((string) ($tag['url'] ?? '#')); ?>" class="text-muted-foreground hover:text-primary transition-colors no-underline">#<?php echo esc_html((string) ($tag['name'] ?? '')); ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($is_outdated) : ?>
        <div class="flex flex-col gap-4 mt-6">
            <div class="relative w-full rounded-lg border px-4 py-3 text-sm border-slate-200 bg-slate-50 text-slate-900 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-200">
                <div class="font-medium tracking-tight">注意</div>
                <div class="text-sm text-muted-foreground mt-1">这篇文章发布于 <?php echo esc_html($outdated_text); ?> ，部分信息可能已过时，请留意。</div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($alert_tips)) : ?>
        <div class="flex flex-col gap-4 mt-6">
            <?php foreach ($alert_tips as $index => $tip) : ?>
                <div class="relative w-full rounded-lg border px-4 py-3 text-sm bg-card text-card-foreground">
                    <div class="font-medium tracking-tight"><?php echo esc_html((string) ($tip['name'] ?? '')); ?></div>
                    <div class="text-sm text-muted-foreground mt-1"><?php echo esc_html((string) ($tip['description'] ?? '')); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
