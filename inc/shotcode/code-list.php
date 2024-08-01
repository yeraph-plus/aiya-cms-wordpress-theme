<?php
if (!defined('ABSPATH')) exit;

//AIYA-CMS 短代码组件：快捷列表

function list_shortcode($atts = array(), $content = null)
{
    $atts = shortcode_atts(
        array(
            'order' => 'true',
            'class' => ''
        ),
        $atts,
    );

    $content = str_replace(array("\r\n", "<br />\n", "</p>\n", "\n<p>"), "\n", $content);

    $html = '';

    //循环格式
    foreach (explode("\n", $content) as $li) {
        if ($li = trim($li)) {
            $html .= "<li>" . do_shortcode($li) . "</li>\n";
        }
    }

    //增加样式
    $class = ($atts['class'] != '') ? ' class="' . $atts['class'] . '"' : '';

    $tag = ($atts['order'] == 'true' || $atts['order'] == true) ? 'ol' : 'ul';

    return '<' . $tag . $class . ">\n" . $html . "</" . $tag . ">\n";
}

add_shortcode('list', 'list_shortcode');
