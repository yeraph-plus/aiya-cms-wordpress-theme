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
 * React Islands 服务端渲染函数
 * ------------------------------------------------------------------------------
 */

/**
 * 渲染 nav-breadcrumb 岛屿的服务端 HTML（用于 hydrateRoot 水合）
 *
 * @param array $props 面包屑数据 ['items' => [...]]
 * @return string 渲染的 HTML
 */
function aya_island_render_nav_breadcrumb($props)
{
    $items = $props['items'] ?? [];
    if (empty($items)) {
        return '';
    }

    $count = count($items);

    // Navigation SVG icon (lucide-react Navigation)
    $nav_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>';

    // ChevronRight SVG icon (lucide-react ChevronRight)
    $chevron_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6"></path></svg>';

    ob_start();
    ?><nav aria-label="breadcrumb" data-slot="breadcrumb" class="my-4"><ol data-slot="breadcrumb-list" class="text-muted-foreground flex flex-wrap items-center gap-1.5 text-sm break-words sm:gap-2.5 flex-nowrap overflow-hidden"><li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5 shrink-0"><?php echo $nav_icon; ?></li><?php foreach ($items as $index => $item) : $is_last = ($index === $count - 1); ?><li data-slot="breadcrumb-separator" role="presentation" aria-hidden="true" class="[&amp;>svg]:size-3.5 shrink-0"><?php echo $chevron_icon; ?></li><li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5 whitespace-nowrap min-w-0"><?php if ($is_last) : ?><span data-slot="breadcrumb-page" role="link" aria-disabled="true" aria-current="page" class="text-foreground font-normal truncate max-w-[240px] sm:max-w-[400px] md:max-w-[400px] block"><?php echo esc_html($item['label']); ?></span><?php else : ?><a data-slot="breadcrumb-link" class="hover:text-foreground transition-colors truncate max-w-[160px] sm:max-w-[240px] md:max-w-[400px] block" href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a><?php endif; ?></li><?php endforeach; ?></ol></nav><?php
    return ob_get_clean();
}

/*
 * ------------------------------------------------------------------------------
 * 模板组件
 * ------------------------------------------------------------------------------
 */

//文章缩略图处理
function aya_get_post_thumb($thumb_url = false, $post_content = '', $size_w = 400, $size_h = 300)
{
    // 如果已传入 URL 直接用它
    if ($thumb_url == false) {
        // 否则从正文提取首张图片
        $thumb_url = aya_match_post_first_image($post_content, false);
    }

    // 无图片时使用主题默认
    if ($thumb_url === false) {
        $thumb_url = aya_opt('site_default_thumb_upload', 'basic');
    }

    //检测主题图像处理依赖是否被加载
    if (function_exists('aya_image_trans_init')) {
        return aya_image_trans_post_thumb($thumb_url, $size_w, $size_h);
    }
    //使用BFI处理缩略图
    else {
        return get_bfi_thumb($thumb_url, $size_w, $size_h);
    }
}
