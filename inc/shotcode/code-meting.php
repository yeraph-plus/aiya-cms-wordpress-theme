<?php
if (!defined('ABSPATH')) exit;

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

add_shortcode('meting', 'meting_shortcode');
