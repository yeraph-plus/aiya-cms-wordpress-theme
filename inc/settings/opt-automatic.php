<?php

if (!defined('ABSPATH')) {
    exit;
}


//主题设置
AYF::new_opt([
    'title' => '格式处理',
    'parent' => 'basic',
    'slug' => 'automatic',
    'desc' => 'AIYA-CMS 主题，文章内容过滤器组件设置',
    'fields' => [
        [
            'desc' => '正文过滤器',
            'type' => 'title_1',
        ],
        [
            'title' => '使用 DOMDocument 处理',
            'desc' => ' HTML 属性匹配更精准，但性能略差 [del]什么年代了还在用传统 preg_match_all()[/del]',
            'id' => 'site_content_dom_handler_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '添加图片描述',
            'desc' => '文章正文内的图片标签自动添加 alt 属性和懒加载属性',
            'id' => 'site_content_img_filter_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '添加标题锚点',
            'desc' => '给文章正文内标题标签自动添加锚点id属性',
            'id' => 'site_content_title_filter_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '添加链接内链',
            'desc' => '给文章正文内链接标签自动添加 [code]rel="nofollow"[/code] 和 [code]target="_blank"[/code] 属性（仅对外部链接生效）',
            'id' => 'site_content_link_filter_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '内链格式设置',
            'desc' => '接上一项设置，可选跳转方式',
            'id' => 'site_content_link_jump_page_type',
            'type' => 'radio',
            'sub' => array(
                'link' => '提示外部页面',
                'go' => '使用内链跳转',
                'false' => '直接跳转（不处理）',
            ),
            'default' => 'link',
        ],
        [
            'desc' => '中文排版纠正',
            'type' => 'title_1',
        ],
        [
            'desc' => '警告：以下功能直接操作文章数据，会覆盖原有数据，使用前请先备份及测试。',
            'type' => 'warning',
        ],
        [
            'desc' => '*此功能使用 [code]jxlwqq/chinese-typesetting[/code] 项目创建：[url=https://github.com/jxlwqq/chinese-typesetting]查看文档[/url]',
            'type' => 'message',
        ],
        [
            'title' => '中文排版纠正',
            'desc' => '在文章保存/更新时触发，执行统一排版纠正',
            'id' => 'site_post_chs_compose_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '启用的排版方法',
            'desc' => '接续上一项设置，选择需要启用的格式过滤器，详细说明请查阅相关项目文档',
            'id' => 'site_post_chs_compose_type',
            'type' => 'checkbox',
            'sub' => array(
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
            'desc' => '提示条目分类法模板',
            'type' => 'title_1',
        ],
        [
            'title' => '启用提示条目分类法',
            'desc' => '为文章创建新的分类法，在文章开头位置输出文章完成状态（更新中、已停更），或提示读者风险操作，等有用的提示',
            'id' => 'site_post_tips_terms_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '加载默认提示条目模板',
            'desc' => '加载一次即可，如需要删除和修改默认提示模板，请先禁用此项，否则会重新创建',
            'id' => 'site_post_tips_default_terms_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'desc' => '自动别名',
            'type' => 'title_1',
        ],
        [
            'desc' => '*此功能使用 [code]overtrue/pinyin[/code] 项目创建',
            'type' => 'message',
        ],
        [
            'title' => '使用拼音生成分类、标签别名',
            'desc' => '在分类法创建/更新时触发，如果未设置别名（留空时）自动生成',
            'id' => 'site_term_auto_pinyin_slug_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'desc' => '*如需文章别名在 URL 结构中生效，请先设置 [url=options-permalink.php]固定链接[/url]',
            'type' => 'message',
        ],
        [
            'title' => '使用拼音生成文章别名',
            'desc' => '在文章保存/更新时触发，如果未设置别名（为空时），则自动生成',
            'id' => 'site_post_auto_pinyin_slug_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '根据文章 ID 生成别名',
            'desc' => '会覆盖上一项设置，强制使用 ID 生成文章的别名，不会检查是否已设置别名
            [br/]仿写格式1 [code]av00000001[/code] 使用文章 ID 数据进行字符补全
            [br/]仿写格式2 [code]bvxxxxxxxx[/code] 使用文章 ID 数据进行Base58编码',
            'id' => 'site_post_auto_slug_type',
            'type' => 'radio',
            'sub' => [
                'off' => '关闭',
                'id_av' => '低仿AV号',
                'id_bv' => '低仿BV号',
            ],
            'default' => 'off',
        ],
        [
            'title' => '文章别名附加前缀',
            'desc' => '在使用 ID 生成别名时设置的别名前缀，请勿包含 [code]<>{}|^[][?#:[/code] 等控制字符以及空格',
            'id' => 'site_post_auto_slug_prefix',
            'type' => 'text',
            'default' => 'BV',
        ],
    ]
]);
