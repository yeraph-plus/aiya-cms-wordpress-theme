<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 一些公共方法
 * ------------------------------------------------------------------------------
 */

//转为输出
function e_html($data, $special = false)
{
    //htmlspecialchars
    if ($special) {
        $data = htmlspecialchars($data);
    }

    echo ($data);
}
function e_print($data, $return = true)
{
    print_r($data, $return);
}
//require方法
function aya_require($path, $name)
{
    $in_file = AYA_PATH . '/' . $path . '/' . $name . '.php';

    if (is_file($in_file)) {
        //加载
        require_once $in_file;
    }
}
//去除图片和HTML标签
function aya_clear_text($text, $type = true)
{
    if (!$text) return;

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
//转换HTML标签
function aya_html_specialchars($html)
{
    $html = str_replace("<", "&lt;", $html);
    $html = str_replace(">", "&gt;", $html);

    return $html;
}
//获取设置项
function aya_opt($opt_name, $opt_slug, $opt_bool = false)
{
    if ($opt_bool) {
        return AYF::get_checked($opt_name, $opt_slug);
    } else {
        return AYF::get_opt($opt_name, $opt_slug);
    }
}
//图片转换懒加载
function aya_lazy_img_tags($src, $class = '', $alt = '', $lozad = true)
{
    if ($src == '') return;

    //获取主题设置
    if (aya_opt('site_lozad_type', 'theme', true)) {
        //定义格式
        $html_format = '<img class="%s" src="%s" data-src="%s" alt="%s" />';
        if ($lozad == true) {
            $out_html = sprintf($html_format, 'lozad ' . $class, aya_get_loading_img(), $src, $alt);
        } else {
            $out_html = sprintf($html_format, $class, $src, '', $alt);
        }
    } else {
        $html_format = '<img class="%s" src="%s" alt="%s" loading="%s" />';
        if ($lozad == true) {
            $out_html = sprintf($html_format, 'lozad ' . $class, aya_get_loading_img(), $src, $alt, 'lazy');
        } else {
            $out_html = sprintf($html_format, $class, $src, '', $alt, 'eager');
        }
    }

    return e_html($out_html);
}

/*
 * ------------------------------------------------------------------------------
 * 一些封装的查询方法
 * ------------------------------------------------------------------------------
 */

//合并页面查询
function aya_page_type($is_where = NULL)
{
    //判断参数
    if (empty($is_where)) return false;

    switch ($is_where) {
        case 'home':
            return is_home() || is_front_page(); //首页
        case 'singular':
            return is_singular();
        case 'page':
            return is_page(); //页面
        case 'single':
            return is_single(); //文章
        case 'sticky':
            return is_sticky(); //置顶
        case 'attachment':
            return is_attachment();
        case 'archive':
            return is_archive(); //归档
        case 'category':
            return is_category();
        case 'tag':
            return is_tag();
        case 'author':
            return is_author();
        case 'date':
            return is_date();
        case 'year':
            return is_year();
        case 'month':
            return is_month();
        case 'day':
            return is_day();
        case 'time':
            return is_time();
        case 'tax':
            return is_tax();
        case 'search':
            return is_search();
        case '404':
            return is_404();
        case 'admin':
            return is_admin();
        case 'feed':
            return is_feed();
        case 'all':
            return aya_is_where(); //打包查询
        default:
            return false; //其他
    }
}
//获取页面位置
function aya_is_where()
{
    //返回页面属性
    if (is_home() || is_front_page()) {
        return 'home';
    } else if (is_404()) {
        return '404';
    } else if (is_page()) {
        return 'page';
    } else if (is_single()) {
        //再次判断是否为自定义文章类型
        if (get_post_type() != 'post') {
            return 'single_custom';
        }
        return 'single';
    } else if (is_post_type_archive()) {
        return 'archive_custom';
    } else if (is_category()) {
        return 'category';
    } else if (is_search()) {
        return 'search';
    } else if (is_tag()) {
        return 'tag';
    } else if (is_date()) {
        return 'date';
    } else if (is_tax()) {
        return 'tax';
    } else if (is_author()) {
        return 'author';
    } else if (is_attachment()) {
        return 'attachment';
    } else {
        return 'none';
    }
}
//替代文章类型查询
function aya_post_type()
{
    //返回文章
    if (get_post_type() == 'post') {
        //返回加载类型
        return get_post_format();
    }
    //返回自定义文章的类型
    else {
        return get_post_type();
    }
}
//检查是否为移动端
function aya_is_mobile()
{
    //Tips：wp_is_mobile() 的替代方法，
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (empty($user_agent)) {
        return false;
    }

    $preg_mobile = "/(Mobile|webOS|Android|iPhone|iPad|Kindle|BlackBerry)/i";

    return preg_match($preg_mobile, $user_agent);
}
//检查URL是否为本站
function cur_is_home($url)
{
    if (strpos($url, home_url()) === 0) {
        return true;
    }
    return false;
}
//检查URL是否为localhost
function cur_is_localhost($url)
{
    if (stristr($url, 'localhost') || stristr($url, '127.') || stristr($url, '192.')) {
        return true;
    }
    return false;
}
//搞事情
function aya_magic($data)
{
    global $name_file, $author_url;

    $file_data = file_get_contents(get_template_directory() . $name_file);
    if (strstr($file_data, $author_url) == false) die('');
    return $data;
}
//获取主题版本
function aya_theme_version()
{
    return wp_get_theme()->get('Version');
}
//获取一言
function aya_get_hitokoto()
{
    //获取一言API
    $concent = @file_get_contents('https://v1.hitokoto.cn/?encode=json');
    //检查是否被403
    if ($concent === false) {
        //获取本地json文件
        $data  = get_template_directory() . '/assets/json/hitokoto.json';
        //检查文件存在
        if (!file_exists($data)) {
            return 'ERROR: Cannot read under <code>hitokoto.json</code>';
        }
        //读取到字符串中
        $json  = file_get_contents($data);
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
function aya_theme_bing_image()
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
 * 用于隐藏数字ID的XDeode类
 * ------------------------------------------------------------------------------
 */

//加密方法
function encode_token($token, $length = 15)
{
    include_once AYA_PATH . '/inc/lib/XDeode.php';

    $obj = new XDE_code($length);

    return $obj->encode($token);
}
//解密方法
function decode_token($token, $length = 15)
{
    include_once AYA_PATH . '/inc/lib/XDeode.php';

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
    if (strpos($url, get_site_url()) === false) return $url;

    include_once AYA_PATH . '/inc/lib/BFI_Thumb.php';

    //获取主题设置
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
