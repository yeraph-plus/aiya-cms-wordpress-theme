<?php

if (!defined('ABSPATH')) {
    exit;
}

$none_page_image = aya_opt('site_404_page_img_upload', 'basic');
?>
<main class="flex-1 flex flex-col">
    <!-- 404 Not Found -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <div class="card bg-base-100 hover:shadow-xl border border-base-300 rounded-lg mb-8 w-full">
            <div class="card-body items-center text-center p-0">
                <figure class="px-6 pt-6">
                    <img src="<?php aya_echo($none_page_image); ?>" alt="404" class="rounded-xl max-h-[300px] w-auto mx-auto object-contain" />
                </figure>
                <div class="card-body">
                    <h2 class="card-title text-2xl md:text-3xl justify-center">
                        <?php aya_echo(__('没有找到页面', 'AIYA')); ?>
                    </h2>
                    <p class="text-base-content/70 mt-2 mb-6">
                        <?php aya_echo(__('抱歉，您访问的页面不存在或已被删除', 'AIYA')); ?>
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
                    <div class="flex items-center justify-center gap-2 text-sm mt-6">
                        <span class="countdown font-mono text-primary text-xl">
                            <span style="--value:5;" x-text="timeLeft"></span>
                        </span>
                        <?php aya_echo(__('秒后自动返回首页', 'AIYA')); ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let timeLeft = 5;
                const countdownElement = document.querySelector('.countdown span');

                const countdown = setInterval(function () {
                    timeLeft--;
                    countdownElement.style.setProperty('--value', timeLeft);

                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        window.location.href = '<?php echo home_url('/'); ?>';
                    }
                }, 1000);
            });
        </script>
    </div>
</main>