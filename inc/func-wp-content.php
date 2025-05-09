<?php

if (!defined('ABSPATH'))
    exit;

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

//文章缩略图处理
function aya_post_thumb($have_thumb_url = false, $post_content = '', $size_w = 400, $size_h = 300)
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

    if (!$the_query)
        return false;

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
