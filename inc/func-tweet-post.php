<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 自定义推文文章类型
 * ------------------------------------------------------------------------------
 */

//注册文章类型
add_action('after_setup_theme', 'aya_post_type_tweet_action');
//MetaBox注册
add_action('add_meta_boxes', 'aya_post_type_tweet_add_meta_box');

function aya_post_type_tweet_action()
{
    //注册文章类型
    AYF::module(
        //'文章类型' => array('name' => '文章类型名','slug' => '别名','icon' => '图标','in_homepage' => 允许显示在首页),
        'Register_Post_Type',
        [
            'tweet' => [
                'name' => __('推文', 'AIYA'),
                'slug' => 'tweet',
                'icon' => 'dashicons-format-quote',
                'in_homepage' => false,
            ],
        ]
    );
}

//使自定义文章类型可以操作置顶
function aya_post_type_tweet_add_meta_box()
{
    add_meta_box('aya_tweet_product_sticky', __('置顶', 'AIYA'), 'aya_tweet_product_sticky', 'tweet', 'side', 'high');
}

function aya_tweet_product_sticky()
{
    printf(
        '<p>
            <label for="super-sticky" class="selectit">
                <input id="super-sticky" name="sticky" type="checkbox" value="sticky" %s />
                %s
            </label>
        </p>',
        checked(is_sticky(), true, false),
        esc_html__('置顶这篇文章', 'AIYA')
    );
}

// TODO 推文类型支持标签索引
// TODO 前台发帖接口，允许所有用户在此类型发帖