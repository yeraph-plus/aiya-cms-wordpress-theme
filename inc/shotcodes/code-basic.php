<?php
if (!defined('ABSPATH'))
    exit;

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
                'type' => 'checkbox',
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
                'type' => 'checkbox',
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
                'type' => 'checkbox',
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
                'type' => 'select',
                'label' => '列表比例',
                'desc' => '选择描述列表的宽度比例',
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
                'id' => 'style',
                'type' => 'select',
                'label' => '样式',
                'desc' => '选择提示框的样式',
                'sub' => [
                    'outline' => '框线',
                    'dash' => '虚线',
                    'soft' => '柔和',
                    'default' => '默认',
                ],
                'default' => 'default',
            ],
            [
                'id' => 'level',
                'type' => 'select',
                'label' => '颜色等级',
                'desc' => '选择一个提示框的颜色等级',
                'sub' => [
                    'success' => '成功',
                    'error' => '危险',
                    'warning' => '警告',
                    'info' => '信息',
                    'default' => '默认',
                ],
                'default' => 'default',
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('button-content', array(
        'id' => 'sc-button-content',
        'title' => '按钮',
        'note' => '创建一个按钮外观的链接，用于在文章中突出显示链接，支持多种颜色和样式',
        'template' => '[button {{attributes}}] {{content}} [/button]',
        'field_build' => array(
            [
                'id' => 'href',
                'type' => 'text',
                'label' => '链接',
                'desc' => '按钮跳转的Url',
                'default' => '#',
            ],
            [
                'id' => 'content',
                'type' => 'text',
                'label' => '标题',
                'desc' => '按钮显示的标题',
                'default' => '跳转',
            ],
            [
                'id' => 'color',
                'type' => 'select',
                'label' => '颜色',
                'desc' => '按钮的颜色',
                'sub' => [
                    'neutral' => '中性色',
                    'primary' => '主题色',
                    'secondary' => '次要主题色',
                    'accent' => '强调色',
                    'success' => '成功绿',
                    'error' => '危险红',
                    'warning' => '警告黄',
                    'info' => '信息蓝',
                ],
                'default' => 'primary',
            ],
            [
                'id' => 'style',
                'type' => 'select',
                'label' => '样式',
                'desc' => '按钮的样式',
                'sub' => [
                    'outline' => '框线',
                    'dash' => '虚线',
                    'soft' => '柔和',
                    'ghost' => '幽灵',
                    'link' => '链接',
                    'default' => '默认',
                ],
                'default' => 'link',
            ],
            [
                'id' => 'size',
                'type' => 'select',
                'label' => '尺寸',
                'desc' => '按钮的尺寸',
                'sub' => [
                    'xs' => '超小',
                    'sm' => '小',
                    'md' => '中',
                    'lg' => '大',
                    'xl' => '超大',
                ],
                'default' => 'lg',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('badge-content', array(
        'id' => 'sc-badge-content',
        'title' => '徽标',
        'note' => '创建一个徽标外观的标签，用于在文章中的标题添加备注或添加提示，支持多种颜色和样式',
        'template' => '[badge {{attributes}}] {{content}} [/badge]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'text',
                'label' => '标题',
                'desc' => '徽标显示的文本',
                'default' => 'NEW!',
            ],
            [
                'id' => 'color',
                'type' => 'select',
                'label' => '颜色',
                'desc' => '徽标的颜色',
                'sub' => [
                    'neutral' => '中性色',
                    'primary' => '主题色',
                    'secondary' => '次要主题色',
                    'accent' => '强调色',
                    'success' => '成功绿',
                    'error' => '危险红',
                    'warning' => '警告黄',
                    'info' => '信息蓝',
                ],
                'default' => 'primary',
            ],
            [
                'id' => 'style',
                'type' => 'select',
                'label' => '样式',
                'desc' => '徽标的样式',
                'sub' => array(
                    'outline' => '框线',
                    'dash' => '虚线',
                    'soft' => '柔和',
                    'ghost' => '幽灵',
                    'default' => '默认',
                ),
                'default' => 'default',
            ],
            [
                'id' => 'size',
                'type' => 'select',
                'label' => '尺寸',
                'desc' => '按钮的尺寸',
                'sub' => [
                    'xs' => '超小',
                    'sm' => '小',
                    'md' => '中',
                    'lg' => '大',
                    'xl' => '超大',
                ],
                'default' => 'lg',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('collapse-content', array(
        'id' => 'sc-collapse-content',
        'title' => '折叠面板',
        'note' => '创建一个折叠面板，用于在文章中可以展开或收起的内容',
        'template' => '[collapse {{attributes}}] {{content}} [/collapse]',
        'field_build' => array(
            [
                'id' => 'title',
                'type' => 'text',
                'label' => '标题',
                'desc' => '折叠面板的标题',
                'default' => '点击展开内容',
            ],
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '标题',
                'desc' => '折叠面板的内容',
                'default' => '这里是折叠面板的内容。',
            ],
        )
    ));
}

//移除一些 WordPress 默认的短代码
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
add_shortcode('collapse', 'aya_shortcode_collapse_content');

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
        return '<a class="inline-flex btn btn-outline" href="' . esc_url('mailto:' . antispambot($content)) . '">' . esc_html(antispambot($content)) . '</a>';
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

    //根据换行符分割
    $lines = explode("\n", $content);

    array_shift($lines);

    foreach ($lines as $li) {

        $li = trim($li);

        if (!empty($li)) {
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

    switch ($atts['dt_width']) {
        case '1':
        default:
            $dt_width = 'w-1/4';
            $dd_width = 'w-3/4';
            break;
        case '2':
            $dt_width = 'w-1/2';
            $dd_width = 'w-1/2';
            break;
        case '3':
            $dt_width = 'w-3/4';
            $dd_width = 'w-1/4';
            break;
    }

    //循环格式
    $html = '';
    $html .= '<dl class="flex flex-wrap not-prose">' . "\n";

    //根据换行符分割
    $lines = explode("\n", $content);

    array_shift($lines);

    //dd计数
    $d = 0;
    foreach ($lines as $lc) {

        $lc = trim($lc);

        if (!empty($lc)) {
            if ($d == 0) {
                $html .= '<dt class="' . $dt_width . '">' . do_shortcode($lc) . '</dt>' . "\n";
                $d = 1;
            } else {
                $html .= '<dd class="' . $dd_width . '">' . do_shortcode($lc) . '</dd>' . "\n";
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
            'level' => 'default', //success warning error info
            'style' => 'default', //outline dash soft
        ),
        $atts,
    );

    $html_format = '<div class="alert alert-%1$s alert-%2$s my-4" role="alert">%3$s</div>';

    return sprintf($html_format, $atts['level'], $atts['style'], stripslashes($content));
}

//AIYA-CMS 短代码：按钮
function aya_shortcode_button_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'style' => 'link', //outline dash soft ghost link
            'color' => 'primary',
            'size' => 'md',
            'href' => '#',
        ),
        $atts,
    );

    $html_format = '<a class="btn btn-%1$s btn-%2$s btn-%3$s" href="%4$s" role="button">%5$s</a>';

    return sprintf($html_format, esc_attr($atts['style']), esc_attr($atts['color']), esc_attr($atts['size']), esc_url($atts['href']), esc_html($content));
}

//AIYA-CMS 短代码组件：徽标
function aya_shortcode_badge_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'style' => 'link', //outline dash soft ghost link
            'color' => 'primary',
            'size' => 'md',
        ),
        $atts,
    );

    $html_format = '<span class="badge badge-%1$s badge-%2$s badge-%3$s ml-4">%4$s</span>';

    return sprintf($html_format, esc_attr($atts['style']), esc_attr($atts['color']), esc_attr($atts['size']), esc_html($content));
}

//AIYA-CMS 短代码：折叠面板
function aya_shortcode_collapse_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'title' => ''
        ),
        $atts,
    );

    $html = '';
    $html .= '<div tabindex="0" class="collapse bg-base-100 border-base-300 border my-4" role="button">';
    $html .= '<div class="collapse-title font-semibold">' . esc_html($atts['title']) . '</div>';
    $html .= '<div class="collapse-content text-base-content">' . do_shortcode($content) . '</div>';
    $html .= '</div>';

    return $html;
}