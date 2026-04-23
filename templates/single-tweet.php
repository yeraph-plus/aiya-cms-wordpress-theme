<?php

if (!defined('ABSPATH')) {
    exit;
}

//没有文章
if (!have_posts()) {
    //重定向到404
    aya_template_none();
}

//执行主循环
while (have_posts()) {
    the_post();
    the_content();
}

//评论模板
aya_comments_template();