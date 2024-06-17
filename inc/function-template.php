<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 自定义一些模板方法
 * ------------------------------------------------------------------------------
 */

//加载header模板
function aya_header($name = null)
{
    $name = (string) $name;

    //动作钩子
    do_action('get_header', $name);
    //模板钩子
    do_action('aya_header');

    //加载body
    include_once AYA_PATH . '/inc/basic-html/body-head.php';

    $templates = array();

    if ($name !== '') {
        $templates[] = 'template-parts/header-' . ucfirst($name) . '.php';
    }

    $templates[] = 'template-parts/header.php';
    $templates[] = 'header.php';

    locate_template($templates, true);

    return;
}
//加载footer模板
function aya_footer($name = null)
{
    $name = (string) $name;

    //动作钩子
    do_action('get_footer', $name);
    //模板钩子
    do_action('aya_footer');

    $templates = array();

    if ($name !== '') {
        $templates[] = 'template-parts/footer-' . ucfirst($name) . '.php';
    }

    $templates[] = 'template-parts/footer.php';
    $templates[] = 'footer.php';

    locate_template($templates, true);

    //加载body
    include_once AYA_PATH . '/inc/basic-html/body-end.php';

    return;
}
//加载Sidebar模板
function aya_sidebar($name = null)
{
    $name = (string) $name;

    //动作钩子
    do_action('get_sidebar', $name);
    //模板钩子
    do_action('aya_sidebar');

    $templates = array();

    if ($name !== '') {
        $templates[] = 'template-parts/sidebar-' . ucfirst($name) . '.php';
    }

    $templates[] = 'template-parts/sidebar.php';

    locate_template($templates, true);

    return;
}
//首页模板
function aya_home_open($name = null)
{
    $name = (string) $name;

    //动作钩子
    do_action('get_home_open', $name);
    //模板钩子
    do_action('aya_home_open');

    return;
}
//加载Part模板
function aya_template_part($slug = null, $name = null)
{
    $slug = (string) $slug;
    $name = (string) $name;

    //动作钩子
    do_action('get_template_part', $slug, $name);

    $templates = array();

    if ($name !== '') {
        $templates[] = "template-parts/{$slug}-{$name}.php";
    }

    $templates[] = "template-parts/{$slug}.php";

    locate_template($templates, true, false);

    return;
}
//获取评论模板
function aya_comments_template()
{
    //检查评论开启
    if (is_attachment() || is_404() || post_password_required()) return;

    if (comments_open() || get_comments_number()) {
        //输出（定义这个位置必须包含"/"）
        comments_template('/template-parts/comments.php', false);
    }

    return;
}
//搜索框设置
function aya_search_form()
{
    aya_template_part('search-from');

    return;
}

/*
 * ------------------------------------------------------------------------------
 * 自定义一些模板索引和基础DIV结构
 * ------------------------------------------------------------------------------
 */

//替代LOOP循环
function aya_while_loop_template()
{
    //如果有文章
    if (have_posts()) {
        //获取主题设置
        $loop_mode = aya_opt('site_loop_mode', 'layout');

        switch ($loop_mode) {
            case 'blog':
                $loop_mode = 'blog-mode';
                break;
            case 'list':
                $loop_mode = 'list-mode';
                break;
            case 'grid':
                $loop_mode = 'grid-mode';
                break;
            case 'fall':
                $loop_mode = 'waterfall';
                break;
            default:
                echo 'ERROR: Loop Grid $args[loop_mode] is not defined.';
                return;
        }
        //执行主循环
        while (have_posts()) {
            the_post();
            aya_template_part('loop/card', $loop_mode);
        }
    }
    //如果没有文章
    else {
        aya_template_part('loop/card', 'none');
    }
    return;
}
//替代正文循环
function aya_while_content_template()
{
    //如果有文章
    if (have_posts()) {
        //获取文章类型
        $post_type = get_post_type();

        //返回加载类型
        switch ($post_type) {
            case 'post':
                //再次查询文章形式
                $post_format = get_post_format();

                switch ($post_format) {
                        /*
                case 'image' || 'audio' || 'video':
                    $template_type = 'media';
                    break;
                case 'gallery':
                    $template_type = 'gallery';
                    break;
                */
                    default:
                        $template_type = 'default';
                        break;
                }

                break;
            case 'page':
                //返回页面格式
                $template_type = 'page';
                break;
            case 'attachment':
                //返回附件格式
                $template_type = 'attachment';
                break;
            case false:
                //如果不是POST
                $template_type = 'none';
                break;
            default:
                //返回自定义文章的类型
                $template_type = $post_type;
                break;
        }
        //执行主循环
        while (have_posts()) {
            the_post();
            aya_template_part('article/content', $template_type);
        }
    }
    //如果没有文章
    else {
        aya_template_part('article/content', 'none');
    }
    return;
}
//LOOP行块/瀑布布局选择器
function aya_grid_waterfall_class()
{
    //获取主题设置
    $loop_mode = aya_opt('site_loop_mode', 'layout');
    $loop_span = aya_opt('site_loop_span', 'layout');

    //兼容瀑布流样式
    if ($loop_mode == 'fall') {
        //输出块内布局
        switch ($loop_span) {
            case '2':
                return 'col-sm-6 col-md-6 col-lg-6';
            case '3':
                return 'col-sm-6 col-md-4 col-lg-4';
            case '4':
                return 'col-sm-6 col-md-4 col-lg-3';
        }
    } else {
        //块外布局
        switch ($loop_span) {
            case '2':
                return 'row-cols-sm-2 row-cols-md-2 row-cols-lg-2';
            case '3':
                return 'row-cols-sm-2 row-cols-md-2 row-cols-lg-3';
            case '4':
                return 'row-cols-sm-2 row-cols-md-3 row-cols-lg-4';
        }
    }
}
//列表LOOP控件
function aya_loop_row_class()
{
    //获取主题设置
    $loop_mode = aya_opt('site_loop_mode', 'layout');

    //class样式
    switch ($loop_mode) {
        case 'blog':
            $row_class = 'g-4 row-cols-1 loop-blog';
            break;
        case 'list':
            $row_class = 'g-4 row-cols-1 loop-list-mode';
            break;
        case 'grid':
            $row_class = 'g-4 ' . aya_grid_waterfall_class() . ' loop-grid-mode';
            break;
        case 'fall':
            $row_class = 'loop-waterfall-mode" data-masonry="{&quot;percentPosition&quot;: true }';
            break;
    }
    return $row_class;
}
//分页组件
function aya_loop_pagination()
{
    //获取主题设置
    $loop_pagination = aya_opt('site_loop_page', 'layout');

    switch ($loop_pagination) {
        case 'page':
            aya_get_page_nav(3);
            return;
        case 'full':
            aya_get_page_nav_item(7);
            return;
        case 'roll':
            aya_get_page_load_more(true);
            return;
        case 'more':
            aya_get_page_load_more(false);
            return;
        default:
            echo 'ERROR: Loop Pagination $args[loop_pagination] is not defined.';
            return;
    }
}
