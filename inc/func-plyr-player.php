<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 *  Plyr 播放器 短代码组件
 * ------------------------------------------------------------------------------
 */

if (is_admin()) {
    AYA_Shortcode::shortcode_register('plyr-client', [
        'id' => 'sc-plyr-client',
        'title' => 'Plyr 播放列表',
        'note' => '音频 / 视频模板时 Plyr 播放器的播放列表',
        'template' => '[plyr_cli {{attributes}} /]',
        'field_build' => [
            [
                'id' => 'title',
                'type' => 'text',
                'label' => '标题',
                'desc' => '视频标题',
                'default' => '',
            ],
            [
                'id' => 'src',
                'type' => 'text',
                'label' => '视频源',
                'desc' => '视频文件 URL',
                'default' => '',
            ],
            [
                'id' => 'poster',
                'type' => 'text',
                'label' => '封面',
                'desc' => '视频封面图片 URL',
                'default' => '',
            ],
            [
                'id' => 'type',
                'type' => 'select',
                'label' => '视频类型',
                'desc' => '选择视频类型',
                'sub' => [
                    'auto' => '自动',
                    'hls' => 'HLS 流',
                ],
                'default' => 'auto',
            ],
        ]
    ]);
}

//短代码功能
add_shortcode('plyr_cli', 'aya_plyr_shortcodes_playlist_methods');

$GLOBALS['aya_plyr_playlist'] = [];

function aya_plyr_playlist_set_props($item)
{
    if (!is_array($item))
        return;

    $GLOBALS['aya_plyr_playlist'][] = $item;
}

function aya_plyr_playlist_get_props()
{
    return $GLOBALS['aya_plyr_playlist'] ?? [];
}

// AIYA-CMS 短代码组件：Plyr Player
function aya_plyr_shortcodes_playlist_methods($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'src' => '',
            'poster' => '',
            'title' => __('无标题', 'aiya-cms'),
            'type' => 'auto',
        ),
        $atts,
    );

    if (empty($atts['src']))
        return '';

    $is_hls = (strpos($atts['src'], '.m3u8') !== false) || $atts['type'] === 'hls';

    $source = [
        'type' => 'video',
        'title' => $atts['title'],
        'sources' => [
            [
                'src' => esc_url_raw($atts['src']),
                'provider' => 'html5',
                'type' => $is_hls ? 'application/x-mpegURL' : 'video/mp4'
            ]
        ],
        'poster' => esc_url_raw($atts['poster'])
    ];

    aya_plyr_playlist_set_props($source);

    return '';
}
