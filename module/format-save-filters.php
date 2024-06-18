<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 文章保存格式过滤器
 * ------------------------------------------------------------------------------
 */

//加载预定义类
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

function aya_filter_chinese_type_setting_format($content)
{
    $typesetting = new ChineseTypesetting();

    $content = $typesetting->correct($content, ['insertSpace', 'removeSpace', 'full2Half', 'fixPunctuation', 'properNoun', 'removeClass', 'removeId']);

    return $content;
}
