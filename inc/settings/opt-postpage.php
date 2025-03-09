<?php
if (!defined('ABSPATH')) exit;

//文章页内设置
AYF::new_box(array(
    'title' => '文章设置',
    'id' => 'post_switch_box',
    'context' => 'side',
    'priority' => 'low',
    'add_box_in' => array('post'),
    'fields' => array(
        array(
            'title' => '文章过时提示：',
            'desc' => '文章指定天数后显示过时提示[br/]设置为[code]0[/code]时关闭',
            'id' => 'days_is_outdated',
            'type' => 'text',
            'default'  => '0',
        ),
    ),
));

//主题设置
AYF::new_opt([
    'title' => '文章页',
    'parent' => 'basic',
    'slug' => 'postpage',
    'desc' => 'AIYA-CMS 主题，文章页面组件设置',
    'fields' => [
        [
            'desc' => '正文格式过滤器',
            'type' => 'title_2',
        ],
        [
            'title' => '使用 DOMDocument 处理',
            'desc' => '标签属性匹配更精准，但性能更差（[del] 什么年代了还在用传统 preg_match_all [/del]）',
            'id' => 'site_content_dom_handler_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'desc' => '过滤器功能',
            'type' => 'title_2',
        ],
        [
            'title' => '图片自动描述',
            'desc' => '文章正文内的图片标签自动添加 alt 属性和懒加载属性',
            'id' => 'site_content_img_filter_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '链接自动内链',
            'desc' => '给文章正文内链接标签自动添加 [code]rel="nofollow"[/code] 和 [code]target="_blank"[/code] 属性（仅对外部链接生效）',
            'id' => 'site_content_link_filter_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '链接跳转设置',
            'desc' => '接上一项设置，可选跳转方式',
            'id' => 'site_content_link_jump_page_type',
            'type' => 'radio',
            'sub'  => array(
                'link' => '提示外部页面',
                'go' => '使用内链跳转',
                'false' => '直接跳转（不处理）',
            ),
            'default' => 'link',
        ],
        [
            'desc' => '提示功能',
            'type' => 'title_2',
        ],
        [
            'title' => '文章末尾声明信息',
            'desc' => '在文章末尾输出声明等额外文本',
            'id' => 'site_single_disclaimer_text',
            'type' => 'textarea',
            'default' => '站点声明：本站部分内容转载自网络，作品版权归原作者及来源网站所有，任何内容转载、商业用途等均须联系原作者并注明来源。',
        ],
        [
            'title' => '相关文章',
            'desc' => '开关文章导航卡片，显示上/下一篇或相关文章（使用标签进行相关度检索）',
            'id' => 'site_single_related_type',
            'type' => 'radio',
            'sub'  => array(
                'false' => '隐藏',
                'next-prev' => '上/下一篇',
                'related' => '相关文章',
            ),
            'default' => 'false',
        ],
        /*
        [
            'desc' => '评论（Comments）',
            'type' => 'title_2',
        ],
        [
            'desc' => '排版纠正',
            'type' => 'title_2',
        ],
        [
            'desc' => '*此功能使用 [code]jxlwqq/chinese-typesetting[/code] 项目：[url="https://github.com/jxlwqq/chinese-typesetting"]查看文档[/url]',
            'type' => 'message',
        ],
        [
            'title' => '启用中文排版',
            'desc' => '在文章保存/更新时触发，执行统一排版纠正',
            'id' => 'site_save_post_chinese_type',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '启用的格式过滤器',
            'desc' => '接续上一项设置，选择需要启用的格式过滤器，详细说明请查阅相关项目文档',
            'id' => 'site_save_post_chinese_setting',
            'type' => 'checkbox',
            'sub'  => array(
                'insertSpace' => '中英文空格补正',
                'removeSpace' => '清除全角标点空格',
                'full2Half' => '全角转半角',
                'fixPunctuation' => '修复错误的标点符号',
                'properNoun' => '专有名词大小写',
                'removeClass' => '清除标签 Class 属性',
                'removeId' => '清除标签 ID 属性',
                'removeStyle' => '清除标签 Style 属性',
                'removeEmptyParagraph' => '清除空的段落标签',
                'removeEmptyTag' => '清除所有空的标签',
                'removeIndent' => '清除段首缩进',
            ),
            'default' => array('insertSpace', 'removeSpace', 'full2Half'),
        ],
        [
            'desc' => '自动别名',
            'type' => 'title_2',
        ],
        [
            'desc' => '*此功能使用 [code]overtrue/pinyin[/code] 项目',
            'type' => 'message',
        ],
        [
            'title' => '使用拼音生成文章别名',
            'desc' => '在文章保存/更新时触发，如果未设置别名（为空时），则自动生成',
            'id' => 'site_save_post_slug_pinyin_type',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '使用拼音生成分类别名',
            'desc' => '在分类保存/更新时触发，如果未设置别名（为空时），则自动生成',
            'id' => 'site_save_term_slug_pinyin_type',
            'type' => 'switch',
            'default' => false,
        ],
        */
    ]
]);
