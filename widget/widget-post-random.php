<?php

//小工具：随机文章

class AYA_Widget_Post_Random extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-post-random',
            'title' => 'AIYA-CMS 随机文章',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏文章列表，展示随机的文章',
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
            'orderby' => 'rand',
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
            echo '<li class="loop-none">' . __('暂无文章', 'AIYA') . '</li>';
        } else {
            //循环
            $i = 0;
            foreach ($the_post as $post => $post_data) {
                $i++;
                $first = ($i == 1) ? ' in-first' : '';
                echo '<li class="loop-list' . $first . '"><img class="lozad card-img" src="' . $post_data['thumb'] . '" alt="' . $post_data['attr_title'] . '" loading="lazy">
                <div class="card-title">
                    <h5><a class="stretched-link" href="' . $post_data['url'] . '">' . $post_data['title'] . '</a></h5>
                    <p><i class="bi bi-clock"></i>&nbsp;' . $post_data['date'] . '&nbsp<i class="bi bi-eye"></i>&nbsp' . $post_data['views'] . '</p>
                </div>
            </li>';
            }
        }
        echo '</ul>';
    }
}
