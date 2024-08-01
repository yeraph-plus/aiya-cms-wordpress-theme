<?php

//小工具：文章列表

class AYA_Widget_Post_Custom extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-post-custom',
            'title' => 'AIYA-CMS 文章列表',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏文章列表，使用自定义Query参数',
            'field_build' => array(
                array(
                    'type' => 'textarea',
                    'id' => 'vars',
                    'name' => 'Query参数',
                    'default' => 'post_type=post&&orderby=rand&&posts_per_page=5&&order=DESC',
                ),
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $vars = parent::widget_opt('vars');
        //自定义Query查询
        $the_post = aya_get_query($vars);

        //生成文章列表
        echo '<ul class="widget-loop p-0 m-0">';
        if ($the_post == false) {
            echo '<li class="loop-none">暂无文章</li>';
        } else {
            //循环
            foreach ($the_post as $post => $post_data) {
                echo '<li class="loop-list"><img class="lozad card-img" src="' . aya_the_loop_thumb($post_data['id'], 400, 300, true) . '" alt="' . $post_data['attr_title'] . '" loading="lazy">
                <div class="card-title">
                    <h5><a class="stretched-link" href="' . $post_data['url'] . '">' . $post_data['title'] . '</a></h5>
                    <p><i class="bi bi-eye"></i>&nbsp' . $post_data['views'] . '&nbsp' . __('次浏览', 'AIYA') . '&nbsp;&nbsp;<i class="bi bi-clock"></i>&nbsp;' . __('发布于', 'AIYA') . '&nbsp;' . $post_data['date'] . '</p>
                </div>
            </li>';
            }
        }
        echo '</ul>';
    }
}
