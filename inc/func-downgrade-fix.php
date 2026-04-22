<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 配置一些独立依赖缺少时的降级方法
 * ------------------------------------------------------------------------------
 */

// 文章缩略图处理（备用方法）
if (!function_exists('aya_get_post_thumb')) {

    //BFI_Thumb组件
    aya_require('BFI_Thumb', 'lib');

    /*
    * ------------------------------------------------------------------------------
    *  BFI_Thumb.php 缩略图组件及相关函数
    * ------------------------------------------------------------------------------
    * 
    * Demo1
    * $size = array( 400, 300, 'opacity' => 50, 'grayscale' => true, 'bfi_thumb' => true );
    * wp_get_attachment_image_src( $attachment_id, $size )
    * 
    * Else
    * the_post_thumbnail( array( 1024, 400, 'bfi_thumb' => true, 'grayscale' => true ) );
    * 
    * Demo2
    * $params = array( 'width' => 400, 'height' => 300, 'opacity' => 50, 'grayscale' => true, 'colorize' => '#ff0000' );
    * bfi_thumb( "URL-to-image.jpg", $params );
    * 
    */

    // BFI_Thumb调用函数
    function get_bfi_thumb($url, $width = 0, $height = 0, $crop_y = 0, $crop_x = 0, $crop_only = false)
    {
        $url = esc_url($url);
        //判断是否是本地图片
        if (strpos($url, AYA_HOME) === false) {
            return $url;
        }

        //图片质量设置
        $thumb_quality = 96;

        //判断参数
        if ($height == 'full') {
            //仅缩放
            $params = array(
                'width' => $width,
                'quality' => $thumb_quality
            );
        } else {
            //生成缩略图
            $params = array(
                'width' => $width, //int pixels
                'height' => $height, //int pixels
                'crop' => true, //bool
                'crop_only' => $crop_only, //bool
                'crop_x' => $crop_x ? $crop_x : 0, //bool string
                'crop_y' => $crop_y ? $crop_y : 0, //bool string
                'quality' => $thumb_quality //int 1-100
            );
        }

        return bfi_thumb($url, $params);
    }

    // 相同逻辑实现
    function aya_get_post_thumb($image_url = false, $post_id = 0, $size_w = 400, $size_h = 300)
    {
        // 如果已传入 URL 直接用它
        if ($image_url == false) {
            // 按文章 ID 读取正文，避免误用全局 $post
            $post_content = $post_id ? (string) get_post_field('post_content', (int) $post_id) : '';
            $image_url = aya_match_post_first_image($post_content, false);
        }

        // 无图片时使用主题默认
        if ($image_url === false) {
            $image_url = aya_opt('site_default_thumb_upload', 'basic');
        }

        return get_bfi_thumb($image_url, $size_w, $size_h);
    }
}
