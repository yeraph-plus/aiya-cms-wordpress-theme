<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 处理WP的分类术语数据结构到数组
 * 
 **/

class AYA_WP_Term_Object
{
    private $term;

    //初始化对象
    public function __construct($term_id = 0, $taxonomy = 'category')
    {
        $this->term = $this->get_term($term_id, $taxonomy);
    }

    //获取当前TERM对象
    public function this_term()
    {
        return $this->term;
    }

    //定位分类术语
    public function get_term($term = 0, $taxonomy = 'category')
    {
        //已经是对象直接返回
        if (is_object($term)) {
            $this->term = $term;
            return $term;
        }

        //检查是否为数字ID
        $term_id = absint($term);

        //检查当前是否在分类页面中，是则返回
        if (empty($term_id) && is_tax() || is_category() || is_tag()) {
            $queried_object = get_queried_object();
            if ($queried_object instanceof WP_Term) {
                $this->term = $queried_object;
                return $queried_object;
            }
        }

        //尝试获取 WP_Term 对象
        if ($term_id > 0) {
            $term = get_term($term_id, $taxonomy);
        }

        //获取成功
        if (!empty($term) && !is_wp_error($term)) {
            $this->term = $term;
            return $term;
        }

        return false;
    }

    //获取ID
    public function get_term_id()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        return $term ? $term->term_id : 0;
    }

    //获取分类法
    public function get_term_taxonomy()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        return $term ? $term->taxonomy : '';
    }

    //获取名称
    public function get_term_name($attribute = false)
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        if (!$term)
            return '';

        $the_name = $term->name;

        //检查名称
        if (strlen($the_name) === 0) {
            $the_name = __('无名称', 'AIYA');
        }

        //清理HTML输出
        if ($attribute == true) {
            $the_name = esc_attr(strip_tags($the_name));
        }

        return $the_name;
    }

    //获取别名
    public function get_term_slug()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        return $term ? $term->slug : '';
    }

    //获取描述
    public function get_term_description()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        if (!$term)
            return '';

        return wp_kses_post($term->description);
    }

    //获取URL
    public function get_term_url()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        if (!$term)
            return '';

        return get_term_link($term);
    }

    //获取文章数量
    public function get_term_count()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        return $term ? $term->count : 0;
    }

    //获取格式化的文章数量
    public function get_term_count_formatted()
    {
        $count = $this->get_term_count();

        if ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }

        return $count;
    }

    //获取父级ID
    public function get_term_parent_id()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        return $term ? $term->parent : 0;
    }

    //获取父级术语
    public function get_term_parent()
    {
        $parent_id = $this->get_term_parent_id();

        if (!$parent_id) {
            return false;
        }

        $taxonomy = $this->get_term_taxonomy();
        return get_term($parent_id, $taxonomy);
    }

    //获取所有父级术语
    public function get_term_parents()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        if (!$term)
            return [];

        $taxonomy = $term->taxonomy;
        $parents = [];
        $parent_id = $term->parent;

        while ($parent_id > 0) {
            $parent = get_term($parent_id, $taxonomy);
            if (!$parent || is_wp_error($parent)) {
                break;
            }

            $parents[] = $parent;
            $parent_id = $parent->parent;
        }

        return array_reverse($parents);
    }

    //获取子术语
    public function get_term_children()
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        if (!$term)
            return [];

        $children = get_terms([
            'taxonomy' => $term->taxonomy,
            'parent' => $term->term_id,
            'hide_empty' => false
        ]);

        return is_wp_error($children) ? [] : $children;
    }

    //获取术语图像（需要依赖ACF或者其他自定义字段插件）
    public function get_term_image($size = 'full')
    {
        $term_id = $this->get_term_id();

        // 检查是否有ACF函数
        if (function_exists('get_field')) {
            $image = get_field('term_image', $this->get_term_taxonomy() . '_' . $term_id);
            if ($image) {
                if (is_array($image)) {
                    return $image['sizes'][$size] ?? $image['url'];
                }
                return $image;
            }
        }

        // 备选：检查自定义字段中是否有图片
        $image_id = get_term_meta($term_id, 'term_image_id', true);
        if ($image_id) {
            $image = wp_get_attachment_image_src($image_id, $size);
            return $image ? $image[0] : '';
        }

        return '';
    }

    //检查术语是否有图片
    public function has_term_image()
    {
        return $this->get_term_image() !== '';
    }

    //获取自定义颜色
    public function get_term_color($default = '')
    {
        $term_id = $this->get_term_id();

        // 检查是否有ACF函数
        if (function_exists('get_field')) {
            $color = get_field('term_color', $this->get_term_taxonomy() . '_' . $term_id);
            if ($color) {
                return $color;
            }
        }

        // 备选：检查自定义字段
        $color = get_term_meta($term_id, 'term_color', true);
        if ($color) {
            return $color;
        }

        return $default;
    }

    //获取术语路径（面包屑）
    public function get_term_breadcrumbs($separator = ' &gt; ')
    {
        $term = is_object($this->term) ? $this->term : $this->get_term();

        if (!$term)
            return '';

        $parents = $this->get_term_parents();
        $breadcrumbs = [];

        foreach ($parents as $parent) {
            $breadcrumbs[] = '<a href="' . esc_url(get_term_link($parent)) . '">' . esc_html($parent->name) . '</a>';
        }

        $breadcrumbs[] = esc_html($term->name);

        return implode($separator, $breadcrumbs);
    }
}

class AYA_Term_In_While extends AYA_WP_Term_Object
{
    private $data = [];

    //初始化父类
    public function __construct($term = 0, $taxonomy = 'category')
    {
        parent::__construct($term, $taxonomy);

        // 预加载基本数据
        $this->data['id'] = $this->get_term_id();
        $this->data['url'] = $this->get_term_url();
        $this->data['name'] = $this->get_term_name();
        $this->data['attr_name'] = $this->get_term_name(true);
        $this->data['taxonomy'] = $this->get_term_taxonomy();
        $this->data['slug'] = $this->get_term_slug();
    }

    //定义魔术方法预加载属性
    public function __get($name)
    {
        // 如果属性已存在直接返回
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        // 按需加载各种属性
        switch ($name) {
            case 'description':
                $this->data['description'] = $this->get_term_description();
                break;
            case 'count':
                $this->data['count'] = $this->get_term_count();
                break;
            case 'count_formatted':
                $this->data['count_formatted'] = $this->get_term_count_formatted();
                break;
            case 'parent_id':
                $this->data['parent_id'] = $this->get_term_parent_id();
                break;
            case 'parent':
                $this->data['parent'] = $this->get_term_parent();
                break;
            case 'parents':
                $this->data['parents'] = $this->get_term_parents();
                break;
            case 'children':
                $this->data['children'] = $this->get_term_children();
                break;
            case 'image':
                $this->data['image'] = $this->get_term_image();
                break;
            case 'has_image':
                $this->data['has_image'] = $this->has_term_image();
                break;
            case 'color':
                $this->data['color'] = $this->get_term_color();
                break;
            case 'breadcrumbs':
                $this->data['breadcrumbs'] = $this->get_term_breadcrumbs();
                break;
            default:
                return null;
        }

        return $this->data[$name];
    }

    //检查属性是否存在
    public function __isset($name)
    {
        //尝试加载属性
        if (!array_key_exists($name, $this->data)) {
            $this->__get($name);
        }

        return isset($this->data[$name]);
    }

    //预加载方法
    public function preload($attributes = ['description', 'count', 'parent', 'children'])
    {
        foreach ($attributes as $attr) {
            $this->__get($attr);
        }
        return $this;
    }
}