<?php

if (!defined('ABSPATH')) {
    exit;
}

//没有文章
if (!have_posts()) {
    aya_react_island(
        'loop-grid',
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

        $post_thumb = aya_get_post_thumb($post_obj->thumbnail_url, $post_obj->content, 300, 200);

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

        $loop_html .= '<a href="' . esc_url($post_obj->url) . '" class="block h-auto w-full" title="' . esc_attr($post_obj->title) . '" rel="bookmark">' . esc_html($post_obj->attr_title) . '</a>';
    }

    aya_react_island(
        'loop-grid',
        ['posts' => $loop_porps, 'loopTitle' => sprintf(__('搜索"%s"的结果'), get_search_query()), 'showSeparator' => false, 'pageType' => 'search'],
        $loop_html,
    );

    //加载分页
    $paged = aya_get_pagination();

    if (!empty($paged['links'])) {
        aya_react_island('loop-pagination', $paged, aya_get_pagination_html($paged));
    }
}
