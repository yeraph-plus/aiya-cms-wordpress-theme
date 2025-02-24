<?php
if (!defined('ABSPATH')) exit;

remove_shortcode('wp_caption');
remove_shortcode('cn_icp');
remove_shortcode('cn_icp_text');
remove_shortcode('cn_ga');
remove_shortcode('cn_ga_text');

add_shortcode('hide', 'aya_shortcode_hide_content');
add_shortcode('email', 'aya_shortcode_email_content');
add_shortcode('alert', 'aya_shortcode_alert_content');
add_shortcode('button', 'aya_shortcode_button_content');
add_shortcode('collapse', 'aya_shortcode_collapse_content');
add_shortcode('list', 'aya_shortcode_list_content');
add_shortcode('col', 'aya_shortcode_column_content');

//AIYA-CMS 短代码组件：隐藏文字段
function aya_shortcode_hide_content($atts = array(), $content = '')
{
    return '<!--' . stripslashes($content) . '-->';
}
//AIYA-CMS 短代码：EMAIL字符转换
function aya_shortcode_email_content($atts = array(), $content = null)
{
    $atts = shortcode_atts(
        array(
            'to' => false,
        ),
        $atts,
    );

    $content = wp_kses($content, 'post');

    //将电子邮件地址字符转换为 HTML 实体
    if ($atts['to']) {
        return '<a href="' . esc_url('mailto:' . antispambot($content)) . '">' . esc_html(antispambot($content)) . '</a>';
    } else {
        return antispambot($content);
    }
}
//AIYA-CMS 短代码：提示框
function aya_shortcode_alert_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'style' => 'info', //secondary success danger warning info light dark
        ),
        $atts,
    );

    $content = stripslashes($content);

    return '<div class="alert ' . sanitize_html_class('alert-' . $atts['style']) . '" role="alert">' . do_shortcode($content) . '</div>';
}
//AIYA-CMS 短代码：按钮
function aya_shortcode_button_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'style' => 'color-solid', //primary secondary success danger warning info light dark
            'href' => '#',
        ),
        $atts,
    );

    $content = stripslashes($content);

    return '<a class="my-1 btn ' . sanitize_html_class('btn-' . $atts['style']) . '" href="' . esc_url($atts['href']) . '" role="button">' . $content . '</a>';
}
//AIYA-CMS 短代码：折叠框
function aya_shortcode_collapse_content($atts = array(), $content = '')
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
