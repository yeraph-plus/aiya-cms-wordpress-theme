<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 
 * 使用 Vite 的开发服务器配置
 * 
 * Tips: Vite 服务器地址和端口配置，应该和 vite.config.js 中的配置完全一致
 * 
 * 参考来源：
 * https://github.com/wp-bond/bond/blob/master/src/Tooling/Vite.php
 * https://github.com/wp-bond/boilerplate/tree/master/app/themes/boilerplate
 * 
 * ------------------------------------------------------------------------------
 */

define('VITE_HOST', 'http://localhost:5173');
define('VITE_ENTRY_POINT', 'src/main.js');
define('VITE_PUBLIC_PATH', './build');

//检查Vite启动状态
function aya_vite_reference()
{
    //创建curl请求对象
    $handle = curl_init(VITE_HOST . '/' . VITE_ENTRY_POINT);

    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_NOBODY, true);

    curl_exec($handle);

    //检查请求是否成功
    $error = curl_errno($handle);
    //关闭请求
    curl_close($handle);

    //返回
    return ($error === 0) ? true : false;
}

//读取manifest.json
function aya_vite_get_manifest()
{
    //缓存读取结果
    static $exists = null;

    if ($exists !== null) {
        return $exists;
    }

    //定位manifest.json
    $manifest_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/.vite/manifest.json';

    if (file_exists($manifest_path)) {

        $manifest_content = file_get_contents($manifest_path);

        $exists = json_decode($manifest_content, true);

        return $exists;

    }
    //产生报错
    else {
        if (!is_admin()) {
            $error_title = __('AIYA-CMS 启动错误', 'AIYA');
            $error_msg = __('没有找到生产环境文件，请检查 Vite 配置是否正确。', 'AIYA');

            wp_die($error_msg, $error_title, array('response' => 500));

            exit;
        }

        return false;
    }

}

//解析静态资源位置
function aya_vite_main_script_url()
{
    $manifest = aya_vite_get_manifest();

    if (isset($manifest[VITE_ENTRY_POINT])) {
        return AYA_URI . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[VITE_ENTRY_POINT]['file'];
    }
    return null;
}

function aya_vite_main_css_urls()
{
    $urls = [];
    $manifest = aya_vite_get_manifest();

    if (!empty($manifest[VITE_ENTRY_POINT])) {
        foreach ($manifest[VITE_ENTRY_POINT]['css'] as $css_file) {
            $urls[] = AYA_URI . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $css_file;
        }
    }

    return $urls;
}

function aya_vite_imports_script_urls()
{
    $urls = [];
    $manifest = aya_vite_get_manifest();

    if (!empty($manifest[VITE_ENTRY_POINT]['imports'])) {
        foreach ($manifest[VITE_ENTRY_POINT]['imports'] as $import) {
            $urls[] = AYA_URI . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[$import]['file'];
        }
    }

    return $urls;
}

/*
 * ------------------------------------------------------------------------------
 * 载入模板构造文件
 * ------------------------------------------------------------------------------
 */

add_action('wp_head', 'aya_dist_scripts_loader');

//模板静态资源载入函数
function aya_dist_scripts_loader()
{
    //如果是开发模式，载入Vite构造文件
    if (aya_is_dev_mode()) {
        if (aya_vite_reference()) {
            //返回client，用于支持HMR
            echo '<script type="module" src="' . VITE_HOST . '/@vite/client"></script>';
            //返回入口文件
            echo '<script type="module" src="' . VITE_HOST . '/' . ltrim(VITE_ENTRY_POINT, '/') . '"></script>';

            return;
        }
    }

    //从生产模式配置
    $res = '';
    $res .= '<script type="module" src="' . aya_vite_main_script_url() . '"></script>';

    $preload_urls = aya_vite_imports_script_urls();
    $css_urls = aya_vite_main_css_urls();

    foreach ($preload_urls as $url) {
        $res .= '<link rel="modulepreload" href="' . $url . '">';
    }

    foreach ($css_urls as $url) {
        $res .= '<link rel="stylesheet" href="' . $url . '">';
    }

    echo $res;

    return;
}

//严格编码数组到JSON
function aya_vue_json_encode($value)
{
    if (is_array($value)) {
        $value = json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    return htmlspecialchars($value, ENT_QUOTES);
}

//简化 Vue 标签拼接
function aya_vue_load($slug = null, $attrs = [])
{
    //清理组件名
    $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $slug);

    if (empty($slug)) {
        //在PHP注册一个报错
        trigger_error('Vue component slug is empty or invalid.', E_USER_WARNING);

        return '';
    }

    //驼峰转换
    //$slug = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $slug));

    $props = '';

    if (!empty($attrs)) {
        //检查关联数组
        $is_assoc = is_array($attrs) && !array_is_list($attrs);

        //处理参数，作为多个独立props传递
        if ($is_assoc) {
            foreach ($attrs as $key => $value) {
                //清理字符
                $prop_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

                if ($prop_name === '') {
                    continue;
                }

                //限定props支持绑定的数据类型判断布尔、数字、数组、字符串
                if ($value === null) {
                    $props .= ' :' . $prop_name . '="null"';
                } elseif (is_bool($value)) {
                    $props .= ' :' . $prop_name . '=' . ($value ? '"true"' : '"false"');
                } elseif (is_numeric($value)) {
                    $props .= ' :' . $prop_name . '="' . $value . '"';
                } elseif (is_array($value)) {
                    $props .= ' :' . $prop_name . '="' . aya_vue_json_encode($value) . '"';
                } else {
                    //不是以上类型则作为静态绑定
                    $props .= ' ' . $prop_name . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
                }
            }

        }
        //直接编码
        else {
            $props .= ' :data="' . aya_vue_json_encode($attrs) . '"';
        }
    }

    echo sprintf('<%1$s%2$s></%1$s>', $slug, $props);

    return;
}

/*
 * ------------------------------------------------------------------------------
 * 自定义一些模板方法
 * 
 * ------------------------------------------------------------------------------
 */

//页面头部插入点
function aya_home_open()
{
    do_action('aya_home_open');
}

//页面尾部插入点
function aya_home_end()
{
    do_action('aya_home_end');
}

//获取模板路径
function aya_template_path()
{
    return get_template_directory() . '/templates/';
}

//加载组件模板
function aya_template_load($name = null)
{
    $name = (string) $name;

    $templates = array("templates/{$name}.php");

    locate_template($templates, true, false);
}

//加载WP方式的组件模板
function aya_template_part($slug = null, $name = null)
{
    $slug = (string) $slug;
    $name = (string) $name;

    //动作钩子
    do_action('get_template_part', $slug, $name);

    $templates = array();

    if ($name !== '') {
        $templates[] = "templates/{$slug}-{$name}.php";
    }

    $templates[] = "templates/{$slug}.php";

    locate_template($templates, true, false);
}

/*
 * ------------------------------------------------------------------------------
 * 一些合并的WP路由判断方法
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
    } else if (is_singular()) {
        $here_is = 'singular';
        //关联判断
        if (is_single()) {
            $here_is = 'single';
        } else if (is_page()) {
            $here_is = 'page';
        } else if (is_attachment()) {
            $here_is = 'attachment';
        }
    } else if (is_archive()) {
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

//替代文章类型查询
function aya_post_type()
{
    static $type_by_post;

    if (isset($type_by_post)) {
        return $type_by_post;
    }

    $type_by_post = get_post_type();

    //如果是文章类型，则返回文章格式
    if ('post' == $type_by_post) {
        $type_by_post = get_post_format();
    }

    //返回自定义文章的类型
    return $type_by_post;
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

/*
 * ------------------------------------------------------------------------------
 * 文章内容条件循环模板
 * ------------------------------------------------------------------------------
 */

//首页入口模板路由
function aya_core_route_entry()
{
    $route_page = aya_is_where();


    aya_template_load('header');

    //模板路由
    switch ($route_page) {
        case 'home':
            //首页循环
            aya_template_load('home');
            break;
        case 'search':
            //搜索结果
            aya_template_load('search');
            break;
        case 'archive':
        case 'post_type_archive':
        case 'category':
        case 'tag':
        case 'date':
        case 'tax':
            //归档
            aya_template_load('archive');
            break;
        case 'author':
            //用户归档
            aya_template_load('author');
            break;
        case 'single':
        case 'page':
        case 'singular':
        case 'attachment':
            //独立页面
            aya_template_load('single');
            break;
        case '404':
        case 'none':
        default:
            //error
            aya_template_load('404');
            break;
    }

    aya_template_load('footer');
}

//替代LOOP循环
function aya_while_have_post()
{
    //如果有文章
    if (have_posts()) {
        $loop_mode = aya_opt('site_loop_mode_type', 'basic');

        switch ($loop_mode) {
            case 'list':
                $loop_mode = 'loop-list';
                break;
            case 'waterfall':
                $loop_mode = 'loop-waterfall';
                break;
            case 'blog':
                $loop_mode = 'loop-blog';
                break;
            case 'grid':
            default:
                $loop_mode = 'loop-grid';
                break;
        }

        //执行主循环
        while (have_posts()) {
            the_post();
            aya_template_load('cards/' . $loop_mode);
        }
    }
    //如果没有文章
    else {
        aya_template_load('cards/loop-none');
    }
}

//替代正文循环
function aya_while_have_content()
{
    //如果有文章
    if (have_posts()) {
        $post_type = aya_post_type();

        switch ($post_type) {
            case '':
                //返回默认格式
                $content_mode = 'content-default';
                break;
            case 'image':
            case 'video':
            case 'audio':
                //返回媒体格式
                $content_mode = 'content-media';
                break;
            case 'gallery':
                //返回图集格式
                $content_mode = 'content-gallery';
                break;
            case 'page':
                //返回页面格式
                $content_mode = 'content-page';
                break;
            case 'attachment':
                //返回附件格式
                $content_mode = 'content-attachment';
                break;
            default:
                //返回自定义文章的类型
                $content_mode = $post_type;
                break;
        }

        //执行主循环
        while (have_posts()) {
            the_post();
            aya_template_load('contents/' . $content_mode);
        }
    }
    //如果没有文章
    else {
        aya_template_load('contents/content-none');
    }
}

//小工具栏
function aya_widget_bar()
{
    //检查页面类型
    switch (aya_is_where()) {
        case 'home':
        case 'search':
        case 'archive':
        case 'post_type_archive':
        case 'category':
        case 'tag':
        case 'author':
        case 'date':
        case 'tax':
        case '404':
        case 'none':
            $sidebar_type = 'archive-sitebar';
            break;
        case 'single':
        case 'page':
        case 'singular':
        case 'attachment':
            $sidebar_type = 'single-sitebar';
            break;
    }
    //小工具栏位
    dynamic_sidebar($sidebar_type);
}

//获取评论模板
function aya_comments_template()
{
    //检查评论开启
    if (is_attachment() || is_404() || post_password_required())
        return;

    if (aya_opt('site_comment_disable_bool', 'basic', true) === false)
        return;

    if (comments_open() || get_comments_number()) {
        //输出（定义这个位置必须包含"/"）
        comments_template('/templates/comments.php', false);
    }
}