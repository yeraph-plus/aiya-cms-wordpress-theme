<?php

//小工具：标签云

class AYA_Widget_Tag_Cloud extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-tag-cloud',
            'title' => 'AIYA-CMS 标签云',
            'classname' => 'widget-panel',
            'desc' => '标签云卡片，栏标签列表组件',
            'field_build' => array(
                [
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => '显示数量',
                    'default' => '20',
                ],
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $limit = parent::widget_opt('limit');

        $tags = get_tags(array(
            "number" => $limit,
            "order" => "DESC"
        ));

        $html = '';
        $html .= '<div class="widget-content flex flex-wrap justify-center">';

        $colors = array('primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark');

        foreach ($tags as $tag) {
            $name = apply_filters('the_title', $tag->name);
            $rand = array_rand($colors, 1);

            $html .= '<a href="' . esc_attr(get_tag_link($tag->term_id)) . '" class="btn btn-outline-' . $colors[$rand] . ' text-sm font-medium py-1 px-2 mr-2 mb-2" title="浏览和 #' . $name . ' 有关的文章">' . $name . '</a>';
        }
        $html .= '</div>';

        aya_echo($html);

        //<a href="#" class="btn btn-outline-">button</a>
        //<a href="#" class="btn btn-outline-">button</a>
        //<a href="#" class="btn btn-outline-">button</a>
        //<a href="#" class="btn btn-outline-">button</a>
        //<a href="#" class="btn btn-outline-">button</a>
        //<a href="#" class="btn btn-outline-">button</a>
        
    }
}
