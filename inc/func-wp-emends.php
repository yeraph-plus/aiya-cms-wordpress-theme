<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 首次启动
 * ------------------------------------------------------------------------------
 */

add_action('after_switch_theme', 'aya_theme_after_init');

function aya_theme_after_init()
{
    //刷新站点重写规则
    flush_rewrite_rules();

    //跳转主题设置页面
    /*
    global $pagenow;

    if ('themes.php' == $pagenow && isset($_GET['activated'])) {
        // options-general.php 改成你的主题设置页面网址
        wp_redirect(admin_url('?page=aya-options-basic'));
        exit;
    }
    */

    //安装数据表
    do_action('aya_install');
}

/*
 * ------------------------------------------------------------------------------
 * 重写路由
 * -------------------s-----------------------------------------------------------
 */

//初始化时增加重写路由规则
add_action('init', 'aya_author_change_rewrite_base');
add_action('init', 'aya_author_add_user_rewrite_rules');
//重写author的链接
add_filter('author_link', 'aya_author_custom_link', 10, 2);
//使用自定义的REST-API路径
add_filter('rest_url_prefix', 'aya_rest_api_url_prefix');
//兼容旧路由规则
add_action('template_redirect', 'aya_author_template_redirect');
add_action('template_redirect', 'aya_rest_api_template_redirect');

//生成/user/{token}格式的作者链接
function aya_author_custom_link($link, $author_id)
{
    return home_url('/user/' . $author_id . '/');
}

//修改作者页面路由基准为user
function aya_author_change_rewrite_base()
{
    global $wp_rewrite;

    $wp_rewrite->author_base = 'user';
    $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
}

//添加 /user/ 重写规则
function aya_author_add_user_rewrite_rules()
{
    add_rewrite_rule(
        '^user/(\d+)/?$',
        'index.php?author=$matches[1]',
        'top'
    );
}

//使 /author/author_name 重定向到 /user/id
function aya_author_template_redirect()
{
    if (is_author() && get_query_var('author_name')) {

        $user = get_user_by('slug', get_query_var('author_name'));

        if ($user) {
            //重定向
            wp_redirect(home_url('/user/' . $user->ID . '/'), 301);
            exit;
        }
    }
}

//使用自定义的API路径
function aya_rest_api_url_prefix()
{
    return 'api';
}

//使 /wp-json 重定向到 /api
function aya_rest_api_template_redirect()
{
    if (strpos($_SERVER['REQUEST_URI'], 'wp-json') !== false) {
        wp_redirect(site_url(str_replace('wp-json', 'api', $_SERVER['REQUEST_URI'])), 301);
        exit;
    }
}

/*
 * ------------------------------------------------------------------------------
 * 配置一些WordPress过滤器和动作
 * ------------------------------------------------------------------------------
 */

//添加钩子 显示一言
add_action('admin_notices', 'aya_theme_admin_hello_hitokoto');
//添加钩子 移除密码保护和私密文章标题前缀文本
add_filter('protected_title_format', 'aya_theme_remove_protected_title_format');
add_filter('private_title_format', 'aya_theme_remove_protected_title_format');
//添加钩子 修改阅读更多文本
add_filter('excerpt_more', 'aya_theme_excerpt_more_filter');

//一言
function aya_theme_admin_hello_hitokoto()
{
    echo '<p id="hello-hitokoto"><span dir="ltr">' . aya_curl_get_hitokoto() . '</span></p>';
}
//取消前缀文本格式
function aya_theme_remove_protected_title_format($format)
{
    return '';
}
//修改阅读更多文本
function aya_theme_excerpt_more_filter()
{
    return '...';
}

/*
 * ------------------------------------------------------------------------------
 * 文章格式过滤器
 * ------------------------------------------------------------------------------
 */

//添加钩子
add_filter('the_content', 'aya_post_content_filter_format');

//合并方法
function aya_post_content_filter_format($content)
{
    if (!defined('AYA_RELEASE')) {
        $start = microtime(true);
    }

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

    if (!defined('AYA_RELEASE')) {
        $end = microtime(true);

        $content .= '<p>The content filter load time: ' . ($end - $start) . ' seconds.</p>';
    }

    return $content;
}

//DOM版过滤器方法
function aya_content_filter_dom_document($content, $encode_utf8 = false)
{
    if (empty($content))
        return $content;

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
    $matches = preg_match_all('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>/', $content, $urls);
    //如果存在a标签
    if (!empty($matches) && !empty($urls[1])) {
        $redirect_option = aya_opt('site_content_link_jump_page_type', 'postpage');
        foreach ($urls[1] as $url) {
            // 如果是外部链接
            if (cur_is_external_url($url)) {
                // 外链提示页面
                if ($redirect_option == 'link') {
                    $url = add_query_arg('target', urlencode($url), home_url('/link/'));
                    $content = str_replace("href=\"$url\"", "href=\"" . $url . "\" rel=\"nofollow\"", $content);
                }
                // 内跳页面
                else if ($redirect_option == 'go') {
                    $url = add_query_arg('url', base64_encode($url), home_url('/go/'));
                    $content = str_replace("href=\"$url\"", "href=\"" . $url . "\" rel=\"nofollow\" target=\"_blank\"", $content);
                }
                // 只添加 nofollow
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

/*
 * ------------------------------------------------------------------------------
 * 自动拼音化别名
 * ------------------------------------------------------------------------------
 */

add_filter('wp_insert_term_data', 'aya_insert_term_data_slug', 10, 3);
add_filter('wp_update_term_data', 'aya_update_term_data_slug', 10, 4);
add_filter('wp_insert_post_data', 'aya_insert_post_data_slug', 10, 2);

//添加分类时替换分类slug为拼音
function aya_insert_term_data_slug($data, $taxonomy, $term_arr)
{
    if (aya_opt('site_term_auto_pinyin_slug_bool', 'postpage')) {
        //已存在，跳过
        if (!empty($term_arr['slug']))
            return $data;

        $data['slug'] = wp_unique_term_slug(sanitize_title(aya_pinyin_permalink($data['name'], true)), (object) $term_arr);
    }

    return $data;
}

//更新分类时替换分类slug为拼音
function aya_update_term_data_slug($data, $term_id, $taxonomy, $term_arr)
{
    if (aya_opt('site_term_auto_pinyin_slug_bool', 'postpage')) {
        //已存在，跳过
        if (!empty($term_arr['slug']))
            return $data;

        $data['slug'] = wp_unique_term_slug(sanitize_title(aya_pinyin_permalink($data['name'], true)), (object) $term_arr);
    }

    return $data;
}

//保存文章时替换文章slug为拼音
function aya_insert_post_data_slug($data, $post_arr)
{
    //跳过自动草稿
    if ('auto-draft' === $post_arr['post_status'])
        return $data;

    if (aya_opt('site_post_auto_pinyin_slug_bool', 'postpage')) {
        //已存在，跳过
        if (!empty($post_arr['post_name']))
            return $data;
        //检查标题是否为空
        if (empty($post_arr['post_title']))
            return $data;


        $formatted_sulg = sanitize_title(aya_pinyin_permalink($post_arr['post_title'], true));

        $data['post_name'] = wp_unique_term_slug($formatted_sulg, (object) $post_arr);
    }

    return $data;
}
