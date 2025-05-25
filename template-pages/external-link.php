<?php

$blog_name = get_bloginfo('name');
$external_page_image = aya_opt('site_ext_page_img_upload', 'basic');
//验证来源
$checked = aya_home_url_referer_check();
//获取URL参数
$external_url = isset($_GET['target']) ? esc_url($_GET['target']) : '';

//不是网址时直接抛回404
if (!cur_is_url($external_url)) {
    return wp_redirect(home_url('/404'));
}
//页头
aya_template_load('header');
?>
<div class="main-boxed external-link w-full p-4">
    <div class="panel w-auto">
        <div class="relative flex items-center justify-center overflow-hidden">
            <div class="px-8 py-8 text-center before:container before:absolute before:left-1/2 before:-translate-x-1/2 before:rounded-full before:bg-[linear-gradient(180deg,#4361EE_0%,rgba(67,97,238,0)_50.73%)] before:aspect-square before:opacity-10 md:py-20">
                <div class="relative m-5">
                    <img alt="loading" class="mx-auto w-[360px] max-w-xs object-cover md:-mt-16 md:max-w-xl" src="<?php aya_echo($external_page_image); ?>">
                    <?php if ($checked): ?>
                        <h5 class="m-10 font-semibold text-base md:text-2xl">
                            <?php aya_echo(__('即将离开', 'AIYA')); ?>
                        </h5>
                        <p class="m-5 flex font-semibold text-base">
                            <?php aya_echo(__('您即将离开', 'AIYA') . $blog_name . __('，此链接将带您前往外部网站。', 'AIYA')); ?>
                        </p>
                        <p class="m-10 flex items-center justify-center font-normal text-base">
                            <a href="<?php aya_echo($external_url); ?>" class="btn btn-primary underline mx-1 text-primary flex items-center justify-center" target="_blank">
                                <i data-feather="external-link" width="16" height="16" class="mr-2"></i>
                                <?php aya_echo($external_url); ?>
                            </a>
                        </p>
                    <?php else: ?>
                        <h5 class="m-10 flex items-center justify-center font-semibold text-base md:text-2xl">
                            <i data-feather="alert-triangle" width="28" height="28" class="mr-2" stroke-width="3"></i>
                            <?php aya_echo(__('错误请求', 'AIYA')); ?>
                        </h5>
                        <p class="m-5 flex items-center justify-center font-semibold text-base text-red-500">
                            <?php aya_echo(__('当前页面是是通过外部来源打开的，请谨慎访问。', 'AIYA')); ?>
                        </p>
                        <p class="m-10 flex items-center justify-center font-normal text-base">
                            <?php aya_echo(__('如需访问，请手动复制链接访问：', 'AIYA') . $external_url); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
//页脚
aya_template_load('footer');
?>