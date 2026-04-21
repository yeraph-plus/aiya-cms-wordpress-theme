<?php

if (!defined('ABSPATH')) {
    exit;
}

//没有文章
if (!have_posts()) {
    aya_react_island(
        'loop-tweet',
        ['posts' => []]
    );
} else {

    //执行主循环
    $loop_html = '';
    $loop_porps = [];

    while (have_posts()) {
        the_post();

        //提取文章对象
        $post_obj = new AYA_Post_In_While();

        //添加到数组
        $loop_porps[] = [
            'id' => $post_obj->id,
            'url' => (string) $post_obj->url,
            'title' => (string) $post_obj->title,
            'attr_title' => (string) $post_obj->attr_title,
            'content' => (string) $post_obj->content,
            'date' => (string) $post_obj->date,
            'date_iso' => (string) $post_obj->date_iso,
            'comments' => (string) $post_obj->comments,
            'likes' => (string) $post_obj->likes,
            'author' => [
                'name' => (string) $post_obj->author_name,
                'avatar' => (string) $post_obj->author_avatar_x32,
            ],
        ];

        $loop_html .= '<a href="' . esc_url($post_obj->url) . '" class="block h-auto w-full" title="' . esc_attr($post_obj->title) . '" rel="bookmark">' . esc_html($post_obj->attr_title) . '</a>';
    }

    aya_react_island(
        'loop-tweet',
        ['posts' => $loop_porps, 'loopTitle' => '推文'],
        $loop_html,
    );

    //加载分页
    aya_template_part_load('pagination', ['paged' => aya_get_pagination()]);
}
