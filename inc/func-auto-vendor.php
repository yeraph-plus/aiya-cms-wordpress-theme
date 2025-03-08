<?php

if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 加载实例方法
 * ------------------------------------------------------------------------------
 */

//加载库
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;
use Overtrue\Pinyin\Pinyin;
use Overtrue\PHPOpenCC\OpenCC;
//use Overtrue\PHPOpenCC\Strategy;

//应用中文格式化实例
function aya_chinese_type_setting($content)
{
    $typesetting = new ChineseTypesetting();

    //$formatted_content = $typesetting->correct($content, ['insertSpace', 'removeSpace', 'full2Half', 'fixPunctuation', 'properNoun']);
    $formatted_content = $typesetting->correct($content, aya_opt('site_save_post_chinese_setting', 'format'));

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
        $slug = $pinyin->abbr($slug, $divider);
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
    return $converter->convert($content, $drive);
}
