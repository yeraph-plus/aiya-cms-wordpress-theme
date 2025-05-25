<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 处理WP的分页数据结构到数组
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

if (!class_exists('AYA_WP_Paged_Object')) {
    class AYA_WP_Paged_Object
    {
        //分页数据格式
        public static function get_pagination($args = [], $format = 'standard')
        {
            global $wp_query;

            $default_args = [
                'total' => $wp_query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
            ];

            $merged_args = wp_parse_args($args, $default_args);

            $paged = new self($merged_args);

            $paged_data = $paged->paged_data;

            switch ($format) {
                case 'full':
                    //完整的分页数据
                    return $paged_data;
                case 'simple':
                    //简单分页数据
                    $current = $paged_data['info']['current'];
                    $total = $paged_data['info']['total'];
                    return [
                        'current' => $current,
                        'total' => $total,
                        'has_prev' => $current > 1,
                        'has_next' => $current < $total,
                        'prev_url' => $current > 1 ? $paged->get_paginated_url($current - 1) : '',
                        //'prev_text' => $args['prev_text'] ?? '',
                        'next_url' => $current < $total ? $paged->get_paginated_url($current + 1) : '',
                        //'next_text' => $args['next_text'] ?? '',
                    ];
                default:
                case 'standard':
                    //标准分页数据
                    return [
                        'current' => $paged_data['info']['current'],
                        'total' => $paged_data['info']['total'],
                        'perPage' => $paged_data['info']['per_page'],
                        'links' => array_map(function ($item) {
                            return [
                                'type' => $item['type'],
                                'url' => $item['url'],
                                'text' => $item['text'],
                                'label' => strip_tags($item['text']),
                                'active' => in_array($item['type'], ['current', 'dots']),
                            ];
                        }, $paged_data['items'])
                    ];
            }
        }

        private $args = [];
        private $paged_data = [];
        //构造
        public function __construct($args = [])
        {
            //与 paginate_links() 兼容的参数
            $this->args = wp_parse_args($args, [
                'base' => '', // 基础URL
                'format' => '', // URL格式
                'total' => 1,  // 总页数
                'current' => 0,  // 当前页码
                'show_all' => false, // 是否显示所有页码
                'end_size' => 1,  // 开始和结束处显示的页码数
                'mid_size' => 2,  // 当前页码左右两边显示的页码数
                'prev_next' => true, // 是否显示上一页下一页
                'prev_text' => __('&laquo; Previous', 'AIYA'), // 上一页文本
                'next_text' => __('Next &raquo;', 'AIYA'),     // 下一页文本
                'add_args' => [],  // 添加到URL的参数
                'add_fragment' => '', // 添加到URL的片段
                'pages_text' => __('Page %1$s of %2$s', 'AIYA'), // 页码文本
                'aria_current' => 'page', // aria-current属性
            ]);

            //处理参数
            $this->prepare_args();
            //构建分页数据
            $this->build_paged_data();
        }
        //处理查询参数
        private function prepare_args()
        {
            global $wp_query, $wp_rewrite;

            // 总页数
            if (empty($this->args['total']) && isset($wp_query->max_num_pages)) {
                $this->args['total'] = $wp_query->max_num_pages;
            }
            // 当前页码
            if (empty($this->args['current'])) {
                $this->args['current'] = max(1, get_query_var('paged') ?: 1);
            }
            // 基础URL
            if (empty($this->args['base'])) {
                // 获取完整的页面链接
                $pagenum_link = html_entity_decode(get_pagenum_link());

                // 分离URL和查询参数
                $url_parts = explode('?', $pagenum_link);

                // 获取当前页面的基础URL，保留域名和路径
                $base = $url_parts[0];

                // 保留原始URL结构，仅在需要的位置添加分页格式
                $this->args['base'] = trailingslashit($base) . '%_%';

                // 处理查询参数
                if (isset($url_parts[1])) {
                    $query_args = [];
                    parse_str($url_parts[1], $query_args);

                    // 移除可能与分页冲突的参数
                    if (isset($query_args['paged'])) {
                        unset($query_args['paged']);
                    }

                    // 合并自定义参数
                    $this->args['add_args'] = array_merge(
                        $query_args,
                        is_array($this->args['add_args']) ? $this->args['add_args'] : []
                    );
                }
            }

            // URL格式
            if (empty($this->args['format'])) {
                if ($wp_rewrite->using_permalinks()) {
                    // 使用永久链接
                    $this->args['format'] = user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged');
                } else {
                    // 不使用永久链接
                    $this->args['format'] = '?paged=%#%';
                }

                // 处理index.php情况
                if ($wp_rewrite->using_index_permalinks() && !strpos($this->args['base'], 'index.php')) {
                    $this->args['format'] = 'index.php/' . $this->args['format'];
                }
            }
        }
        //使用页码拼接URL
        private function get_paginated_url($page_num)
        {
            if ($page_num <= 1) {
                // 第一页返回不带分页参数的URL
                $url = str_replace('%_%', '', $this->args['base']);
            } else {
                // 替换占位符生成URL
                $url = str_replace('%_%', str_replace('%#%', $page_num, $this->args['format']), $this->args['base']);
            }

            // 添加额外查询参数
            if (!empty($this->args['add_args'])) {
                $url = add_query_arg($this->args['add_args'], $url);
            }

            // 添加URL片段
            if (!empty($this->args['add_fragment'])) {
                $url .= $this->args['add_fragment'];
            }

            return $url;
        }
        //构建分页数据数组
        private function build_paged_data()
        {
            $total = $this->args['total'];
            $current = $this->args['current'];
            $end_size = $this->args['end_size'];
            $mid_size = $this->args['mid_size'];
            $show_all = $this->args['show_all'];

            //基础数据
            $this->paged_data['info'] = [
                'total' => $total,
                'current' => $current,
                'per_page' => get_query_var('posts_per_page'),
                'pages_text' => sprintf($this->args['pages_text'], $current, $total),
            ];

            //如果只有一页
            if ($total <= 1) {
                $this->paged_data['items'] = [];
                return;
            }

            //构建分页列表
            $this->paged_data['items'] = [];

            //添加上一页
            if ($this->args['prev_next'] && $current > 1) {
                $this->paged_data['items'][] = [
                    'type' => 'prev',
                    'url' => $this->get_paginated_url($current - 1),
                    'text' => $this->args['prev_text'],
                    'class' => 'prev page-numbers',
                ];
            }

            //生成页码
            $dots_added = false;

            for ($n = 1; $n <= $total; $n++) {
                //是否显示当前页码
                $should_show = false;

                if ($show_all) {
                    // 显示所有页码
                    $should_show = true;
                } elseif ($n <= $end_size) {
                    // 开头部分页码
                    $should_show = true;
                } elseif ($n >= $total - $end_size + 1) {
                    // 结尾部分页码
                    $should_show = true;
                } elseif ($n >= $current - $mid_size && $n <= $current + $mid_size) {
                    // 中间部分页码
                    $should_show = true;
                }

                if ($should_show) {
                    $dots_added = false;
                    if ($n === $current) {
                        //当前页码
                        $this->paged_data['items'][] = [
                            'type' => 'current',
                            'url' => '#', //当前页没有链接
                            'text' => $n,
                            'class' => 'page-numbers current',
                            'aria-current' => $this->args['aria_current'],
                        ];
                    } else {
                        //其他页码
                        $this->paged_data['items'][] = [
                            'type' => 'page',
                            'url' => $this->get_paginated_url($n),
                            'text' => $n,
                            'class' => 'page-numbers',
                        ];
                    }
                } elseif (!$dots_added) {
                    //省略号
                    $dots_added = true;
                    $this->paged_data['items'][] = [
                        'type' => 'dots',
                        'url' => '',
                        'text' => '&hellip;',
                        'class' => 'page-numbers dots',
                    ];
                }
            }
            //添加下一页
            if ($this->args['prev_next'] && $current < $total) {
                $this->paged_data['items'][] = [
                    'type' => 'next',
                    'url' => $this->get_paginated_url($current + 1),
                    'text' => $this->args['next_text'],
                    'class' => 'next page-numbers',
                ];
            }
        }
    }
}