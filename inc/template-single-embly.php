<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 文章组件
 * ------------------------------------------------------------------------------
 */

//生成作者面板
function aya_single_author_panel()
{
    if (!aya_opt('site_single_author_panel', 'format')) return;

    global $post;

    $user_id = $post->post_author;

    $html = '';
    $html .= '<div class="author-panel mx-2 my-4 d-flex flex-row align-items-center justify-content-start">';
    $html .= '<div class="author-image">' . get_avatar($user_id, 64) . '</div>';

    $html .= '<div class="author-meta ml-sm-auto">';
    $html .= '<a href="' . get_author_posts_url($user_id) . '">' . get_the_author_meta('display_name', $user_id) . '</a>';
    if (get_the_author_meta('description', $user_id) != '') {
        $html .= '<i class="bi bi-dot"></i>';
        $html .= '<span class="des">' . get_the_author_meta('description', $user_id) . '</span>';
    }
    $html .= '</div></div>';

    return e_html($html);
}
//生成上一篇&下一篇文章
function aya_single_prev_next_post()
{
    if (!aya_opt('site_single_prev_next_post', 'format')) return;

    $prev_post = get_previous_post();
    $next_post = get_next_post();
    //如果文章为空
    if (empty($prev_post) || empty($next_post)) {
        $width = 'style="width: 100%;"';
    } else {
        $width = 'style="width: 50%;"';
    }
    $html = '';
    $html .= '<div class="post-prev-next">';

    if (!empty($prev_post)) {
        $html .= '<div class="pd-in-card mb-2 mt-2" ' . $width . '><div class="card" style="background: url(' . aya_the_loop_image($prev_post->ID, 400, 300) . ');"><div class="prev-post">';
        $html .= '<p><i class="bi bi-chevron-double-left"></i> ' . __('上一篇', 'AIYA') . '</p>';
        $html .= '<a class="stretched-link" href="' . aya_get_post_url($prev_post) . '" title="' . aya_get_post_title($prev_post) . '" rel="next">' . aya_get_post_title($prev_post) . '</a>';
        $html .= '</div></div></div>';
    }
    if (!empty($next_post)) {
        $html .= '<div class="pd-in-card mb-2 mt-2" ' . $width . '><div class="card" style="background: url(' . aya_the_loop_image($next_post, 400, 300) . ');"><div class="next-post">';
        $html .= '<p> ' . __('下一篇', 'AIYA') . ' <i class="bi bi-chevron-double-right"></i></p>';
        $html .= '<a class="stretched-link" href="' . aya_get_post_url($next_post) . '" title="' . aya_get_post_title($next_post) . '" rel="next">' . aya_get_post_title($next_post) . '</a>';
        $html .= '</div></div></div>';
    }

    $html .= '</div>';

    return e_html($html);
}
//生成文章过时提示
function aya_single_outdated_tip()
{
    if (!aya_opt('site_single_outdated_tip', 'format')) return;

    global $post;

    $verify_post = aya_verify_post_is_outdated($post, 30);

    if ($verify_post) {
        return e_html('<blockquote class="post-outdated-tip">' . __('这篇文章的发布时间已经超过 30 天，部分信息可能已过时。', 'AIYA') . '</blockquote>');
    }
    return '';
}
//生成文章点赞组件
function aya_single_specs_like()
{
    //TODO
    return '';

    global $post;

    //判断是否已点赞
    $zan = '';
    if (isset($_COOKIE['specs_zan_' . $post->ID])) {
        $zan = 'done';
    }

    $html = '';
    $html .= '<div class="post-specslike text-center">';
    $html .= '<a href="javascript:;" data-action="ding" data-id="' . $post->ID . '" class="specsZan ' . $zan . '"><i class="bi bi-hand-thumbs-up-fill"></i>' . $zan . '</a>';
    $html .= '</div>';

    return e_html($html);
}
//生成相关文章组件
function aya_single_related_more()
{
    //TODO
    return '';
    //获取主题设置
    if (!aya_opt('site_single_related_post', 'format') == false) return;

    //$post_type = get_post_type();

    //获取相关文章
    //$related_post = aya_get_related_post($post_type);

    //如果文章为空
    if (empty($related_post)) {
        //return;
    }

    $html = '';
    $html .= '<div class="related-post mx-2 my-4"><h3 class="related-post-title">相关文章</h3><div class="related-post-list">';

    foreach ($related_post as $post) {
        $html .= '<li class="related-post-item">';
        $html .= '<a href="' . aya_get_post_url($post->ID) . '" title="' . aya_get_post_title($post->ID) . '">' . aya_get_post_title($post->ID) . '</a>';
        $html .= '</li>';
    }
    $html .= '</div></div>';

    e_html($html);
}
//获取文章补充信息
function aya_single_dis_claimer_info()
{
    //获取主题设置
    $dis_text = aya_opt('site_single_dis_claimer', 'format');

    if ($dis_text === '') return;

    e_html('<div class="post-info text-center">' . $dis_text . '</div>');
}
