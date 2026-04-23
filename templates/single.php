<?php

if (!defined('ABSPATH')) {
    exit;
}

//没有文章
if (!have_posts()) {
    //重定向到404
    aya_template_none();
}

//判断文章类型
$post_type = get_post_type();

switch ($post_type) {
    case 'post':
        //判断文章格式
        $post_format = get_post_format();

        switch ($post_format) {
            case 'audio':
            case 'video':
                //返回附件格式
                $content_layout = 'media';
                break;
            case 'gallery':
            case 'image':
            default:
                //返回默认格式
                $content_layout = 'default';
                break;
        }
        break;
    case 'page':
        //返回页面格式
        $content_layout = 'page';
        break;
    case 'attachment':
        //返回附件格式
        $content_layout = 'media';
        break;
    default:
        $content_layout = 'default';
        break;
}

//执行主循环
while (have_posts()) {
    the_post();
    aya_template_load('contents/' . $content_layout);
}

//评论模板
aya_comments_template();
