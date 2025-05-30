<?php

if (!defined('ABSPATH')) {
    exit;
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
    //自定义页面
    if (aya_is_land_page() !== false) {
        return aya_land_page_route_entry();
    }

    //页头
    aya_template_load('part/header');

    $route_page = aya_is_where();

    //模板路由
    switch ($route_page) {
        //首页循环
        case 'home':
        case 'home_paged':
            aya_template_load('index');
            break;
        //搜索结果
        case 'search':
            aya_template_load('search');
            break;
        //归档
        case 'archive':
        case 'post_type_archive':
        case 'category':
        case 'tag':
        case 'date':
        case 'tax':
            aya_template_load('archive');
            break;
        //用户归档
        case 'author':
            aya_template_load('author');
            break;
        //独立页面
        case 'single':
        case 'page':
        case 'singular':
        case 'attachment':
            aya_template_load('content');
            break;
        //处理路由错误
        case '404':
        case 'none':
        default:
            aya_template_load('404');
            break;
    }

    //页脚
    aya_template_load('part/footer');
}

//独立页面入口模板路由
function aya_land_page_route_entry()
{
    global $aya_land_page;

    $page_type = aya_is_land_page();

    //如果没有找到对应的页面配置，跳入404
    if ($page_type === false) {

        aya_template_load('part/header');

        aya_template_load('404');

        aya_template_load('part/footer');

        //结束模板
        return;
    }

    //获取页面配置
    $page_config = $aya_land_page[$page_type];

    //是否使用原始模板（带页头页脚）
    $use_original = isset($page_config['orginal']) ? $page_config['orginal'] : true;

    //页头
    if ($use_original) {
        aya_template_load('part/header');
    }

    //已经定义了的页面
    if (isset($page_config['template'])) {
        aya_template_load('page/' . $page_config['template']);
    } else {
        aya_template_load('404');
    }

    //页脚
    if ($use_original) {
        aya_template_load('part/footer');
    }
}

//替代LOOP循环
function aya_while_the_post()
{
    if (!have_posts()) {
        //没有文章
        aya_template_load('loops/none');

        return;
    }

    //全局设置
    $loop_layout = aya_opt('site_loop_layout_type', 'basic');
    $loop_grid = aya_opt('site_loop_column_type', 'basic');

    //允许过滤器修改布局模式
    $loop_layout = apply_filters('aya_loop_layout', $loop_layout);

    //指定默认值
    if (!in_array($loop_layout, ['waterfall', 'grid', 'list'])) {
        $loop_layout = 'grid';
    }

    do_action('aya_before_loop', $loop_layout);

    //指定布局样式
    if ($loop_layout === 'list') {
        //列表样式
        $grid_class = 'flex flex-col space-y-4';
    } else if ($loop_layout === 'waterfall') {
        //瀑布流样式
        $grid_class = 'relative masonry-grid w-full" data-columns="' . $loop_grid;
    } else {
        //网格样式
        $grid_class = 'gap-4 grid ';
        switch ($loop_grid) {
            case '2':
                $grid_class .= 'grid-cols-1 sm:grid-cols-2';
                break;
            case '3':
                $grid_class .= 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
                break;
            case '4':
            default:
                $grid_class .= 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4';
                break;
            case '5':
                $grid_class .= 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5';
                break;
        }
    }

    //before container
    aya_echo('<div id="main-loop" class="' . $grid_class . '">');
    //瀑布流模式列宽参考容器
    if ($loop_layout === 'waterfall') {
        aya_echo('<div class="masonry-sizer"></div>');
    }
    //执行主循环
    while (have_posts()) {
        the_post();
        aya_template_load('loops/' . $loop_layout);
    }

    //after container
    aya_echo('</div>');

    do_action('aya_after_loop', $loop_layout);

    //加载分页
    aya_template_load('part/pagination');
}

//替代正文循环
function aya_while_have_content()
{
    if (!have_posts()) {
        //没有文章
        aya_template_load('404');

        return;
    }

    //判断文章类型
    $post_type = get_post_type();

    do_action('aya_before_content');

    switch ($post_type) {
        case 'post':
            //返回默认格式
            $content_layout = 'default';
            break;
        case 'page':
            //返回页面格式
            $content_layout = 'page';
            break;
        case 'attachment':
            //返回附件格式
            $content_layout = 'media';
            break;
        default:
            //返回自定义文章的类型
            $content_layout = $post_type;
            break;
    }

    //执行主循环
    while (have_posts()) {
        the_post();
        aya_template_load('contents/' . $content_layout);
    }

    do_action('aya_after_content');
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
 * 预定义的数据处理器
 * ------------------------------------------------------------------------------
 */

//获取菜单
function aya_get_menu($menu_name)
{
    //返回对象
    return AYA_WP_Menu_Object::get_menu($menu_name);
}

function aya_get_menu_json($menu_name)
{
    $menu_object = AYA_WP_Menu_Object::get_menu($menu_name);

    //返回JSON
    return json_encode($menu_object, JSON_UNESCAPED_UNICODE);
}

//获取面包屑导航
function aya_get_breadcrumb()
{
    return AYA_WP_Breadcrumb_Object::get_breadcrumb();
}

//获取简单分页列表
function aya_get_simple_pagination()
{
    return AYA_WP_Paged_Object::get_pagination([
        'mid_size' => 0,
    ], 'simple');
}

//获取分页列表
function aya_get_pagination()
{
    $paged = AYA_WP_Paged_Object::get_pagination([
        'mid_size' => 3,
        'prev_text' => __('上一页', 'AIYA'),
        'next_text' => __('下一页', 'AIYA'),
    ]);

    if (!empty($paged) || $paged['total'] != 1) {
        return $paged;
    }
    return;
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

//feather-icons标签格式
function aya_feather_icon($icon, $size = 24, $class = '', $ext = '')
{
    if (empty($icon)) {
        return '';
    }

    $html = '';
    $html .= '<i data-feather="' . $icon . '" width="' . $size . '" height="' . $size . '" class="' . $class . '" ' . $ext . '></i>';

    return $html;
}

//BBCode语法转换方法
function aya_preg_desc($desc)
{
    if (empty($desc)) {
        return '';
    }

    $desc = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');

    $bbcode_search = [
        '/\[br\/]/',
        '/\[(b|strong)\](.*?)\[\/(b|strong)\]/is',
        '/\[(i|em)\](.*?)\[\/(i|em)\]/is',
        '/\[u\](.*?)\[\/u\]/is',
        '/\[(s|del)\](.*?)\[\/(s|del)\]/is',
        '/\[code\](.*?)\[\/code\]/is',
        '/\[pre\](.*?)\[\/pre\]/is',
        '/\[url=([^\]"\'<>]+)\](.*?)\[\/url\]/is',
    ];

    $bbcode_replace = [
        '<br />',
        '<strong class="font-bold">$2</strong>',
        '<em class="italic">$2</em>',
        '<ins class="underline decoration-solid">$1</ins>',
        '<del class="line-through">$1</del>',
        '<code class="bg-base-200 text-base-content rounded px-1 py-0.5 text-sm font-mono">$1</code>',
        '<pre class="bg-base-200 text-base-content rounded p-4 my-2 overflow-x-auto text-sm font-mono">$1</pre>',
        '<a href="$1" class="link link-primary hover:link-hover" target="_blank" rel="noopener noreferrer">$2</a>',
    ];

    $desc = preg_replace($bbcode_search, $bbcode_replace, $desc);

    return $desc;
}

//文章缩略图处理
function aya_post_thumb($have_thumb_url = false, $post_content = '', $size_w = 400, $size_h = 300)
{
    //没有传回正文时忽略
    if ($have_thumb_url === false && empty($post_content)) {
        return false;
    }
    //特色图片取到 false 时从正文遍历
    if ($have_thumb_url === false) {
        $have_thumb_url = aya_match_post_first_image($post_content, false);
    }
    //正文取到 false 时使用主题默认
    if ($have_thumb_url === false) {
        $have_thumb_url = aya_opt('site_default_thumb_upload', 'basic');
    }

    $the_thumb_url = $have_thumb_url;

    //检测主题图像处理依赖是否被加载
    if (function_exists('aya_image_trans_init')) {
        return aya_image_trans_post_thumb($the_thumb_url, $size_w, $size_h);
    }
    //使用BFI处理缩略图
    else {
        return get_bfi_thumb($the_thumb_url, $size_w, $size_h);
    }
}

//文章状态徽章处理
function aya_the_post_status_badge($status_array = [])
{
    $html = '';

    //匹配徽章色彩样式
    foreach ($status_array as $status_item => $status_name) {

        switch ($status_item) {
            case 'sticky':
                $badge_class = "badge-primary";
                break;
            case 'newest':
                $badge_class = "badge-secondary";
                break;
            case 'password':
            case 'private':
                $badge_class = "badge-neutral";
                break;
            case 'pending':
            case 'future':
            case 'draft':
            case 'auto-draft':
                $badge_class = "badge-info";
                break;
            case 'inherit':
            case 'trash':
                $badge_class = "badge-error";
                break;
            default:
                $badge_class = "badge-accent";
        }

        $html .= '<span class="badge ' . $badge_class . '">' . $status_name . '</span>';
    }

    return aya_echo($html);
}

/*
 * ------------------------------------------------------------------------------
 * 正文组件
 * ------------------------------------------------------------------------------
 */

//文章顶部中显示的小贴士信息
function aya_the_post_tips($post_id = 0)
{
    $html = '';

    $terms = get_the_terms($post_id, 'tips');

    if ($terms && !is_wp_error($terms)) {
        //显示全部
        foreach ($terms as $term) {
            //获取设置的颜色样式
            $alert_by = get_term_meta($term->term_id, 'alert_level', true);

            $html .= '<div role="alert" class="alert alert-outline alert-' . esc_attr($alert_by) . ' mb-2">';
            $html .= '<span><b>' . esc_html($term->name) . '</b> ' . esc_html($term->description) . '</span>';
            $html .= '</div>';
        }
    }

    return aya_echo($html);
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

    return aya_echo($html) . paginate_links();
}

//区块标题模板
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



/*
 * ------------------------------------------------------------------------------
 * 轮播组件数据处理
 * ------------------------------------------------------------------------------
 */

function aya_carousel_component()
{
    return;

    if (aya_is_where() !== 'home') {
        return;
    }
    if (aya_opt('site_carousel_load_bool', 'land')) {
        $carousel_layout = aya_opt('site_carousel_layout_type', 'land');
        $carousel_list = aya_opt('site_carousel_post_list', 'land');

        aya_print($carousel_list);
    }
}
