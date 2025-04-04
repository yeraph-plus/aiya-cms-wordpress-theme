<?php

if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 文章格式过滤器
 * ------------------------------------------------------------------------------
 */

//添加钩子
add_action('the_content', 'aya_post_content_filter_format');
//合并方法
function aya_post_content_filter_format($content)
{
    if (!defined('AYA_RELEASE')) $start = microtime(true);

    if (aya_opt('site_content_dom_handler_bool', 'postpage')) {
        $content = aya_content_filter_dom_document($content);
    } else {
        //普通正则方法
        if (aya_opt('site_content_link_filter_bool', 'postpage')) {
            $content = aya_content_filter_link_tag($content);
        }
        if (aya_opt('site_content_img_filter_bool', 'postpage')) {
            $content = aya_content_filter_img_tag($content);
        }
    }

    if (!defined('AYA_RELEASE')) $end = microtime(true);
    if (!defined('AYA_RELEASE')) $content = 'The content load time: ' . ($end - $start) . ' seconds.' . $content;

    return $content;
}

//DOM版过滤器方法
function aya_content_filter_dom_document($content, $encode_utf8 = false)
{
    if (empty($content)) return $content;

    global $post;

    //强制编码为UTF-8
    if ($encode_utf8) {
        @$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
    }
    //用 DOMDocument 处理 HTML
    $dom = new DOMDocument();
    //忽略 HTML5 警告
    libxml_use_internal_errors(true);
    //加载 HTML
    $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    //清除报错
    //libxml_clear_errors();
    //格式<img>标签
    if (aya_opt('site_content_img_filter_bool', 'postpage')) {
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            //添加class
            $existing_class = $img->getAttribute('class');
            $img->setAttribute('class', trim($existing_class . ' lozad'));
            //添加属性
            $img->setAttribute('loading', 'lazy'); //eager
            //判断补充alt属性
            $existing_alt = $img->getAttribute('alt');
            if (empty($$existing_alt)) {
                $img->setAttribute('alt', get_the_title($post));
            }
        }
    }
    //格式化<a>标签
    if (aya_opt('site_content_link_filter_bool', 'postpage')) {
        $redirect_option = aya_opt('site_content_link_jump_page_type', 'postpage');
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            //如果是外部链接
            if (cur_is_external_url($href)) {
                //添加nofollow
                $rel = $link->getAttribute('rel');
                $rel = preg_replace('/\bnofollow\b/', '', $rel);
                $link->setAttribute('rel', trim($rel . ' nofollow'));
                //外链转内链
                if ($redirect_option == 'link') {
                    //外链提示页面
                    $link->setAttribute('href', add_query_arg('target', urlencode($href), home_url('/link/')));
                } else if ($redirect_option == 'go') {
                    //内跳页面
                    $link->setAttribute('href', add_query_arg('url', base64_encode($href), home_url('/go/')));
                    //添加target
                    $link->setAttribute('target', '_blank');
                } else {
                    //只添加target
                    $link->setAttribute('target', '_blank');
                }
            }
        }
    }
    //保存
    $this_content = $dom->saveHTML();
    return $this_content;
}

//格式化<a>标签
function aya_content_filter_link_tag($content)
{
    //遍历
    $matches = preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $urls);
    //如果存在a标签
    if (!is_null($urls)) {
        $redirect_option = aya_opt('site_content_link_jump_page_type', 'postpage');
        foreach ($urls[2] as $url) {
            //如果是外部链接
            if (cur_is_external_url($url)) {
                //外链提示页面
                if ($redirect_option == 'link') {
                    $url = add_query_arg('target', urlencode($url), home_url('/link/'));
                    $content = str_replace("href=\"$url\"", "href=\"" . $url . "\" rel=\"nofollow\"", $content);
                }
                //内跳页面
                else if ($redirect_option == 'go') {

                    $url = add_query_arg('url', base64_encode($url), home_url('/go/'));
                    $content = str_replace("href=\"$url\"", "href=\"" . $url . "\" rel=\"nofollow\" target=\"_blank\"", $content);
                }
                //只添加nofollow
                else {
                    $content = str_replace("href=\"$url\"", "href=\"" . $url . "\" rel=\"nofollow\" target=\"_blank\"", $content);
                }
            }
        }
    }

    return $content;
}

//格式化<img>标签
function aya_content_filter_img_tag($content)
{
    //遍历
    $matches = preg_match_all('/<img [^>]+>/', $content, $images);
    //如果存在img标签
    if (!is_null($images)) {
        global $post;

        foreach ($images[0] as $i => $image) {
            //检查class
            if (!preg_match('/class=["\']?([^"\']*)["\']?/i', $image)) {
                //添加
                $new_image = preg_replace('/<img /', '<img class="lozad" ', $image);
            } else {
                //追加
                $new_image = preg_replace('/class=["\']?([^"\']*)["\']?/i', 'class="lozad $1"', $image);
            }
            //检查alt
            if (!preg_match('/alt=["\']?([^"\']*)["\']?/i', $image)) {
                //如果没有alt属性，添加alt属性
                $new_image = preg_replace('/<img /', '<img alt="' . get_the_title($post) . '" ', $image);
            } else {
                //如果alt属性为空，添加alt属性
                $new_image = preg_replace('/alt=["\']?([^"\']*)["\']?/i', 'alt="' . get_the_title($post) . '" ', $image);
            }

            $content = str_replace($image, $new_image, $content);
        }
    }

    return $content;
}

//弃用：格式化<h>标签（用于生成文章目录）
function aya_content_filter_h1_tag($content)
{
    //遍历
    $matches = preg_match_all('/<h[123]>(.*?)<\/h[123]>/im', $content, $h1s);
    //如果存在标题
    if (!is_null($h1s)) {

        foreach ($h1s[1] as $i => $title) {
            //重写h1标签
            $start = stripos($content, $h1s[0][$i]);
            $end = strlen($h1s[0][$i]);
            $level = substr($h1s[0][$i], 1, 2);

            $content = substr_replace($content, '<' . $level . ' id="menu-' . $i + 1 . '">' . $title . '</' . $level . '>', $start, $end);
        }
    }

    return $content;
}

//弃用：格式化<table>标签
function aya_content_filter_table_tag($content)
{
    //遍历
    $matches = preg_match_all('/<table.*?>[\s\S]*<\/table>/', $content, $tables);
    //如果存在table标签
    if (!is_null($tables)) {

        foreach ($tables[0] as $i => $value) {
            //附加表格样式
            $table_html = str_replace('<table', '<table class="bordered"', $tables[0][$i]);

            $content = str_replace($tables[0][$i], $table_html, $content);
        }
    }

    return $content;
}

//弃用：格式化<pre>标签
function aya_content_filter_pre_tag($content)
{
    //遍历
    $matches = preg_match_all('/<pre.*?>(.+?)<\/pre>/is', $content, $pres);
    //如果存在pre标签
    if (!is_null($pres)) {

        foreach ($pres[1] as $match) {
            $code_html = trim($match);
            //检查pre标签class
            $code_html = preg_replace('/<pre class="([^"]*)">/', '<pre class="line-numbers $1">', $code_html);

            //如果没有code标签
            if (!(substr($code_html, 0, strlen('<code')) === '<code')) {
                $code_html = '<code class="language-plaintext">' . $code_html . '</code>';
            }
            //如果有code标签
            //UPDATE：不处理，前台直接JS处理
            /*
            if (substr($code_html, 0, strlen("<code>")) === "<code>") {
                //转义HTML
                $code_html = aya_html_clean($code_html);
                $code_html = '<code>' . substr($code_html, strlen('<code>'));
            }
            */
            $content = str_replace($match, $code_html, $content);
        }
    }

    return $content;
}

/*
 * ------------------------------------------------------------------------------
 * 文章保存过滤器
 * ------------------------------------------------------------------------------
 */

//添加动作 文章保存时循环一次
add_action('save_post', 'aya_save_formatting');

//循环方法
function aya_save_formatting($post_id)
{
    //如果是新文章就先跳过
    if (empty($post_id)) {
        return;
    }
    //检查用户权限
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    //检查是否为自动保存
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    //防止进入递归，先注销钩子
    remove_action('save_post', 'aya_save_formatting');

    //获取文章标题
    $post_title = get_post_field('post_title', $post_id);
    //获取文章内容
    $post_content = get_post_field('post_content', $post_id);

    //是否排版
    if (aya_opt('site_post_chs_compose_bool', 'postpage')) {

        //过滤一些禁止的参数
        $correct_array = aya_opt('site_post_chs_compose_type', 'postpage');

        //对文章内容进行格式化
        $formatted_content = aya_chs_type_setting($post_content, $correct_array);
        //对文章标题进行格式化
        $formatted_title = aya_chs_type_setting($post_title, $correct_array);
    }

    //是否刷新文章日期
    if (get_post_meta($post_id, 'reset_post_datetime', true)) {
        // 重置发布日期为当前时间
        $reset_date_time = current_time('mysql');
    }


    //更新文章内容
    $post_array = array();

    $post_array['ID'] = $post_id;

    if (!empty($formatted_content)) {
        $post_array['post_content'] = $formatted_content;
    }
    if (!empty($formatted_title)) {
        $post_array['post_title'] = $formatted_title;
    }
    if (!empty($reset_date_time)) {
        $post_array['post_date'] = $reset_date_time;
        $post_array['post_date_gmt'] = get_gmt_from_date($reset_date_time);
    }
    //更新文章
    wp_update_post($post_array);
    //恢复钩子
    add_action('save_post', 'aya_save_formatting');
}
