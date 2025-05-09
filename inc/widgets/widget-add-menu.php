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
        );

        return $widget_args;
    }
    function widget_func()
    {
        $html = '';

        $html .= '<div class="widget-content widget-menu">';

        foreach (aya_menu_array_get('widget-menu') as $key => $menu):

            if (!empty($menu['child'])):
                $html .= '<div class="relative mb-2"><div x-data="dropdown" @click.outside="open = false" class="dropdown">';
                $html .= '<button type="button" class="flex items-center dropdown-toggle" @click="toggle">' . $menu['label'] . '&nbsp;' . aya_feather_icon('chevron-down', '16', 'mr-1', '') . '</button>';
                $html .= '<ul x-cloak x-show="open" x-transition x-transition.duration.300ms class="ltr:right-0 rtl:left-0 whitespace-nowrap">';

                foreach ($menu['child'] as $key => $child):
                    $html .= '<li><a href="' . $child['url'] . '">' . $child['label'] . '</a></li>';
                endforeach;

                $html .= '</ul>';
                $html .= '</div></div>';
            else:
                $html .= '<a class="flex items-center mb-2" href="' . $menu['url'] . '">' . $menu['label'] . '</a>';
            endif;

        endforeach;

        $html .= '</div>';

        //返回
        aya_echo($html);
    }
}
