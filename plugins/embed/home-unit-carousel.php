<?php
if (!defined('ABSPATH')) exit;
/*
 * ------------------------------------------------------------------------------
 * 轮播和分类组件
 * ------------------------------------------------------------------------------
 */

//轮播：生成组件
function index_banner_section()
{
    //获取主题设置
    $mode_option = aya_option('banner_mode');
    if ($mode_option == '0') return;
    //检查列表是否为空
    $post_array = banner_add_posts();
    if ($post_array == false) return;
    //检查移动端和设置
    if (!wp_is_mobile() && $mode_option == '1' && count($post_array) > 4) {
        get_template_part('template-parts/section', 'banner-cms');
    } else {
        get_template_part('template-parts/section', 'banner-default');
    }
}
add_action('aya_index_banner', 'index_banner_section');

//轮播：生成指示器
function banner_indicators($array = array())
{
    //生成Bootstrap格式的指示器
    $i = 0;
    $html = '';
    //循环
    foreach ($array as $key => $value) {
        $active = ($i == 0) ? 'active' : '';
        $html .= '<button type="button" data-bs-target="#carousel-banner" data-bs-slide-to="' . $key . '"aria-label="Slide ' . $key . '" class="' . $active . '"></button>';
        $i++;
    }
    echo $html;
}

//轮播：生成内容
function banner_inner($array = array())
{
    //生成Bootstrap格式的DIV
    $i = 0;
    $html = '';
    foreach ($array as $key => $value) {
        $active = ($i == 0) ? 'active' : '';
        $html .= '<div class="banner-item carousel-item ' . $active . '">';
        $html .= '<a class="stretched-link" href="' . $value['url'] . '">';
        $html .= '<div class="banner-thumb"><img loading="lazy" src="' . $value['img'] . '"></div>';
        $html .= '<div class="banner-title"><h5>' . $value['title'] . '</h5><p>' . $value['des'] . '</p></div>';
        if (!empty($value['tag'])) {
            $html .= '<em>' . $value['tag'] . '</em>';
        }
        $html .= '</a></div>';
        $i++;
    }
    echo $html;
}

//轮播：生成内容
function banner_outer($array = array())
{
    //生成DIV
    $html = '';
    foreach ($array as $key => $value) {
        $html .= '<div class="banner-card card">';
        $html .= '<a class=" stretched-link" href="' . $value['url'] . '">';
        $html .= '<div class="banner-thumb"><img loading="lazy" src="' . $value['img'] . '"></div>';
        $html .= '<div class="banner-title"><h5>' . $value['title'] . '</h5><p>' . $value['des'] . '</p></div>';
        if (!empty($value['tag'])) {
            $html .= '<em>' . $value['tag'] . '</em>';
        }
        $html .= '</a></div>';
    }
    echo $html;
}

//轮播：设置轮播列表数量
function banner_add_posts()
{
    //获取轮播列表
    $banner_array = banner_add_loop_post();
    //获取置顶列表
    if (aya_option('banner_sticky_post') == true) {
        $sticky_array = banner_add_sticky_post();
        //合并数组
        if (!empty($sticky_array) && !empty($banner_array)) {
            $banner_array = array_merge($banner_array, $sticky_array);
        } elseif (empty($banner_array)) {
            $banner_array = $sticky_array;
        }
    }
    //检查数量
    $banner_num = intval(aya_option('banner_loop_number'));
    if ($banner_num != 0) {
        $banner_array = array_slice($banner_array, 0, $banner_num);
    }
    return $banner_array;
}

//轮播：生成自定义的文章列表
function banner_cms_posts($type = 0, $num = 3)
{
    //获取列表
    $post_array = banner_add_posts();
    $count = count($post_array);
    //拆分数组
    if ($type == 0) {
        //计算
        $the_count = $count - $num;
        return array_slice($post_array, 0, $the_count);
    } else {
        //计算
        $type = $count - $type;
        return array_slice($post_array, $type, $num);
    }
}

//轮播：处理轮播列表
function banner_add_loop_post()
{
    //获取主题设置
    $banner_loop = aya_option('banner_loop');
    //为空直接返回
    if (!is_array($banner_loop) || empty($banner_loop)) return false;
    //处理列表数据
    foreach ($banner_loop as $key => $value) {
        //处理数组
        if (is_numeric($value['url'])) {
            //检查文章ID
            if (get_permalink($value['url']) == false) continue;
            $post_id = $value['url'];
            $value['url'] = get_permalink($post_id);
            $value['title'] = empty($value['title']) ? get_post_title($post_id) : $value['title'];
            $value['tag'] = empty($value['tag']) ? '' : $value['tag'];
            $value['img'] = empty($value['img']) ? get_post_thumb($post_id, 700, 400) : $value['img'];
        }
        //处理数组
        $value['title'] = empty($value['title']) ? $value['url'] : $value['title'];
        $value['tag'] = empty($value['tag']) ? '' : $value['tag'];
        $value['img'] = empty($value['img']) ? get_bfi_thumb(aya_option('nopic'), 700, 400) : get_bfi_thumb($value['img'], 700, 400);
        //返回
        $banner_array[$key] = array(
            'url' => $value['url'],
            'title' => $value['title'],
            'des' => $value['des'],
            'tag' => $value['tag'],
            'img' => $value['img'],
        );
    }
    return $banner_array;
}

//轮播：置顶文章加入轮播
function banner_add_sticky_post()
{
    //获取置顶文章列表
    $sticky_option = get_option('sticky_posts');
    //为空直接返回
    if (!is_array($sticky_option) || empty($sticky_option)) return false;
    //创建查询
    $sticky_array = array();
    $len_post = count($sticky_option);
    $args = array(
        'post__in' => $sticky_option,
        'post_type' => 'post',
        'ignore_sticky_posts' => 1,
    );
    $the_query = new WP_Query($args);
    //计数
    $i = 0;
    //获取文章信息
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) : $the_query->the_post();
            if ($i > $len_post) break;
            //生成新的数组
            $sticky_array[$i] = array(
                'url' => get_permalink(),
                'title' => get_post_title(),
                'des' => '',
                'tag' => '置顶推荐',
                'img' => get_post_thumb(0, 700, 400),
            );
            $i++;
        endwhile;
        wp_reset_query(); //重置Query查询
    }
    return $sticky_array;
}
