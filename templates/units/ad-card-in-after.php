<?php

if (!defined('ABSPATH')) {
    exit;
}

?>
<?php if (aya_opt('site_ad_home_after', 'ads') !== ''): ?>
    <!-- Advertisement -->
    <div class="relative flex-shrink-0 w-auto p-4">
        <div class="border border-base-300 rounded-xl bg-base-200 transition-all p-2">
            <div class="absolute flex m-4 badge">
                <?php aya_echo(__('推广', 'AIYA')); ?>
            </div>
            <?php aya_echo(aya_opt('site_ad_home_after', 'ads')); ?>
        </div>
    </div>
<?php endif; ?>