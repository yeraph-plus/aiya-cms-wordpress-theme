<?php
if (!defined('ABSPATH')) exit;

//AIYA-CMS 短代码组件：下载提示框

function copy_board_box_shortcode($atts = array(), $content = '')
{
    //定义简码参数
    $content = strip_tags($content);

    $atts = shortcode_atts(
        array(
            'title' => '',
            'icon' => '',
            'href_link' => false,
        ),
        $atts
    );

    switch($atts['icon']){
        case 'magnet':
            $icon = '<i data-feather="globe" width="20" height="20" stroke-width="2"></i>';
            break;
        case 'file':
            $icon = '<i data-feather="folder" width="20" height="20" stroke-width="2"></i>';
            break;
        default:
            $icon = '<i data-feather="link" width="20" height="20" stroke-width="2"></i>';
    }

    //开始输出组件

    $html = '';
    $html .= '<div class="bg-[#f1f2f3] p-5 rounded dark:bg-[#060818]"><form>';
    $html .= '<h5 class="mb-3 text-lg font-semibold">';
    $html .= $atts['title'] . '</h5>';
    $html .= '<p class="mb-3 font-semibold"><span id="copyLink">' . $content . '</span></p>';
    $html .= '<button type="button" class="btn btn-primary " data-clipboard-target="#copyLink">';
    $html .= '</form></div>';

    return $html;
}
add_shortcode('copy_board', 'copy_board_box_shortcode');
?>
<div class="bg-[#f1f2f3] p-5 rounded dark:bg-[#060818]">
    <form>
        <p class="mb-3 font-semibold"> <span> Link -> </span> <span id="copyLink"> http://www.admin-dashboard.com/code</span></p>
        <span class="absolute opacity-0" id="copyHiddenCode">2291</span>
        <div class="flex flex-wrap gap-2 mt-5">
            <button type="button" class="btn btn-primary " data-clipboard-target="#copyLink">
                <svg> ... </svg> Copy Link
            </button>
            <button type="button" class="btn btn-dark " data-clipboard-target="#copyHiddenCode">
                <svg> ... </svg> Copy Hidden Code
            </button>
        </div>
    </form>
</div>