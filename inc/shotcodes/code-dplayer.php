<?php

if (!defined('ABSPATH')) {
    exit;
}

//to be continued


//AIYA-CMS 短代码组件：DPlayer
function dplayer_shortcode($atts = array(), $content = '')
{
    if (!is_singular() && !is_admin()) return;
    //记录函数加载次数
    static $instances = 0;
    $instances++;

    $atts = shortcode_atts(
        array(
            'autoplay'  => 'false',
            'theme' => '#FADFA3',
            'loop' => 'false',
            'mutex' => 'true',
            'm3u8' => false,
            'url' => '',
            'pic' => get_template_directory_uri() . '/image/video.png',
            'thumbnails' => '',
            'type' => 'auto'
        ),
        $atts,
        'dplayer_shortcode'
    );

    $atts['autoplay'] = wp_validate_boolean($atts['autoplay']);
    $atts['m3u8'] = wp_validate_boolean($atts['m3u8']);
    $atts['mutex'] = wp_validate_boolean($atts['mutex']);
    $atts['theme'] = esc_attr($atts['theme']);
    $atts['loop'] = esc_attr($atts['loop']);
    $atts['url'] = esc_url_raw($atts['url']);
    $atts['pic'] = esc_url_raw($atts['pic']);
    $atts['thumbnails'] = esc_url_raw($atts['thumbnails']);
    $atts['type'] = sanitize_text_field($atts['type']);

    /**
     * DPlayer配置如下
     * 
     * <script>
     * const dp %u = new DPlayer({container: document.getElementById("dplayer-%u"),
     * 	autoplay: %b,
     * 	theme: "%s",
     * 	loop: %s,
     *  screenshot: false,
     *  hotkey: true,
     * 	preload: "auto",
     *  volume: 0.7,
     * 	mutex: %s,
     * 	video: {
     * 	    url: "%s",
     *      pic: "%s",
     *      type: "%s"
     *  },
     * });
     * </script>
     **/
    $output = sprintf(
        '<script>const dp = new DPlayer({container:document.getElementById("dplayer-%u"), autoplay: %b, theme: "%s", loop: %s, screenshot: false, hotkey: true, preload: "auto", volume: 0.7, mutex: %s, video: {url: "%s", pic: "%s", thumbnails: "%s", type: "%s"}});</script>',
        $instances,
        $atts['autoplay'],
        $atts['theme'],
        $atts['loop'],
        $atts['mutex'],
        $atts['url'],
        $atts['pic'],
        $atts['thumbnails'],
        $atts['type'],
    );

    //载入DPlayer.js
    if ($instances = 1) {
        wp_enqueue_script('dplayer');
    }
    //载入hls.js
    if ($atts['m3u8'] !== false) {
        wp_enqueue_script('hls-light');
    }
    //载入播放器配置
    add_action('wp_footer', function () use ($output) {
        echo $output . '\n';
    }, 999);
    //输出HTML
    $html = '<div id="dplayer-' . $instances . '"></div>';
    return $html;
}
add_shortcode('dplayer', 'dplayer_shortcode');

function dplayer_select_shortcode($atts = array())
{
    //记录函数加载次数
    static $instances = 0;
    $instances++;

    $atts = shortcode_atts(
        array(
            'url' => '',
            'pic' => '',
            'thumbnails' => '',
            'type' => 'auto'
        ),
        $atts,
        'dplayer_select_shortcode'
    );

    $atts['url'] = esc_url_raw($atts['url']);
    $atts['pic'] = esc_url_raw($atts['pic']);
    $atts['thumbnails'] = esc_url_raw($atts['thumbnails']);
    $atts['type'] = sanitize_text_field($atts['type']);

    $output = sprintf(
        '<script>function switch%u(){dp.switchVideo({url: "%s", pic: "%s", thumbnails: "%s", type: "%s"})}</script>',
        $instances,
        $atts['url'],
        $atts['pic'],
        $atts['thumbnails'],
        $atts['type'],
    );

    //输出播放器配置
    add_action('wp_footer', function () use ($output) {
        echo $output . '\n';
    }, 999);
    //输出HTML
    $html = '<a class="btn" href="javascript:;" onclick="switch' . $instances . '()">选集' . $instances . '</a>';
    return $html;
}
add_shortcode('dplayer_select', 'dplayer_select_shortcode');
