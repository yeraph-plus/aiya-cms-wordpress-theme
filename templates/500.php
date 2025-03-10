<?php

$none_page_image = aya_opt('site_501_page_img_upload', 'basic');

//面包屑
aya_template_load('parts/breadcrumb');

?>
<!-- 501 -->
<div class="main-boxed 501-not-implemented w-full p-4">
    <div class="panel w-auto">
        <div class="relative flex items-center justify-center overflow-hidden">
            <div class="px-8 py-8 text-center before:container before:absolute before:left-1/2 before:-translate-x-1/2 before:rounded-full before:bg-[linear-gradient(180deg,#4361EE_0%,rgba(67,97,238,0)_50.73%)] before:aspect-square before:opacity-10 md:py-20">
                <div class="relative m-5">
                    <img alt="501" class="mx-auto w-[360px] max-w-xs object-cover md:-mt-16 md:max-w-xl" src="<?php aya_echo($none_page_image); ?>">
                    <h5 class="m-10 font-semibold text-base md:text-2xl">
                        <?php aya_echo(__('内部错误', 'AIYA')); ?>
                    </h5>
                    <p class="m-5 flex items-center justify-center text-base">
                        <a href="/" class="underline mx-1 text-primary">
                            <?php aya_echo(__('返回首页', 'AIYA')); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>