<?php

if (!defined('ABSPATH')) {
    exit;
}

//面包屑参数
AYA_WP_Breadcrumb_Object::add_item(__('订阅功能', 'AIYA'), home_url('sponsor'));
?>
<main class="flex-1 flex flex-col">
    <!-- Full Width Post Page -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <?php aya_vue_load('user-bill-panel', aya_user_sponsor_plan_data()); ?>
    </div>
</main>