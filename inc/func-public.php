<?php

if (!defined('ABSPATH'))
    exit;

/*
 * ------------------------------------------------------------------------------
 * 一些公共方法
 * ------------------------------------------------------------------------------
 */

//去除图片和HTML标签
function aya_clear_text($text, $type = true)
{
    if (!$text)
        return;

    //清理预定义字符
    $text = trim($text);
    //清理图片
    $text = preg_replace('/<a(.*?)href=("|\')([^>]*).(bmp|gif|jpeg|jpg|png|swf|webp)("|\')(.*?)>(.*?)<\/a>/i', '', $text);
    $text = preg_replace('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i', '', $text);
    //清理html
    if ($type == true) {
        $text = strip_tags($text);
        $text = preg_replace('/<[^<]+>/', '', $text);
    }
    //返回
    return trim($text);
}

//根据特定字符截取词长度的方法
function aya_trim_slug($input, $length, $join = '-', $strip_html = true)
{
    //剥去字符串HTML
    if ($strip_html) {
        $input = strip_tags($input);
    }

    //计算输入长度
    if (!$length || $length === '' || strlen($input) <= $length) {
        return $input;
    }

    $trimmed_text = substr($input, 0, $length);

    //查找最后截取字符串的最后一个分隔符位置
    if ($join !== '') {
        $last_space = strrpos(substr($input, 0, $length), $join);

        if ($last_space) {
            $trimmed_text = substr($input, 0, $last_space);
        }
    }

    return $trimmed_text;
}

//搞事情
function aya_magic($data)
{
    if (false === strstr(file_get_contents(get_template_directory() . $GLOBALS['magic_file']), $GLOBALS['author_url']))
        die();
    return $data;
}

//创建本地文件夹
function aya_local_mkdir($dirname)
{
    //在 wp-content 下创建
    $local_dir = trailingslashit(WP_CONTENT_DIR) . $dirname;
    //判断文件夹是否存在
    if (!is_dir($local_dir)) {
        //创建文件夹
        wp_mkdir_p($local_dir);
    }
    //返回拼接的路径
    return $local_dir;
}

//转换URL为本地路径的方法
function aya_local_path_with_url($path, $reverse = true)
{
    //获取WP上传目录
    $wp_content_url = set_url_scheme(WP_CONTENT_URL);
    $wp_content_dir = WP_CONTENT_DIR; //trailingslashit()
    //转换URL为本地路径
    if ($reverse) {
        $url = esc_url($path);
        //验证是否为本地URL
        if (!cur_is_localhost($url) && cur_is_external_url($url))
            return false;

        //截取URL
        $url_file = str_replace($wp_content_url, '', $url);
        //拼接为本地路径
        $local_file = $wp_content_dir . $url_file;
        //验证文件
        if (file_exists($local_file)) {
            return $local_file;
        }

        return false;
    }
    //转换本地路径为URL
    else {
        //截取本地路径
        $path_file = str_replace($wp_content_dir, '', $path);
        //拼接为URL
        $url_file = $wp_content_url . $path_file;

        return $url_file;
    }
}

//SQL计数器
function aya_sql_counter()
{
    return sprintf('( Run Time %.3f seconds / %d SQL querys / Memory Usage %.2fMB )', timer_stop(0, 3), get_num_queries(), memory_get_peak_usage() / 1024 / 1024);
}

//访问来源检查方法
function aya_home_url_referer_check()
{
    //判断请求头
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST); //来源域名
        $site_host = parse_url(home_url(), PHP_URL_HOST); //本站域名

        if ($referer_host !== $site_host) {
            //是外部来源
            return false;
        }
    } else {
        //是直接访问
        return false;
    }

    return true;
}

//检查字符串是否是链接
function cur_is_url($url)
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;
    }
    return false;
}

//判断是否是外部链接
function cur_is_external_url($url)
{
    $home_host = parse_url(home_url(), PHP_URL_HOST);
    $link_host = parse_url($url, PHP_URL_HOST);

    return $link_host && $link_host !== $home_host;
}

//检查URL是否为localhost
function cur_is_localhost($url)
{
    if (stristr($url, 'localhost') || stristr($url, '127.') || stristr($url, '192.')) {
        return true;
    }
    return false;
}

//检查路径是否为正常路径
function cur_is_path($path)
{
    if (strpos($path, '/') === 0 || strpos($path, './') === 0 || strpos($path, '../') === 0) {
        return true;
    }
    return false;
}

/*
 * ------------------------------------------------------------------------------
 * COOKIE 操作
 * ------------------------------------------------------------------------------
 */

//添加 COOKIE
function set_cookie($key, $token, $time = 86400)
{
    //默认保存24小时
    setcookie($key, $token, time() + $time, COOKIEPATH, COOKIE_DOMAIN);
}

//获取 COOKIE
function get_cookie($key, $len = 0)
{
    //检查Cookie是否存在
    if (!isset($_COOKIE[$key])) {
        return false;
    }
    //检查Cookie是否正确
    if ($len != 0 && $len != strlen($_COOKIE[$key])) {
        return false;
    }
    return $_COOKIE[$key];
}

//销毁 COOKIE
function delete_cookie($key, $time = 999999999)
{
    setcookie($key, '', time() - $time, COOKIEPATH, COOKIE_DOMAIN);
}

//新建会话
function open_session()
{
    session_start();
}

//关闭会话
function close_session()
{
    session_write_close();
}

//请求会话
function call_session($ation)
{
    open_session();

    try {
        $ation();
    } finally {
        close_session();
    }
}

/*
 * ------------------------------------------------------------------------------
 * AJAX操作
 * ------------------------------------------------------------------------------
 */

//注册异步方法
function aya_ajax_register($name, $callback, $public = false)
{
    add_action('wp_ajax_' . $name, $callback);

    if ($public) {
        add_action('wp_ajax_nopriv_' . $name, $callback);
    }
}

//获取AJAX位置
function aya_ajax_url($action, $args = array())
{
    $url = admin_url('admin-ajax.php?action=') . $action;

    if (!empty($args)) {
        $url .= '&' . http_build_query($args);
    }

    return $url;
}

//访问原始 POST 数据
function aya_get_req_body()
{
    //使用PHP伪协议方法
    $body = @file_get_contents('php://input');

    return json_decode($body, true);
}

/*
 * ------------------------------------------------------------------------------
 * 一些自定义遍历方法
 * ------------------------------------------------------------------------------
 */

//遍历法提取内容中第 1 个链接
function aya_match_post_first_url($the_content, $callback = false)
{
    //遍历内容提取链接
    $match_all = preg_match_all('/<a.*?href=[\'"](.*?)[\'"].*?>/i', $the_content, $matches);

    //返回
    if (isset($matches[1][0])) {
        //返回全部
        if ($callback == true) {
            //返回全部数组，交给其他函数处理
            return $matches[1];
        }
        return $matches[1][0];
    }

    return false;
}

//遍历法提取内容中第 1 张图片
function aya_match_post_first_image($the_content, $callback = false)
{
    //遍历内容提取图片
    $match_all = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $the_content, $matches);

    //返回
    if (isset($matches[1][0])) {
        //返回全部
        if ($callback == true) {
            //返回全部数组，交给其他函数处理
            return $matches[1];
        }
        //返回第一张
        return $matches[1][0];
    }

    return false;
}

//截取标题关键词
function aya_match_post_first_words($the_title, $callback = false)
{
    //遍历标题匹配括号[]、<>、()、{}、【】、（）、《》
    $pattern = '/\[([^\[\]]+)\]|<([^>]+)>|\(([^)]+)\)|\{([^}]+)\}|【([^\【\】]+)】|（([^）]+)）|《([^》]+)》/';
    $match_all = preg_match_all($pattern, $the_title, $matches);
    //var_dump($matches);

    //返回取到的第一个
    if (isset($matches[0][0])) {
        $string = $matches[0][0];
        mb_internal_encoding('UTF-8');
        //去除第一个字符
        return mb_substr($string, 1, mb_strlen($string) - 2);
    }

    return false;
}
