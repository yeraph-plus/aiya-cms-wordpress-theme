<?php
if (!defined('ABSPATH')) exit;

if (is_admin()) {
    //初始化简码输入框组件按钮
    AYA_Shortcode::instance();

    AYA_Shortcode::shortcode_register('hidden-content', array(
        'id' => 'sc-display-hide',
        'title' => '隐藏文本段',
        'note' => '包含在此简码内的文本不会在前台显示，用于充当文章编辑时的注释，或隐藏一段文本',
        'template' => '[hide {{attributes}}]<br/> {{content}} <br/>[/hide]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '在这里输入要隐藏的文本',
                'default' => '隐藏的文本段。',
            ],
            [
                'id' => 'inline',
                'type'  => 'checkbox',
                'label' => '作为注释输出',
                'desc' => '使当前段落作为 html 注释输出到前台页面，或者完全不输出',
                'default' => false,
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('email-link', array(
        'id' => 'sc-email-link',
        'title' => 'E-mail 链接',
        'note' => '将电子邮件地址字符转换为 HTML 实体显示，避免被爬虫抓取',
        'template' => '[email {{attributes}}] {{content}} [/email]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'text',
                'label' => '邮件地址',
                'desc' => '例如： admin@example.com',
                'default' => '',
            ],
            [
                'id' => 'mailto',
                'type'  => 'checkbox',
                'label' => '使用 mailto 链接',
                'desc' => '转换为 mailto 链接，或直接显示',
                'default' => false,
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('quick-li-content', array(
        'id' => 'sc-li-list',
        'title' => '快捷列表（li）',
        'note' => '将文本按行转换为列表显示',
        'template' => '[list {{attributes}}]<br/> {{content}} <br/>[/list]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '按每行顺序格式化结构为：<li>第1行</li>/<li>第2行</li>',
                'default' => '',
            ],
            [
                'id' => 'order',
                'type'  => 'checkbox',
                'label' => '编号列表',
                'desc' => '启用此项将显示为编号列表，否则使用符号列表',
                'default' => false,
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('quick-col-content', array(
        'id' => 'sc-col-list',
        'title' => '快捷列表（Column）',
        'note' => '将文本按行转换为描述列表显示',
        'template' => '[col_list {{attributes}}]<br/> {{content}} <br/>[/col_list]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '按每行顺序格式化结构为：<dt>第1行</dt>/<dd>第2行</dd>',
                'default' => '',
            ],
            [
                'id' => 'dt_width',
                'type'  => 'select',
                'label' => '列表比例',
                'desc'  => '选择描述列表的宽度比例',
                'sub' => [
                    '1' => '1/4列表',
                    '2' => '1/2列表',
                    '3' => '3/4列表',
                ],
                'default' => '1',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('alert-content', array(
        'id' => 'sc-default-alert',
        'title' => '提示框',
        'note' => '默认样式的提示框，用于在文章中显示提示信息，支持多种颜色',
        'template' => '[alert {{attributes}}] {{content}} [/alert]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '在这里输入提示框的内容',
                'default' => '<strong>提示，</strong>这是一个提示。',
            ],
            [
                'id' => 'level',
                'type'  => 'select',
                'label' => '颜色等级',
                'desc'  => '选择一个提示框的颜色等级',
                'sub' => [
                    'primary' => '默认',
                    'secondary' => '次要',
                    'success' => '成功',
                    'danger' => '危险',
                    'warning' => '警告',
                    'info' => '信息',
                ],
                'default' => 'primary',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('button-content', array(
        'id'       => 'sc-button-content',
        'title'    => '按钮',
        'note'    => '创建一个按钮外观的链接，用于在文章中突出显示链接，支持多种颜色和样式',
        'template' => '[button {{attributes}}] {{content}} [/button]',
        'field_build'   => array(
            [
                'id' => 'href',
                'type'  => 'text',
                'label' => '链接',
                'desc'  => '按钮跳转的Url',
                'default'   => '#',
            ],
            [
                'id' => 'content',
                'type'  => 'text',
                'label' => '标题',
                'desc'  => '按钮显示的标题',
                'default'   => '点击跳转',
            ],
            [
                'id' => 'color',
                'type'  => 'select',
                'label' => '颜色',
                'desc'  => '按钮的颜色',
                'sub' => [
                    'primary' => '主题色',
                    'secondary' => '主题色2',
                    'dark' => '默认黑',
                    'success' => '成功绿',
                    'danger' => '危险红',
                    'warning' => '警告黄',
                    'info' => '信息蓝',
                ],
                'default' => 'primary',
            ],
            [
                'id' => 'style',
                'type'  => 'select',
                'label' => '样式',
                'desc'  => '按钮的样式',
                'sub' => array(
                    'default' => '默认',
                    'outline' => '反色',
                ),
                'default' => 'default',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('badge-content', array(
        'id'       => 'sc-badge-content',
        'title'    => '徽标',
        'note'    => '创建一个徽标外观的标签，用于在文章中的标题添加备注或添加提示，支持多种颜色和样式',
        'template' => '[badge {{attributes}}] {{content}} [/badge]',
        'field_build'   => array(
            [
                'id' => 'content',
                'type'  => 'text',
                'label' => '标题',
                'desc'  => '徽标显示的文本',
                'default'   => 'NEW!',
            ],
            [
                'id' => 'color',
                'type'  => 'select',
                'label' => '颜色',
                'desc'  => '徽标的颜色',
                'sub' => [
                    'primary' => '主题色',
                    'secondary' => '主题色2',
                    'dark' => '默认黑',
                    'success' => '成功绿',
                    'danger' => '危险红',
                    'warning' => '警告黄',
                    'info' => '信息蓝',
                ],
                'default' => 'primary',
            ],
            [
                'id' => 'style',
                'type'  => 'select',
                'label' => '样式',
                'desc'  => '徽标的样式',
                'sub' => array(
                    'default' => '默认',
                    'outline' => '反色',
                ),
                'default' => 'default',
            ],
        )
    ));
}

remove_shortcode('wp_caption');
remove_shortcode('cn_icp');
remove_shortcode('cn_icp_text');
remove_shortcode('cn_ga');
remove_shortcode('cn_ga_text');

add_shortcode('hide', 'aya_shortcode_hide_content');
add_shortcode('email', 'aya_shortcode_email_content');
add_shortcode('list', 'aya_shortcode_li_list_content');
add_shortcode('col_list', 'aya_shortcode_column_list_content');
add_shortcode('alert', 'aya_shortcode_alert_content');
add_shortcode('button', 'aya_shortcode_button_content');
add_shortcode('badge', 'aya_shortcode_badge_content');

//AIYA-CMS 短代码组件：隐藏文字段
function aya_shortcode_hide_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'inline' => 'false',
        ),
        $atts,
    );
    //
    if ($atts['inline'] == 'true' || $atts['inline'] == 'on' || $atts['inline'] == true) {
        return '<!--' . esc_html($content) . '-->';
    } else {
        return '';
    }
}

//AIYA-CMS 短代码：EMAIL字符转换
function aya_shortcode_email_content($atts = array(), $content = null)
{
    $atts = shortcode_atts(
        array(
            'mailto' => 'false',
        ),
        $atts,
    );

    $content = wp_kses($content, 'post');

    //将电子邮件地址字符转换为 HTML 实体
    if ($atts['mailto'] == 'true' || $atts['mailto'] == 'on' || $atts['mailto'] == true) {
        return '<a class="inline-flex btn btn-outline-primary" href="' . esc_url('mailto:' . antispambot($content)) . '">' . esc_html(antispambot($content)) . '</a>';
    } else {
        return antispambot($content);
    }
}

//AIYA-CMS 短代码组件：快捷列表
function aya_shortcode_li_list_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'order' => 'false',
        ),
        $atts,
    );

    $content = str_replace(array("\r\n", "<br />\n", "</p>\n", "\n<p>"), "\n", $content);

    //增加样式
    $tag = ($atts['order'] == 'true' || $atts['order'] == 'on' || $atts['order'] == true) ? 'ul' : 'ol';

    //循环格式
    $html = '';
    $html .= '<' . $tag . '>' . "\n";

    foreach (explode("\n", $content) as $li) {
        if ($li = trim($li)) {
            $html .= '<li>' . do_shortcode($li) . '</li>' . "\n";
        }
    }

    $html .= '</' . $tag . '>' . "\n";

    return $html;
}

//AIYA-CMS 短代码组件：快捷描述列表
function aya_shortcode_column_list_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'dt_width' => '1', //dt宽度
        ),
        $atts,
    );

    $content = str_replace(array("\r\n", "<br />\n", "</p>\n", "\n<p>"), "\n", $content);

    $dt_width = intval($atts['dt_width']);
    $dd_width = 4 - $dt_width;

    //循环格式
    $html = '';
    $html .= '<dl class="flex flex-wrap">' . "\n";
    $d = 0; //dd计数
    foreach (explode("\n", $content) as $lc) {
        if ($lc = trim($lc)) {
            if ($d == 0) {
                $html .= '<dt class="w-' . $dt_width . '/4">' . do_shortcode($lc) . '</dt>' . "\n";
                $d = 1;
            } else {
                $html .= '<dd class="w-' . $dd_width . '/4">' . do_shortcode($lc) . '</dd>' . "\n";
                $d = 0;
            }
        }
    }

    $html .= '</dl>' . "\n";

    return $html;
}

//AIYA-CMS 短代码：提示框
function aya_shortcode_alert_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'level' => 'primary', //primary secondary success warning danger info light dark
        ),
        $atts,
    );

    $html_format = '<div class="flex items-center p-3.5 my-2 rounded text-%1$s bg-%1$s-light dark:bg-%1$s-dark-light" role="alert">%2$s</div>';

    return sprintf($html_format, $atts['level'], stripslashes($content));
}

//AIYA-CMS 短代码：按钮
function aya_shortcode_button_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'style' => 'default',
            'color' => 'primary',
            'href' => '#',
        ),
        $atts,
    );

    if ($atts['style'] == 'outline') {
        $html_format = '<a class="btn btn-outline-%1$s" href="%2$s" role="button">%3$s</a>';
    }
    //default
    else {
        $html_format = '<a class="btn btn-%1$s" href="%2$s" role="button">%3$s</a>';
    }

    return sprintf($html_format, sanitize_html_class($atts['color']), esc_url($atts['href']), esc_html($content));
}

//AIYA-CMS 短代码组件：徽标
function aya_shortcode_badge_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'style' => 'default',
            'color' => 'primary',
        ),
        $atts,
    );

    if ($atts['style'] == 'outline') {
        $html_format = '<span class="ml-4 badge badge-outline-%1$s">%2$s</span>';
    }
    //default
    else {
        $html_format = '<span class="ml-4 badge bg-%1$s">%2$s</span>';
    }

    return sprintf($html_format, sanitize_html_class($atts['color']), esc_html($content));
}
