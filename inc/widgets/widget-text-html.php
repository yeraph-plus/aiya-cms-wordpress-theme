<?php

//小工具：搜索

class AYA_Widget_Text_Html extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-text-html',
            'title' => 'AIYA-CMS 文本',
            'classname' => 'widget-panel',
            'desc' => '文本卡片，支持HTML',
            'field_build' => array(
                array(
                    'type' => 'textarea',
                    'id' => 'text',
                    'name' => '内容',
                    'default' => '',
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
        $content = parent::widget_opt('text');
        $esc_html = parent::widget_opt('type_html');

        if ($esc_html == 'true') {
            aya_echo(esc_html($content));
        } else {
            $html = '';
            $html .= '<div class="widget-content">';
            $html .= esc_attr($content);
            $html .= '</div>';

            aya_echo($html);
        }
    }
}
