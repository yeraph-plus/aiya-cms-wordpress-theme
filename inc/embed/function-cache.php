<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 缓存方法
 * ------------------------------------------------------------------------------
 */

function aya_cache_key_load($key)
{
    return "wp:'CACHE_CONTENT':" . $key;
}

// 缓存获取
function aya_cache_get($key, $force = false, &$found = null)
{
    return wp_cache_get($key, 'CACHE_CONTENT', $force, $found);
}

// 缓存设置
function aya_cache_set($key, $value, $expiration = null)
{
    $expiration = ($expiration === null) ? AYA_CACHE_SECOND : $expiration;
    return wp_cache_set($key, $value, 'CACHE_CONTENT', $expiration);
}

// 缓存删除
function aya_cache_delete($key, $time = 0)
{
    return wp_cache_delete($key, 'CACHE_CONTENT', $time);
}

// 缓存删除
function aya_cache_delete_multiple($keys)
{
    return wp_cache_delete_multiple($keys, 'CACHE_CONTENT');
}

// 缓存删除
function aya_cache_delete_find_keys($find_key)
{
    aya_cache_roc_call(function ($redis) use ($find_key) {
        $keys = $redis->keys($find_key);
        if ($keys && is_array($keys)) {
            foreach ($keys as $key) {
                $redis->del($key);
            }
        }
    });
}

// 清除缓存注册
function aya_cache_del_register()
{
    add_action('comment_post', 'aya_cache_del_comments_post', 10, 3);
    add_action('transition_comment_status', 'aya_cache_del_comment', 10, 3);
    add_action('aya_option_updated', 'aya_cache_del_options_updated', 10, 1);
    add_action('save_post', 'aya_cache_del_save_post', 10, 3);
}

add_action('init', 'aya_cache_del_register');

function aya_cache_del_comments_post($comment_ID, $comment_approved, $commentdata)
{
    if ($comment_approved) {
        aya_cache_delete(sprintf(PKC_AUTHOR_COMMENTS, md5($commentdata['comment_author_email'])));
        aya_cache_delete(PKC_TOTAL_COMMENTS);
        aya_cache_delete(PKC_WIDGET_NEW_COMMENTS);
    }
}

function aya_cache_del_comment($new_status, $old_status, $comment)
{
    aya_cache_delete(sprintf(PKC_AUTHOR_COMMENTS, $comment->comment_author_email));
    aya_cache_delete(PKC_TOTAL_COMMENTS);
    aya_cache_delete(PKC_WIDGET_NEW_COMMENTS);
}

function aya_cache_del_options_updated($opts)
{
    wp_cache_flush();
}

function aya_cache_del_save_post($post_id, $post, $is_update)
{
    if ($post->post_type == 'moments') {
        aya_cache_delete_find_keys(aya_cache_key_load(sprintf(PKC_MOMENTS, '*')));
    }
}


function aya_cache_roc_call($function)
{
    if (function_exists('redis_object_cache')) {
        if (redis_object_cache()->get_status() == 'Connected') {
            global $wp_object_cache;
            $function($wp_object_cache->redis_instance());
        }
    }
}
