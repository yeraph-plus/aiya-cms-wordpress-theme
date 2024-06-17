<?php

//小工具：最新文章

class AYA_Widget_Post_Newest extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-post-newest',
            'title' => 'AIYA-CMS 最新文章',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏文章列表，展示最近发布的文章',
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
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $limit,
            'post_status' => 'publish',
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
            foreach ($the_post as $post => $post_data) {
                $date = $post_data['date'];
                $time_stamp = strtotime($date);
                echo '<li class="loop-list">
                <div class="card-event d-flex flex-column">
                    <span class="event-mon">' . date('M', $time_stamp) . '</span>
                    <span class="event-day">' . date('d', $time_stamp) . '</span>
                </div>
                <div class="card-title">
                    <h5><a class="stretched-link" href="' . $post_data['url'] . '">' . $post_data['title'] . '</a></h5>
                    <p>' . __('发布者', 'AIYA') . '&nbsp;<i class="bi bi-person"></i>&nbsp;' . $post_data['author'] . '</p>
                </div>
            </li>';
            }
        }
        echo '</ul>';
    }
}
//' . __('', 'AIYA') . '