<?php

$end_divider = !empty($hydrate_props['endDivider']);
$statement_text = $hydrate_props['statementText'] ?? '';
$prev_post = $hydrate_props['prevPost'] ?? null;
$next_post = $hydrate_props['nextPost'] ?? null;
?>
<footer class="my-8 space-y-8">
    <?php if ($end_divider) : ?>
        <div class="flex items-center gap-4 text-muted-foreground/50">
            <div class="h-px bg-border flex-1"></div>
            <span class="text-xs font-semibold uppercase tracking-widest">The End</span>
            <div class="h-px bg-border flex-1"></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($statement_text)) : ?>
        <div class="bg-muted/30 rounded-lg p-6 text-center">
            <p class="text-sm text-muted-foreground leading-relaxed mx-auto"><?php echo esc_html($statement_text); ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (!empty($prev_post['url']) && !empty($prev_post['title'])) : ?>
            <a href="<?php echo esc_url($prev_post['url']); ?>" class="group relative flex flex-col p-6 rounded-lg border border-border bg-card hover:bg-muted/50 transition-all hover:border-primary/50">
                <div class="flex items-center gap-2 text-xs font-medium text-muted-foreground mb-2">
                    <span>上一篇</span>
                </div>
                <h3 class="text-lg font-semibold group-hover:text-primary transition-colors line-clamp-2"><?php echo esc_html($prev_post['title']); ?></h3>
            </a>
        <?php else : ?>
            <div class="hidden md:block"></div>
        <?php endif; ?>

        <?php if (!empty($next_post['url']) && !empty($next_post['title'])) : ?>
            <a href="<?php echo esc_url($next_post['url']); ?>" class="group relative flex flex-col items-end text-right p-6 rounded-lg border border-border bg-card hover:bg-muted/50 transition-all hover:border-primary/50">
                <div class="flex items-center gap-2 text-xs font-medium text-muted-foreground mb-2">
                    <span>下一篇</span>
                </div>
                <h3 class="text-lg font-semibold group-hover:text-primary transition-colors line-clamp-2"><?php echo esc_html($next_post['title']); ?></h3>
            </a>
        <?php else : ?>
            <div class="hidden md:block"></div>
        <?php endif; ?>
    </div>
</footer>
