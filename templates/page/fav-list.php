<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 收藏夹页面
 * ------------------------------------------------------------------------------
 */

$favorites = aya_user_get_favorite_posts();
$the_posts = [];
$store_nonce = '';

if ($favorites !== false && is_array($favorites)) {
    //循环查询结果
    $the_posts = [];
    foreach ($favorites as $post) {
        $post = new AYA_Post_In_While($post);

        $the_posts[$post->id] = [
            'id' => $post->id,
            'date' => $post->date,
            'modified' => $post->modified,
            'title' => $post->title,
            'status' => $post->status,
            'url' => $post->url,
        ];
    }
    //交互功能安全参数
    $store_nonce = aya_nonce_active_store();
}

//面包屑参数
AYA_WP_Breadcrumb_Object::add_item(__('收藏列表', 'AIYA'), home_url('favlist'));
?>
<main class="flex-1 flex flex-col">
    <!-- Full Width Post Page -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <?php aya_vue_load('user-fav-panel', [
            'ajax-url' => admin_url('admin-ajax.php'),
            'posts' => $the_posts,
            'nonce' => $store_nonce
        ]); ?>
    </div>
</main>