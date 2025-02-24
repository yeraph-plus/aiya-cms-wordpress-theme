<?php

//小工具：随机文章

class AYA_Widget_Post_Random extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-post-random',
            'title' => 'AIYA-CMS 随机文章',
            'classname' => 'widget-panel',
            'desc' => '文章列表卡片，展示随机的文章',
            'field_build' => array(
                [
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => '显示数量',
                    'default' => '5',
                ],
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $limit = parent::widget_opt('limit');
        //自定义Query查询
        $args = array(
            'orderby' => 'rand',
            'order' => 'DESC',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $limit,
            'paged'    => '1',
            'post_type' => 'post'
        );

        $post = new AYA_Post_Query();

        $the_query = $post->query($args);

        $html = '';
        $html .= '<div class="widget-content flex flex-col gap-4">';

        //生成文章列表
        if ($the_query == false) {
            $html .= '<span class="text-center text-base text-gray-500 m-4">' . __('暂无文章', 'AIYA') . '</span>';
        } else {
            //循环
            foreach ($the_query as $post => $post_data) {
                $post_thumb = aya_post_thumb($post_data['thumb_url'], $post_data['id'], 200, 150);

                $html .= '<div class="relative flex items-center group">';
                $html .= '<img class="w-[100px] h-[75px] rounded-lg mr-2" src="' . $post_thumb . '" alt="thumb">';
                $html .= '<div class="flex flex-col">';
                $html .= '<a class="text-sm font-bold group-hover:text-primary transition-all duration-300 line-clamp-2 mb-2" href="' . $post_data['url'] . '" title="' . $post_data['attr_title'] . '">' . $post_data['title'] . '</a>';
                $html .= '<span class="flex items-center text-sm text-gray-500"><i data-feather="eye" width="16" height="16" class="mr-1"></i>' . $post_data['views'] . ' ' . __('次浏览', 'AIYA') . '<i data-feather="heart" width="16" height="16" class="ml-3 mr-1"></i>' . $post_data['likes'] . ' ' . __('次点赞', 'AIYA') . '</span>';
                $html .= '</div></div>';
            }
        }
        $html .= '</div>';

        aya_echo($html);
    }
}
