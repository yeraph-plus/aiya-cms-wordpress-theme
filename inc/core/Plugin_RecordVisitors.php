<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 插件 浏览量计数器
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

if (!class_exists('AYA_Plugin_RecordVisitors')) {

    class AYA_Plugin_RecordVisitors
    {
        private $AMP_COUNT;

        public function __construct()
        {
            //倍率调整
            $this->is_record_modifed();
            //计数器
            add_action('wp_footer', array($this, 'record_visitors'));
            add_action('the_post', array($this, 'add_view_count_to_post_object'));
            add_action('loop_start', array($this, 'prefetch_post_view_count'));
        }

        //从系统中检查常量已定义
        private function is_record_modifed()
        {
            //检查常量是否已定义
            if (defined('AYA_MODIFY_VISITORS')) {
                $apm = AYA_MODIFY_VISITORS;

                //检查是否为数字且不是负数
                if (!is_numeric($apm) || $apm < 0) {
                    $this->AMP_COUNT = 1;
                } else {
                    $this->AMP_COUNT = (float) $apm;
                }

            } else {
                $this->AMP_COUNT = 1;
            }
        }

        //浏览量计数器
        public function record_visitors()
        {
            if (is_singular()) {
                global $post;

                $count = get_post_meta($post->ID, 'view_count', true);

                if ($count) {
                    $count = intval($count) + 1;
                    update_post_meta($post->ID, 'view_count', $count);
                } else {
                    update_post_meta($post->ID, 'view_count', 1);
                }
            }
        }

        public function add_view_count_to_post_object($post)
        {
            if (is_object($post) && property_exists($post, 'ID')) {

                $the_views = get_post_meta($post->ID, 'view_count', true);

                $post->view_count = intval($the_views) * $this->AMP_COUNT;
            } else {
                $post->view_count = 0;
            }

            return $post;
        }

        //添加 Post_meta 预加载
        public function prefetch_post_view_count($wp_query)
        {
            //获取查询中所有文章
            $post_ids = wp_list_pluck($wp_query->posts, 'ID');

            if (!empty($post_ids)) {

                update_meta_cache('post', $post_ids);
            }
        }
    }

    //实例化
    new AYA_Plugin_RecordVisitors();
}