<?php

//小工具：热门文章

class AYA_Widget_Post_Views extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-post-views',
            'title' => 'AIYA-CMS 热门文章',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏文章列表，展示点击量最多的文章',
            'field_build' => array(
                array(
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => '显示数量',
                    'default' => '5',
                ),
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $limit = parent::widget_opt('limit');
        //自定义Query查询
        $args = array(
            'date_query' => array(
                'after' => date('Y-m-d', strtotime('-30 days')),
                'inclusive' => true,
            ),
            'meta_key' => 'views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $limit,
            'paged'    => '1',
            'post_type' => 'post'
        );
        $the_post = aya_get_query($args);

        //生成文章列表
        echo '<ul class="widget-loop p-0 m-0">';
        if ($the_post == false) {
            echo '<li class="loop-none">暂无文章</li>';
        } else {
            //循环
            $i = 0;
            foreach ($the_post as $post => $post_data) {
                $i++;
                $first = ($i == 1) ? ' in-first' : '';
                echo '<li class="loop-list' . $first . '"><img class="lozad card-img" src="' . $post_data['thumb'] . '" alt="' . $post_data['attr_title'] . '" loading="lazy">
                <div class="card-title">
                    <h5><a class="stretched-link" href="' . $post_data['url'] . '">' . $post_data['title'] . '</a></h5>
                    <p><i class="bi bi-eye"></i>&nbsp' . $post_data['views'] . '&nbsp' . __('次浏览', 'AIYA') . '</p>
                </div>
            </li>';
            }
        }
        echo '</ul>';
    }
}
