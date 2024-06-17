<?php

//小工具：侧边栏菜单

class AYA_Widget_Menu extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-menu',
            'title' => 'AIYA-CMS 菜单',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏菜单组件，于 外观->菜单 中设置',
        );

        return $widget_args;
    }
    function widget_func()
    {
        //定义子菜单结构
        $args = array(
            'theme_location' => 'widget-menu',
            'depth' => 2,
            'container' => false,
            'menu_class' => 'nav d-flex flex-column',
            'fallback_cb' => '__return_false',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'walker' => new AYA_Bootstarp_Nav_Menu()
        );
        //返回
        echo '<div class="widget-bg">';
        echo wp_nav_menu($args);
        echo '</div>';
    }
}
