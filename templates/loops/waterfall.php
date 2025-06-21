<?php

if (!defined('ABSPATH')) {
    exit;
}

//TODO

//执行主循环
while (have_posts()) {
    the_post();
    $post_obj = new AYA_Post_In_While();

    $post_thumb = aya_the_post_thumb($post_obj->thumbnail_url, $post_obj->content, 400, 0);
}

?>
<!-- POST ID: <?php aya_echo($post_obj->id); ?> -->