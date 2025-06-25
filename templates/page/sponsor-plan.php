<?php

if (!defined('ABSPATH')) {
    exit;
}

$page_id = aya_opt('site_sponsor_description_page', 'access');
$page_title = get_post($page_id)->post_title;
$page_content = apply_filters('the_content', get_post($page_id)->post_content);

//面包屑参数
AYA_WP_Breadcrumb_Object::add_item(__('订阅功能', 'AIYA'), home_url('sponsor'));
?>
<main class="flex-1 flex flex-col">
    <!-- Full Width Post Page -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <div class="bg-base-100 border border-base-300 rounded-lg">
            <?php aya_vue_load('user-bill-panel', aya_user_sponsor_plan_data()); ?>
            <?php if (!empty($page_id)): ?>
                <div class="divider m-6 px-6 lg:px-12 font-semibold text-base-content/50">
                    <?php aya_echo($page_title); ?>
                </div>
                <!-- The Page -->
                <div class="article-content p-6 prose prose-base lg:prose-lg max-w-none">
                    <?php aya_echo($page_content); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>