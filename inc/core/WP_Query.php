<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 封装WP的复杂查询方法
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 2.0
 **/

if (!class_exists('AYA_WP_Query_Object')) {
    class AYA_WP_Query_Object
    {
        private $default_args = [
            'order' => 'DESC',
            'orderby' => 'date',
            'ignore_sticky_posts' => true,
            'post_status' => 'publish',
            'posts_per_page' => 10
        ];

        //原查询方法
        public function query($args = array(), $output_type = 'posts')
        {
            //检查参数
            if (!is_array($args) || empty($args)) {
                return [];
            }

            /**
             * WP_Query()参数指南：
             * https://developer.wordpress.org/reference/classes/wp_query/
             * 
             * 如需使用query_posts()方法：
             * 
             * //新建查询
             * if (!is_main_query()) $the_query = query_posts($args);
             * 
             * //重置查询
             * wp_reset_query();
             * 
             * Tips: 由于query_posts可以替代主查询，并不建议这样使用。
             */

            //合并默认参数
            $args = wp_parse_args($args, $this->default_args);

            switch ($output_type) {
                //只获取文章ID
                case 'ids':
                    $args['fields'] = 'ids';
                    $query = new WP_Query($args);
                    $result = $query->posts;
                    break;
                //返回默认对象数组
                case 'posts':
                    $query = new WP_Query($args);
                    $result = $query->posts;
                    break;
                //返回完整的WP_Query对象
                case 'query':
                default:
                    $result = new WP_Query($args);
                    break;
            }

            //重置查询
            wp_reset_postdata();

            return $result ?: [];
        }
        //修改默认查询参数
        public function set_default($args)
        {
            if (is_array($args)) {
                $this->default_args = wp_parse_args($args, $this->default_args);
            }

            return $this;
        }
        //查询文章列表
        public function get_posts($args, $limit = 10, $orderby = 'date', $asc_order = false)
        {
            $query_args = wp_parse_args($args, [
                'posts_per_page' => $limit,
                'orderby' => $orderby,
                'order' => ($asc_order ? 'ASC' : 'DESC'),
            ]);

            return $this->query($query_args);
        }
        //查询分类文章列表
        public function get_posts_by_category($categories, $limit = 10, $orderby = 'date')
        {
            $args = [];

            if (is_numeric($categories)) {
                $args['cat'] = $categories;
            } elseif (is_string($categories)) {
                $args['category_name'] = $categories;
            } elseif (is_array($categories)) {
                $args['category__in'] = $categories;
            }

            return $this->get_posts($args, $limit, $orderby);
        }
        //查询标签文章列表
        public function get_posts_by_tag($tags, $limit = 10, $orderby = 'date')
        {
            $args = [];

            if (is_numeric($tags)) {
                $args['tag_id'] = $tags;
            } elseif (is_string($tags)) {
                $args['tag'] = $tags;
            } elseif (is_array($tags)) {
                $args['tag__in'] = $tags;
            }

            return $this->get_posts($args, $limit, $orderby);
        }
        //查询自定义分类法文章列表
        public function get_posts_by_taxonomy($taxonomy, $terms, $limit = 10, $field = 'term_id')
        {
            //套入数组
            if (!is_array($terms)) {
                $terms = [$terms];
            }

            $args = [
                'tax_query' => [
                    [
                        'taxonomy' => $taxonomy,
                        'field' => $field,
                        'terms' => $terms,
                        'include_children' => true,
                        'operator' => 'IN'
                    ]
                ]
            ];

            return $this->get_posts($args, $limit);
        }
        //查询指定元数据的文章列表
        public function get_posts_by_meta($key, $value, $compare = '=', $limit = 10)
        {
            $args = [
                'meta_query' => [
                    [
                        'key' => $key,
                        'value' => $value,
                        'compare' => $compare
                    ]
                ]
            ];

            return $this->get_posts($args, $limit);
        }
        //查询指定作者的文章列表
        public function get_posts_by_author($author, $limit = 10, $orderby = 'date')
        {
            //参数为ID或用户名
            $args = is_numeric($author)
                ? ['author' => $author]
                : ['author_name' => $author];

            return $this->get_posts($args, $limit, $orderby);
        }
        //查询指定类型的文章列表
        public function get_posts_by_type($post_type, $limit = 10, $paged = 1, $orderby = 'date')
        {

            $args = [
                'post_type' => $post_type,
                'paged' => $paged > 0 ? $paged : 1
            ];

            return $this->get_posts($args, $limit, $orderby);
        }
        //查询指定日期范围的文章列表
        public function get_posts_by_date_range($year, $month = null, $day = null, $limit = 10)
        {
            $args = ['year' => $year];

            if ($month) {
                $args['monthnum'] = $month;
            }

            if ($day) {
                $args['day'] = $day;
            }

            return $this->get_posts($args, $limit);
        }
        //查询指定关键词的文章列表
        public function search_posts($keyword, $post_types = ['post'], $limit = 10)
        {
            $args = [
                's' => $keyword,
                'post_type' => $post_types
            ];

            return $this->get_posts($args, $limit);
        }
        //查询指定的ID列表
        public function list_posts($post_ids, $post_types = ['post'], $orderby = 'post__in')
        {
            if (empty($post_ids)) {
                return [];
            }

            $args = [
                'post__in' => (array) $post_ids,
                'posts_per_page' => -1,
                'orderby' => $orderby,
                'post_type' => $post_types,
            ];

            return $this->query($args);
        }
        //查询指定ID或SLUG
        public function get_post($id_or_slug, $post_type = 'post')
        {
            $args = is_numeric($id_or_slug)
                ? ['p' => $id_or_slug]
                : ['name' => $id_or_slug];

            $args['post_type'] = $post_type;

            $result = $this->query($args);

            return !empty($result) ? $result[0] : null;
        }
        //查询相关文章
        public function get_related_posts($post_id, $limit = 5)
        {
            //获取当前文章的分类和标签
            $categories = wp_get_post_categories($post_id);
            $tags = wp_get_post_tags($post_id);

            $tag_ids = [];
            foreach ($tags as $tag) {
                $tag_ids[] = $tag->term_id;
            }

            $args = [
                'post__not_in' => [$post_id], // 排除当前文章
                'posts_per_page' => $limit,
                'tax_query' => [
                    'relation' => 'OR',
                    [
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $categories
                    ]
                ]
            ];

            //如果有标签，添加到查询中
            if (!empty($tag_ids)) {
                $args['tax_query'][] = [
                    'taxonomy' => 'post_tag',
                    'field' => 'term_id',
                    'terms' => $tag_ids
                ];
            }

            return $this->query($args);
        }
        //查询热评排行
        public function get_popular_comments_posts($limit = 10, $args = [])
        {
            $query_args = wp_parse_args($args, [
                'posts_per_page' => $limit,
                'ignore_sticky_posts' => true,
                'orderby' => 'comment_count',
                'order' => 'DESC'
            ]);

            return $this->query($query_args);
        }
        //查询热门排行
        public function get_popular_views_posts($limit = 10, $args = [])
        {
            $query_args = wp_parse_args($args, [
                //限制查询时间范围为30天内
                'date_query' => array(
                    'after' => date('Y-m-d', strtotime('-30 days')),
                    'inclusive' => true,
                ),
                'meta_key' => 'view_count',
                'posts_per_page' => $limit,
                'ignore_sticky_posts' => true,
                'orderby' => 'meta_value_num',
                'order' => 'DESC'
            ]);

            return $this->query($query_args);
        }
        //查询喜欢排行
        public function get_popular_likes_posts($limit = 10, $args = [])
        {
            $query_args = wp_parse_args($args, [
                'meta_key' => 'like_count',
                'posts_per_page' => $limit,
                'ignore_sticky_posts' => true,
                'orderby' => 'meta_value_num',
                'order' => 'DESC'
            ]);

            return $this->query($query_args);
        }
        //查询随机文章
        public function get_random_posts($limit = 5, $args = [])
        {
            $query_args = wp_parse_args($args, [
                'posts_per_page' => $limit,
                'orderby' => 'rand'
            ]);

            return $this->query($query_args);
        }
    }
}

class AYA_Query_Post extends AYA_WP_Query_Object
{
    //魔术方法，捕获所有调用
    public function __call($name, $arguments)
    {
        //跳过特定方法
        $exclude_methods = [
            'set_default',
        ];

        if (in_array($name, $exclude_methods)) {
            return;
        }

        //...预留的后续操作数据缓存和请求拦截位置

        return call_user_func_array(array($this, $name), $arguments);
    }
}