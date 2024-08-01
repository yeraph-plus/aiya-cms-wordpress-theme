<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 生成Bootstrap菜单
 * ------------------------------------------------------------------------------
 */

//生成菜单
function aya_nav_menu($menu, $class = '', $dep = 2)
{
    //检查参数
    if (function_exists('wp_nav_menu') && has_nav_menu($menu)) {
        //定义子菜单结构
        $args = array(
            'theme_location' => $menu,
            'depth' => $dep, // 1 = no dropdowns, 2 = with dropdowns.
            'container' => false,
            'menu_class' => $class,
            'fallback_cb' => '__return_false',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'walker' => new AYA_Bootstarp_Nav_Menu()
        );
        //返回
        return wp_nav_menu($args);
    } else {
        //返回提示
        echo __('创建你的第一个导航菜单：[后台 -> 外观 -> 菜单]', 'AIYA');
    }
}
//生成夹层菜单
function aya_nav_hamburg_menu($menu)
{
    aya_nav_menu($menu, '', 1);
    echo '
    <div class="nav-scroller">
      <nav class="nav" aria-label="Secondary navigation">
        <a class="nav-link" aria-current="page" href="#">Link</a>
        <a class="nav-link" href="#">
          Link
          <span class="badge text-bg-light rounded-pill align-text-bottom">27</span>
        </a>
        <a class="nav-link" href="#">Link</a>
        <a class="nav-link" href="#">Link</a>
        <a class="nav-link" href="#">Link</a>
        <a class="nav-link" href="#">Link</a>
      </nav>
    </div>
    ';
}

//用于生成菜单和子菜单的walker类
if (!class_exists('AYA_Bootstarp_Nav_Menu')) {
    class AYA_Bootstarp_Nav_Menu extends Walker_Nav_menu
    {
        private $current_item;

        //兼容Bootstrap5定位器
        private $dropdown_menu_alignment_values = [
            'dropdown-menu-start',
            'dropdown-menu-end',
            'dropdown-menu-sm-start',
            'dropdown-menu-sm-end',
            'dropdown-menu-md-start',
            'dropdown-menu-md-end',
            'dropdown-menu-lg-start',
            'dropdown-menu-lg-end',
            'dropdown-menu-xl-start',
            'dropdown-menu-xl-end',
            'dropdown-menu-xxl-start',
            'dropdown-menu-xxl-end'
        ];

        function start_lvl(&$output, $depth = 0, $args = null)
        {
            $dropdown_menu_class[] = '';
            //循环子级菜单
            foreach ($this->current_item->classes as $class) {
                if (in_array($class, $this->dropdown_menu_alignment_values)) {
                    $dropdown_menu_class[] = $class;
                }
            }
            $indent = str_repeat("\t", $depth);
            $submenu = ($depth > 0) ? ' sub-menu' : '';
            $output .= "\n$indent<ul class=\"dropdown-menu$submenu " . esc_attr(implode(" ", $dropdown_menu_class)) . " depth_$depth\">\n";
        }

        function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
        {
            $this->current_item = $item;

            $indent = ($depth) ? str_repeat("\t", $depth) : '';

            $current_url = home_url(add_query_arg(array()));

            $li_attributes = '';
            $class_names = $value = '';
            //判断样式
            $classes = empty($item->classes) ? array() : (array) $item->classes;

            $classes[] = ($args->walker->has_children) ? 'dropdown' : '';
            $classes[] = 'nav-item';
            $classes[] = 'nav-item-' . $item->ID;
            if ($depth && $args->walker->has_children) {
                $classes[] = 'dropdown-menu dropdown-menu-end';
            }
            $classes[] = ($item->url == $current_url) ? 'active' : '';

            $class_names =  join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
            $class_names = ' class="' . esc_attr($class_names) . '"';

            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
            $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

            $output .= $indent . '<li ' . $id . $value . $class_names . $li_attributes . '>';

            $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
            $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
            $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
            $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

            $active_class = ($item->current || $item->current_item_ancestor || in_array("current_page_parent", $item->classes, true) || in_array("current-post-ancestor", $item->classes, true)) ? 'active' : '';
            $nav_link_class = ($depth > 0) ? 'dropdown-item ' : 'nav-link ';
            $attributes .= ($args->walker->has_children) ? ' class="' . $nav_link_class . $active_class . ' dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : ' class="' . $nav_link_class . $active_class . '"';

            $item_output = $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    }
}
