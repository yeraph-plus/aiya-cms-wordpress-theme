<?php

//小工具：搜索

class AYA_Widget_Search extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-search',
            'title' => __('AIYA-CMS 搜索', 'aiya-cms'),
            'classname' => 'widget-panel',
            'desc' => __('搜索框卡片', 'aiya-cms'),
        );

        return $widget_args;
    }

    function widget_func()
    {
        aya_react_island('widget-search', []);
    }
}
