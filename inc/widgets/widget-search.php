<?php

//小工具：搜索

class AYA_Widget_Search extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-search',
            'title' => 'AIYA-CMS 搜索',
            'classname' => 'widget-panel',
            'desc' => '搜索框卡片',
        );

        return $widget_args;
    }

    function widget_func()
    {
        aya_react_island('widget-search', []);
    }
}
