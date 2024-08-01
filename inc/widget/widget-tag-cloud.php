<?php

//小工具：标签云

class AYA_Widget_Tag_Cloud extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-tagcloud',
            'title' => 'AIYA-CMS 标签云',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏标签列表组件',
            'field_build' => array(
                array(
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => '显示数量',
                    'default' => '20',
                ),
            ),
        );

        return $widget_args;
    }
    function widget_func()
    {
        $limit = parent::widget_opt('limit');

        echo '<div class="widget-tagcloud p-0 m-0">';
        $tags = get_tags(array(
            "number" => $limit,
            "order" => "DESC"
        ));
        foreach ($tags as $tag) {
            $name = apply_filters('the_title', $tag->name);
            $url = esc_attr(get_tag_link($tag->term_id));
            echo '<a href="' . $url . '" class="tag-item" title="浏览和 #' . $name . ' 有关的文章">' . $name . '</a>';
        }
        echo '</div>';
    }
}
