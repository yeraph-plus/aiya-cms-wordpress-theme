<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 归档页组件
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
    return e_html($badge);
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
        return e_html($html_tags);
    }
    return '';
}
