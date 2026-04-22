<?php

if (!defined('ABSPATH')) {
    exit;
}

//没有文章
if (!have_posts()) {
    //没有文章
    aya_react_island('ui-empty', [
        'title' => __('暂无内容', 'aiya-cms'),
        'description' => __('当前没有任何文章被分类到此归档', 'aiya-cms'),
    ]);
} else {
    //执行主循环
    $loop_porps = [];

    while (have_posts()) {
        the_post();

        //提取文章对象
        $post_obj = new AYA_Post_In_While();
        $post_thumb = aya_get_post_thumb($post_obj->thumbnail_url, $post_obj->id, 300, 200);

        //添加到数组
        $loop_porps[] = [
            'id' => $post_obj->id,
            'url' => (string) $post_obj->url,
            'title' => (string) $post_obj->title,
            'attr_title' => (string) $post_obj->attr_title,
            'thumbnail' => (string) $post_thumb,
            'preview' => (string) $post_obj->preview,
            'date' => (string) $post_obj->date,
            'date_iso' => (string) $post_obj->date_iso,
            'views' => (string) $post_obj->views,
            'comments' => (string) $post_obj->comments,
            'likes' => (string) $post_obj->likes,
            'status' => (array) $post_obj->status,
            'cat_list' => (array) $post_obj->cat_list,
            'author' => [
                'name' => (string) $post_obj->author_name,
                'avatar' => (string) $post_obj->author_avatar_x32,
            ],
        ];
    }

    aya_template_part_load('loop-grid', [
        'posts' => $loop_porps,
        'label_icon' => 'inbox',
        'label_title' => single_term_title('', false),
        'is_main_loop' => true,
    ]);

    //加载分页
    aya_template_part_load('pagination', ['paged' => aya_get_pagination()]);
}
