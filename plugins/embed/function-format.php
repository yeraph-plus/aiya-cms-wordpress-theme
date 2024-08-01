<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 获取中文排版格式化的实例
 * ------------------------------------------------------------------------------
 */

//加载预定义类

function get_chinese_setting_format($content)
{
    include_once AYA_PATH . '/inc/lib/ChineseTypesetting.php';

    $typesetting = new ChineseTypesetting();

    $content = $typesetting->insertSpace($content);
    $content = $typesetting->removeSpace($content);
    $content = $typesetting->full2Half($content);

    return $content;
}

/*
 * ------------------------------------------------------------------------------
 * 一些格式化方法
 * ------------------------------------------------------------------------------
 */
//add_action('save_post', 'aya_theme_save_post_auto_add_tags');
//正文图片添加懒加载状态
add_filter('the_content', 'aya_post_content_img_lazy', 99);
//加上bootstrap的表格class
add_filter('the_content', 'aya_post_content_bootstrap_table_class', 99);
//转义文内代码段的内容
add_filter('the_content', 'aya_post_content_pre_encode', 99);
//加上目录锚点ID
add_filter('the_content', 'aya_post_content_menu_id_list', 99);
//外部链接添加nofollow
add_filter('the_content', 'aya_post_content_link_nofollow', 99);
//添加中文格式化的实例
add_filter('the_content', 'aya_post_content_text_format', 199);

//格式化<img>标签
function aya_post_content_img_lazy($content)
{
    //遍历
    preg_match_all('/<img (.*?)\/>/', $content, $imgs);
    //如果存在img标签
    if (!is_null($imgs)) {
        foreach ($imgs[1] as $i => $value) {
            //再次遍历
            //preg_match('/src="(.*?)"/', $value, $src);
            //标签添加额外属性
            $replace_img = str_replace('alt=""', 'alt="content-image-' . $i . '"', $imgs[0][$i]);

            //获取主题设置
            if (get_checked('site_lozad_type', 'theme') == true) {
                $replace_img = str_replace('src', 'data-src', $replace_img);
                $replace_img = str_replace('<img', '<img class="lozad"', $replace_img);
            } else {
                $replace_img = str_replace('<img', '<img loding="lazy"', $replace_img);
            }
            $content = str_replace($imgs[0][$i], $replace_img, $content);
        }
    }

    return $content;
}
//格式化<table>标签
function aya_post_content_bootstrap_table_class($content)
{
    //遍历
    preg_match_all('/<table.*?>[\s\S]*<\/table>/', $content, $tables);
    //如果存在table标签
    if (!is_null($tables)) {
        foreach ($tables[0] as $i => $value) {
            $out = str_replace('<table', '<table class="table table-bordered"', $tables[0][$i]);
            $content = str_replace($tables[0][$i], $out, $content);
        }
    }

    return $content;
}
//格式化<h>标签
function aya_post_content_menu_id_list($content)
{
    //遍历
    preg_match_all('/<h[123]>(.*?)<\/h[123]>/im', $content, $ms);
    //如果存在标题
    if (!is_null($ms)) {
        foreach ($ms[1] as $i => $title) {
            $start = stripos($content, $ms[0][$i]);
            $end = strlen($ms[0][$i]);
            $level = substr($ms[0][$i], 1, 2);
            $content = substr_replace($content, '<' . $level . ' id="menu-' . $i + 1 . '">' . $title . '</' . $level . '>', $start, $end);
        }
    }

    return $content;
}
//格式化<pre>标签
function aya_post_content_pre_encode($content)
{
    //遍历
    preg_match_all('/<pre.*?>(.+?)<\/pre>/is', $content, $matches);
    //如果存在pre标签
    if (!is_null($matches)) {
        foreach ($matches[1] as $match) {
            $code = trim($match);
            //如果没有code标签
            if (!(substr($code, 0, strlen('<code')) === '<code')) {
                $code = '<code class="language-plaintext">' . $code . '</code>';
            }
            //如果有code标签
            //DEBUG：不处理，前台直接JS处理
            /*
            if (substr($code, 0, strlen("<code>")) === "<code>") {
                //转义HTML
                $code = aya_html_specialchars($code);
                $code = '<code>' . substr($code, strlen('<code>'));
            }
            */
            $content = str_replace($match, $code, $content);
        }
    }

    return $content;
}
//格式化<a>标签
function aya_post_content_link_nofollow($content)
{
    //遍历
    preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $url);
    //如果存在a标签
    if (!is_null($url)) {
        foreach ($url[2] as $val) {
            $content = str_replace("href=\"$val\"", "href=\"$val\" rel=\"nofollow\" target=\"_blank\"", $content);
        }
    }

    return $content;
}
//格式化<a>标签 (内链跳转)
function aya_post_content_link_go_page($content)
{
    //遍历
    preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $url);
    //如果存在a标签
    if (!is_null($url)) {
        foreach ($url[2] as $val) {
            $verify_val = strpos($val, '://');
            $verify_self = strpos($val, home_url());
            $verify_file = preg_match('/\.(jpg|jepg|png|ico|bmp|bnp|gif|tiff|zip|rar|exe|dmg|7z|svg|mp3|mp4|avi|3gp|webp)/i', $val);
            if ($verify_val !== false && $verify_self === false && !$verify_file) {
                $content = str_replace("href=\"$val\"", "href=\"" . home_url() . "/go/?url=" . base64_encode($val) . "\" rel=\"nofollow\" target=\"_blank\"", $content);
            }
        }
    }

    return $content;
}
//格式化<p>标签
function aya_post_content_text_format($content)
{
    //遍历
    preg_match_all('/<p>(.*)<\/p>/', $content, $text);
    //如果存在p标签
    if (!is_null($text)) {
        foreach ($text[1] as $text_val) {
            $format_text = get_chinese_setting_format($text_val);
            $content = str_replace($text_val, $format_text, $content);
        }
    }

    return $content;
}
