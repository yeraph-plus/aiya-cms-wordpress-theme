<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 自定义一些模板方法
 * ------------------------------------------------------------------------------
 */

// 页面头部插入点
function aya_home_open()
{
    do_action('aya_home_open');
}

// 页面尾部插入点
function aya_home_end()
{
    do_action('aya_home_end');
}

// 获取模板路径
function aya_template_path()
{
    return get_template_directory() . '/templates/';
}

// 加载组件模板
function aya_template_load($name = null)
{
    $name = (string) $name;

    $templates = array("templates/{$name}.php");

    locate_template($templates, true, false);
}

// 加载WP方式的组件模板
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

// 使用片段模式加载模板
function aya_template_part_path($name = null)
{
    $name = (string) $name;

    if ($name === '') {
        return false;
    }

    return aya_template_path() . 'fragments/' . $name . '.php';
}

// 使用 include 加载并传入数据
function aya_template_part_load($name = null, $data = [])
{
    $template_path = aya_template_part_path($name);

    if ($template_path === false || !is_file($template_path)) {
        return;
    }

    extract($data);

    ob_start();

    include $template_path;

    echo trim((string) ob_get_clean());

    return;
}

/*
 * ------------------------------------------------------------------------------
 * 文章内容条件循环模板
 * ------------------------------------------------------------------------------
 */

//首页入口模板路由
function aya_route_core()
{
    $page_type = aya_is_land_page();

    //自定义页面
    if ($page_type !== false) {
        aya_landpage_sub_route($page_type);

        return;
    }
    //页头
    aya_template_load('header');

    //默认模板路由
    $route_page = aya_is_where();

    //模板路由
    switch ($route_page) {
        //首页循环
        case 'home':
        case 'home_pre':
            aya_template_load('index');
            break;
        //搜索结果
        case 'search':
            aya_template_load('search');
            break;
        //归档
        case 'archive':
        case 'category':
        case 'tag':
        case 'date':
        case 'tax':
            aya_template_load('archive');
            break;
        case 'custom_archive':
            // 获取当前自定义文章类型名称
            aya_template_part('archive', get_post_type());
            break;
        //用户归档
        case 'author':
            aya_template_load('author');
            break;
        //独立页面
        case 'single':
            aya_template_part('single', get_post_type());
            break;
        case 'page':
        case 'singular':
        case 'attachment':
            aya_template_load('single');
            break;
        //处理路由错误
        case '404':
        case 'none':
        default:
            aya_template_load('404');
            break;
    }

    //页脚
    aya_template_load('footer');
}

//独立页面入口模板路由
function aya_landpage_sub_route($page_type)
{
    //如果没有找到对应的页面配置，跳入404
    if ($page_type === false) {
        aya_template_load('header');
        aya_template_load('404');
        aya_template_load('footer');

        return;
    }

    global $aya_land_page;

    //是否是原始模板
    $page_orginal = isset($aya_land_page[$page_type]['original']) ? $aya_land_page[$page_type]['original'] : (isset($aya_land_page[$page_type]['orginal']) ? $aya_land_page[$page_type]['orginal'] : false);

    //执行回调函数
    if (isset($aya_land_page[$page_type]['callback']) && is_callable($aya_land_page[$page_type]['callback'])) {
        call_user_func($aya_land_page[$page_type]['callback']);
    }

    if ($page_orginal) {
        aya_template_load('header');
    }

    //获取页面配置
    $page_template = $aya_land_page[$page_type]['template'];

    //已经定义了的页面
    if (isset($page_template)) {
        aya_template_load('pages/' . $page_template);
    } else {
        aya_template_load('404');
    }

    if ($page_orginal) {
        aya_template_load('footer');
    }

    return;
}

//处理重定向到404页面
function aya_template_none()
{
    //重定向到404
    wp_redirect(home_url('/404/'));

    exit;
}

//小工具栏
function aya_widget_sidebar()
{
    //检查页面类型
    switch (aya_is_where()) {
        case 'home':
        case '404':
            $sidebar_type = 'index-widget';
            break;
        case 'search':
        case 'archive':
        case 'custom_archive':
        case 'category':
        case 'tag':
        case 'date':
        case 'tax':
            $sidebar_type = 'archive-widget';
            break;
        case 'single':
        case 'page':
        case 'singular':
        case 'attachment':
            $sidebar_type = 'single-widget';
            break;
        case 'author':
            $sidebar_type = 'author-widget';
            break;
        case 'none':
        default:
            $sidebar_type = 'index-widget';
            break;
    }
    //小工具栏位
    dynamic_sidebar($sidebar_type);
}

//获取评论模板
function aya_comments_template()
{
    //输出（定义这个位置必须包含"/"）
    comments_template('/templates/comments.php', false);
}

//获取WP评论设置
function aya_get_comments_settings()
{
    return [
        //提交前检查
        'requireAuthorNameEmail' => get_option('require_name_email', true),
        'commentRegistration' => get_option('comment_registration', false),
        'commentMaxLinks' => get_option('comment_max_links', 2),
        'commentModeration' => get_option('comment_moderation', true),
        'commentPreviouslyApproved' => get_option('comment_previously_approved', true),
        //评论嵌套设置
        'threadComments' => get_option('thread_comments', true),
        'threadCommentsDepth' => get_option('thread_comments_depth', 5),
        //评论分页设置
        'pageComments' => get_option('page_comments', false),
        'commentsPerPage' => get_option('comments_per_page', 20),
        'defaultCommentsPage' => get_option('default_comments_page', 'newest'),
        'commentOrder' => get_option('comment_order', 'asc'),
    ];
}

/*
 * ------------------------------------------------------------------------------
 * 模板组件
 * ------------------------------------------------------------------------------
 */

//用于隐藏数字ID的加密方法
function aya_token_encode($token, $length = 15)
{
    $obj = new XDE_code($length);

    return $obj->encode($token);
}

//用于隐藏数字ID的解密方法
function aya_token_decode($token, $length = 15)
{
    $obj = new XDE_code($length);

    return $obj->decode($token);
}
