<?php

//小工具：搜索

class AYA_Widget_Serach extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-serach',
            'title' => 'AIYA-CMS 搜索',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏搜索组件',
        );

        return $widget_args;
    }
    function widget_func()
    {
        // Use active theme search form if it exists.
        aya_search_form();
    }
}
