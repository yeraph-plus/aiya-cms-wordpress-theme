<?php

//小工具：热门文章

class AYA_Widget_Post_Views extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = [
            'id' => 'widget-post-top-views',
            'title' => 'AIYA-CMS 热门文章',
            'classname' => 'widget-card-box',
            'desc' => '文章列表卡片，展示点击量最多的文章',
            'field_build' => [
                [
                    'type' => 'input',
                    'id' => 'title',
                    'name' => '标题',
                    'default' => '热门',
                ],
                [
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => '显示数量',
                    'default' => '5',
                ],
            ],
        ];

        return $widget_args;
    }

    function widget_func()
    {
        $title = parent::widget_opt('title');
        $limit = parent::widget_opt('limit');

        //创建查询
        $query_obj = new AYA_WP_Query_Object();
        $query_posts = $query_obj->get_popular_views_posts($limit);
        $posts_data = [];
        foreach ($query_posts as $post) {
            //循环文章对象
            $post_obj = new AYA_Post_In_While($post);

            $post_thumb = aya_get_post_thumb($post_obj->thumbnail_url, $post_obj->id, 150, 100);

            $posts_data[] = [
                'id' => $post_obj->id,
                'url' => (string) $post_obj->url,
                'title' => (string) $post_obj->title,
                'attr_title' => (string) $post_obj->attr_title,
                'thumbnail' => (string) $post_thumb,
                'date' => (string) $post_obj->date,
                'date_ago' => (string) $post_obj->date_ago,
                'views' => (string) $post_obj->views,
                'comments' => (string) $post_obj->comments,
                'likes' => (string) $post_obj->likes,
            ];
        }

        aya_react_island(
            'widget-post',
            ['posts' => $posts_data, 'widgetTitle' => $title, 'postType' => 'views']
        );
    }
}
