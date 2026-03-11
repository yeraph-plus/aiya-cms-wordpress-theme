<?php

//小工具：侧边栏菜单

class AYA_Widget_Menu extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-menu',
            'title' => 'AIYA-CMS 菜单',
            'classname' => 'widget-panel',
            'desc' => '菜单卡片，菜单内容于 外观->菜单 中设置',
            'field_build' => [
                [
                    'type' => 'input',
                    'id' => 'title',
                    'name' => '标题',
                    'default' => '菜单',
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
