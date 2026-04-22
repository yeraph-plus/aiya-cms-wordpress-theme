<?php

$posts = is_array($posts ?? null) ? $posts : [];
$is_main_loop = ($is_main_loop ?? false);
// 无文章
if ($posts === []) {
    return;
}
// 状态徽章，使用 badge-slot 运行时渲染
$render_status_badges = static function ($status_list) {
    if (!is_array($status_list) || $status_list === []) {
        return;
    }

    $html = '';
    $html .= '<div class="absolute top-2 left-2 flex flex-col gap-1 z-10 pointer-events-none">';
    foreach ($status_list as $key => $label) {
        $html .= '<span class="badge-slot px-2 py-1 shadow-sm backdrop-blur-sm" data-badge-alias="' . esc_attr($key) . '">';
        $html .= '<span>' . esc_html($label) . '</span>';
        $html .= '</span>';
    }
    $html .= '</div>';

    return $html;
};

?>
<div class="my-4 space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="icon-slot text-primary shrink-0" data-icon="<?php aya_echo($label_icon); ?>" data-icon-size="6"></span>
            <h3 class="text-xl font-bold tracking-tight"><?php aya_echo($label_title); ?></h3>
        </div>
        <?php if ($is_main_loop) : ?>
            <div class="hidden md:flex items-center gap-2 bg-muted/50 p-1 rounded-lg border" data-post-grid-controls data-target-id="main-post-loop">
                <button type="button" data-post-grid-toggle="grid" data-active="true" aria-pressed="true" class="inline-flex h-8 items-center justify-center rounded-md px-3 text-sm transition-colors bg-secondary text-foreground">
                    <span class="icon-slot shrink-0" data-icon="layout-grid" data-icon-size="4"></span>
                </button>
                <button type="button" data-post-grid-toggle="list" aria-pressed="false" class="inline-flex h-8 items-center justify-center rounded-md px-3 text-sm transition-colors text-muted-foreground hover:bg-accent">
                    <span class="icon-slot shrink-0" data-icon="layout-list" data-icon-size="4"></span>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div id="<?php aya_echo($is_main_loop ?  'main-post-loop' : uniqid('post-section-')); ?>" data-post-grid-root data-layout="grid" class="grid gap-2 md:gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5">
        <?php foreach ($posts as $post) : ?>
            <article data-post-grid-card class="group relative flex flex-col shadow-sm transition-all hover:shadow-md overflow-hidden py-0 gap-0 border-0 ring-1 ring-border rounded-lg bg-card">
                <div data-post-grid-media class="relative w-full h-40 md:h-44 xl:h-48 bg-muted overflow-hidden rounded-t-lg">
                    <?php echo $render_status_badges($post['status']); ?>
                    <?php if ($post['thumbnail'] !== '') : ?>
                        <img src="<?php aya_echo($post['thumbnail']); ?>" alt="<?php aya_echo($post['attr_title']); ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                    <?php else : ?>
                        <div class="flex h-full w-full items-center justify-center text-muted-foreground">No Image</div>
                    <?php endif; ?>
                </div>
                <div data-post-grid-body class="flex flex-1 flex-col p-4">
                    <div class="flex items-center overflow-hidden whitespace-nowrap gap-2 text-xs text-muted-foreground mb-2">
                        <span class="flex items-center gap-1">
                            <span class="icon-slot shrink-0" data-icon="calendar" data-icon-size="3"></span>
                            <span><?php aya_echo($post['date']); ?></span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <span class="icon-slot shrink-0" data-icon="views" data-icon-size="3"></span>
                            <span><?php aya_echo($post['views'] . __(' 阅读', 'aiya-cms')); ?></span>
                        </span>
                    </div>
                    <div data-post-grid-categories class="hidden md:flex gap-1 mb-2 overflow-hidden whitespace-nowrap">
                        <?php foreach ($post['cat_list'] as $cat) : ?>
                            <a href="<?php aya_echo($cat['url']); ?>" class="no-underline flex-shrink-0">
                                <span class="inline-flex items-center rounded-md border bg-secondary px-1.5 py-0 text-xs h-5 hover:bg-secondary/80">
                                    <?php aya_echo($cat['name']); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <h3 data-post-grid-title class="line-clamp-2 text-md md:text-lg mb-2 font-semibold">
                        <a href="<?php aya_echo($post['url']); ?>" class="hover:underline focus:outline-none" title="<?php aya_echo($post['attr_title']); ?>">
                            <?php aya_echo($post['title']); ?>
                        </a>
                    </h3>
                    <div data-post-grid-preview class="hidden md:line-clamp-2 text-sm text-muted-foreground"><?php aya_echo($post['preview']); ?></div>
                    <div data-post-grid-footer class="mt-auto flex items-center justify-between pt-4 text-xs text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <?php if (!empty($post['author']['avatar'])) : ?>
                                <img src="<?php aya_echo($post['author']['avatar']); ?>" alt="<?php aya_echo($post['author']['name']); ?>" class="h-6 w-6 rounded-full" />
                            <?php endif; ?>
                            <span class="hidden md:block font-medium"><?php aya_echo($post['author']['name']); ?></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="flex items-center gap-1">
                                <span class="icon-slot shrink-0" data-icon="comments" data-icon-size="3"></span>
                                <span><?php aya_echo($post['comments']); ?></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="icon-slot shrink-0" data-icon="heart" data-icon-size="3"></span>
                                <span><?php aya_echo($post['likes']); ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
