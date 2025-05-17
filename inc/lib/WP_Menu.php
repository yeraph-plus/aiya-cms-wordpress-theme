<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 直接提取WP的菜单数据结构到数组
 * 
 **/

class AYA_WP_Menu_ObjectInArray
{
    public $menu;

    public function __construct($menu_name, $convet_json = false)
    {
        //if($menu_name = '') return;

        $menu_array = $this->menu_object_in_array($menu_name);

        if ($convet_json) {
            $this->menu = json_encode($menu_array, JSON_UNESCAPED_UNICODE);
        } else {
            $this->menu = $menu_array;
        }
    }

    //将WP菜单对象构造为数组
    private function menu_object_in_array($menu_name = '')
    {
        $locations = get_nav_menu_locations();
        $menu_array = array();

        //检查菜单是否存在
        if (empty($locations) || !isset($locations[$menu_name])) {
            return array('error' => 'Menu not found or invalid menu name.');

        }
        //提取对象
        $menu = wp_get_nav_menu_object($locations[$menu_name]);
        //查询
        $menu_items = wp_get_nav_menu_items($menu->term_id);

        //重新循环数组，将ID替换为键名用于递归
        $each_items = array();

        foreach ($menu_items as $item) {
            $each_items[$item->ID] = $item;
        }

        //递归函数
        $menu_array = $this->menu_array_build($each_items);

        //将数组重新排序
        //$value_down = array_values($menu_array);
        return $menu_array;
    }

    //用来处理菜单层级的子方法
    private function menu_array_build($items, $parent_id = 0)
    {
        $branch_menu = array();

        foreach ($items as $item) {
            if ($item->menu_item_parent == $parent_id) {
                $child = $this->menu_array_build($items, $item->ID);
                if ($child) {
                    $item->child = $child;
                }

                // 检查当前菜单链接是否是当前页面
                $is_active = $this->is_current_page($item->url);

                $branch_menu[$item->ID] = array(
                    'label' => $item->title,
                    'url' => $item->url,
                    'is_active' => $is_active, // 添加 is_active 字段
                    'child' => isset($item->child) ? $item->child : array()
                );
            }
        }

        return $branch_menu;
    }

    //判断当前页面的 URL
    private function is_current_page($url)
    {
        $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));

        return trailingslashit($url) === trailingslashit($current_url);
    }
}

//获取菜单
function aya_get_menu($menu_name)
{
    $menu_object = new AYA_WP_Menu_ObjectInArray($menu_name);

    //返回对象
    return $menu_object->menu;
}

function aya_get_menu_json($menu_name)
{
    $menu_object = new AYA_WP_Menu_ObjectInArray($menu_name, true);

    //返回JSON
    return $menu_object->menu;
}