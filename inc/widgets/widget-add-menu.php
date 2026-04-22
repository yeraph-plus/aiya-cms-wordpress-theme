<?php

//小工具：侧边栏菜单

class AYA_Widget_Menu extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-menu',
            'title' => __('AIYA-CMS 菜单', 'aiya-cms'),
            'classname' => 'widget-panel',
            'desc' => __('菜单卡片，菜单内容于 外观->菜单 中设置', 'aiya-cms'),
            'field_build' => [
                [
                    'type' => 'input',
                    'id' => 'title',
                    'name' => __('标题', 'aiya-cms'),
                    'default' => __('菜单', 'aiya-cms'),
                ],
            ],
        );

        return $widget_args;
    }
    function widget_func()
    {
        $title = parent::widget_opt('title');
        $menu_items = aya_get_menu('widget-menu');

        aya_react_island(
            'widget-menu',
            ['menu' => $menu_items, 'widgetTitle' => $title]
        );
    }
}
