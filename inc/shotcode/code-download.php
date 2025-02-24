<?php
if (!defined('ABSPATH')) exit;

//AIYA-CMS 短代码组件：下载提示框

function download_box_shortcode($atts = array(), $content = '')
{
    //定义简码参数
    $atts = shortcode_atts(array(
        'file_name' => '',
        'file_size' => '',
        'file_des' => '',
        'btn_name' => '',
        'btn_link'  => '',
        'od_link' => '',
        'magnet_xt' => false,
        'copy_link' => false,
    ), $atts, 'download_box_shortcode');
    $html = '';
    //开始输出组件
    $html .= '<div class="tips-box down-box">';
    if ($atts['file_name'] !== '') {
        $html .= '<b>' . $atts['file_name'] . '</b><br /><p>' . $atts['file_size'] . ' / ' . $atts['file_des'] . '</p>';
    }
    $html .= '<div class="down-concent">' . $content . '</div>';
    $html .= '<div class="down-btn">';
    //输出自定义按钮
    if ($atts['btn_link'] !== '') {
        $have_name = ($atts['btn_name'] !== '') ? $atts['btn_name'] : '打开链接';
        $html .= '<a class="btn" href="' . $atts['btn_link'] . '"><i class="bi bi-link"></i> ' . $have_name . '</a>';
    }
    //输出OD盘
    if ($atts['od_link'] !== '') {
        $html .= '<a class="btn" href="' . $atts['od_link'] . '"><i class="bi bi-box"></i> OneDrive</a>';
    }
    //输出磁力链接
    if ($atts['magnet_xt'] !== false) {
        $html .= '<a class="btn" href="' . strip_tags($content) . '"><i class="bi bi-magnet"></i> Magnet</a>';
    }
    //输出复制按钮
    if ($atts['copy_link'] !== false) {
        //$html .= '<input id="down-this-clip" type="hidden" value="' . str_replace('<br />', '', $content) . '" />';
        //$html .= '<button id="down-clip" class="btn"><i class="bi bi-clipboard"></i> 复制代码 </button>';
    }
    $html .= '</div></div>';

    return $html;
}
add_shortcode('down_board', 'download_box_shortcode');
