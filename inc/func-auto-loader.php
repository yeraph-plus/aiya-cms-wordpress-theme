<?php

if (!defined('ABSPATH')) {
    exit;
}

//加载 Composer 依赖
aya_require('autoload', 'lib/vendor');

//加载其他工具类
if (!class_exists('AYA_WP_Menu_ObjectInArray')) {
    require_once AYA_PATH . '/inc/lib/WP_Menu.php';
}


/*
 * ------------------------------------------------------------------------------
 * 用于隐藏数字ID的XDeode类
 * ------------------------------------------------------------------------------
 */

if (!class_exists('XDE_code')) {
    require_once AYA_PATH . '/inc/lib/XDeode.php';
}

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

    include_once AYA_PATH . '/inc/class/BFI_Thumb.php';

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
 * 一些可能会用到的 Curl 方法
 * ------------------------------------------------------------------------------
 */

//获取一言
function aya_curl_get_hitokoto()
{
    //获取一言API
    $concent = @file_get_contents('https://v1.hitokoto.cn/?encode=json');
    //检查是否被403
    if ($concent === false) {
        //获取本地json文件
        $data = AYA_PATH . '/assets/json/hitokoto.json';
        //检查文件存在
        if (!file_exists($data)) {
            return 'ERROR: Cannot read under <code>hitokoto.json</code>';
        }
        //读取到字符串中
        $json = file_get_contents($data);
        //读取JSON
        $array = json_decode($json, true);
        //随机提取一条
        $count = count($array);
        if ($count != 0) {
            $hitokoto = $array[array_rand($array)]['hitokoto'];
        } else {
            $hitokoto = 'ERROR: Cannot read under <code>hitokoto.json</code>';
        }
        //返回数据
        return $hitokoto;
    }
    //读取JSON
    $concent = json_decode($concent, true);
    //提取一言
    $hitokoto = $concent['hitokoto'];
    //返回数据
    return $hitokoto;
}

//获取必应每日一图
function aya_curl_bing_image()
{
    //header('Location: $imgurl');
    //获取必应API
    $content = @file_get_contents('https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
    //读取JSON
    $content = json_decode($content, true);
    //提取图片url
    $imgurl = 'https://www.bing.com' . $content['images'][0]['url'];
    //返回数据
    return $imgurl;
}

//获取百度翻译
function aya_curl_baidu_translator($name)
{
    $api_url = 'https://fanyi-api.baidu.com/api/trans/vip/translate';
    $app_id = 'baidu_app_id';
    $app_key = 'baidu_api_key';
    $app_salt = rand(10000, 99999);

    if (!$app_id || !$app_key) {
        return false;
    }
    //生成SDK文档的签名
    $str = $app_id . $name . $app_salt . $app_key;
    $sign = md5($str);
    //请求参数
    $args = array(
        'q' => $name,
        'from' => 'auto',
        'to' => 'en',
        'appid' => $app_id,
        'salt' => $app_salt,
        'sign' => $sign,
    );
    //使用WP的 POST 请求方法
    $wp_post_args = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $args,
        'cookies' => array(),
    );

    //发送请求
    $response = wp_remote_post($api_url, $wp_post_args);

    //获取返回数据
    if (is_wp_error($response)) {
        return false;
    }
    //读取JSON
    $data = json_decode(wp_remote_retrieve_body($response));

    if (isset($data->error_code)) {
        return false;
    }

    $result = $data->trans_result[0]->dst;

    return $result;
}

/*
 * ------------------------------------------------------------------------------
 * 加载Composer的实例方法
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