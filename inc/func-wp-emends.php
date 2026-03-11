<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 首次启动
 * ------------------------------------------------------------------------------
 */

add_action('after_switch_theme', 'aya_theme_after_init');

function aya_theme_after_init()
{
    //刷新站点重写规则
    flush_rewrite_rules();

    //跳转主题设置页面
    /*
    global $pagenow;

    if ('themes.php' == $pagenow && isset($_GET['activated'])) {
        // options-general.php
        wp_redirect(admin_url('?page=aya-options-basic'));
        exit;
    }
    */

    //安装数据表
    do_action('aya_install');
}

/*
 * ------------------------------------------------------------------------------
 * 重写路由
 * -------------------s-----------------------------------------------------------
 */

//初始化时增加重写路由规则
add_action('init', 'aya_rewrite_base_rule');
//重写author的链接
add_filter('author_link', 'aya_rewrite_author_link', 10, 2);
add_action('template_redirect', 'aya_rewrite_author_template_redirect');
//注册自定义页面路由
add_filter('query_vars', 'aya_rewrite_register_query_vars');
add_filter('pre_get_document_title', 'aya_rewrite_land_page_document_title');
add_filter('redirect_canonical', 'aya_rewrite_page_cancel_redirect_canonical');
//使用自定义的REST-API路径
add_filter('rest_url_prefix', 'aya_rewrite_rest_api_url_prefix');
add_action('template_redirect', 'aya_rewrite_rest_api_template_redirect');

//根据主题设置，追加新的页面重写规则
function aya_rewrite_base_rule()
{
    global $wp_rewrite, $aya_land_page;

    //修改作者页面路由基准为user
    $wp_rewrite->author_base = 'user';
    $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';

    //添加 /user/ 重写规则
    add_rewrite_rule(
        '^user/(\d+)/?$',
        'index.php?author=$matches[1]',
        'top'
    );

    //添加独立页面的重写规则
    if (!empty($aya_land_page)) {
        //循环全部
        foreach ($aya_land_page as $slug => $page) {
            //使用最小路由规则
            add_rewrite_rule(
                '^' . $slug . '/?$',
                'index.php?land_page_vars=' . $slug,
                'top'
            );
            //使用参数支持的路由规则
            add_rewrite_rule(
                '^' . $slug . '/([^/]+)/?$',
                'index.php?land_page_vars=' . $slug . '&land_param=$matches[1]',
                'top'
            );
        }
    }
}

//生成/user/{token}格式的作者链接
function aya_rewrite_author_link($link, $author_id)
{
    return home_url('/user/' . $author_id . '/');
}

//使 /author/author_name 重定向到 /user/id
function aya_rewrite_author_template_redirect()
{
    if (is_author() && get_query_var('author_name')) {

        $user = get_user_by('slug', get_query_var('author_name'));

        if ($user) {
            //重定向
            wp_redirect(home_url('/user/' . $user->ID . '/'), 301);
            exit;
        }
    }
}

//注册查询变量
function aya_rewrite_register_query_vars($vars)
{
    $vars[] = 'land_page_vars';
    $vars[] = 'land_param';

    return $vars;
}

//捕获自定义页面查询
function aya_is_land_page()
{
    //捕获自定义页面查询变量
    $page_type = get_query_var('land_page_vars');

    //如果没有查询变量，则不是自定义页面
    if (empty($page_type)) {
        return false;
    }

    global $aya_land_page;

    //检查页面类型是否在配置中
    if (!isset($aya_land_page[$page_type])) {
        return false;
    }

    //返回页面类型
    return $page_type;
}

//处理自定义页面标题
function aya_rewrite_land_page_document_title($title)
{
    //捕获自定义页面查询
    $page_type = aya_is_land_page();

    if ($page_type === false) {
        return false;
    }

    global $aya_land_page;

    //获取标题参数
    $page_title = $aya_land_page[$page_type]['title'] ?? __('独立页面', 'AIYA');

    //获取站点名称
    $site_name = get_bloginfo('name');

    return $page_title . ' - ' . $site_name;
}

//DEBUG：始终取消自定义页面自动重定向
function aya_rewrite_page_cancel_redirect_canonical($redirect_url)
{
    //获取到查询参数时
    $page_type = aya_is_land_page();

    if ($page_type === false) {
        return false;
    }

    return $redirect_url;
}

//使用自定义的API路径
function aya_rewrite_rest_api_url_prefix()
{
    return 'api';
}

//使 /wp-json 重定向到 /api
function aya_rewrite_rest_api_template_redirect()
{
    if (strpos($_SERVER['REQUEST_URI'], 'wp-json') !== false) {
        wp_redirect(site_url(str_replace('wp-json', 'api', $_SERVER['REQUEST_URI'])), 301);

        exit;
    }
}

/*
 * ------------------------------------------------------------------------------
 * 配置一些WordPress过滤器和动作
 * ------------------------------------------------------------------------------
 */

//添加钩子 显示一言
add_action('admin_notices', 'aya_theme_admin_hello_hitokoto');
//添加钩子 移除密码保护和私密文章标题前缀文本
add_filter('protected_title_format', 'aya_theme_remove_protected_title_format');
add_filter('private_title_format', 'aya_theme_remove_protected_title_format');
//添加钩子 修改阅读更多文本
add_filter('excerpt_more', 'aya_theme_excerpt_more_filter');
//添加钩子 禁用评论表单中的URL字段
add_filter('comment_form_default_fields', 'aya_theme_disable_comment_url');
//移除动作 禁用评论表单中的Cookies存储
remove_action('set_comment_cookies', 'wp_set_comment_cookies');

//一言
function aya_theme_admin_hello_hitokoto()
{
    echo '<p id="hello-hitokoto"><span dir="ltr">' . aya_curl_get_hitokoto() . '</span></p>';
}
//取消前缀文本格式
function aya_theme_remove_protected_title_format($format)
{
    return '';
}
//修改阅读更多文本
function aya_theme_excerpt_more_filter()
{
    return '...';
}
//过滤评论表单
function aya_theme_disable_comment_url($fields)
{
    if (isset($fields['url'])) {
        unset($fields['url']);
    }
    if (isset($fields['cookies'])) {
        unset($fields['cookies']);
    }

    return $fields;
}

/*
 * ------------------------------------------------------------------------------
 * 文章点赞和元数据处理
 * ------------------------------------------------------------------------------
 */

//添加钩子 点赞计数器
add_action('the_post', 'aya_add_like_count_to_post_object');
//添加钩子 添加meta数据预加载
add_action('loop_start', 'aya_prefetch_post_like_count');

//点赞计数器
function aya_add_like_count_to_post_object($post)
{
    if (is_object($post) && property_exists($post, 'ID')) {

        $the_likes = get_post_meta($post->ID, 'like_count', true);

        $post->like_count = intval($the_likes);
    }

    return $post;
}

//添加 Post_meta 预加载
function aya_prefetch_post_like_count($wp_query)
{
    //获取查询中所有文章
    $post_ids = wp_list_pluck($wp_query->posts, 'ID');

    if (!empty($post_ids)) {

        update_meta_cache('post', $post_ids);
    }
}


/*
 * ------------------------------------------------------------------------------
 * 文章格式过滤器
 * ------------------------------------------------------------------------------
 */

//添加钩子
add_filter('the_content', 'aya_post_content_filter_format');

//合并方法
function aya_post_content_filter_format($content)
{
    if (aya_is_debug()) {
        $start = microtime(true);
    }

    if (aya_opt('site_content_dom_handler_bool', 'automatic')) {
        //使用DOM处理器
        $content = aya_content_filter_dom_document($content);
    } else {
        //普通正则方法
        if (aya_opt('site_content_link_filter_bool', 'automatic')) {
            $content = aya_content_filter_link_tag($content);
        }
        if (aya_opt('site_content_img_filter_bool', 'automatic')) {
            $content = aya_content_filter_img_tag($content);
        }
    }

    if (aya_is_debug()) {
        $end = microtime(true);

        $content .= '<p>The content filter load time: ' . ($end - $start) . ' seconds.</p>';
    }

    return $content;
}

//DOM版过滤器方法
function aya_content_filter_dom_document($content, $encode_utf8 = false)
{
    if (empty($content))
        return $content;

    global $post;

    //强制编码为UTF-8
    if ($encode_utf8) {
        @$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
    }
    //用 DOMDocument 处理 HTML
    $dom = new DOMDocument();
    //忽略 HTML5 警告
    libxml_use_internal_errors(true);
    //加载 HTML
    $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    //清除报错
    //libxml_clear_errors();

    //格式<img>标签
    if (aya_opt('site_content_img_filter_bool', 'automatic')) {
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            if (!($img instanceof DOMElement)) {
                continue;
            }
            //添加属性
            $img->setAttribute('loading', 'lazy'); //eager
            //判断补充alt属性
            $existing_alt = $img->getAttribute('alt');
            if (empty($existing_alt)) {
                $img->setAttribute('alt', get_the_title($post));
            }
        }
    }

    //格式化<a>标签
    if (aya_opt('site_content_link_filter_bool', 'automatic')) {
        $redirect_option = aya_opt('site_content_link_jump_page_type', 'automatic');
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            if (!($link instanceof DOMElement)) {
                continue;
            }
            $href = $link->getAttribute('href');

            //如果是外部链接
            if (cur_is_external_url($href)) {
                //添加nofollow
                $rel = $link->getAttribute('rel');
                $rel = preg_replace('/\bnofollow\b/', '', $rel);
                $link->setAttribute('rel', trim($rel . ' nofollow'));
                //外链转内链
                if ($redirect_option == 'link') {
                    //外链提示页面
                    $link->setAttribute('href', add_query_arg('target', urlencode($href), home_url('/link/')));
                } else if ($redirect_option == 'go') {
                    //内跳页面
                    $link->setAttribute('href', add_query_arg('url', base64_encode($href), home_url('/go/')));
                    //添加target
                    $link->setAttribute('target', '_blank');
                } else {
                    //只添加target
                    $link->setAttribute('target', '_blank');
                }
            }
        }
    }

    //保存
    $this_content = $dom->saveHTML();

    return $this_content;
}

//格式化<a>标签
function aya_content_filter_link_tag($content)
{
    $redirect_option = aya_opt('site_content_link_jump_page_type', 'automatic');

    return preg_replace_callback('/<a\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) use ($redirect_option) {
        $full_tag = $matches[0];
        $url = $matches[2];

        // 如果是外部链接
        if (cur_is_external_url($url)) {
            // 处理跳转逻辑
            $new_url = $url;
            $need_target = false;

            if ($redirect_option == 'link') {
                $new_url = add_query_arg('target', urlencode($url), home_url('/link/'));
            } elseif ($redirect_option == 'go') {
                $new_url = add_query_arg('url', base64_encode($url), home_url('/go/'));
                $need_target = true;
            } else {
                $need_target = true;
            }

            // 替换 href (仅替换匹配到的部分，避免误伤)
            if ($new_url !== $url) {
                $full_tag = str_replace($url, $new_url, $full_tag);
            }

            // 处理 rel="nofollow"
            if (strpos($full_tag, 'rel=') !== false) {
                $full_tag = preg_replace_callback('/rel=["\']([^"\']*)["\']/i', function ($m) {
                    $rels = preg_split('/\s+/', $m[1], -1, PREG_SPLIT_NO_EMPTY);
                    if (!in_array('nofollow', $rels)) {
                        $rels[] = 'nofollow';
                    }
                    return 'rel="' . implode(' ', $rels) . '"';
                }, $full_tag);
            } else {
                // 使用 preg_replace 确保插入位置正确且不破坏其他属性
                $full_tag = preg_replace('/(<a\s+)/i', '$1rel="nofollow" ', $full_tag, 1);
            }

            // 处理 target="_blank"
            if ($need_target) {
                if (strpos($full_tag, 'target=') !== false) {
                    $full_tag = preg_replace('/target=["\'][^"\']*["\']/i', 'target="_blank"', $full_tag);
                } else {
                    $full_tag = preg_replace('/(<a\s+)/i', '$1target="_blank" ', $full_tag, 1);
                }
            }
        }

        return $full_tag;
    }, $content);
}

//格式化<img>标签
function aya_content_filter_img_tag($content)
{
    if (empty($content)) {
        return $content;
    }

    global $post;
    $post_title = get_the_title($post);

    return preg_replace_callback('/<img\s+([^>]+)>/i', function ($matches) use ($post_title) {
        $full_tag = $matches[0];

        // 添加 loading="lazy"
        if (strpos($full_tag, 'loading=') === false) {
            $full_tag = preg_replace('/(<img\s+)/i', '$1loading="lazy" ', $full_tag, 1);
        }

        // 处理 alt 属性
        if (strpos($full_tag, 'alt=') === false) {
            // 没有 alt 属性，添加
            $full_tag = preg_replace('/(<img\s+)/i', '$1alt="' . esc_attr($post_title) . '" ', $full_tag, 1);
        } else {
            // 有 alt 属性，检查是否为空
            if (preg_match('/alt=["\']\s*["\']/i', $full_tag)) {
                $full_tag = preg_replace('/alt=["\']\s*["\']/i', 'alt="' . esc_attr($post_title) . '"', $full_tag);
            }
        }

        return $full_tag;
    }, $content);
}

/*
 * ------------------------------------------------------------------------------
 * 文章保存过滤器
 * ------------------------------------------------------------------------------
 */

//添加动作 文章保存时循环一次
add_action('save_post', 'aya_save_post_formatting');

//循环方法
function aya_save_post_formatting($post_id)
{
    //如果是新文章就先跳过
    if (empty($post_id)) {
        return;
    }
    //检查用户权限
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    //检查是否为自动保存
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    //防止进入递归，先注销钩子
    remove_action('save_post', 'aya_save_post_formatting');

    //获取文章标题
    $post_title = get_post_field('post_title', $post_id);
    //获取文章内容
    $post_content = get_post_field('post_content', $post_id);

    //是否排版
    if (aya_opt('site_post_chs_compose_bool', 'automatic')) {

        //过滤一些禁止的参数
        $correct_array = aya_opt('site_post_chs_compose_type', 'automatic');

        //对文章内容进行格式化
        $formatted_content = aya_chs_type_setting($post_content, $correct_array);
        //对文章标题进行格式化
        $formatted_title = aya_chs_type_setting($post_title, $correct_array);
    }

    //是否刷新文章日期
    if (get_post_meta($post_id, 'reset_post_datetime', true)) {
        // 重置发布日期为当前时间
        $reset_date_time = current_time('mysql');
    }

    //更新文章内容
    $post_array = array();

    $post_array['ID'] = $post_id;

    if (!empty($formatted_content)) {
        $post_array['post_content'] = $formatted_content;
    }
    if (!empty($formatted_title)) {
        $post_array['post_title'] = $formatted_title;
    }
    if (!empty($reset_date_time)) {
        $post_array['post_date'] = $reset_date_time;
        $post_array['post_date_gmt'] = get_gmt_from_date($reset_date_time);
    }
    //更新文章
    wp_update_post($post_array);
    //恢复钩子
    add_action('save_post', 'aya_save_post_formatting');
}

/*
 * ------------------------------------------------------------------------------
 * 自定义文章提示分类法模板
 * ------------------------------------------------------------------------------
 */

// 在主题首次启用时添加默认提示分类
add_action('aya_install', 'aya_tax_tips_add_default_terms');

if (aya_opt('site_post_add_tips_terms_bool', 'basic')) {
    //注册提示分类法
    AYP::action(
        //'分类法' => array('name' => '分类法名', 'slug' => '别名', 'post_type' => array('此分类法适用的文章类型', ), 'tag_mode' => 设置为true则使用标签分类法模板 ),
        'Register_Tax_Type',
        [
            'status' => [
                'name' => __('小贴士', 'AIYA'),
                'slug' => 'tips',
                'post_type' => ['post'],
            ],
        ]
    );
    //添加分类法设置
    AYF::new_tex([
        'add_meta_in' => 'tips',
        'fields' => [
            [
                'title' => '提示框颜色',
                'desc' => '选择提示框样式，提示用户重要的消息',
                'id' => 'alert_level',
                'type' => 'select',
                'sub' => [
                    'default' => '默认',
                    'info' => '信息蓝',
                    'success' => '成功绿',
                    'warning' => '警告黄',
                    'error' => '危险红',
                ],
                'default' => 'default',
            ],
        ]
    ]);
}

//添加默认提示列表
function aya_tax_tips_add_default_terms()
{
    $taxonomy = 'tips';

    $terms = [
        '更新中' => [
            'description' => '这篇文章正在更新中，可能还会有新的内容添加。',
            'slug' => 'updating'
        ],
        '需要更新' => [
            'description' => '这篇文章的信息已经失效，当前内容仅供参考。',
            'slug' => 'invalid'
        ],
        '已废弃' => [
            'description' => '这篇文章的信息已经废弃，不再更新。',
            'slug' => 'abandoned'
        ],
        '危险操作' => [
            'description' => '这篇教程包含修改系统核心文件等操作，请注意备份。',
            'slug' => 'dangerous'
        ],
        '来源不明' => [
            'description' => '这篇文章的信息来源未经证明，捕风捉影罢了。',
            'slug' => 'rumor'
        ],
    ];

    foreach ($terms as $term_name => $term_args) {
        //检查项目存在
        if (!term_exists($term_name, $taxonomy)) {
            wp_insert_term($term_name, $taxonomy, $term_args);
        }
    }
}

//文章顶部中显示的小贴士信息
function aya_get_post_tips($post_id = 0)
{
    $terms = get_the_terms($post_id, 'tips');

    if ($terms && !is_wp_error($terms)) {
        //显示全部
        foreach ($terms as $term) {
            //获取设置的颜色样式
            $alert_by = get_term_meta($term->term_id, 'alert_level', true);

            $tips[] = [
                'alert' => esc_attr($alert_by),
                'name' => esc_html($term->name),
                'description' => esc_html($term->description),
            ];
        }
    }

    return $tips ?? [];
}

/*
 * ------------------------------------------------------------------------------
 * 自定义推文文章类型
 * ------------------------------------------------------------------------------
 */
//注册文章类型
add_action('after_setup_theme', 'aya_post_type_tweet_action');
//MetaBox注册
//add_action('add_meta_boxes', 'aya_post_type_tweet_add_meta_box');

function aya_post_type_tweet_action()
{
    //注册文章类型
    AYP::action(
        //'文章类型' => array('name' => '文章类型名','slug' => '别名','icon' => '图标','in_homepage' => 允许显示在首页),
        'Register_Post_Type',
        [
            'tweet' => [
                'name' => __('推文', 'AIYA'),
                'slug' => 'tweet',
                'icon' => 'dashicons-format-quote',
                'in_homepage' => false,
            ],
        ]
    );
}

//使自定义文章类型可以操作置顶
function aya_post_type_tweet_add_meta_box()
{
    add_meta_box('aya_tweet_product_sticky', __('置顶', 'AIYA'), 'aya_tweet_product_sticky', 'tweet', 'side', 'high');
}

function aya_tweet_product_sticky()
{
    printf(
        '<p>
            <label for="super-sticky" class="selectit">
                <input id="super-sticky" name="sticky" type="checkbox" value="sticky" %s />
                %s
            </label>
        </p>',
        checked(is_sticky(), true, false),
        esc_html__('置顶这篇文章', 'AIYA')
    );
}

/*
 * ------------------------------------------------------------------------------
 * 自动别名
 * ------------------------------------------------------------------------------
 */

add_filter('wp_insert_term_data', 'aya_insert_term_data_slug', 10, 3);
add_filter('wp_update_term_data', 'aya_update_term_data_slug', 10, 4);
add_filter('wp_insert_post_data', 'aya_insert_post_data_slug', 10, 2);
add_filter('wp_unique_post_slug', 'aya_auto_unique_post_slug', 10, 6);
//批量刷新工具函数
//Tips: 用于批量刷新文章别名，取消下面这行的注释并打开任意页面一次，然后重新注释
//add_action('init', 'aya_post_all_slug_rewrite_update');

//添加分类时替换分类slug为拼音
function aya_insert_term_data_slug($data, $taxonomy, $term_arr)
{
    if (aya_opt('site_term_auto_pinyin_slug_bool', 'automatic')) {
        //已存在，跳过
        if (!empty($term_arr['slug'])) {
            return $data;
        }

        $pinyin_slug = sanitize_title(aya_pinyin_permalink($data['name'], true));

        $data['slug'] = wp_unique_term_slug($pinyin_slug, (object) $term_arr);
    }

    return $data;
}

//更新分类时替换分类slug为拼音
function aya_update_term_data_slug($data, $term_id, $taxonomy, $term_arr)
{
    if (aya_opt('site_term_auto_pinyin_slug_bool', 'automatic')) {
        //已存在，跳过
        if (!empty($term_arr['slug'])) {
            return $data;
        }

        $pinyin_slug = sanitize_title(aya_pinyin_permalink($data['name'], true));

        $data['slug'] = wp_unique_term_slug($pinyin_slug, (object) $term_arr);
    }

    return $data;
}

//保存文章时替换文章slug自定义格式
function aya_insert_post_data_slug($data, $post_arr)
{
    //跳过自动草稿
    if ('auto-draft' === $post_arr['post_status']) {
        return $data;
    }

    //处理拼音化别名
    if (aya_opt('site_post_auto_pinyin_slug_bool', 'automatic')) {
        //已存在，跳过
        if (!empty($post_arr['post_name'])) {
            return $data;
        }
        //检查标题是否为空
        if (empty($post_arr['post_title'])) {
            return $data;
        }

        //使用拼音生成别名
        $formatted_sulg = sanitize_title(aya_pinyin_permalink($post_arr['post_title'], true));
        //替换数据
        $data['post_name'] = wp_unique_term_slug($formatted_sulg, (object) $post_arr);
    }

    return $data;
}

//强制文章别名
function aya_auto_unique_post_slug($slug, $post_id, $post_status, $post_type, $post_parent)
{
    //只针对文章类型
    if ($post_type === 'post') {
        //添加文章时防止循环
        $num_id = absint($post_id);

        if ($num_id === 0) {
            return $slug;
        }

        //获取设置
        $slug_type = aya_opt('site_post_auto_slug_type', 'automatic');

        if ($slug_type !== 'off') {

            $formatted_slug = aya_auto_post_slug_format($post_id);

            if ($formatted_slug !== false) {
                return $formatted_slug;
            }
        }
    }

    return $slug;
}

//使用ID生成别名
function aya_auto_post_slug_format($post_id)
{
    //等于0时
    if ($post_id <= 0) {
        return false;
    }

    //获取设置
    $slug_type = aya_opt('site_post_auto_slug_type', 'automatic');
    //别名前缀
    $prefix = aya_opt('site_post_auto_slug_prefix', 'automatic');
    //处理输入只允许字母数字和结构无关的特殊字符
    $prefix = preg_replace('/[^a-zA-Z0-9\-._~:\/?#[\]@!$&\'()*+,;=]/u', '', $prefix);

    //低仿AV号
    if ($slug_type === 'id_av') {

        return $prefix . str_pad($post_id, 8, '0', STR_PAD_LEFT);
    }
    //低仿BV号
    else if ($slug_type === 'id_bv') {

        return $prefix . aya_token_encode($post_id, 8);
    }

    //未知参数
    return false;
}

//批量刷新文章别名
function aya_post_all_slug_rewrite_update()
{
    //获取所有文章
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'ID',
        'order' => 'ASC'
    );

    $posts = get_posts($args);

    $count = 0;
    $message = 'Applying posts slug batch update...';

    foreach ($posts as $post) {
        //使用指定格式或默认格式生成新别名
        $new_slug = aya_auto_post_slug_format($post->ID);

        //只有当返回值不是false时才更新别名
        if ($new_slug !== false) {
            //确保别名唯一性
            $new_slug = wp_unique_post_slug($new_slug, $post->ID, $post->post_status, $post->post_type, $post->post_parent);

            //更新文章别名
            $result = wp_update_post(array(
                'ID' => $post->ID,
                'post_name' => $new_slug
            ));

            if ($result) {
                $count++;
                $message .= 'POST_ID(' . $post->ID . ') -> ' . $new_slug . PHP_EOL;
            }
        }
    }

    return aya_print($message);
}
