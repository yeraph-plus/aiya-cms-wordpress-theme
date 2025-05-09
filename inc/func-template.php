<?php

if (!defined('ABSPATH'))
    exit;

/*
 * ------------------------------------------------------------------------------
 * 模板组件
 * ------------------------------------------------------------------------------
 */

//feather-icons模板格式
function aya_feather_icon($icon, $size = 24, $class = '', $ext = '')
{
    if (empty($icon)) {
        return '';
    }

    $html = '';
    $html .= '<i data-feather="' . $icon . '" width="' . $size . '" height="' . $size . '" class="' . $class . '" ' . $ext . '></i>';

    return $html;
}

/*
 * ------------------------------------------------------------------------------
 * 分页
 * ------------------------------------------------------------------------------
 */

//分页链接模板方法
function aya_pagination_item_link($paged_array, $get_data = '')
{
    $html_template = '<a href="%1$s" class="item %2$s">%3$s</a>';
    //取出指定数据
    if ($get_data != '') {
        //返回包含样式的标签
        if ($get_data == 'home') {
            $value = $paged_array['page_home'];

            $link_add_class = ($value['event_none']) ? '!hidden' : 'mr-2';
            $link_name = aya_feather_icon('home', '16', 'mr-1') . '首页';
        } else if ($get_data == 'prev') {
            $value = $paged_array['page_prev'];

            $link_add_class = ($value['event_none']) ? 'events-none' : ''; //使链接失效
            $link_name = aya_feather_icon('chevron-left', '16', 'mr-1') . '上一页';
        } else if ($get_data == 'next') {
            $value = $paged_array['page_next'];

            $link_add_class = ($value['event_none']) ? 'events-none' : ''; //使链接失效
            $link_name = '下一页' . aya_feather_icon('chevron-right', '16', 'mr-1');
        } else {
            return '';
        }

        $link_url = ($value['event_none']) ? '#' : $value['link'];

        $item_html = sprintf($html_template, $link_url, $link_add_class, $link_name);
    }
    //遍历生成
    else {
        //踢出循环
        $paged_array = array_slice($paged_array, 1);

        $item_html = '';
        foreach ($paged_array as $item => $value) {
            $item_class = '';
            if ($value['event_none']) {
                $item_class = 'events-none';
            }
            if ($value['is_active']) {
                $item_class = 'active';
            }
            $item_html .= sprintf($html_template, $value['link'], $item_class, $value['text']);
        }
    }

    return aya_echo($item_html);
}

//Ajax分页链接模板
function aya_pagination_next_page()
{
    $page_link = get_next_posts_page_link();
}

//评论分页链接的模板方法
function aya_comment_pagination_item_link($label_type = 'next')
{
    if (!is_singular()) {
        return;
    }

    $page = get_query_var('cpage');

    if ((int) $page <= 1) {
        return;
    }

    //上一页
    if ($label_type === 'prev') {
        $get_page = $page - 1;
        $label = aya_feather_icon('chevron-left', '16', '', 'stroke-width="2"') . __('Older', 'AIYA');
    }
    //下一页
    else if ($label_type === 'next') {
        $get_page = $page + 1;
        $label = __('Newer', 'AIYA') . aya_feather_icon('chevrons-right', '16', '', 'stroke-width="2"');
    }

    $html_template = '<a href="%1$s" class="item %2$s">%3$s</a>';

    $html = sprintf($html_template, esc_url(get_comments_pagenum_link($get_page)), '', $label);

    return aya_echo($html);
}

/*
 * ------------------------------------------------------------------------------
 * 面包屑
 * ------------------------------------------------------------------------------
 */

//获取面包屑列表中的分类
function aya_breadcrumb_category_parents()
{
    global $cat;

    //获取当前分类
    $categorys = get_the_category();
    //计数层级
    if (count($categorys) <= 0 && is_single()) {
        return false;
    }

    if (is_single()) {
        $category = $categorys[0];
        //没取到则换方式获取
        if ($category == null && is_category()) {
            $category = get_category($cat);
        }
        $cats = get_category_parents($category->term_id, true, '');
    } else {
        $cats = get_category_parents($cat, true, '');
    }

    if (is_wp_error($cats)) {
        return '';
    }
    //格式化到HTML
    $cats = str_replace("<a", '<li><a class=""', $cats);
    $cats = str_replace("</a>", '</a></li>', $cats);

    return $cats;
}

//面包屑模板方法
function aya_breadcrumb_item_link()
{
    $is_where = aya_is_where();

    $link_template = '<li><a href="%1$s" class="%2$s">%3$s</a></li>';
    $text_template = '<li><p class="%1$s">%2$s<p></li>';

    $item_html = '';
    $item_html .= '<ol class="breadcrumb">';
    $item_html .= '<li class="first-li"><a href="' . home_url() . '" class="flex items-center">' . aya_feather_icon('navigation', '16', 'mr-1', '') . __('首页', 'AIYA') . '</a></li>';

    switch ($is_where) {
        case 'singular':
            //尝试查询当前的文章类型名
            $post_type = get_post_type();
            $post_type_object = get_post_type_object($post_type);
            $post_type_name = $post_type_object->name;
            $item_html .= sprintf($text_template, '', $post_type_name);
            $item_html .= sprintf($text_template, 'active', get_the_title());
            break;
        case 'page':
            //尝试获取页面的层级
            $post = get_post();
            if ($post->post_parent) {
                $anc = get_post_ancestors($post->ID);
                $title = array_reverse($anc);
                foreach ($title as $ancestor) {
                    $item_html .= sprintf($link_template, get_permalink($ancestor), '', get_the_title($ancestor));
                }
            }
            $item_html .= sprintf($text_template, 'active', get_the_title());
            break;
        case 'single':
            //获取当前分类
            $item_html .= aya_breadcrumb_category_parents();
            $item_html .= sprintf($text_template, 'active', get_the_title());
            break;
        case 'attachment':
            $item_html .= sprintf($text_template, '', get_the_title());
            $item_html .= sprintf($text_template, 'active', __('附件', 'AIYA'));
            break;
        case 'search':
            $item_html .= sprintf($text_template, '', __('搜索 "', 'AIYA') . esc_html(get_search_query()) . __('"', 'AIYA'));
            break;
        case 'category' || 'tax':
            //获取当前分类
            $item_html .= aya_breadcrumb_category_parents();
            $item_html .= sprintf($text_template, 'active', __('文章列表', 'AIYA'));
            break;
        case 'tag':
            $item_html .= sprintf($text_template, '', __('标签', 'AIYA'));
            $item_html .= sprintf($text_template, 'active', single_tag_title('#', false));
            break;
        case 'author':
            $item_html .= sprintf($text_template, 'active', __('用户 ', 'AIYA') . get_the_author_meta('nickname') . __(' 的主页 ', 'AIYA'));
            break;
        case 'date' || 'year' || 'month' || 'day':
            $item_html .= sprintf($text_template, '', __('归档', 'AIYA'));
            $item_html .= sprintf($text_template, 'active', __('发布时间 ', 'AIYA') . get_the_date() . __(' 的文章 ', 'AIYA'));
            break;
        case 'archive':
            $item_html .= sprintf($text_template, '', __('归档', 'AIYA'));
            $item_html .= sprintf($text_template, 'active', post_type_archive_title('', false));
            break;
        case '404':
            $item_html .= sprintf($text_template, 'active', __('404 NOT FOUND', 'AIYA'));
            break;
        default:
            $item_html .= sprintf($text_template, 'active', wp_get_document_title());
            break;
    }

    if (is_paged()) {
        $item_html .= sprintf($text_template, '', __('第 ', 'AIYA') . get_query_var('paged') . __(' 页', 'AIYA'));
    }

    $item_html .= '</ol>';

    return aya_echo($item_html);
}

/*
 * ------------------------------------------------------------------------------
 * 正文组件
 * ------------------------------------------------------------------------------
 */

//分块标题
function aya_section_tittle($title, $icon = 'navigation')
{
    if (empty($title)) {
        return '';
    }

    $html = '';
    $html .= '<div class="section-tittle">';
    $html .= aya_feather_icon($icon, '24', 'mr-2 mt-1', '') . '<h2>' . $title . '</h2>';
    $html .= '</div>';

    return aya_echo($html);
}

//小工具卡片模板
function aya_widget_card($title, $content)
{
    if (empty($title)) {
        return '';
    }

    $html = '';
    $html .= '<aside class="widget widget-panel">';
    $html .= '<h3 class="widget-title">' . $title . '</h3>';
    $html .= '<div class="widget-content">' . $content . '</div>';
    $html .= '</aside>';

    return aya_echo($html);
}

//文章徽章标记
function aya_single_badge_starts($post_id = 0)
{
    $badge = '';

    //检查置顶
    if (is_sticky($post_id)) {
        $badge .= '<span class="title-badge bg-[#2196f3]">';
        $badge .= aya_feather_icon('paperclip', '16', 'mr-1', '') . __('置顶', 'AIYA');
        $badge .= '</span>';
    }
    //检查密码保护
    if (post_password_required($post_id)) {
        $badge .= '<span class="title-badge bg-[#caa70a]">';
        $badge .= aya_feather_icon('lock', '16', 'mr-1', '') . __('密码保护', 'AIYA');
        $badge .= '</span>';
    }
    //检查私密文章
    if (get_post_status($post_id) == 'private') {
        $badge .= '<span class="title-badge bg-[#d63384]">';
        $badge .= aya_feather_icon('eye-off', '16', 'mr-1', '') . __('私密', 'AIYA');
        $badge .= '</span>';
    }
    //检查最新文章
    $post_date = get_the_date('U', $post_id);
    if (date('U') - $post_date < 86400) {
        $badge .= '<span class="title-badge bg-[#e7515a]">';
        $badge .= aya_feather_icon('clock', '16', 'mr-1', '') . __('最新', 'AIYA');
        $badge .= '</span>';
    }

    return aya_echo($badge);
}

//文章分类徽记标记
function aya_single_badge_category($post_id = 0)
{
    $badge_before = '<span class="title-badge bg-primary">';
    $badge_after = '</span>';

    $the_cat_list = get_the_term_list($post_id, 'category', $badge_before, $badge_after . $badge_before, $badge_after);

    if (!is_wp_error($the_cat_list)) {
        return aya_echo($the_cat_list);
    }
}

//文章末尾标签
function aya_single_badge_tags($post_id = 0)
{
    $cats_before = '<span class="tags-badge">';
    $cats_after = '</span>';
    $cats_icon = aya_feather_icon('tag', '12', 'mr-1', '');
    $tags_icon = aya_feather_icon('hash', '12', 'mr-1', '');

    $the_cat_list = get_the_term_list($post_id, 'category', $cats_before . $cats_icon, $cats_after . $cats_before . $cats_icon, $cats_after);
    $the_tag_list = get_the_term_list($post_id, 'post_tag', $cats_before . $tags_icon, $cats_after . $cats_before . $tags_icon, $cats_after);

    if (!is_wp_error($the_cat_list) || !is_wp_error($the_tag_list)) {
        return aya_echo($the_cat_list . $the_tag_list);
    }
}

//文章顶部中显示标签
function aya_single_status_tags($post_id = 0)
{
    $terms = get_the_terms($post_id, 'status');

    $html = '';
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $html .= '<div class="relative flex items-center text-dark bg-dark-light rounded border-l-4 border-dark dark:bg-dark-dark-light dark:text-white-light dark:border-white-light/20 p-4 mb-4">';
            $html .= aya_feather_icon('alert-octagon', '20', 'mr-2', '') . '<span><b>' . esc_html($term->name) . '</b> ' . esc_html($term->description) . '</span>';
            $html .= '</div>';
        }
    }

    return aya_echo($html);
}
