<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 一些公共方法
 * ------------------------------------------------------------------------------
 */

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
function aya_magic($data = true)
{
    if (false === strstr(file_get_contents(get_template_directory() . $GLOBALS['magic_file']), $GLOBALS['author_url'])) die;
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

//SQL计数器
function aya_sql_counter()
{
    return sprintf('( Run Time %.3f seconds / %d SQL querys / Memory Usage %.2fMB )', timer_stop(0, 3), get_num_queries(), memory_get_peak_usage() / 1024 / 1024);
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
 * 一些自定义方法
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

/*
 * ------------------------------------------------------------------------------
 * 用于隐藏数字ID的XDeode类
 * ------------------------------------------------------------------------------
 */

//加密方法
function encode_token($token, $length = 15)
{
    include_once get_template_directory() . '/inc/lib/XDeode.php';

    $obj = new XDE_code($length);

    return $obj->encode($token);
}

//解密方法
function decode_token($token, $length = 15)
{
    include_once get_template_directory() . '/inc/lib/XDeode.php';

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

    include_once get_template_directory() . '/inc/lib/BFI_Thumb.php';

    //图片质量设置
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
        $data  = get_template_directory() . '/static/json/hitokoto.json';
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
