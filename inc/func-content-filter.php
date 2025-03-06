<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 文章内容过滤器
 * ------------------------------------------------------------------------------
 */

//添加钩子
add_action('the_content', 'aya_post_content_filter_format');

//合并方法
function aya_post_content_filter_format($content)
{
    if (aya_opt('site_content_link_filter_bool', 'postpage')) {
        $content = aya_post_content_filter_link_tag($content);
    }
    if (aya_opt('site_content_img_filter_bool', 'postpage')) {
        $content = aya_post_content_filter_img_tag($content);
    }

    return $content;
}

//外链转内链
function aya_link_jump_page($url)
{
    return $url;

    $option = aya_opt('site_content_link_jump_page_type', 'postpage');
    //生成格式
    switch ($option) {
        case 'go':
            return home_url() . "/go/?url=" . base64_encode($url);
        case 'link':
            return home_url() . "/link/?url=" . base64_encode($url);
        case 'false':
        default:
            return $url;
    }
}

//格式化<a>标签
function aya_post_content_filter_link_tag($content)
{
    //遍历
    preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $urls);
    //如果存在a标签
    if (!is_null($urls)) {

        foreach ($urls[2] as $url) {
            //验证URL
            $verify_val = strpos($url, '://');
            $verify_self = strpos($url, home_url());
            $verify_file = preg_match('/\.(jpg|jepg|png|ico|bmp|bnp|gif|tiff|zip|rar|exe|dmg|7z|svg|mp3|mp4|flv|wmv|heic|webp)/i', $url);
            //不是本站链接且不是文件
            if ($verify_val !== false && $verify_self === false && !$verify_file) {
                $content = str_replace("href=\"$url\"", "href=\"" . aya_link_jump_page($url) . "\" rel=\"nofollow\" target=\"_blank\"", $content);
            }
        }
    }

    return $content;
}

//格式化<img>标签
function aya_post_content_filter_img_tag($content)
{
    //遍历
    preg_match_all('/<img [^>]+>/', $content, $images);
    //如果存在img标签
    if (!is_array($images)) {
        return $content;
    }

    foreach ($images[0] as $i => $image) {
        //检查class
        if (!preg_match('/class=["\']?([^"\']*)["\']?/i', $image)) {
            //添加
            $image = preg_replace('/<img /', '<img class="lozad" ', $image);
        } else {
            //追加
            $image = preg_replace('/class=["\']?([^"\']*)["\']?/i', 'class="$1 lozad"', $image);
        }
        //检查alt
        if (!preg_match('/alt=["\']?([^"\']*)["\']?/i', $image)) {
            global $post;
            // 如果没有alt属性，添加alt属性
            $image = preg_replace('/<img /', '<img alt="' . get_the_title($post->ID) . '-PIC-' . $i + 1 . '" ', $image);
        }

        $content = str_replace($image, $image, $content);
    }

    return $content;
}

//弃用：格式化<h>标签（用于生成文章目录）
function aya_post_content_filter_h1_tag($content)
{
    //遍历
    preg_match_all('/<h[123]>(.*?)<\/h[123]>/im', $content, $h1s);
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
function aya_post_content_filter_table_tag($content)
{
    //遍历
    preg_match_all('/<table.*?>[\s\S]*<\/table>/', $content, $tables);
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
function aya_post_content_filter_pre_tag($content)
{
    //遍历
    preg_match_all('/<pre.*?>(.+?)<\/pre>/is', $content, $pres);
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
 * 加载实例方法
 * ------------------------------------------------------------------------------
 */

//加载库
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;
use Overtrue\Pinyin\Pinyin;

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

/*
 * ------------------------------------------------------------------------------
 * 文章保存过滤器
 * ------------------------------------------------------------------------------
 */

//add_filter('wp_insert_term_data', 'aya_insert_term_data_slug', 10, 3);
//add_filter('wp_update_term_data', 'aya_update_term_data_slug', 10, 4);
//add_filter('wp_insert_post_data', 'aya_insert_post_data_slug', 10, 2);

//添加分类时替换分类slug为拼音
function aya_insert_term_data_slug($data, $taxonomy, $term_arr)
{
    if (aya_opt('site_save_term_slug_pinyin_type', 'format')) {
        //已存在，跳过
        if (!empty($term_arr['slug'])) return $data;

        $data['slug'] = wp_unique_term_slug(sanitize_title(aya_pinyin_permalink($data['name'], true)), (object) $term_arr);
    }

    return $data;
}

//更新分类时替换分类slug为拼音
function aya_update_term_data_slug($data, $term_id, $taxonomy, $term_arr)
{
    if (aya_opt('site_save_term_slug_pinyin_type', 'format')) {
        //已存在，跳过
        if (!empty($term_arr['slug'])) return $data;

        $data['slug'] = wp_unique_term_slug(sanitize_title(aya_pinyin_permalink($data['name'], true)), (object) $term_arr);
    }

    return $data;
}

//保存文章时替换文章slug为拼音
function aya_insert_post_data_slug($data, $post_arr)
{
    //跳过自动草稿
    if ('auto-draft' === $post_arr['post_status']) return $data;

    if (aya_opt('site_save_post_slug_pinyin_type', 'format')) {
        //已存在，跳过
        if (!empty($post_arr['post_name'])) return $data;
        //检查标题是否为空
        if (empty($post_arr['post_title'])) return $data;


        $formatted_sulg = sanitize_title(aya_pinyin_permalink($post_arr['post_title'], true));

        $data['post_name'] = wp_unique_term_slug($formatted_sulg, (object) $post_arr);
    }

    if (aya_opt('site_save_post_chinese_type', 'format')) {
        $data['post_title'] = aya_chinese_type_setting($post_arr['post_title']);
        $data['post_content'] = aya_chinese_type_setting($post_arr['post_content']);
    }

    return $data;
}

//添加动作 文章保存时循环一次
//add_action('save_post', 'aya_save_formatting');

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
    if (aya_opt('site_save_post_chinese_type', 'format')) {
        //对文章内容进行格式化
        $formatted_content = aya_chinese_type_setting(nl2br($post_content));
        //对文章标题进行格式化
        $formatted_title = aya_chinese_type_setting($post_title);
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
    //更新文章
    wp_update_post($post_array);
    //恢复钩子
    add_action('save_post', 'aya_save_formatting');
}
