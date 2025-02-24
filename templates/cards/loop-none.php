<?php

$none_page_image = aya_opt('site_none_content_upload', 'basic');

?>
<!-- POST ID: none -->
<div class="panel col-span-full group w-full">
    <div class="relative flex items-center justify-center overflow-hidden">
        <div class="px-8 py-8 text-center">
            <div class="relative m-5">
                <img alt="none" class="mx-auto w-[160px] md:w-[360px] object-cover" src="<?php aya_echo($none_page_image); ?>">
                <h5 class="m-10 font-semibold text-1xl md:text-2xl">
                    <?php aya_echo(__('没有文章', 'AIYA')); ?>
                </h5>
            </div>
        </div>
    </div>
</div>