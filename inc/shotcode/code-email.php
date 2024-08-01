<?php
if (!defined('ABSPATH')) exit;

//AIYA-CMS 短代码组件：EMAIL字符转换

function email_shortcode($atts = array(), $content = null)
{
    $atts = shortcode_atts(
        array(
            'to' => '10086@189.cn',
        ),
        $atts,
    );
    $content = wp_kses($content, 'post');

    return antispambot($content, $atts['to']);
}

add_shortcode('email', 'email_shortcode');
