<?php
if (!defined('ABSPATH')) exit;
/*
 * ------------------------------------------------------------------------------
 * 文章组件
 * ------------------------------------------------------------------------------
 */

//LOOP分区标题
function aya_loop_section_title($title_text = '', $more_url = '')
{
    if ($title_text == '') return;
    //插入链接
    if ($more_url !== '') {
        $more_url = '<a href="' . $more_url . '">' . __('MORE', 'AIYA') . ' <i class="bi bi-chevron-right"></i></a>';
    }

    e_html('<div class="loop-section-title mx-2 my-4 d-flex flex-row align-items-center justify-content-start"><h3>' . $title_text . '</h3>' . $more_url . '</div>');
}
//生成文章标题徽章标记
function aya_loop_title_badge($post_id = 0)
{
    $badge = '';

    //检查置顶
    if (is_sticky($post_id)) {
        $badge .= '<span class="badge me-2 badge-sticky"><i class="bi bi-pin-angle"></i> 置顶</span>';
    }
    //检查密码保护
    if (post_password_required($post_id)) {
        $badge .= '<span class="badge me-2 badge-password"><i class="bi bi-lock"></i> 密码保护</span>';
    }
    //检查私密文章
    if (get_post_status($post_id) == 'private') {
        $badge .= '<span class="badge me-2 badge-private"><i class="bi bi-eye-slash"></i> 私密</span>';
    }
    //检查最新文章
    if (aya_opt('site_loop_new_badge', 'layout')) {
        $post_date = get_the_date('U', $post_id);
        if (date('U') - $post_date < 86400) {
            $badge .= '<span class="badge me-2 badge-new"><i class="bi bi-send"></i> 最新</span>';
        }
    }
    //输出
    return $badge;
}
//生成文章标签列表
function aya_loop_tags_list($post_id = 0)
{
    $html_tags = '';
    //获取分类
    $html_tags .= '<em class="cat">' . get_the_category_list(', ', '', $post_id) . '</em>';
    //获取标签
    $html_tags .= get_the_tag_list('<em>', '</em><em>', '</em>');
    //省去判断文章类型是否支持标签，直接检查WP是否报错
    if (!is_wp_error($html_tags)) {
        return $html_tags;
    }
    return '';
}

//生成作者面板
function aya_single_author_panel()
{
    global $post;

    $user_id = $post->post_author;

    $html = '';
    $html .= '<div class="author-panel mx-2 my-4 d-flex flex-row align-items-center justify-content-start">';
    $html .= '<div class="author-image">' . get_avatar($user_id, 64) . '</div>';

    $html .= '<div class="author-meta ml-sm-auto">';
    $html .= '<a href="' . get_author_posts_url($user_id) . '">' . get_the_author_meta('display_name', $user_id) . '</a>';
    $html .= '<i class="bi bi-dot"></i>';
    $html .= '<span class="des">' . get_the_author_meta('description', $user_id) . '</span>';
    $html .= '</div></div>';

    e_html($html);
}
//生成上一篇&下一篇文章
function aya_single_prev_next_post()
{
    //获取主题设置
    if (aya_opt('site_prev_next_post', 'single')) return;

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
        $html .= '<div class="pd-in-card mb-2 mt-2" ' . $width . '><div class="card" style="background: url(' . aya_get_post_thumb($prev_post->ID, 400, 300) . ');"><div class="prev-post">';
        $html .= '<p><i class="bi bi-chevron-double-left"></i> 上一篇</p>';
        $html .= '<a class="stretched-link" href="' . aya_get_post_url($prev_post->ID) . '" title="' . aya_get_post_title($prev_post->ID) . '" rel="next">' . aya_get_post_title($prev_post->ID) . '</a>';
        $html .= '</div></div></div>';
    }
    if (!empty($next_post)) {
        $html .= '<div class="pd-in-card mb-2 mt-2" ' . $width . '><div class="card" style="background: url(' . aya_get_post_thumb($next_post->ID, 400, 300) . ');"><div class="next-post">';
        $html .= '<p> 下一篇 <i class="bi bi-chevron-double-right"></i></p>';
        $html .= '<a class="stretched-link" href="' . aya_get_post_url($next_post->ID) . '" title="' . aya_get_post_title($next_post->ID) . '" rel="next">' . aya_get_post_title($next_post->ID) . '</a>';
        $html .= '</div></div></div>';
    }

    $html .= '</div>';

    e_html($html);
}
//生成文章点赞组件
function aya_single_specs_like()
{
    //获取主题设置
    return;
    if (aya_opt('site_belike_post', 'single') == false) return;

    global $post;
    //判断是否已点赞
    if (isset($_COOKIE['specs_zan_' . $post->ID])) {
        $zan = 'done';
    }
    $html = '';
    $html .= '<div class="post-specslike text-center">';
    $html .= '<a href="javascript:;" data-action="ding" data-id="' . $post->ID . '" class="specsZan ' . $zan . '"><i class="bi bi-hand-thumbs-up-fill"></i>' . $zan . '</a>';
    $html .= '</div>';

    e_html($html);
}
//生成相关文章组件
function aya_single_related_more()
{
    //获取主题设置
    if (aya_opt('site_related_post', 'single') == false) return;
    return;

    //$post_type = get_post_type();

    //获取相关文章
    //$related_post = aya_get_related_post($post_type);

    //如果文章为空
    if (empty($related_post)) {
        //return;
    }

    $html = '';
    $html .= '<div class="related-post mx-2 my-4">';
    $html .= '<h3 class="related-post-title">相关文章</h3>';
    $html .= '<div class="related-post-list">';
    foreach ($related_post as $post) {
        $html .= '<div class="related-post-item">';
        $html .= '<a href="' . aya_get_post_url($post->ID) . '" title="' . aya_get_post_title($post->ID) . '">' . aya_get_post_title($post->ID) . '</a>';
        $html .= '</div>';
    }
    $html .= '</div></div>';

    e_html($html);
}
//获取文章补充信息
function aya_get_single_info()
{
    //获取主题设置
    $text = aya_opt('site_single_info', 'single');

    if ($text == '') return;

    e_html('<div class="post-info">' . $text . '</div>');
}
