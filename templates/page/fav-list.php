<?php

if (!defined('ABSPATH')) {
    exit;
}

//面包屑参数
AYA_WP_Breadcrumb_Object::add_item(__('收藏列表', 'AIYA'), home_url('favlist'));
?>
<main class="flex-1 flex flex-col">
    <!-- Full Width Post Page -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <?php aya_vue_load('user-fav-panel', aya_user_favorite_posts_data()); ?>
    </div>
</main>