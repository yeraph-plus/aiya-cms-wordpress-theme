<?php
if (!defined('ABSPATH')) exit;


//to be continued


add_shortcode('aplayer', 'aplayer_shortcode');
add_shortcode('aplayer_trac', 'aplayer_trac_shortcode');
add_shortcode('meting', 'meting_shortcode');

//AIYA-CMS 短代码组件：APlayer
function aplayer_shortcode($atts = array(), $content = '')
{
    if (!is_singular() && !is_admin()) return;
    //记录函数加载次数
    static $instances = 0;
    $instances++;

    $atts = shortcode_atts(
        array(
            'autoplay' => 'false',
            'theme' => '#ebd0c2',
            'loop' => 'all',
            'order' => 'list',
            'm3u8' => false,
        ),
        $atts,
        'aplayer_shortcode'
    );

    $atts['autoplay'] = wp_validate_boolean($atts['autoplay']);
    $atts['m3u8'] = wp_validate_boolean($atts['m3u8']);
    $atts['theme'] = esc_attr($atts['theme']);
    $atts['loop'] = esc_attr($atts['loop']);
    $atts['order'] = esc_attr($atts['order']);

    //获取媒体信息
    $content = str_replace(PHP_EOL, '', strip_tags(nl2br(apply_shortcodes($content))));

    if (empty($content)) return;

    /**
     * APlayer配置如下
     * 
     * <script>
     * const ap%u = new APlayer({element: document.getElementById("aplayer-%u"),
     * 	autoplay: %b,
     * 	theme: "%s",
     * 	loop: "%s",
     * 	order: "%s",
     * 	preload: "auto",
     * 	mutex: true,
     * 	volume: 0.7,
     *  listFolded: false,
     * 	listMaxHeight: 90,
     * 	lrcType: 1,
     * 	audio: [
     * 	%s
     * 	],
     * });
     * </script>
     **/

    $output = sprintf(
        '<script>const ap = new APlayer({element: document.getElementById("aplayer-%u"), autoplay: %b, theme: "%s", loop: "%s", order: "%s", preload: "auto", mutex: true, volume: 0.7, listFolded: false, listMaxHeight: 90, lrcType: 1, audio: [%s]});</script>',
        $instances,
        $atts['autoplay'],
        $atts['theme'],
        $atts['loop'],
        $atts['order'],
        $content
    );

    //载入APlayer.js
    if ($instances = 1) {
        wp_enqueue_script('aplayer');
        wp_enqueue_style('aplayer');
    }
    //载入hls.js
    if ($atts['m3u8'] !== false) {
        wp_enqueue_script('hls-light');
    }
    //输出播放器配置
    add_action('wp_footer', function () use ($output) {
        echo $output . '\n';
    }, 999);
    //输出HTML
    $html = '<div id="aplayer-' . $instances . '" class="aplayer"></div>';
    return $html;
}
//AIYA-CMS 短代码组件：APlayer曲目
function aplayer_trac_shortcode($atts = array())
{
    $atts = shortcode_atts(
        array(
            'name' => '未知曲目',
            'artist' => '未知艺术家',
            'url' => '',
            'cover' => get_template_directory_uri() . '/image/music.png',
            'lrc' => '[00:00.000]此歌曲暂无歌词，请您欣赏',
            'type' => 'auto'
        ),
        $atts,
        'aplayer_trac_shortcode'
    );

    $atts['name'] = sanitize_text_field($atts['name']);
    $atts['artist'] = sanitize_text_field($atts['artist']);
    $atts['url'] = esc_url_raw($atts['url']);
    $atts['cover'] = esc_url_raw($atts['cover']);
    $atts['lrc'] = sanitize_text_field($atts['lrc']);
    $atts['type'] = sanitize_text_field($atts['type']);

    $output = sprintf(
        '{name: "%s", artist: "%s", url: "%s", cover: "%s", lrc: "%s", type: "%s"}',
        $atts['name'],
        $atts['artist'],
        $atts['url'],
        $atts['cover'],
        $atts['lrc'],
        $atts['type']
    );

    return $output . ',';
}
//AIYA-CMS 短代码组件：Meting
function meting_shortcode($atts = array())
{
    //记录函数加载次数
    static $instances = 0;
    $instances++;

    $atts = shortcode_atts(
        array(
            'auto' => 'https://music.163.com/#/playlist?id=60198',
            'server' => '',
            'type' => '',
            'id' => ''
        ),
        $atts,
    );

    $atts['auto'] = esc_url_raw($atts['auto']);
    $atts['server'] = esc_attr($atts['server']);
    $atts['type'] = esc_attr($atts['type']);
    $atts['id'] = esc_attr($atts['id']);

    //载入Meting.js
    if ($instances = 1) {
        wp_enqueue_script('aplayer');
        wp_enqueue_style('aplayer');
        wp_enqueue_script('meting');
    }
    //输出API配置
    //$output = '<script>var meting_api = "http://example.com/api.php?server=:server&type=:type&id=:id&auth=:auth&r=:r";</script>';
    //add_action('wp_footer', function() use($output){echo $output.'\n';}, 999);
    //输出HTML
    if ($atts['auto'] !== '') {
        $html = '<meting-js auto="' . $atts['auto'] . '"></meting-js>';
    } else {
        $html = '<meting-js server="' . $atts['server'] . '" type="' . $atts['type'] . '" id="' . $atts['id'] . '"></meting-js>';
    }
    return $html;
}
