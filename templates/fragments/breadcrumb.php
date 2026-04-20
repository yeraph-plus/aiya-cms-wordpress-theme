<?php

if (empty($items) || !is_array($items) || count($items) <= 1) {
    return;
}

?>
<nav aria-label="breadcrumb" data-slot="breadcrumb" class="my-4">
    <ol data-slot="breadcrumb-list" class="text-muted-foreground flex flex-wrap items-center gap-1.5 text-sm break-words sm:gap-2.5 flex-nowrap overflow-hidden">
        <li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5 shrink-0">
            <span class="icon-slot" data-icon="navigation"></span>
        </li>
        <?php foreach ($items as $index => $item) :
            $is_last = ($index === count($items) - 1);
        ?>
            <li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5 whitespace-nowrap min-w-0">
                <?php if ($is_last) : ?>
                    <span data-slot="breadcrumb-page" role="link" aria-disabled="true" aria-current="page" class="text-foreground font-normal truncate max-w-[240px] sm:max-w-[400px] md:max-w-[400px] block">
                        <?php echo esc_html((string) ($item['label'] ?? '')); ?>
                    </span>
                <?php else : ?>
                    <a data-slot="breadcrumb-link" href="<?php echo esc_url((string) ($item['url'] ?? '#')); ?>" class="hover:text-foreground transition-colors truncate max-w-[160px] sm:max-w-[240px] md:max-w-[400px] block">
                        <?php echo esc_html((string) ($item['label'] ?? '')); ?>
                    </a>
                <?php endif; ?>
            </li>
            <?php if (!$is_last) : ?>
                <li data-slot="breadcrumb-separator" role="presentation" aria-hidden="true" class="[&>svg]:size-3.5 shrink-0">
                    <span class="icon-slot" data-icon="chevron-right" data-icon-class="h-3.5 w-3.5"></span>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>