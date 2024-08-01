<?php

//小工具：搜索

class AYA_Widget_Text_Html extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-text-html',
            'title' => 'AIYA-CMS 文本',
            'classname' => 'widget-card-box',
            'desc' => '展示近期评论列表',
            'field_build' => array(
                array(
                    'type' => 'textarea',
                    'id' => 'text',
                    'name' => '内容',
                    'default' => '5',
                ),
                array(
                    'type' => 'checkbox',
                    'id' => 'type_html',
                    'name' => '是否输出为HTML',
                    'default' => '5',
                ),
            ),
        );

        return $widget_args;
    }
    function widget_func()
    {
        $text = parent::widget_opt('text');
        $html = parent::widget_opt('type_html');

        if ($html == 'true') {
            echo esc_html($text);
        } else {
            echo '<div class="widget-bg widget-body">' . esc_attr($text) . '</div>';
        }
    }
}
