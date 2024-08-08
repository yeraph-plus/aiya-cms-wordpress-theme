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
function e_print($data, $return = false)
{
    echo '<pre>';

    print_r($data, $return);

    echo '</pre>';
}
//require方法
function aya_require($name, $path = '')
{
    if ($path === '') {
        $path = 'inc';
    } else {
        $path = 'inc/' . $path;
    }

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
function aya_html_clean($html)
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
//图片转换懒加载
function aya_lazy_img_tags($src, $class = '', $alt = '', $width = 'auto', $height = 'auto', $lazy_load = true)
{
    $src = esc_url($src);

    if ($src == '') return;

    //使用 lozad.js 的格式
    $html_format = '<img class="lozad %s" src="%s" data-src="%s" alt="%s" width="%s" height="%s" />';
    //判断懒加载
    if ($lazy_load) {
        $out_html = sprintf($html_format, $class, aya_opt('site_image_load_ani', 'theme'), $src, $alt, $width, $height);
    } else {
        $out_html = sprintf($html_format, $class, $src, '', $alt, $width, $height);
    }
    return e_html($out_html);
    //使用Chrome支持格式
    /*
    $html_format = '<img class="%s" src="%s" alt="%s" width="%s" height="%s" loading="%s" />';

    if ($lazy_load) {
        $out_html = sprintf($html_format, $class, $src, $alt, $width, $height, 'lazy');
    } else {
        $out_html = sprintf($html_format, $class, $src, $alt, $width, $height, 'eager');
    }

    return e_html($out_html);
    */
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
        if (!cur_is_home($url) && !cur_is_localhost($url)) return false;

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
//搞事情
function aya_magic($data = true)
{
    if (false === strstr(file_get_contents(get_template_directory() . $GLOBALS['magic_file']), $GLOBALS['author_url'])) die;
    return $data;
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
//获取主题版本
function aya_theme_version()
{
    return wp_get_theme()->get('Version');
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
    $api_url  = 'https://fanyi-api.baidu.com/api/trans/vip/translate';
    $app_id   = 'baidu_app_id';
    $app_key  = 'baidu_api_key';
    $app_salt = rand(10000, 99999);

    if (!$app_id || !$app_key) {
        return false;
    }
    //生成SDK文档的签名
    $str  = $app_id . $name . $app_salt . $app_key;
    $sign = md5($str);
    //请求参数
    $args = array(
        'q'     => $name,
        'from'  => 'auto',
        'to'    => 'en',
        'appid' => $app_id,
        'salt'  => $app_salt,
        'sign'  => $sign,
    );
    //使用WP的 POST 请求方法
    $wp_post_args = array(
        'method'      => 'POST',
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(),
        'body'        => $args,
        'cookies'     => array(),
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
