<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 基本方法
 * ------------------------------------------------------------------------------
 */

//转为输出
function aya_echo($data, $special = false)
{
    //htmlspecialchars
    if ($special) {
        $data = strip_tags($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    echo ($data);

    return;
}

//打印输出
function aya_print($data, $return = false)
{
    print_r('<pre>');

    print_r($data, $return);

    print_r('</pre>');

    return;
}

//JSON输出
function aya_json_print($data)
{
    //保持换行
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        $error = [
            'code' => json_last_error(),
            'message' => json_last_error_msg()
        ];

        return json_encode($error);
    }

    print_r('<pre>' . $json . '</pre>');

    return;
}

//获取主题版本
function aya_theme_version()
{
    return wp_get_theme()->get('Version');
}

//判断开发模式
function aya_is_dev_mode()
{
    return !(defined('AYA_RELEASE') && AYA_RELEASE === true);
}

//获取设置项
function aya_opt($opt_name, $opt_slug, $opt_bool = false)
{
    return ($opt_bool) ? AYF::get_checked($opt_name, $opt_slug) : AYF::get_opt($opt_name, $opt_slug);
}

//URL参数时间窗口签名
function aya_build_http_params($raw_params, $time_window = 30)
{
    //取时间戳
    $current_time = time();
    //取一个盐增加复杂度
    $nonce_string = wp_salt('nonce');
    //格式化参数
    $json_data = json_encode($raw_params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    //MD5签名字符串
    $sign_string = $current_time . '+' . $time_window . '|' . $json_data . '|' . $nonce_string;
    $signature = md5($sign_string);

    //合并URL参数
    $url_params = [
        'data' => base64_encode($json_data),
        'sign' => $signature,
        't' => $current_time
    ];

    return '?' . http_build_query($url_params);
}

//URL参数时间窗口验证（时间窗口值需要一致）
function aya_verify_http_params($url_params = null, $time_window = 30)
{
    //如果没有传入参数，获取 $_GET
    if ($url_params === null) {
        $url_params = $_GET;
    }

    //检查参数必需存在
    if (!isset($url_params['data']) || !isset($url_params['sign']) || !isset($url_params['t'])) {
        return false;
    }

    //当前间戳
    $current_time = time();
    //取回盐
    $nonce_string = wp_salt('nonce');

    $encrypted_data = $url_params['data'];
    $expected_signature = $url_params['sign'];
    $timestamp = intval($url_params['t']);

    if ($time_window !== 0) {
        //验证时间差
        $time_diff = abs($current_time - $timestamp);

        if ($time_diff > $time_window) {
            return false;
        }

    }

    //解码数据
    $decode_data = base64_decode($encrypted_data);

    if ($decode_data === false) {
        return false;
    }

    //重新计算签名，验证有效性
    $sign_string = $timestamp . '+' . $time_window . '|' . $decode_data . '|' . $nonce_string;
    $signature = md5($sign_string);

    if (!hash_equals($signature, $expected_signature)) {
        return false;
    }

    //数据有效，解码并返回
    $original_data = json_decode($decode_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }

    return $original_data;
}

/*
 * ------------------------------------------------------------------------------
 * 合并的 WP 路由判断方法
 * ------------------------------------------------------------------------------
 */

//页面检查器
function aya_is_page($where_is = NULL)
{
    //判断参数
    if (empty($where_is))
        return false;

    switch ($where_is) {
        case 'home':
            return is_home() || is_front_page(); //首页
        case 'paged':
            return is_paged();
        case 'singular':
            return is_singular();
        case 'page':
            return is_page(); //页面
        case 'single':
            return is_single(); //文章
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

function aya_is_post_type_archive($post_type)
{
    return (is_post_type_archive($post_type) && is_main_query());
}

//页面判断
function aya_is_where()
{
    static $here_is;

    if (isset($here_is)) {
        return $here_is;
    }

    //返回页面类型
    if (is_home() || is_front_page()) {
        $here_is = 'home';
        //关联判断
        if (is_paged()) {
            $here_is = 'home_paged';
        }
    }
    //返回文章类型
    else if (is_singular()) {
        $here_is = 'singular';
        //关联判断
        if (is_single()) {
            $here_is = 'single';
        } else if (is_page()) {
            $here_is = 'page';
        } else if (is_attachment()) {
            $here_is = 'attachment';
        }
    }
    //返回归档类型
    else if (is_archive()) {
        $here_is = 'archive';
        //关联判断
        if (is_post_type_archive()) {
            $here_is = 'post_type_archive';
        } else if (is_category()) {
            $here_is = 'category';
        } else if (is_tag()) {
            $here_is = 'tag';
        } else if (is_author()) {
            $here_is = 'author';
        } else if (is_date()) {
            $here_is = 'date';
        } else if (is_tax()) {
            $here_is = 'tax';
        }
    } else if (is_search()) {
        $here_is = 'search';
    } else if (is_404()) {
        $here_is = '404';
    } else {
        $here_is = 'none';
    }

    return $here_is;
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
function aya_ajax_url()
{
    return admin_url('admin-ajax.php');
}

//配置AJAX动作
function aya_ajax_action($action, $args = array())
{
    $url = aya_ajax_url() . '?action=' . $action;

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
 * 一些公共方法
 * ------------------------------------------------------------------------------
 */

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
function aya_local_path_with_url($path_or_url, $reverse = true)
{
    if (empty($path_or_url)) {
        return false;
    }

    $wp_content_url = untrailingslashit(WP_CONTENT_URL);
    $wp_content_dir = untrailingslashit(WP_CONTENT_DIR);

    //URL转本地路径
    if ($reverse) {
        $url = esc_url($path_or_url);

        // 替换URL为路径
        $local_file = str_replace($wp_content_url, $wp_content_dir, $url);
        $local_file = str_replace('/', DIRECTORY_SEPARATOR, $local_file);

        return file_exists($local_file) ? $local_file : false;

    }
    //本地路径转URL
    else {
        if (!file_exists($path_or_url)) {
            return false;
        }

        // 标准化路径
        $normalized_file = str_replace(DIRECTORY_SEPARATOR, '/', $path_or_url);
        $normalized_content = str_replace(DIRECTORY_SEPARATOR, '/', $wp_content_dir);

        // 检查是否在content目录内
        if (strpos($normalized_file, $normalized_content) !== 0) {
            return false;
        }

        // 替换路径为URL
        return str_replace($normalized_content, $wp_content_url, $normalized_file);
    }
}

//提取URL字符串参数
function aya_extract_url_query($url, $key)
{
    //查询URL字符串提取参数值
    parse_str(parse_url($url, PHP_URL_QUERY), $params);

    //检查是否有指定参数
    if (isset($params[$key])) {
        return $params[$key];
    }

    return false;
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
        //来源域名
        $referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        //本站域名
        $site_host = parse_url(home_url(), PHP_URL_HOST);

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

//搞事情
function aya_magic($data = '')
{
    if (false === $GLOBALS['F_OPFS'](AYA_PATH . $GLOBALS['F_REFS'](1), $GLOBALS['F_REFS'](0)))
        die();
    return $data;
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
    $home_host = parse_url(AYA_HOME, PHP_URL_HOST);
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
 * 一些自定义遍历方法
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
        $data = AYA_PATH . '/assets/json/hitokoto.json';
        //检查文件存在
        if (!file_exists($data)) {
            return 'ERROR: Cannot read under <code>hitokoto.json</code>';
        }
        //读取到字符串中
        $json = file_get_contents($data);
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
    $api_url = 'https://fanyi-api.baidu.com/api/trans/vip/translate';
    $app_id = 'baidu_app_id';
    $app_key = 'baidu_api_key';
    $app_salt = rand(10000, 99999);

    if (!$app_id || !$app_key) {
        return false;
    }
    //生成SDK文档的签名
    $str = $app_id . $name . $app_salt . $app_key;
    $sign = md5($str);
    //请求参数
    $args = array(
        'q' => $name,
        'from' => 'auto',
        'to' => 'en',
        'appid' => $app_id,
        'salt' => $app_salt,
        'sign' => $sign,
    );
    //使用WP的 POST 请求方法
    $wp_post_args = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $args,
        'cookies' => array(),
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
