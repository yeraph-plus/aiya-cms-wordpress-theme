<?php

if (!defined('ABSPATH')) {
    exit;
}

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
                'label' => '内容',
                'desc' => '折叠面板的内容',
                'default' => '这里是折叠面板的内容。',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('clipboard-box', array(
        'id' => 'sc-clipboard-box',
        'title' => '一键复制',
        'note' => '创建一个提供复制功能的卡片，用于快捷复制文本或打开链接',
        'template' => '[clip_board {{attributes}}] {{content}} [/clip_board]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '需要被复制的文本或链接',
                'default' => 'magnet:?xt=urn:btih:',
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('sponsor-ship-content', array(
        'id' => 'sc-sponsor-ship-content',
        'title' => '赞助者可见',
        'note' => '创建一个赞助后的内容块，用于在文章中显示仅限赞助商可见的内容',
        'template' => '[sponsor_ship {{attributes}}] {{content}} [/sponsor_ship]',
        'field_build' => array(
            [
                'id' => 'title',
                'type' => 'text',
                'label' => '标题',
                'desc' => '内容块的标题',
                'default' => '支援者限定',
            ],
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '赞助者可见的内容',
                'default' => '这是赞助者可见的内容。',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('logged-in-content', array(
        'id' => 'sc-logged-in-content',
        'title' => '登录后可见',
        'note' => '创建一个登录后可见的内容块，用于在文章中显示仅限登录用户可见的内容',
        'template' => '[logged_in {{attributes}}] {{content}} [/logged_in]',
        'field_build' => array(
            [
                'id' => 'title',
                'type' => 'text',
                'label' => '标题',
                'desc' => '内容块的标题',
                'default' => '登录后下载',
            ],
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '登录后可见的内容',
                'default' => '这是登录后可见的内容。',
            ],
        )
    ));

    AYA_Shortcode::shortcode_register('bili-iframe', array(
        'id' => 'sc-bili-card',
        'title' => '嵌入B站视频',
        'note' => '嵌入B站的H5播放器到页面',
        'template' => '[bili_iframe {{attributes}} /]',
        'field_build' => array(
            [
                'id' => 'bvid',
                'type' => 'text',
                'label' => 'BV号',
                'desc' => 'BV号，例如BV1UT42167xb',
                'default' => '',
            ],
            [
                'id' => 'h5_player',
                'type' => 'checkbox',
                'label' => '使用 HTML5 播放器',
                'desc' => '切换使用B站标准的外链播放器或者移动端HTML5播放器',
                'default' => true,
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('afdian-iframe', array(
        'id' => 'sc-afdian-card',
        'title' => '嵌入爱发电主页',
        'note' => '嵌入爱发电的主页或按钮到页面',
        'template' => '[afdian_iframe {{attributes}} /]',
        'field_build' => array(
            [
                'id' => 'slug',
                'type' => 'text',
                'label' => '后缀',
                'desc' => '创作者个人主页后缀，留空时使用系统设置',
                'default' => '',
            ],
            [
                'id' => 'type_btn',
                'type' => 'checkbox',
                'label' => '使用按钮链接',
                'desc' => '使当前段落作为 html 注释输出到前台页面，或者完全不输出',
                'default' => false,
            ]
        )
    ));

    AYA_Shortcode::shortcode_register('github-iframe', array(
        'id' => 'sc-github-repo',
        'title' => '嵌入GitHub仓库',
        'note' => '嵌入GitHub仓库卡片到页面（填写“用户名/仓库名”）',
        'template' => '[github_card {{attributes}} /]',
        'field_build' => array(
            [
                'id' => 'repo',
                'type' => 'text',
                'label' => '<user>/<repo>',
                'desc' => '',
                'default' => '',
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
add_shortcode('collapse', 'aya_shortcode_collapse_content');
add_shortcode('clip_board', 'aya_shortcode_clipboard_in_content');
add_shortcode('sponsor_ship', 'aya_shortcode_sponsor_ship_content');
add_shortcode('logged_in', 'aya_shortcode_logged_in_content');
add_shortcode('bili_iframe', 'aya_shortcode_bilibili_iframe');
add_shortcode('afdian_iframe', 'aya_shortcode_afdian_iframe');
add_shortcode('github_card', 'aya_shortcode_github_repo_iframe');

//AIYA-CMS 短代码组件：隐藏文字段
function aya_shortcode_hide_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'inline' => 'false',
        ),
        $atts,
    );

    if (filter_var($atts['inline'], FILTER_VALIDATE_BOOLEAN)) {
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
    if (filter_var($atts['mailto'], FILTER_VALIDATE_BOOLEAN)) {
        return '<a class="inline-flex" href="' . esc_url('mailto:' . antispambot($content)) . '">' . esc_html(antispambot($content)) . '</a>';
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

    //增加样式
    $tag = (filter_var($atts['order'], FILTER_VALIDATE_BOOLEAN)) ? 'ul' : 'ol';

    //循环格式
    $html = '';
    $html .= '<' . $tag . '>' . "\n";

    //根据换行符分割
    $content = str_replace(array("\r\n", "<br />\n", "</p>\n", "\n<p>"), "\n", $content);
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

    $content = str_replace(array("\r\n", "<br />\n", "</p>\n", "\n<p>"), "\n", $content);
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
    $html .= '<details class="art-collapse group">';
    $html .= '<summary class="art-collapse-title">';
    $html .= '<span>' . esc_html($atts['title']) . '</span>';
    $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="art-collapse-icon"><path d="m6 9 6 6 6-6"/></svg>';
    $html .= '</summary>';
    $html .= '<div class="art-collapse-content">' . do_shortcode($content) . '</div>';
    $html .= '</details>';

    return $html;
}

//AIYA-CMS 短代码组件：剪贴板功能卡片
function aya_shortcode_clipboard_in_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(),
        $atts
    );

    $content = do_shortcode($content);
    $content_clean = trim(strip_tags($content));

    $html = '<div class="group relative my-4 rounded-lg border border-border bg-secondary/30">';
    $html .= '<div class="p-4 font-mono text-sm break-all text-foreground">' . esc_html($content_clean) . '</div>';
    $html .= '<button class="absolute top-2 right-2 p-2 rounded-lg hover:bg-muted text-muted-foreground hover:text-foreground transition-colors z-10" data-clipboard-text="' . esc_attr($content_clean) . '" aria-label="' . __('复制', 'AIYA') . '">';
    $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-copy-icon lucide-copy"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>';
    $html .= '</button>';
    $html .= '</div>';

    return $html;
}

//AIYA-CMS 短代码：赞助者可见内容
function aya_shortcode_sponsor_ship_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(),
        $atts,
    );

    $user_level = aya_user_toggle_level();
    $is_allowed = (in_array($user_level, ['administrator', 'author', 'sponsor']));

    $html = '';

    //管理员、赞助者或投稿权限的用户可见
    if ($is_allowed) {
        $html .= do_shortcode($content);

        return $html;
    }

    $html .= '<div class="relative overflow-hidden rounded-lg border border-pink-500 bg-gradient-to-br from-pink-500/5 via-transparent to-pink-500/5 px-4 py-8 my-4 shadow-sm ring-1 ring-pink-500/10 backdrop-blur-sm text-pink-600">';
    $html .= '<div class="flex items-center gap-2 mb-2">';
    $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-keyhole-icon lucide-lock-keyhole"><circle cx="12" cy="16" r="1"/><rect x="3" y="10" width="18" height="12" rx="2"/><path d="M7 10V7a5 5 0 0 1 10 0v3"/></svg>';

    $html .= '<span class="text-xl font-bold">' . __('赞助内容', 'AIYA') . '</span>';
    $html .= '</div>';
    $html .= '<span>';

    if ($user_level == 'guest') {
        $html .= __('此内容仅限订阅用户可见，请登录后查看', 'AIYA');
    } else {
        $html .= __('仅限订阅用户可见：', 'AIYA') . '<a href="' . home_url('sponsor') . '" class="link">' . __('获取订阅', 'AIYA') . '</a>';
    }
    $html .= '</span>';
    $html .= '</div>';

    return $html;
}

//AIYA-CMS 短代码：登录可见内容
function aya_shortcode_logged_in_content($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(),
        $atts,
    );

    $is_allowed = (aya_user_toggle_level() !== 'guest');

    $html = '';

    if ($is_allowed) {
        $html .= do_shortcode($content);

        return $html;
    }

    $html .= '<div class="relative overflow-hidden rounded-lg border border-primary bg-gradient-to-br from-primary/5 via-transparent to-primary/5 px-4 py-8 my-4 shadow-sm ring-1 ring-primary/10 backdrop-blur-sm text-primary/95">';
    $html .= '<div class="flex items-center gap-2 mb-2">';
    $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-keyhole-icon lucide-lock-keyhole"><circle cx="12" cy="16" r="1"/><rect x="3" y="10" width="18" height="12" rx="2"/><path d="M7 10V7a5 5 0 0 1 10 0v3"/></svg>';

    $html .= '<span class="text-xl font-bold">' . __('赞助内容', 'AIYA') . '</span>';
    $html .= '</div>';
    $html .= '<span>' . __('仅限登录后查看，请先登录', 'AIYA') . '</span>';
    $html .= '</div>';

    return $html;
}

//AIYA-CMS 短代码组件：Iframe嵌入页面
function aya_shortcode_bilibili_iframe($atts)
{
    $atts = shortcode_atts(
        array(
            'bvid' => '',
            'h5_player' => 'false',
        ),
        $atts
    );

    if (empty($atts['bvid'])) {
        return '';
    }

    if (filter_var($atts['h5_player'], FILTER_VALIDATE_BOOLEAN)) {
        $src = '//www.bilibili.com/blackboard/html5mobileplayer.html?isOutside=true&bvid=' . $atts['bvid'];
    } else {
        $src = '//player.bilibili.com/player.html?isOutside=true&bvid=' . $atts['bvid'];
    }

    $html = '';

    $html .= '<div class="iframe-container">';
    $html .= '<iframe src="' . $src . '" width="640" height="360" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true" loading="lazy">';
    $html .= '<p class="text-center py-8 text-gray-500">No specified.</p>';
    $html .= '</iframe>';
    $html .= '</div>';

    return $html;
}

//AIYA-CMS 短代码组件：爱发电卡片
function aya_shortcode_afdian_iframe($atts)
{
    $atts = shortcode_atts(
        array(
            'slug' => '',
            'type_btn' => 'false',
        ),
        $atts
    );

    $afdian_slug = (empty($atts['slug'])) ? aya_opt('stie_afdian_homepage_text', 'access') : $atts['slug'];

    if (empty($afdian_slug)) {
        return '';
    }

    $html = '';

    if (filter_var($atts['type_btn'], FILTER_VALIDATE_BOOLEAN)) {
        $src = aya_get_afdian_link() . $afdian_slug;

        $html .= '<a href="' . $src . '" target="_blank">';
        $html .= '<img width="200" src="https://pic1.afdiancdn.com/static/img/welcome/button-sponsorme.png" alt="">';
        $html .= '</a>';
    } else {
        $src = aya_get_afdian_link() . 'leaflet?slug=' . $afdian_slug;

        $html .= '<div class="iframe-container">';
        $html .= '<iframe src="' . $src . '" width="640" height="200" scrolling="no" frameborder="0" loading="lazy">';
        $html .= '<p class="text-center py-8 text-gray-500">No specified.</p>';
        $html .= '</iframe>';
        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}

//AIYA-CMS 短代码组件：GitHub仓库卡片
function aya_shortcode_github_repo_iframe($atts)
{
    $atts = shortcode_atts(
        array(
            'repo' => '',
        ),
        $atts
    );

    if (empty($atts['repo'])) {
        return '';
    }

    $card_src = 'https://gh-card.dev/repos/' . esc_html($atts['repo']) . '.svg';

    $html = '';

    $html .= '<div class="iframe-container">';
    $html .= '<iframe src="' . $card_src . '" loading="lazy">';
    $html .= '<p class="text-center py-8 text-gray-500">No specified.</p>';
    $html .= '</iframe>';
    $html .= '</div>';

    return $html;
}
