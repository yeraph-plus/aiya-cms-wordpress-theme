<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 插件 文章点赞计数器和用户功能组
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

if (!class_exists('AYA_Plugin_UserAjaxActiveStore')) {

    class AYA_Plugin_UserAjaxActiveStore
    {
        public function __construct()
        {
            //前端action事件名：click_likes
            add_action('wp_ajax_click_likes', array($this, 'set_post_click_likes'));
            add_action('wp_ajax_nopriv_click_likes', array($this, 'set_post_click_likes'));
            //前端action事件名：click_favorites
            add_action('wp_ajax_click_favorites', array($this, 'set_post_click_favorites'));
            //前端action事件名：query_favorites
            add_action('wp_ajax_query_favorites', array($this, 'get_post_is_favorited'));
            //传递数据
            /*
            wp_localize_script('aya-click-likes-script', 'clickLikes', array(
                'nonce' => wp_create_nonce('aya_post_up_store'),
                'ajax_url' => admin_url('admin-ajax.php')
            ));
            */

            add_action('the_post', array($this, 'add_like_count_to_post_object'));
            add_action('loop_start', array($this, 'prefetch_post_like_count'));
        }
        //点赞计数器
        public function add_like_count_to_post_object($post)
        {
            if (is_object($post) && property_exists($post, 'ID')) {

                $the_likes = get_post_meta($post->ID, 'like_count', true);

                $post->like_count = intval($the_likes);
            }

            return $post;
        }
        //点赞请求
        public function set_post_click_likes()
        {
            //验证请求
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aya_post_up_store') || !isset($_POST['post_id'])) {

                wp_send_json(array('status' => 'error', 'message' => __('非法请求', 'AIYA')));

                return;
            }

            $post_id = intval($_POST['post_id']);

            // 获取并更新点赞数
            $count = get_post_meta($post_id, 'like_count', true);

            if ($count) {
                $count = intval($count) + 1;
                update_post_meta($post_id, 'like_count', $count);
            } else {
                update_post_meta($post_id, 'like_count', 1);
            }

            wp_send_json(array(
                'status' => 'done',
                'count' => $count
            ));
        }
        //收藏请求
        public function set_post_click_favorites()
        {
            //验证请求
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aya_post_up_store') || !isset($_POST['post_id'])) {

                wp_send_json(array('status' => 'error', 'message' => __('非法请求', 'AIYA')));

                return;
            }

            $post_id = intval($_POST['post_id']);

            $user_id = get_current_user_id();

            //获取用户收藏列表
            $favorites = get_user_meta($user_id, 'favorite_posts', true);

            if (!is_array($favorites)) {
                $favorites = array();
            }

            //检查是否已收藏
            if (in_array($post_id, $favorites)) {
                //已收藏，取消收藏
                $favorites = array_diff($favorites, array($post_id));
                update_user_meta($user_id, 'favorite_posts', $favorites);
                wp_send_json(array(
                    'status' => 'removed',
                    'message' => __('已取消收藏', 'AIYA'),
                    'post_id' => $post_id
                ));
            }
            //未收藏，添加到收藏列表
            else {
                $favorites[] = $post_id;

                update_user_meta($user_id, 'favorite_posts', $favorites);
                wp_send_json(array(
                    'status' => 'added',
                    'message' => __('已添加到收藏', 'AIYA'),
                    'post_id' => $post_id
                ));
            }
        }
        //获取已被收藏
        public function get_post_is_favorited()
        {
            //验证请求
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aya_post_up_store') || !isset($_POST['post_id'])) {

                wp_send_json(array('status' => 'error', 'message' => __('非法请求', 'AIYA')));

                return;
            }

            $post_id = intval($_POST['post_id']);

            $user_id = get_current_user_id();

            $favorites = get_user_meta($user_id, 'favorite_posts', true);

            //检查是否已收藏
            if (!is_array($favorites) || !in_array($post_id, $favorites)) {
                wp_send_json(array(
                    'status' => 'not_favorited',
                    'message' => __('未收藏', 'AIYA'),
                    'post_id' => $post_id
                ));
            } else {
                wp_send_json(array(
                    'status' => 'favorited',
                    'message' => __('已收藏', 'AIYA'),
                    'post_id' => $post_id
                ));
            }
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
    new AYA_Plugin_UserAjaxActiveStore();
}