<?php
if (!defined('ABSPATH')) exit;

//to be continued

add_shortcode('collapse', 'aya_shortcode_collapse_content');

//AIYA-CMS 短代码：折叠框
function aya_shortcode_collapse_tabs($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'style' => 'color-solid',
            'title' => __('展开'),
        ),
        $atts,
    );

    $id = 'collapse-' . uniqid();
    $content = stripslashes($content);

    $html = '';
    $html .= '<button class="btn ' . sanitize_html_class('btn-' . $atts['style']) . '" type="button" data-bs-toggle="collapse" data-bs-target="#' . $id . '" aria-expanded="false" aria-controls="' . $id . '">' . esc_html($atts['title']) . '</button>';
    $html .= '<div class="collapse" id="' . $id . '"><div class="card-body">' . do_shortcode($content) . '</div></div>';

    return $html;
}

