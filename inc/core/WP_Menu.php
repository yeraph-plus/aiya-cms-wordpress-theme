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
            $cache_key = 'aiya_menu_' . $menu_name;
            $cache_group = 'aiya_cms_menus';

            // 缓存结构
            $menu_structure = wp_cache_get($cache_key, $cache_group);

            if (false === $menu_structure) {
                // 获取结构（可缓存部分）
                $menu_structure = self::get_menu_structure($menu_name);

                if (!isset($menu_structure['error'])) {
                    wp_cache_set($cache_key, $menu_structure, $cache_group, AYA_CACHE_SECOND);
                }
            }

            if (isset($menu_structure['error'])) {
                return $menu_structure;
            }

            // 注入激活状态（动态部分）
            return self::inject_active_state($menu_structure);
        }

        public static function delete_cache($menu_id = 0)
        {
            $locations = get_nav_menu_locations();

            if (empty($locations)) {
                return;
            }

            foreach ($locations as $location => $assigned_menu_id) {
                // Clear cache if this location uses the updated menu, or if clearing all ($menu_id === 0)
                if ($menu_id === 0 || $assigned_menu_id == $menu_id) {
                    wp_cache_delete('aiya_menu_' . $location, 'aiya_cms_menus');
                }
            }
        }

        // 构建菜单结构 O(n)
        private static function get_menu_structure($menu_name = '')
        {
            $locations = get_nav_menu_locations();

            if (empty($locations) || !isset($locations[$menu_name])) {
                return array('error' => 'Menu not found or invalid menu name.');
            }

            $menu = wp_get_nav_menu_object($locations[$menu_name]);
            if (!$menu) {
                return array('error' => 'Menu object not found.');
            }

            $menu_items = wp_get_nav_menu_items($menu->term_id);
            if (!$menu_items) {
                return array();
            }

            // Optimization: Group by parent ID
            // 优化：按父级ID分组，避免递归中的全量遍历
            $items_by_parent = array();
            foreach ($menu_items as $item) {
                $items_by_parent[$item->menu_item_parent][] = $item;
            }

            return self::build_tree($items_by_parent, 0);
        }

        // 递归树
        private static function build_tree(&$items_by_parent, $parent_id)
        {
            $branch = array();

            if (empty($items_by_parent[$parent_id])) {
                return $branch;
            }

            foreach ($items_by_parent[$parent_id] as $item) {
                $children = self::build_tree($items_by_parent, $item->ID);

                // Structure data
                $branch[$item->ID] = array(
                    'label'     => $item->title,
                    'url'       => $item->url,
                    'target'    => $item->target,
                    'object_id' => $item->object_id,
                    'object'    => $item->object,
                    'type'      => $item->type,
                    'child'     => $children
                );
            }

            return $branch;
        }

        // 注入激活状态
        private static function inject_active_state($menu_tree)
        {
            // Prepare comparison data once
            $queried_object_id = get_queried_object_id();
            $queried_object = get_queried_object();
            $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));

            foreach ($menu_tree as $id => &$item) {
                $is_active = false;

                // 优先使用 Object ID 比对
                if ($item['object_id'] && $queried_object) {
                    if ($item['type'] === 'post_type' && isset($queried_object->ID) && $item['object_id'] == $queried_object->ID) {
                        $is_active = true;
                    } elseif ($item['type'] === 'taxonomy' && isset($queried_object->term_id) && $item['object_id'] == $queried_object->term_id) {
                        $is_active = true;
                    }
                }

                // 降级方案：URL比对
                if (!$is_active) {
                    $is_active = (trailingslashit($item['url']) === trailingslashit($current_url));
                }

                $item['is_active'] = $is_active;

                // Recursion
                if (!empty($item['child'])) {
                    $item['child'] = self::inject_active_state($item['child']);

                    // 如果子项激活，父项也视为激活（祖先逻辑）
                    foreach ($item['child'] as $child) {
                        if ($child['is_active']) {
                            $item['is_active'] = true;
                            break;
                        }
                    }
                }
            }
            // Break reference
            unset($item);

            return $menu_tree;
        }
    }

    // 自动挂载缓存清理钩子
    add_action('wp_update_nav_menu', array('AYA_WP_Menu_Object', 'delete_cache'));
    add_action('wp_delete_nav_menu', array('AYA_WP_Menu_Object', 'delete_cache'));


    //获取菜单
    function aya_get_menu($menu_name)
    {
        //返回对象
        return AYA_WP_Menu_Object::get_menu($menu_name);
    }

    function aya_get_menu_json($menu_name)
    {
        $menu_object = AYA_WP_Menu_Object::get_menu($menu_name);

        //返回JSON
        return json_encode($menu_object, JSON_UNESCAPED_UNICODE);
    }

    function aya_get_menu_html($menu = array())
    {
        //$menu_object = AYA_WP_Menu_Object::get_menu($menu_name);

        $html = '<ul>';

        foreach ($menu as $item) {
            $label = isset($item['label']) ? (string) $item['label'] : '';
            $url = isset($item['url']) ? (string) $item['url'] : '#';
            $target = isset($item['target']) ? (string) $item['target'] : '';
            $is_active = !empty($item['is_active']);

            $rel = '';
            if ($target === '_blank') {
                $rel = 'noopener noreferrer';
            }

            $children = [];
            if (isset($item['child']) && is_array($item['child'])) {
                $children = $item['child'];
            }

            $html .= '<li>';
            $html .= '<a href="' . esc_url($url) . '"';
            if ($target !== '') {
                $html .= ' target="' . esc_attr($target) . '"';
            }
            if ($rel !== '') {
                $html .= ' rel="' . esc_attr($rel) . '"';
            }
            if ($is_active) {
                $html .= ' aria-current="page"';
            }
            $html .= '>' . esc_html($label) . '</a>';

            if (!empty($children)) {
                $html .= '<ul>';
                foreach ($children as $child) {
                    $child_label = isset($child['label']) ? (string) $child['label'] : '';
                    $child_url = isset($child['url']) ? (string) $child['url'] : '#';
                    $child_target = isset($child['target']) ? (string) $child['target'] : '';
                    $child_is_active = !empty($child['is_active']);

                    $child_rel = '';
                    if ($child_target === '_blank') {
                        $child_rel = 'noopener noreferrer';
                    }

                    $html .= '<li>';
                    $html .= '<a href="' . esc_url($child_url) . '"';
                    if ($child_target !== '') {
                        $html .= ' target="' . esc_attr($child_target) . '"';
                    }
                    if ($child_rel !== '') {
                        $html .= ' rel="' . esc_attr($child_rel) . '"';
                    }
                    if ($child_is_active) {
                        $html .= ' aria-current="page"';
                    }
                    $html .= '>' . esc_html($child_label) . '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }
}
