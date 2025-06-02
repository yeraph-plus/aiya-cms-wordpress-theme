<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 推文文章类型
 * ------------------------------------------------------------------------------
 */

//注册文章类型
//add_action('after_setup_theme', 'aya_post_type_tweet_action');
//MetaBox注册
//add_action('add_meta_boxes', 'aya_post_type_tweet_add_meta_box');

function aya_post_type_tweet_action()
{
    //注册文章类型
    $in_homepage = aya_opt('site_plugin_post_tweet', 'in_home');

    AYP::action(
        //'文章类型' => array('name' => '文章类型名','slug' => '别名','icon' => '图标','in_homepage' => 允许显示在首页),
        'Register_Post_Type',
        [
            'tweet' => [
                'name' => __('推文', 'AIYA'),
                'slug' => 'tweet',
                'icon' => 'dashicons-format-quote',
                'in_homepage' => $in_homepage,
            ],
        ]
    );
    //创建分类法
    if (aya_opt('site_plugin_post_tweet', 'use_tags')) {
        AYP::action(
            //'分类法' => array('name' => '分类法名', 'slug' => '别名', 'post_type' => array('此分类法适用的文章类型', ), 'tag_mode' => 设置为true则使用标签分类法模板 ),
            'Register_Tax_Type',
            [
                'collect' => [
                    'name' => __('标签', 'AIYA'),
                    'slug' => 'tweet_tags',
                    'post_type' => array('tweet'),
                    'tag_mode' => true,
                ],
            ]
        );
    }
}

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