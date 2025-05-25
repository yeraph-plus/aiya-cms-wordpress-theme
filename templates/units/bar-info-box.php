<?php
if (aya_opt('site_info_box_bool', 'land')): ?>
    <!-- Info Box -->
    <div class="flex-shrink-0 p-4 w-auto">
        <div class="p-4 border border-base-200 rounded-md bg-base-200/50 transition-all duration-300 ease-in-out">
            <h5 class="text-base text-primary font-bold mb-3 ">
                <?php aya_echo(aya_opt('site_info_title_text', 'land')); ?>
            </h5>
            <p class="text-base-content/70">
                <?php aya_echo(aya_preg_desc(aya_opt('site_info_desc_text', 'land'))); ?>
            </p>
        </div>
    </div>
<?php endif; ?>