<?php

// 是否显示特色图片
$has_thumbnail = !empty($thumbnail_url) ? $thumbnail_url : '';
// 状态徽章，使用 badge-slot 运行时渲染
$render_status_badges = static function ($status_list) {
    if (!is_array($status_list) || $status_list === []) {
        return;
    }

    $html = '';
    foreach ($status_list as $key => $label) {
        $html .= '<span class="badge-slot" data-badge-alias="' . esc_attr($key) . '">';
        $html .= '<span>' . esc_html($label) . '</span>';
        $html .= '</span>';
    }

    echo $html;
};
// 提示条目，使用 alert-slot 运行时渲染
$render_alert_tips = static function ($tips) {
    if (!is_array($tips) || $tips === []) {
        return;
    }
    $html = '';
    foreach ($tips as $index => $tip) {
        $html .= '<div data-alert-slot data-alert-level="' . esc_attr($tip['alert']) . '" data-alert-title="' . esc_attr($tip['name']) . '" data-alert-description="' . esc_attr($tip['description']) . '">';
        $html .= '<div class="font-medium tracking-tight">' . esc_html($tip['name']) . '</div>';
        $html .= '<div class="text-sm text-muted-foreground mt-1">' . esc_html($tip['description']) . '</div>';
        $html .= '</div>';
    }
    echo $html;
};
// 循环分类标签
$render_categories = static function ($categories) {
    if (!is_array($categories) || $categories === []) {
        return;
    }
    $html = '';
    $html .= '<span class="text-muted-foreground">' . __('分类：', 'aiya-cms') . '</span>';
    foreach ($categories as $index => $cat) {
        $html .= '<a href="' . esc_attr($cat['url']) . '" class="bg-primary/90 text-primary-foreground hover:bg-primary/80 text-sm px-2 py-1 rounded-md">';
        $html .= esc_html($cat['name']);
        $html .= '</a>';
    }
    echo $html;
};
// 循环标签
$render_tags = static function ($tags) {
    if (!is_array($tags) || $tags === []) {
        return;
    }
    $html = '';
    foreach ($tags as $index => $tag) {
        $html .= '<a href="' . esc_attr($tag['url']) . '" text-muted-foreground hover:text-primary transition-colors no-underline">';
        $html .= esc_html('# ' . $tag['name']);
        $html .= '</a>';
    }
    echo $html;
};

?>
<div class="relative mb-8">
    <?php if ($has_thumbnail) :
        //获取特色图片生成裁剪
        $thumbnail = aya_get_post_thumb($thumbnail_url, 0, 1200, 640);
    ?>
        <div class="relative w-full h-60 md:h-72 lg:h-96 rounded-lg overflow-hidden mb-6">
            <img src="<?php aya_echo($thumbnail); ?>" alt="<?php aya_echo($title); ?>" class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 z-10">
                <div class="flex flex-col gap-4">
                    <h1 class="text-xl md:text-3xl lg:text-4xl font-bold leading-tight flex flex-wrap items-center gap-3 text-white drop-shadow-lg">
                        <?php aya_echo($title); ?>
                        <?php $render_status_badges($status); ?>
                    </h1>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-4 text-sm text-white/90">
                            <a href="<?php aya_echo(($author['url'] ?? '#')); ?>" class="flex items-center gap-2 hover:opacity-80 transition-opacity" title="<?php aya_echo($author['name']); ?>">
                                <img src="<?php aya_echo($author['avatar']); ?>" alt="<?php aya_echo($author['name']); ?>" class="h-8 w-8 rounded-full border border-border object-cover" />
                                <span class="font-medium"><?php aya_echo($author['name']); ?></span>
                            </a>
                            <div class="flex items-center gap-1.5" title="<?php aya_echo($date_iso); ?>">
                                <span class="icon-slot shrink-0" data-icon="calendar" data-icon-size="4"></span>
                                <time datetime="<?php aya_echo($date_iso); ?>"><?php aya_echo($date); ?></time>
                                <span class="opacity-80"><?php aya_echo(sprintf(__('[ 上次更新于 %s ]', 'aiya-cms'), $modified_ago)); ?></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="icon-slot shrink-0" data-icon="views" data-icon-size="4"></span>
                                <span><?php aya_echo($views); ?></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="icon-slot shrink-0" data-icon="heart" data-icon-size="4"></span>
                                <span><?php aya_echo($likes); ?></span>
                            </div>
                        </div>
                        <a href="#comments" class="inline-flex items-center justify-center gap-2 rounded-md px-3 py-2 text-sm border-none backdrop-blur-sm transition-colors bg-white/20 hover:bg-white/30 text-white">
                            <span class="icon-slot shrink-0" data-icon="comments" data-icon-size="4"></span>
                            <span><?php aya_echo($comments); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mb-4 pb-4 border-b border-border">
            <div class="flex flex-col gap-4">
                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold leading-tight flex flex-wrap items-center gap-3 text-foreground">
                    <?php aya_echo($title); ?>
                    <?php $render_status_badges($status); ?>
                </h1>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                        <a href="<?php aya_echo($author['url']); ?>" class="flex items-center gap-2 hover:opacity-80 transition-opacity" title="<?php aya_echo($author['name']); ?>">
                            <img src="<?php aya_echo($author['avatar']); ?>" alt="<?php aya_echo($author['name']); ?>" class="h-8 w-8 rounded-full border border-border object-cover" />
                            <span class="font-medium text-base-content"><?php aya_echo($author['name']); ?></span>
                        </a>
                        <div class="flex items-center gap-1.5" title="<?php aya_echo($date_iso); ?>">
                            <span class="icon-slot shrink-0" data-icon="calendar" data-icon-size="4"></span>
                            <time datetime="<?php aya_echo($date_iso); ?>"><?php aya_echo($date); ?></time>
                            <span class="opacity-80"><?php aya_echo(sprintf(__('[ 上次更新于 %s ]', 'aiya-cms'), $modified_ago)); ?></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="icon-slot shrink-0" data-icon="views" data-icon-size="4"></span>
                            <span><?php aya_echo($views); ?></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="icon-slot shrink-0" data-icon="heart" data-icon-size="4"></span>
                            <span><?php aya_echo($likes); ?></span>
                        </div>
                    </div>

                    <a href="#comments" class="inline-flex items-center justify-center gap-2 rounded-md border px-3 py-2 text-sm transition-colors bg-background hover:bg-muted/80">
                        <span class="icon-slot shrink-0" data-icon="comments" data-icon-size="4"></span>
                        <span><?php aya_echo($comments); ?></span>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="flex flex-wrap items-center gap-3 my-4 text-sm">
        <?php $render_categories($categories); ?>
        <?php $render_tags($tags); ?>
    </div>
    <div class="flex flex-col gap-4 my-4">
        <?php $render_alert_tips($alert_tips); ?>
    </div>
</div>