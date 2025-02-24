<?php
if (!defined('ABSPATH')) exit;

/**
 * 
 * 用于解决Feed和Sitemap等各种XML中找不到问题在哪儿的的空白字符输出 -_-
 * 
 */

//直接将此方法添加到缓冲区
//ob_start('xml_whitespace_fix');
function xml_whitespace_fix($input)
{
    $allowed = false;
    $found = false;

    foreach (headers_list() as $header) {
        if (preg_match("/^content-type:\\s+(text\\/|application\\/((xhtml|atom|rss)\\+xml|xml))/i", $header)) {
            $allowed = true;
        }

        if (preg_match("/^content-type:\\s+/i", $header)) {
            $found = true;
        }
    }

    if ($allowed || !$found) {
        return preg_replace("/\\A\\s*/m", "", $input);
    } else {
        return $input;
    }
}

/**
 * 
 * 压缩html节约流量
 * 
 */

//排除pre标签压缩
//add_filter('the_content', 'pre_no_compress',999);
function pre_no_compress($content)
{
    if (preg_match_all('/<\/pre>/i', $content, $matches)) {
        $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->' . $content;
        $content .= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
    }
    return $content;
}

//直接将此方法添加到缓冲区
//ob_start('html_compress_main');
function html_compress_main($buffer)
{
    $initial = strlen($buffer);
    $buffer = explode("<!--wp-compress-html-->", $buffer);
    $count = count($buffer);
    $out = "";
    for ($i = 0; $i <= $count; $i++) {
        if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
            $buffer[$i] = (str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]));
        } else {
            $buffer[$i] = (str_replace("\t", " ", $buffer[$i]));
            $buffer[$i] = (str_replace("\n\n", "\n", $buffer[$i]));
            $buffer[$i] = (str_replace("\n", "", $buffer[$i]));
            $buffer[$i] = (str_replace("\r", "", $buffer[$i]));
            while (stristr($buffer[$i], '  ')) {
                $buffer[$i] = (str_replace("  ", " ", $buffer[$i]));
            }
        }
        $out .= $buffer[$i];
    }
    $final = strlen($out);
    $savings = ($initial - $final) / $initial * 100;
    $savings = round($savings, 2);
    $info = "<!--压缩前为:{$initial}bytes;压缩后为:{$final}bytes;节约:{$savings}%-->";
    return $out . $info;
}
