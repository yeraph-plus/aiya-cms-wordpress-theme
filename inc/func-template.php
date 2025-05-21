<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 模板组件
 * ------------------------------------------------------------------------------
 */

//导航栏LOGO
function aya_blog_logo($link_class = '', $logo_class = '')
{
    $logo_url = aya_opt('site_logo_image_upload', 'basic');
    $site_name = get_bloginfo('name');
    $is_home = is_front_page() || is_home();

    $html = '';
    $html .= '<div class="logo" itemscope itemtype="https://schema.org/Organization">';
    $html .= '<a href="' . get_home_url() . '" class="flex items-center overflow-hidden whitespace-nowrap ' . $link_class . '" itemprop="url">';
    $html .= '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" class="' . $logo_class . '" itemprop="logo">';

    if (aya_opt('site_logo_text_bool', 'basic', 1)) {
        $text_tag = $is_home ? 'h1' : 'span';
        $text_class = $logo_url ? 'ml-2' : '';
        $html .= '<' . $text_tag . ' class="' . $text_class . '" itemprop="name">' . esc_html($site_name) . '</' . $text_tag . '>';
    }

    $html .= '</a>';
    $html .= '</div>';

    return aya_echo($html);
}

function aya_blog_breadcrumb()
{
    $items = aya_get_breadcrumb();

    $html = '<nav class="breadcrumb" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">';
    foreach ($items as $i => $item) {
        $html .= '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        if ($item['url'] && $i < count($items) - 1) {
            $html .= '<a href="' . esc_url($item['url']) . '" itemprop="item"><span itemprop="name">' . esc_html($item['label']) . '</span></a>';
        } else {
            $html .= '<span itemprop="name">' . esc_html($item['label']) . '</span>';
        }
        $html .= '<meta itemprop="position" content="' . ($i + 1) . '" />';
        $html .= '</span>';
        if ($i < count($items) - 1) {
            $html .= ' &gt; ';
        }
    }
    $html .= '</nav>';

    return aya_echo($html);
}

/*
 * ------------------------------------------------------------------------------
 * 自定义一些模板方法
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
 * 文章内容条件循环模板
 * ------------------------------------------------------------------------------
 */

//首页入口模板路由
function aya_core_route_entry()
{
    $route_page = aya_is_where();

    //aya_template_load('header');

    //模板路由
    switch ($route_page) {
        case 'home':
            //首页循环
            aya_template_load('index');
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

    //aya_template_load('footer');
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
        case 'none':
            $sidebar_type = 'index-widget';
            break;
        case 'search':
            $sidebar_type = 'search-widget';
            break;
        case 'archive':
        case 'post_type_archive':
        case 'category':
        case 'tag':
        case 'date':
        case 'tax':
        case '404':
            $sidebar_type = 'archive-widget';
            break;
        case 'author':
            $sidebar_type = 'author-widget';
            break;
        case 'single':
        case 'page':
        case 'singular':
        case 'attachment':
            $sidebar_type = 'single-widget';
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
