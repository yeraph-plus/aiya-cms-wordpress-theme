<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 生成Bootstrap面包屑导航
 * ------------------------------------------------------------------------------
 */

//获取面包屑导航
function aya_breadcrumbs()
{
    $is_where = aya_is_where();

    $html = '<nav class="navbar-breadcrumb mx-2 my-4" aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb">';
    $html .= '<li class="breadcrumb-item"><i class="bi bi-cursor"></i> <a class="breadcrumb-link" href="' . home_url() . '">' . __('首页', 'AIYA') . '</a></li>';

    //格式
    $item_before = '<li class="breadcrumb-item active" aria-current="page">';
    $item_after = '</li>';
    //导航分层
    switch ($is_where) {
        case 'page':
            $html .= $item_before . aya_theme_breadcrumbs_post_title() . $item_after;
            break;
        case 'single':
            $html .= aya_theme_breadcrumbs_to_category();
            $html .= $item_before . __('正文', 'AIYA') . $item_after;
            break;
        case 'single_custom':
            $html .= $item_before . aya_theme_breadcrumbs_post_type() . $item_after;
            $html .= $item_before . __('正文', 'AIYA') . $item_after;
            break;
        case 'attachment':
            $html .= $item_before . aya_theme_breadcrumbs_post_title() . $item_after;
            $html .= $item_before . __('附件', 'AIYA') . $item_after;
            break;
        case 'archive_custom':
            $html .= $item_before . post_type_archive_title('', false) . $item_after;
            break;
        case 'category':
            $html .= aya_theme_breadcrumbs_to_category();
            $html .= $item_before . __('文章', 'AIYA') . $item_after;
            break;
        case 'tax':
            $html .= aya_theme_breadcrumbs_to_category();
            $html .= $item_before . __('归档', 'AIYA') . $item_after;
            break;
        case 'search':
            $html .= $item_before . __('搜索结果：', 'AIYA') . esc_html(get_search_query()) . $item_after;
            break;
        case 'tag':
            $html .= $item_before . __('标签', 'AIYA') . $item_after;
            $html .= $item_before . single_tag_title('#', false) . $item_after;
            break;
        case 'date':
            $html .= $item_before . __('归档', 'AIYA') . $item_after;
            $html .= $item_before . get_the_date() . $item_after;
            break;
        case 'author':
            $html .= $item_before . __('用户主页', 'AIYA') . $item_after;
            $html .= $item_before . get_the_author_meta('nickname') . $item_after;
            break;
        case '404':
            $html .= $item_before . __('404 NOT FOUND', 'AIYA') . $item_after;
            break;
        default:
            $html .= $item_before . wp_get_document_title() . $item_after;
            break;
    }
    $html .= '</ol></nav>';
    //输出
    e_html($html);
}
//获取面包屑列表中的分类
function aya_theme_breadcrumbs_to_category()
{
    global $cat;

    $check_single = aya_page_type('single');
    $check_category = aya_page_type('category');

    //获取当前分类
    $categorys = get_the_category();
    //计数层级
    if (count($categorys) <= 0 && $check_single == true) {
        return false;
    }
    if ($check_single == true) {
        $category = $categorys[0];
        //没取到则换方式获取
        if ($category == null && $check_category == true) {
            $category = get_category($cat);
        }
        $cats = get_category_parents($category->term_id, true, '');
    } else {
        $cats = get_category_parents($cat, true, '');
    }
    //格式化到HTML
    $cats = str_replace("<a", '<li class="breadcrumb-item"><a class="breadcrumb-link"', $cats);
    $cats = str_replace("</a>", '</a></li>', $cats);

    return $cats;
}
//获取面包屑列表中的页面文章类型
function aya_theme_breadcrumbs_post_type()
{
    switch (get_post_type()) {
        case 'tweet':
            return __('推文', 'AIYA');
        default:
            return __('页面', 'AIYA');
    }
}
//获取面包屑列表中的页面标题
function aya_theme_breadcrumbs_post_title()
{
    global $post;

    return $post->post_title;
}
