<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 外链提示模板
 * ------------------------------------------------------------------------------
 */

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

//面包屑参数
AYA_WP_Breadcrumb_Object::add_item(__('外部链接提示', 'AIYA'), '#');
?>
<main class="flex-1 flex flex-col">
    <!-- External Link Tips -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <div class="card bg-base-100 hover:shadow-xl border border-base-300 rounded-lg mb-8 w-full">
            <div class="card-body items-center text-center">
                <?php if ($checked): ?>
                    <figure class="px-6 pt-6">
                        <img src="<?php aya_echo($external_page_image); ?>" alt="External link" class="rounded-xl max-h-[300px] w-auto mx-auto object-contain" />
                    </figure>
                    <h2 class="card-title text-2xl mt-6">
                        <?php aya_echo(__('即将离开', 'AIYA')); ?>
                    </h2>
                    <p class="text-base-content/80 my-4">
                        <?php aya_echo(__('您即将离开', 'AIYA') . $blog_name . __('，此链接将带您前往外部网站。', 'AIYA')); ?>
                    </p>
                    <div class="card-actions mt-4">
                        <a href="<?php aya_echo($external_url); ?>" class="btn btn-primary btn-md gap-2" target="_blank">
                            <icon name="arrow-uturn-right" class="size-4 mr-1"></icon>
                            <?php aya_echo(__('前往', 'AIYA')); ?>
                        </a>
                    </div>
                    <div class="badge badge-neutral badge-sm mt-2 truncate max-w-xs">
                        <?php aya_echo($external_url); ?>
                    </div>
                <?php else: ?>
                    <h2 class="card-title text-2xl mt-6 flex items-center gap-2 text-error">
                        <?php aya_echo(__('错误请求', 'AIYA')); ?>
                    </h2>
                    <p class="text-error mt-2">
                        <?php aya_echo(__('当前页面是通过外部来源打开的，请谨慎访问。', 'AIYA')); ?>
                    </p>
                    <div class="mt-4">
                        <p class="text-sm opacity-70">
                            <?php aya_echo(__('如需访问，请手动复制链接：', 'AIYA')); ?>
                            <span class="mt-2 truncate max-w-xs">
                                <?php aya_echo($external_url); ?>
                            </span>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>