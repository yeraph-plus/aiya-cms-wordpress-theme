<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 插件 文章点赞计数器
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

if (!class_exists('AYA_Plugin_RecordClickLikes')) {

    class AYA_Plugin_RecordClickLikes
    {
        public function __destruct()
        {
            //前端action事件名：click_likes
            add_action('wp_ajax_click_likes', array($this, 'set_post_click_likes'));
            add_action('wp_ajax_nopriv_click_likes', array($this, 'set_post_click_likes'));

            add_action('the_post', array($this, 'add_like_count_to_post_object'));
            add_action('loop_start', array($this, 'prefetch_post_like_count'));
        }
        //点赞计数器
        public function set_post_click_likes()
        {
            //验证请求
            if (!isset($_POST['post_id'])) {
                $response = array('status' => 'error');

                wp_send_json($response);
            } else {
                $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

                $count = get_post_meta($post_id, 'like_count', true);

                if ($count) {
                    $count = intval($count) + 1;
                    update_post_meta($post_id, 'like_count', $count);
                } else {
                    update_post_meta($post_id, 'like_count', 1);
                }

                $response = array('status' => 'done');

                wp_send_json($response);
            }
        }

        public function add_like_count_to_post_object($post)
        {
            if (is_object($post) && property_exists($post, 'ID')) {

                $the_likes = get_post_meta($post->ID, 'like_count', true);

                $post->like_count = intval($the_likes);
            }

            return $post;
        }

        //添加 Post_meta 预加载
        public function prefetch_post_like_count($wp_query)
        {
            //获取查询中所有文章
            $post_ids = wp_list_pluck($wp_query->posts, 'ID');

            if (!empty($post_ids)) {

                update_meta_cache('post', $post_ids);
            }
        }
    }

    //实例化
    new AYA_Plugin_RecordClickLikes();
}