<?php
if (!defined('ABSPATH')) exit;

//to be continued

add_shortcode('collapse', 'aya_shortcode_collapse_content');
add_shortcode('list', 'aya_shortcode_list_content');
add_shortcode('col', 'aya_shortcode_column_content');

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

//AIYA-CMS 短代码组件：快捷列表
function aya_shortcode_list_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'order' => 'true',
            'inline' => 'false'
        ),
        $atts,
    );

    $content = str_replace(array("\r\n", "<br />\n", "</p>\n", "\n<p>"), "\n", $content);

    //增加样式
    $tag = ($atts['order'] == 'true' || $atts['order'] == true) ? 'ol' : 'ul';

    if ($atts['inline'] == 'true' || $atts['inline'] == true) {
        $ul_class = 'list-inline';
        $li_class = 'list-inline-item';
    } else {
        $ul_class = 'list-unstyled';
        $li_class = '';
    }

    //循环格式
    $html = '';
    $html .= '<' . $tag . ' class="' . $ul_class . '">' . "\n";

    foreach (explode("\n", $content) as $li) {
        if ($li = trim($li)) {
            $html .= '<li class="' . $li_class . '">' . do_shortcode($li) . '</li>' . "\n";
        }
    }

    $html .= '</' . $tag . '>' . "\n";

    return $html;
}

//AIYA-CMS 短代码组件：快捷描述列表
function aya_shortcode_column_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'dt' => '3',
            'dd' => '9',
        ),
        $atts,
    );

    $content = str_replace(array("\r\n", "<br />\n", "</p>\n", "\n<p>"), "\n", $content);

    //循环格式
    $html = '';
    $html .= '<dl class="row">' . "\n";
    $d = 0; //dd计数
    foreach (explode("\n", $content) as $lc) {
        if ($lc = trim($lc)) {
            if ($d == 0) {
                $html .= '<dt class="' . sanitize_html_class('col-sm-' . $atts['dt']) . '">' . do_shortcode($lc) . '</dt>' . "\n";
                $d = 1;
            } else {
                $html .= '<dd class="' . sanitize_html_class('col-sm-' . $atts['dd']) . '">' . do_shortcode($lc) . '</dd>' . "\n";
                $d = 0;
            }
        }
    }

    $html .= '</dl>' . "\n";

    return $html;
}
