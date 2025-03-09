<?php

if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 模板数据封装方法
 * ------------------------------------------------------------------------------
 */

//将WP菜单对象构造为数组
function aya_menu_array_get($menu_name = '')
{
    $action = new AYA_Plugin_Menu_Object_In_Array($menu_name, false);

    return $action->menu;
}

//生成分页
function aya_pagination_array_get()
{
    $action = new AYA_Plugin_Pagination_link_In_Array(true, 4);

    return $action->pagination;
}

//获取登录注册设置
function aya_user_can_register()
{
    //已登录直接返回
    if (is_user_logged_in()) {
        return false;
    }

    //获取站点设置
    if (get_option('users_can_register')) {
        return true;
    } else {
        return false;
    }
}

//获取登录用户信息
function aya_user_login_in_data()
{
    if (is_user_logged_in()) {
        //获取用户对象
        $current_user = wp_get_current_user();

        //判断用户权限更新选项表单
        $user_menu = array();
        $user_add_badge = '';

        $user_menu[] = array(
            'label' => __('首页', 'AIYA'),
            'url' => home_url(),
        );

        //编辑及以上用户
        if (current_user_can('edit_posts')) {
            $user_menu[] = array(
                'label' => __('仪表盘', 'AIYA'),
                'url' => admin_url(),
            );
            $user_add_badge = '<span class="bg-success-light text-success px-1">Editor</span>';
        }

        //合并到新的数据
        $user_data = array(
            'logged' => true,
            'id' => $current_user->ID,
            'avatar' => get_avatar_url($current_user->ID, 64),
            'name' => $current_user->display_name,
            'email' => $current_user->user_email,
            'menus' => $user_menu,
            'badge' => $user_add_badge,
        );
    } else {
        //返回一个占位数据
        $user_data = array(
            'logged' => false,
            'id' => 0,
            'avatar' => get_avatar_url(0, 64),
            'name' => '请先登录',
            'email' => 'mail@mail.mail',
            'badge' => '',
        );
    }

    return aya_json_echo($user_data);
}

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

//评论分页链接的模板方法
function aya_comment_pagination_item_link($label_type = 'next')
{
    if (! is_singular()) {
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

function aya_pagination_next_page()
{
    $page_link = get_next_posts_page_link();
}

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

//文章缩略图处理
function aya_post_thumb($have_thumb_url = false, $post_content = '',  $size_w = 400, $size_h = 300)
{
    //取到 false 时从正文遍历
    if ($have_thumb_url === false) {
        $the_thumb_url = aya_match_post_first_image($post_content, false);
    } else {
        $the_thumb_url = $have_thumb_url;
    }

    //使用主题默认
    if ($the_thumb_url === false) {
        $the_thumb_url = aya_opt('site_default_thumb_upload', 'basic');
    }

    //检测主题图像处理依赖是否被加载
    if (class_exists('AYA_Image_Core')) {
        return NULL;
    }
    //使用BFI处理缩略图
    else {
        return get_bfi_thumb($the_thumb_url, $size_w, $size_h);
    }
}

//计算文章时效性
function aya_get_post_is_outdated($post_id = 0, $out_day = 30)
{
    $out_day = intval($out_day);

    //设置为0时
    if ($out_day == 0) {
        return false;
    }

    $publish_time = get_post_time('U', false, $post_id, true);
    $modified_time = get_post_modified_time('U', false, $post_id, true);

    //判断更新时间取最近
    $last_time = ($modified_time > $publish_time) ? $modified_time : $publish_time;

    //时间30天
    if (time() > $last_time + 86400 * $out_day) {
        return true;
    }
    return false;
}

//根据当前文章新建查询相关文章
function aya_get_related_posts($post_id = 0, $per_num = 5)
{
    if ($post_id == 0) {
        $post_id = get_the_ID();
    }

    //获取文章标签
    $this_post_tags = get_the_tags($post_id);
    //获取文章分类
    $this_post_categories = get_the_category($post_id);

    // 初始化查询参数
    $args = array(
        'post__not_in' => array($post_id), // 排除当前文章
        'ignore_sticky_posts' => 1,
        'posts_per_page' => $per_num,
    );

    //如果有标签或分类，构建tax_query参数
    if ($this_post_tags || $this_post_categories) {

        $args['tax_query'] = array('relation' => 'OR'); //使用 OR 逻辑模糊查询

        if ($this_post_tags) {

            $tag_ids = array();
            foreach ($this_post_tags as $tag) {
                $tag_ids[] = $tag->term_id;
            }

            $args['tax_query'][] = array(
                'taxonomy' => 'post_tag',
                'field' => 'term_id',
                'terms' => $tag_ids,
            );
        }

        if ($this_post_categories) {

            $category_ids = array();
            foreach ($this_post_categories as $category) {
                $category_ids[] = $category->term_id;
            }

            $args['tax_query'][] = array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $category_ids,
            );
        }
    }

    //执行查询
    $post = new AYA_Post_Query();

    $the_query = $post->query($args);

    if (!$the_query) return false;

    //输出相关文章
    $posts = array();

    foreach ($the_query as $post => $post_data) {
        $posts[] = array(
            'title' => $post_data['title'],
            'attr_title' => $post_data['attr_title'],
            'url' => $post_data['url'],
        );
    }

    return $posts;
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
