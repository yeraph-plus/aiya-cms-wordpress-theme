<?php

if (!defined('ABSPATH')) {
    exit;
}

$none_content_image = aya_opt('site_none_content_upload', 'basic');
?>
<div class="w-full">
    <!-- Empty Category -->
    <div class="card bg-base-100 hover:shadow-xl border border-base-300 rounded-lg mb-8 w-full">
        <div class="card-body items-center text-center p-0">
            <figure class="px-6 pt-6">
                <img src="<?php aya_echo($none_content_image); ?>" alt="none" class="rounded-xl max-h-[300px] w-auto mx-auto object-contain" />
            </figure>
            <div class="card-body">
                <h2 class="card-title text-2xl md:text-3xl justify-center">
                    <?php aya_echo(__('没有内容', 'AIYA')); ?>
                </h2>
                <p class="text-base-content/70 mt-2 mb-6">
                    <?php aya_echo(__('没有找到符合条件的文章内容，请尝试其他搜索条件或浏览其他分类。', 'AIYA')); ?>
                </p>
                <div class="card-actions justify-center">
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">
                        <icon name="home" class="size-4 mr-1"></icon>
                        <?php aya_echo(__('返回首页', 'AIYA')); ?>
                    </a>

                    <button class="btn btn-outline" onclick="history.back()">
                        <icon name="arrow-uturn-left" class="size-4 mr-1"></icon>
                        <?php aya_echo(__('返回上页', 'AIYA')); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>