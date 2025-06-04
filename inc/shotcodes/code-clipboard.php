<?php

if (!defined('ABSPATH')) {
    exit;
}


add_shortcode('clip_board', 'clip_board_box_shortcode');

if (is_admin()) {
    AYA_Shortcode::shortcode_register('clipboard-box', array(
        'id'       => 'sc-clipboard-box',
        'title'    => '一键复制',
        'note'    => '创建一个提供复制功能的卡片，用于快捷复制文本或打开链接',
        'template' => '[clip_board {{attributes}}] {{content}} [/clip_board]',
        'field_build'   => array(
            [
                'id' => 'title',
                'type'  => 'text',
                'label' => '标题',
                'desc'  => '卡片标题，留空时隐藏',
                'default'   => '',
            ],
            [
                'id' => 'content',
                'type'  => 'textarea',
                'label' => '内容',
                'desc'  => '需要被复制的文本或链接',
                'default'   => 'magnet:?xt=urn:btih:',
            ],
            [
                'id' => 'is_link',
                'type'  => 'checkbox',
                'label' => '直接跳转按钮',
                'desc' => '如果被复制的内容是链接，增加直接打开链接的按钮',
                'default' => false,
            ]
        )
    ));
}
//AIYA-CMS 短代码组件：剪贴板功能卡片
function clip_board_box_shortcode($atts = array(), $content = '')
{
    //定义简码参数
    $atts = shortcode_atts(
        array(
            'title' => '',
            'is_link' => 'false',
        ),
        $atts
    );

    $content = esc_html(do_shortcode($content));
    $title = esc_html($atts['title']);

    //配置组件
    if ($atts['is_link'] == 'true') {
        $clip_icon = 'link';
        $ext_btn = '<button class="btn btn-primary" onclick="window.open(\'' . $content . '\', \'_blank\');">' . __('直接打开', 'AIYA') . '</button>';
    } else {
        $clip_icon = 'clipboard';
        $ext_btn = '';
    }
    //储存一个id用于多次调用
    static $clip_box = 0;
    $clip_box++;
    $clip_box_id = 'clip-' . $clip_box;

    if ($clip_box == 1) {
        //引入JS
        add_filter('aya_int_add_scripts', function ($strings) {
            return $strings . aya_int_script('clipboard.js', '2.0.11', 'clipboard.min.js', false);
        });
    }

    $html = '';
    $html .= '<div class="bg-[#f1f1f1] p-4 my-2 rounded dark:bg-[#060818]">';
    if (!empty($title)) {
        $html .= '<h5 class="mb-3 text-lg font-semibold flex items-center">' . aya_feather_icon($clip_icon, 16, 'mr-2') . $title . '</h5>';
    }
    $html .= '<div x-data="copyTextBtn(\'' . $clip_box_id . '\')">';
    $html .= '<p class="mb-2 font-semibold break-all"><span id="' . $clip_box_id . '">' . $content . '</span></p>';
    $html .= '<div class="mt-5 flex flex-wrap items-center gap-2">';
    $html .= '<button class="btn btn-primary" x-ref="copyBtn">' . __('复制', 'AIYA') . '</button>' . $ext_btn;
    $html .= '<p class="text-base font-bold my-3" x-show="isCopied">' . __('已复制！', 'AIYA') . '</p>';
    $html .= '</div></div>';
    $html .= '</div>';

    return htmlspecialchars_decode($html);
}
