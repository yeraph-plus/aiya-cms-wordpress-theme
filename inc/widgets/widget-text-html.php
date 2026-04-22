<?php

//小工具：搜索

class AYA_Widget_Text_Html extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-text-html',
            'title' => __('AIYA-CMS 文本', 'aiya-cms'),
            'classname' => 'widget-panel',
            'desc' => __('文本卡片，支持HTML', 'aiya-cms'),
            'field_build' => array(
                array(
                    'type' => 'textarea',
                    'id' => 'text',
                    'name' => __('内容', 'aiya-cms'),
                    'default' => '',
                ),
                array(
                    'type' => 'checkbox',
                    'id' => 'type_html',
                    'name' => __('是否输出为HTML', 'aiya-cms'),
                    'default' => 'true',
                ),
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $content = parent::widget_opt('text');
        $esc_html = parent::widget_opt('type_html');

        if ($esc_html == 'true') {
            aya_echo(esc_html($content));
        } else {
            aya_echo(esc_attr($content));
        }
    }
}
