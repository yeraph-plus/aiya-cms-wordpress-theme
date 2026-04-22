<?php

//获取声明文本
$statement_content = aya_opt('site_post_statement_text', 'land');
// 是否显示交互按钮
$use_interaction = (bool) $use_interaction ?? false;

?>
<footer class="my-8 space-y-8">
    <?php if ($use_interaction) : ?>
        <div class="flex items-center justify-center md:justify-center">
            <?php
            aya_react_island('content-button-group', [
                'post_id' => $post_id,
                'likes' => $likes,
                'is_favorite' => $is_favorite,
            ]);
            ?>
        </div>
    <?php endif; ?>
    <div class="flex items-center gap-4 text-muted-foreground/50">
        <div class="h-px bg-border flex-1"></div>
        <span class="text-xs font-semibold uppercase tracking-widest">The End</span>
        <div class="h-px bg-border flex-1"></div>
    </div>
    <div class="bg-muted/30 rounded-lg px-6 py-4 text-center">
        <p class="text-sm text-muted-foreground leading-relaxed mx-auto"><?php aya_echo(aya_preg_desc($statement_content)); ?></p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (!empty($prev_post['url']) && !empty($prev_post['title'])) : ?>
            <a href="<?php aya_echo($prev_post['url']); ?>" class="group relative flex flex-col p-6 rounded-lg border border-border bg-card hover:bg-muted/50 transition-all hover:border-primary/50">
                <div class="flex items-center gap-2 text-xs font-medium text-muted-foreground mb-2">
                    <span class="icon-slot shrink-0" data-icon="arrow-left" data-icon-size="4"></span>
                    <span><?php aya_echo('上一篇', 'aiya-cms'); ?></span>
                </div>
                <h3 class="text-lg font-semibold group-hover:text-primary transition-colors line-clamp-2"><?php aya_echo($prev_post['title']); ?></h3>
            </a>
        <?php else : ?>
            <div class="hidden md:block"></div>
        <?php endif; ?>

        <?php if (!empty($next_post['url']) && !empty($next_post['title'])) : ?>
            <a href="<?php aya_echo($next_post['url']); ?>" class="group relative flex flex-col items-end text-right p-6 rounded-lg border border-border bg-card hover:bg-muted/50 transition-all hover:border-primary/50">
                <div class="flex items-center gap-2 text-xs font-medium text-muted-foreground mb-2">
                    <span><?php aya_echo('下一篇', 'aiya-cms'); ?></span>
                    <span class="icon-slot shrink-0" data-icon="arrow-right" data-icon-size="4"></span>
                </div>
                <h3 class="text-lg font-semibold group-hover:text-primary transition-colors line-clamp-2"><?php aya_echo($next_post['title']); ?></h3>
            </a>
        <?php else : ?>
            <div class="hidden md:block"></div>
        <?php endif; ?>
    </div>
</footer>