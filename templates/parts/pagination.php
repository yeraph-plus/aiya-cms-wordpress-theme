<?php

$paged_nav = aya_pagination_array_get();

if (!$paged_nav) return;

$paged_total_info = $paged_nav['paged_total_info'];
$paged_item = $paged_nav['paged_array'];

?>
<!-- Pagination -->
<div class="loop-pagination w-full px-4 pt-4">
    <?php if (aya_opt('site_loop_paged_type', 'basic') == 'next' || aya_is_mobile()): ?>
        <div class="border-t border-[#e0e6ed] shadow-[4px_6px_10px_-3px_#bfc9d4] px-4 sm:px-0 mt-4 mb-4"></div>
        <nav class="nav-pagination flex items-center" aria-label="Pagination">
            <div class="flex flex-1 justify-start">
                <?php aya_pagination_item_link($paged_item, 'home'); ?>
                <?php aya_pagination_item_link($paged_item, 'prev'); ?>
            </div>
            <div class="hidden sm:flex flex-1 justify-center text-sm text-white-dark">
                <?php aya_echo($paged_total_info) ?>
            </div>
            <div class="flex flex-1 justify-end">
                <?php aya_pagination_item_link($paged_item, 'next'); ?>
            </div>
        </nav>
    <?php elseif (aya_opt('site_loop_paged_type', 'basic') == 'page'): ?>
        <div class="border-t border-[#e0e6ed] shadow-[4px_6px_10px_-3px_#bfc9d4] px-4 sm:px-0 mt-4 mb-4"></div>
        <nav class="nav-pagination flex items-center justify-center">
            <?php aya_pagination_item_link($paged_item); ?>
        </nav>
        <div class="flex flex-2 justify-center p-4">
            <div class="text-sm text-white-dark">
                <?php aya_echo($paged_total_info) ?>
            </div>
        </div>
    <?php elseif (aya_opt('site_loop_paged_type', 'basic') == 'more'): ?>
    <?php endif; ?>
</div>