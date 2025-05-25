<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 直接提取WP的菜单数据结构到数组
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

if (!class_exists('AYA_WP_Menu_Object')) {
    class AYA_WP_Menu_Object
    {
        public static function get_menu($menu_name)
        {
            return self::menu_object_in_array($menu_name);
        }

        //将WP菜单对象构造为数组
        private static function menu_object_in_array($menu_name = '')
        {
            $locations = get_nav_menu_locations();
            $menu_array = array();

            if (empty($locations) || !isset($locations[$menu_name])) {
                return array('error' => 'Menu not found or invalid menu name.');
            }
            $menu = wp_get_nav_menu_object($locations[$menu_name]);
            $menu_items = wp_get_nav_menu_items($menu->term_id);

            $each_items = array();
            foreach ($menu_items as $item) {
                $each_items[$item->ID] = $item;
            }

            $menu_array = self::menu_array_build($each_items);

            return $menu_array;
        }

        //处理菜单层级的递归方法
        private static function menu_array_build($items, $parent_id = 0)
        {
            $branch_menu = array();

            foreach ($items as $item) {
                if ($item->menu_item_parent == $parent_id) {
                    $child = self::menu_array_build($items, $item->ID);
                    if ($child) {
                        $item->child = $child;
                    }
                    $is_active = self::is_current_page($item->url);

                    $branch_menu[$item->ID] = array(
                        'label' => $item->title,
                        'url' => $item->url,
                        'is_active' => $is_active,
                        'child' => isset($item->child) ? $item->child : array()
                    );
                }
            }

            return $branch_menu;
        }

        //判断当前页面的 URL
        private static function is_current_page($url)
        {
            $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));
            return trailingslashit($url) === trailingslashit($current_url);
        }
    }
}