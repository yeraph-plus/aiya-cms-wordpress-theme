<?php

if (!defined('ABSPATH')) {
    exit;
}

$route_page = aya_is_where();
$is_pre_paged = ($route_page == 'home_pre');
//创建查询
$query_obj = new AYA_WP_Query_Object();

//首页动作钩子
aya_home_open();

//获取轮播组件
$carousel_section = aya_opt('site_carousel_section_type', 'land');

if ($carousel_section !== 'off' && !$is_pre_paged) {

    $carousel_items = aya_opt('site_carousel_section_item_mult', 'land');

    //循环设置数据查询文章
    $carousel_list = [];

    if (is_array($carousel_items)) {
        foreach ($carousel_items as $item) {

            $c_item = $item;

            //不是 URL 时创建查询
            if (!empty($item['url']) && !cur_is_url($item['url'])) {
                $carousel_post = $query_obj->get_post($item['url']);

                //查询到了文章
                if (is_object($carousel_post)) {

                    $post_obj = new AYA_Post_In_While($carousel_post);

                    //使用文章内容替换
                    $c_item['url'] = $post_obj->url;

                    if (empty($c_item['thumbnail'])) {
                        $c_item['thumbnail'] = aya_get_post_thumb($post_obj->thumbnail_url, $post_obj->content, 1200, 640);
                    }
                    if (empty($c_item['title'])) {
                        $c_item['title'] = $post_obj->attr_title;
                    }
                    if (empty($c_item['description'])) {
                        $c_item['description'] = $post_obj->preview;
                    }
                }
            }

            //图片是否存在，没有图片时跳过数据
            if (empty($c_item['thumbnail'])) {
                continue;
            }

            //尝试压缩原图
            $try_to_thumb = aya_get_post_thumb($c_item['thumbnail'], '', 1200, 640);

            if ($try_to_thumb !== false) {
                $c_item['thumbnail'] = $try_to_thumb;
            }

            //添加到轮播列表
            $carousel_list[] = $c_item;
        }
    }

    if (!empty($carousel_list)) {
        aya_react_island(
            'loop-carousel',
            ['posts' => $carousel_list, 'layout' => $carousel_section]
        );
    }
}

//获取独立文章查询
$post_sections = aya_opt('site_post_home_section_mult', 'land');

if (!empty($post_sections) && !$is_pre_paged) {
    //循环查询文章分类
    foreach ($post_sections as $section) {
        $section_posts = $query_obj->get_posts_by_category($section['category_ids'], $section['limit'], $section['orderby']);

        $section_posts_data = [];

        //循环文章对象
        foreach ($section_posts as $section_post) {
            $section_post_obj = new AYA_Post_In_While($section_post);

            $section_post_thumb = aya_get_post_thumb($section_post_obj->thumbnail_url, $section_post_obj->content, 300, 200);

            $section_posts_data[] = [
                'id' => $section_post_obj->id,
                'url' => (string) $section_post_obj->url,
                'title' => (string) $section_post_obj->title,
                'attr_title' => (string) $section_post_obj->attr_title,
                'thumbnail' => (string) $section_post_thumb,
                'preview' => (string) $section_post_obj->preview,
                'date' => (string) $section_post_obj->date,
                'date_iso' => (string) $section_post_obj->date_iso,
                'views' => (string) $section_post_obj->views,
                'comments' => (string) $section_post_obj->comments,
                'likes' => (string) $section_post_obj->likes,
                'status' => (array) $section_post_obj->status,
                'cat_list' => (array) $section_post_obj->cat_list,
                'author' => [
                    'name' => (string) $section_post_obj->author_name,
                    'avatar' => (string) $section_post_obj->author_avatar_x32,
                ],
            ];
        }

        aya_react_island(
            'loop-section',
            ['posts' => $section_posts_data, 'loopTitle' => $section['title']]
        );
    }
}

if (!have_posts()) {
    //没有文章
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
        ['posts' => $loop_porps, 'loopTitle' => '首页', 'showSeparator' => true, 'pageType' => 'index'],
        $loop_html,
    );

    //加载分页
    $paged = aya_get_pagination();

    if (!empty($paged['links'])) {
        aya_react_island('loop-pagination', $paged, aya_get_pagination_html($paged));
    }
}

//首页动作钩子
aya_home_end();
