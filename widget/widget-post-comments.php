<?php

//小工具：热评文章

class AYA_Widget_Post_Comments extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-post-comments',
            'title' => 'AIYA-CMS 热评文章',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏文章列表，展示评论最多的文章',
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
            'orderby' => 'comment_count',
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
            foreach ($the_post as $post => $post_data) {
                echo '<li class="loop-list">
                <div class="card-shade d-flex flex-column">
                    <span class="shade-icon"><i class="bi bi bi-chat-square-dots-fill"></i></span>
                    <span class="shade-second">' . str_pad($post_data['comments'], 2, "0", STR_PAD_LEFT) . '</span>
                </div>
                <div class="card-title">
                    <h5><a class="stretched-link" href="' . $post_data['url'] . '">' . $post_data['title'] . '</a></h5>
                    <p><i class="bi bi-clock"></i>&nbsp;' . __('发布于', 'AIYA') . '&nbsp;' . $post_data['date'] . '</p>
                </div>
            </li>';
            }
        }
        echo '</ul>';
    }
}
