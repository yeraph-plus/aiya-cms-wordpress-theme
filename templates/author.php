<?php

if (!defined('ABSPATH')) {
    exit;
}

// 获取当前作者对象
$author_obj = get_queried_object();
$author_id = $author_obj->ID;

// 准备用户详情组件数据
$author_props = [
    'avatar' => get_avatar_url($author_id, ['size' => 128]),
    'name' => $author_obj->display_name,
    'role' => aya_user_toggle_level($author_id),
    'description' => get_the_author_meta('description', $author_id),
];

// 渲染用户详情组件
aya_react_island(
    'loop-author',
    $author_props,
    '<div class="mb-8 p-6 bg-gray-50 rounded-lg flex items-center gap-6">
        <img src="' . esc_url($author_props['avatar']) . '" alt="' . esc_attr($author_props['name']) . '" class="w-24 h-24 rounded-full">
        <div>
            <h1 class="text-2xl font-bold mb-2">' . esc_html($author_props['name']) . '</h1>
            <p class="text-gray-600">' . esc_html($author_props['description']) . '</p>
        </div>
    </div>'
);

//没有文章
if (!have_posts()) {
    aya_react_island(
        'loop-grid',
        ['posts' => []],
        __('没有文章', 'AIYA'),
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

    $author = get_queried_object()->display_name;

    aya_react_island(
        'loop-grid',
        ['posts' => $loop_porps, 'loopTitle' => __('用户：「') . $author . __('」'), 'showSeparator' => true, 'pageType' => 'author'],
        $loop_html,
    );

    //加载分页
    $paged = aya_get_pagination();

    if (!empty($paged['links'])) {
        aya_react_island('loop-pagination', $paged, aya_get_pagination_html($paged));
    }
}
