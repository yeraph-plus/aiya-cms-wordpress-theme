<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * footrer组件
 * ------------------------------------------------------------------------------
 */

//页脚悬浮按钮
function aya_footer_scroll_button()
{
    $html = '<div class="scroll-button scroll-default-hide">';
    if (aya_page_type('home')) {
        $html .= '<button class="button" onclick="switchDarkMode()" data-bs-toggle="tooltip" data-bs-placement="left" title="开灯 / 关灯"><i class="bi bi-lightbulb-fill"></i></button>';
    }
    if (!aya_page_type('home')) {
        $html .= '<button class="button" onclick="backButton()" data-bs-toggle="tooltip" data-bs-placement="left" title="返回上一页"><i class="bi bi-arrow-return-left"></i></button>';
    }
    if (aya_page_type('single')) {
        $html .= '<button class="button" onclick="switchTo(\'respond\')" data-bs-toggle="tooltip" data-bs-placement="left" title="评论"><i class="bi bi-chat-text"></i></button>';
    }
    $html .= '<button class="button" onclick="switchToTop()" data-bs-toggle="tooltip" data-bs-placement="left" title="返回顶部"><i class="bi bi-chevron-up"></i></button>';
    $html .= '</div>';

    e_html($html);
}
//底部栏菜单切换
function aya_footer_menu_toggle()
{
    return aya_nav_menu('footer-menu', 'navbar-nav me-auto mb-0 ms-xl-0', 2);
}
//页脚备案号
function aya_footer_beian()
{
    //获取主题设置
    $icp_beian = aya_opt('site_icp_beian', 'theme');
    $mps_beian = aya_opt('site_mps_beian', 'theme');

    $html = '';

    if ($icp_beian !== '') {
        $html .= '<a class="beian" href="https://beian.miit.gov.cn/" rel="noopener noreferrer" target="_blank"><i class="bi bi-shield-check me-1"></i>' . $icp_beian . '</a><br />';
    }

    if ($mps_beian !== '') {
        //$number_of = preg_match('/\d+/', $mps_beian, $number_of);
        //$html .= '<a class="beian" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $number_of . '" rel="noopener noreferrer" target="_blank">' . $mps_beian . '</a>';
        $html .= '<a class="beian" href="http://www.beian.gov.cn/portal/registerSystemInfo" rel="noopener noreferrer" target="_blank">' . $mps_beian . '</a><br />';
    }

    e_html($html);
}
//SQL计数器
function aya_sql_counter()
{
    $load_time = sprintf('SQL查询 %d 次 / 耗时 %.3f 秒 / 内存占用 %.2fMB', get_num_queries(), timer_stop(0, 3), memory_get_peak_usage() / 1024 / 1024);

    e_html('<span class="load">' . $load_time . '</span>');
}
