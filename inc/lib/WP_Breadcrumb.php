<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 直接提取WP的菜单数据结构到数组
 * 
 **/

//获取面包屑导航
function aya_get_breadcrumb()
{
    return AYA_WP_Breadcrumb_Object::get_breadcrumb();
}

class AYA_WP_Breadcrumb_Object
{
    public static function get_breadcrumb()
    {
        $items = [];

        // 首页
        $items[] = [
            'label' => __('首页', 'AIYA'),
            'url' => home_url('/'),
        ];

        if (is_home() || is_front_page()) {
            return $items;
        }

        if (is_category() || is_tax()) {
            $term = get_queried_object();
            if ($term && $term->parent) {
                $ancestors = array_reverse(get_ancestors($term->term_id, $term->taxonomy));
                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, $term->taxonomy);
                    $items[] = [
                        'label' => $ancestor->name,
                        'url' => get_term_link($ancestor),
                    ];
                }
            }
            $items[] = [
                'label' => single_cat_title('', false),
                'url' => get_term_link($term),
            ];
        } elseif (is_single()) {
            $post = get_queried_object();
            // 文章所属分类
            $cats = get_the_category($post->ID);
            if ($cats) {
                $cat = $cats[0];
                $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));
                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_category($ancestor_id);
                    $items[] = [
                        'label' => $ancestor->name,
                        'url' => get_category_link($ancestor),
                    ];
                }
                $items[] = [
                    'label' => $cat->name,
                    'url' => get_category_link($cat),
                ];
            }
            // 当前文章
            $items[] = [
                'label' => get_the_title($post),
                'url' => get_permalink($post),
            ];
        } elseif (is_page()) {
            $post = get_queried_object();
            $ancestors = array_reverse(get_post_ancestors($post));
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_post($ancestor_id);
                $items[] = [
                    'label' => get_the_title($ancestor),
                    'url' => get_permalink($ancestor),
                ];
            }
            $items[] = [
                'label' => get_the_title($post),
                'url' => get_permalink($post),
            ];
        } elseif (is_tag()) {
            $items[] = [
                'label' => __('标签', 'AIYA'),
                'url' => '',
            ];
            $items[] = [
                'label' => single_tag_title('', false),
                'url' => get_term_link(get_queried_object()),
            ];
        } elseif (is_author()) {
            $items[] = [
                'label' => __('作者', 'AIYA'),
                'url' => '',
            ];
            $items[] = [
                'label' => get_the_author_meta('display_name', get_query_var('author')),
                'url' => get_author_posts_url(get_query_var('author')),
            ];
        } elseif (is_search()) {
            $items[] = [
                'label' => sprintf(__('搜索: %s', 'AIYA'), get_search_query()),
                'url' => '',
            ];
        } elseif (is_404()) {
            $items[] = [
                'label' => __('404 NOT FOUND', 'AIYA'),
                'url' => '',
            ];
        }

        return $items;
    }
}
