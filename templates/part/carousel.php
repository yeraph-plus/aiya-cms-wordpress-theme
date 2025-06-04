<?php

if (!defined('ABSPATH')) {
    exit;
}

//仅在首页显示轮播组件
if (aya_is_where() !== 'home' || !aya_opt('site_carousel_section_bool', 'land')) {
    return '';
}

$carousel_opt_mult = aya_opt('site_carousel_post_list', 'land');
$carousel_layout = aya_opt('site_carousel_template_type', 'land');

//循环设置数据查询文章
$carousel_list = [];

foreach ($carousel_opt_mult as $item) {

    $self_item = $item;

    //不是 URL 时创建查询
    if (!empty($item['url']) && !cur_is_url($item['url'])) {
        $query = new AYA_Query_Post();
        $the_post = $query->get_post($item['url']);

        //查询到了文章
        if (is_object($the_post)) {

            $post_obj = new AYA_Post_In_While($the_post);

            //使用文章内容替换
            $self_item['url'] = $post_obj->url;

            if (empty($self_item['thumbnail'])) {
                $self_item['thumbnail'] = aya_the_post_thumb($post_obj->thumbnail_url, $post_obj->content, 1200, 640);
            }
            if (empty($self_item['title'])) {
                $self_item['title'] = $post_obj->title;
            }
            if (empty($self_item['description'])) {
                $self_item['description'] = $post_obj->get_post_preview(100);
            }
        }
    }

    //图片是否存在，没有图片时跳过数据
    if (empty($self_item['thumbnail'])) {

        continue;
    } else {
        //尝试压缩原图
        $try_to_thumb = aya_the_post_thumb($self_item['thumbnail'], '', 1200, 640);

        if ($try_to_thumb !== false) {
            $self_item['thumbnail'] = $try_to_thumb;
        }
    }
    //添加到轮播列表
    $carousel_list[] = $self_item;
}

aya_vue_load('carousel', [
    'post-data' => $carousel_list,
    'layout-type' => $carousel_layout,
]);