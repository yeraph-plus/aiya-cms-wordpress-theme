<?php

if (!defined('ABSPATH')) {
    exit;
}

if (is_admin()) {
    AYA_Shortcode::shortcode_register('bili-iframe', array(
        'id' => 'sc-bili-card',
        'title' => '嵌入B站视频',
        'note' => '嵌入B站的H5播放器到页面',
        'template' => '[bili_iframe {{attributes}} /]',
        'field_build' => array(
            [
                'id' => 'bvid',
                'type' => 'text',
                'label' => 'BV号',
                'desc' => 'BV号，例如BV1UT42167xb',
                'default' => '',
            ],
            [
                'id' => 'h5_player',
                'type' => 'checkbox',
                'label' => '使用 HTML5 播放器',
                'desc' => '切换使用B站标准的外链播放器或者移动端HTML5播放器',
                'default' => true,
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('afdian-iframe', array(
        'id' => 'sc-afdian-card',
        'title' => '嵌入爱发电主页',
        'note' => '嵌入爱发电的主页或按钮到页面',
        'template' => '[afdian_iframe {{attributes}} /]',
        'field_build' => array(
            [
                'id' => 'slug',
                'type' => 'text',
                'label' => '后缀',
                'desc' => '创作者个人主页后缀，留空时使用系统设置',
                'default' => '',
            ],
            [
                'id' => 'type_btn',
                'type' => 'checkbox',
                'label' => '使用按钮链接',
                'desc' => '使当前段落作为 html 注释输出到前台页面，或者完全不输出',
                'default' => false,
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('github-iframe', array(
        'id' => 'sc-github-repo',
        'title' => '嵌入GitHub仓库',
        'note' => '嵌入GitHub仓库卡片到页面（填写“用户名/仓库名”）',
        'template' => '[github_card {{attributes}} /]',
        'field_build' => array(
            [
                'id' => 'repo',
                'type' => 'text',
                'label' => '<user>/<repo>',
                'desc' => '',
                'default' => '',
            ],
        )
    ));

}

add_shortcode('bili_iframe', 'aya_shortcode_bilibili_iframe');
add_shortcode('afdian_iframe', 'aya_shortcode_afdian_iframe');
add_shortcode('github_card', 'aya_shortcode_github_repo_iframe');

//AIYA-CMS 短代码组件：Iframe嵌入页面
function aya_shortcode_bilibili_iframe($atts)
{
    $atts = shortcode_atts(
        array(
            'bvid' => '',
            'h5_player' => 'false',
        ),
        $atts
    );

    if (empty($atts['bvid'])) {
        return '';
    }

    if (filter_var($atts['h5_player'], FILTER_VALIDATE_BOOLEAN)) {
        $src = '//www.bilibili.com/blackboard/html5mobileplayer.html?isOutside=true&bvid=' . $atts['bvid'];
    } else {
        $src = '//player.bilibili.com/player.html?isOutside=true&bvid=' . $atts['bvid'];
    }

    $html = '';

    $html .= '<div class="iframe-container">';
    $html .= '<iframe src="' . $src . '" width="640" height="360" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true" loading="lazy">';
    $html .= '<p class="text-center py-8 text-gray-500">No specified.</p>';
    $html .= '</iframe>';
    $html .= '</div>';

    return $html;
}

//AIYA-CMS 短代码组件：爱发电卡片
function aya_shortcode_afdian_iframe($atts)
{
    $atts = shortcode_atts(
        array(
            'slug' => '',
            'type_btn' => 'false',
        ),
        $atts
    );

    $afdian_slug = (empty($atts['slug'])) ? aya_opt('stie_afdian_homepage_text', 'access') : $atts['slug'];

    if (empty($afdian_slug)) {
        return '';
    }

    $html = '';

    if (filter_var($atts['type_btn'], FILTER_VALIDATE_BOOLEAN)) {
        $src = aya_get_afdian_link() . $afdian_slug;

        $html .= '<a href="' . $src . '" target="_blank">';
        $html .= '<img width="200" src="https://pic1.afdiancdn.com/static/img/welcome/button-sponsorme.png" alt="">';
        $html .= '</a>';
    } else {
        $src = aya_get_afdian_link() . 'leaflet?slug=' . $afdian_slug;

        $html .= '<div class="iframe-container">';
        $html .= '<iframe src="' . $src . '" width="640" height="200" scrolling="no" frameborder="0" loading="lazy">';
        $html .= '<p class="text-center py-8 text-gray-500">No specified.</p>';
        $html .= '</iframe>';
        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}

//AIYA-CMS 短代码组件：GitHub仓库卡片
function aya_shortcode_github_repo_iframe($atts)
{
    $atts = shortcode_atts(
        array(
            'repo' => '',
        ),
        $atts
    );

    if (empty($atts['repo'])) {
        return '';
    }

    $card_src = 'https://gh-card.dev/repos/' . esc_html($atts['repo']) . '.svg';

    $html = '';

    $html .= '<div class="iframe-container">';
    $html .= '<iframe src="' . $card_src . '" loading="lazy">';
    $html .= '<p class="text-center py-8 text-gray-500">No specified.</p>';
    $html .= '</iframe>';
    $html .= '</div>';

    return $html;

}