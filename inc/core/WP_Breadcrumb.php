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

if (!class_exists('AYA_WP_Breadcrumb_Object')) {
    class AYA_WP_Breadcrumb_Object
    {
        public static function get_breadcrumb()
        {
            // 首页且没有分页时不显示面包屑
            if ((is_home() || is_front_page()) && !is_paged()) {
                return false;
            }

            // 基本的面包屑项，以首页开始
            $items = [
                [
                    'label' => __('首页', 'AIYA'),
                    'url' => home_url('/'),
                ]
            ];

            // 根据页面类型构建面包屑
            if (is_home() || is_front_page()) {
                // 首页分页情况
                if (is_paged()) {
                    self::add_pagination_item($items);
                }
            } elseif (is_archive()) {
                // 归档页面
                if (is_category() || is_tax()) {
                    // 分类或自定义分类法
                    self::add_term_ancestors($items);
                } elseif (is_tag()) {
                    // 标签页
                    $items[] = [
                        'label' => __('标签', 'AIYA'),
                        'url' => '',
                    ];
                    $items[] = [
                        'label' => single_tag_title('', false),
                        'url' => get_term_link(get_queried_object()),
                    ];
                } elseif (is_author()) {
                    // 作者页
                    $items[] = [
                        'label' => __('作者', 'AIYA'),
                        'url' => '',
                    ];
                    $items[] = [
                        'label' => get_the_author_meta('display_name', get_query_var('author')),
                        'url' => get_author_posts_url(get_query_var('author')),
                    ];
                } elseif (is_date()) {
                    // 日期归档 (包括年、月、日)
                    self::add_date_items($items);
                } else {
                    // 其他归档类型 (例如自定义文章类型)
                    $items[] = [
                        'label' => post_type_archive_title('', false),
                        'url' => get_post_type_archive_link(get_post_type()),
                    ];
                }

                // 归档页面的分页
                if (is_paged()) {
                    self::add_pagination_item($items);
                }
            } elseif (is_singular()) {
                // 单页内容 (文章、页面、自定义文章类型)
                if (is_single()) {
                    // 普通文章和自定义文章类型
                    $post = get_queried_object();
                    $post_type = get_post_type();

                    if ($post_type === 'post') {
                        // 标准文章，添加分类祖先
                        $cats = get_the_category($post->ID);
                        if (!empty($cats)) {
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
                    } else {
                        // 自定义文章类型，添加文章类型归档
                        $post_type_obj = get_post_type_object($post_type);
                        if ($post_type_obj && $post_type_obj->has_archive) {
                            $items[] = [
                                'label' => $post_type_obj->labels->name,
                                'url' => get_post_type_archive_link($post_type),
                            ];
                        }

                        // 如果有分类，添加它
                        $taxonomies = get_object_taxonomies($post_type, 'objects');
                        foreach ($taxonomies as $taxonomy) {
                            if ($taxonomy->hierarchical) {
                                $terms = get_the_terms($post->ID, $taxonomy->name);
                                if (!empty($terms) && !is_wp_error($terms)) {
                                    $term = reset($terms);
                                    $ancestors = array_reverse(get_ancestors($term->term_id, $taxonomy->name));
                                    foreach ($ancestors as $ancestor_id) {
                                        $ancestor = get_term($ancestor_id, $taxonomy->name);
                                        $items[] = [
                                            'label' => $ancestor->name,
                                            'url' => get_term_link($ancestor),
                                        ];
                                    }
                                    $items[] = [
                                        'label' => $term->name,
                                        'url' => get_term_link($term),
                                    ];
                                    break; // 只使用第一个分类法
                                }
                            }
                        }
                    }

                    // 当前文章
                    $items[] = [
                        'label' => get_the_title($post),
                        'url' => get_permalink($post),
                    ];

                    // 添加文章分页（如果有多页）
                    self::add_post_pagination_item($items, $post);
                } elseif (is_page()) {
                    // 页面
                    $post = get_queried_object();
                    $ancestors = array_reverse(get_post_ancestors($post));
                    foreach ($ancestors as $ancestor_id) {
                        $ancestor = get_post($ancestor_id);
                        $items[] = [
                            'label' => get_the_title($ancestor),
                            'url' => get_permalink($ancestor),
                        ];
                    }

                    // 当前页面
                    $items[] = [
                        'label' => get_the_title($post),
                        'url' => get_permalink($post),
                    ];

                    // 添加页面分页（如果有多页）
                    self::add_post_pagination_item($items, $post);
                }
            } elseif (is_search()) {
                // 搜索结果页
                $items[] = [
                    'label' => sprintf(__('搜索: %s', 'AIYA'), get_search_query()),
                    'url' => get_search_link(get_search_query()),
                ];

                // 搜索结果分页
                if (is_paged()) {
                    self::add_pagination_item($items);
                }
            } elseif (is_404()) {
                // 404页面
                $items[] = [
                    'label' => __('404 NOT FOUND', 'AIYA'),
                    'url' => '',
                ];
            }

            return $items;
        }
        //递归分类法和分类法父级
        private static function add_term_ancestors(&$items)
        {
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

            // 当前分类/分类法
            $items[] = [
                'label' => single_term_title('', false),
                'url' => get_term_link($term),
            ];
        }

        //分解日期归档
        private static function add_date_items(&$items)
        {
            if (is_year()) {
                $items[] = [
                    'label' => sprintf(__('%s年', 'AIYA'), get_the_time('Y')),
                    'url' => get_year_link(get_the_time('Y')),
                ];
            } else if (is_month()) {
                $items[] = [
                    'label' => sprintf(__('%s年', 'AIYA'), get_the_time('Y')),
                    'url' => get_year_link(get_the_time('Y')),
                ];
                $items[] = [
                    'label' => sprintf(__('%s月', 'AIYA'), get_the_time('m')),
                    'url' => get_month_link(get_the_time('Y'), get_the_time('m')),
                ];
            } else if (is_day()) {
                $items[] = [
                    'label' => sprintf(__('%s年', 'AIYA'), get_the_time('Y')),
                    'url' => get_year_link(get_the_time('Y')),
                ];
                $items[] = [
                    'label' => sprintf(__('%s月', 'AIYA'), get_the_time('m')),
                    'url' => get_month_link(get_the_time('Y'), get_the_time('m')),
                ];
                $items[] = [
                    'label' => sprintf(__('%s日', 'AIYA'), get_the_time('d')),
                    'url' => get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')),
                ];
            }
        }

        //获取分页参数
        private static function add_pagination_item(&$items)
        {
            $paged = get_query_var('paged');
            $items[] = [
                'label' => sprintf(__('第 %s 页', 'AIYA'), $paged),
                'url' => get_pagenum_link($paged),
            ];
        }

        //获取文章/页面内部分页
        private static function add_post_pagination_item(&$items, $post)
        {
            global $page, $pages;
            if (count($pages) > 1 && $page > 1) {
                $items[] = [
                    'label' => sprintf(__('第 %s 页', 'AIYA'), $page),
                    'url' => get_permalink($post) . 'page/' . $page,
                ];
            }
        }
    }
}