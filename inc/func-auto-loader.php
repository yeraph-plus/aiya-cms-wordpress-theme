<?php

if (!defined('ABSPATH')) {
    exit;
}

//加载 Composer 依赖
aya_require('autoload', 'vendor');
//加载引用组件
aya_require('BFI_Thumb', 'lib');
aya_require('XDeode', 'lib');
aya_require('Plugin_RecordClickLikes', 'core');
aya_require('Plugin_RecordVisitors', 'core');
//加载其他工具类
aya_require('WP_Breadcrumb', 'core');
aya_require('WP_Menu', 'core');
aya_require('WP_Paged', 'core');
aya_require('WP_Post', 'core');
aya_require('WP_Post', 'core');
//aya_require('WP_Term', 'core');
//aya_require('WP_Query', 'core');

/*
 * ------------------------------------------------------------------------------
 * 用于隐藏数字ID的XDeode类
 * ------------------------------------------------------------------------------
 */

//加密方法
function aya_token_encode($token, $length = 15)
{
    $obj = new XDE_code($length);

    return $obj->encode($token);
}

//解密方法
function aya_token_decode($token, $length = 15)
{
    $obj = new XDE_code($length);

    return $obj->decode($token);
}

/*
 * ------------------------------------------------------------------------------
 * 缩略图组件及相关函数
 * ------------------------------------------------------------------------------
 * 
 * BFI_Thumb.php
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

//BFI_Thumb调用函数
function get_bfi_thumb($url, $width = 0, $height = 0, $crop_y = 0, $crop_x = 0, $crop_only = false)
{
    $url = esc_url($url);
    //判断是否是本地图片
    if (strpos($url, get_site_url()) === false) {
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

/*
 * ------------------------------------------------------------------------------
 * 加载Composer实例中的方法
 * ------------------------------------------------------------------------------
 */

//加载库
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;
use Overtrue\Pinyin\Pinyin;
use Overtrue\PHPOpenCC\OpenCC;
//use Overtrue\PHPOpenCC\Strategy;
use Overtrue\ChineseCalendar\Calendar;

//应用中文格式化实例
function aya_chs_type_setting($content, $correct_array)
{
    $typesetting = new ChineseTypesetting();

    //$formatted_content = $typesetting->correct($content, ['insertSpace', 'removeSpace', 'full2Half', 'fixPunctuation', 'properNoun']);

    $formatted_content = $typesetting->correct($content, $correct_array);

    //返回格式化后的内容
    return $formatted_content;
}

//应用拼音转 SLUG 换实例
function aya_pinyin_permalink($slug, $abbr = false)
{
    //传入如果不是字符串
    $slug = strval($slug);
    //设置最大词长
    $length = 60;
    //设置字符
    $divider = '-'; //可用参数 '_', '-', '.', ''

    $pinyin = new Pinyin();

    //是否使用索引模式
    if ($abbr === true) {
        $slug = $pinyin->permalink($slug, $divider);
    } else {
        $slug = $pinyin->abbr($slug);
    }
    //截取最大长度
    $slug = aya_trim_slug($slug, $length, $divider);
    //返回格式化后的内容 //格式为：'带着希望去旅行' -> 'dai-zhe-xi-wang-qu-lyu-xing'
    return $slug;
}

//应用通用拼音转换实例
function aya_pinyin_setting($content, $tone = true)
{
    //传入如果不是字符串
    $content = strval($content);

    $pinyin = new Pinyin();

    //是否添加声调
    $tone = ($tone) ? 'none' : '';

    //返回格式化后的内容
    return $pinyin->sentence($content, $tone);
}

//应用繁体转换实例
function aya_opencc_setting($content, $drive = 's2t')
{
    //传入如果不是字符串
    $content = strval($content);

    //创建转换器实例
    $converter = new OpenCC();

    //进行转换
    return $converter->convert($content, 'T2JP');
}

//应用农历转换实例
function aya_calendar_setting($content, $drive = 's2t')
{
    //传入如果不是字符串
    $content = strval($content);

    //创建转换器实例
    $calendar = new Calendar();

    //date_default_timezone_set('PRC'); 
    $result = $calendar->solar(2017, 5, 5); // 阳历
    $result = $calendar->lunar(2017, 4, 10); // 阴历
    $result = $calendar->solar(2017, 5, 5, 23); // 阳历，带 $hour 参数

    //进行转换
    return '';
}