<?php

if (!defined('ABSPATH')) {
    exit;
}

//创建主题设置
AYF::new_opt([
    'title' => '页面组件',
    'parent' => 'basic',
    'slug' => 'land',
    'desc' => 'AIYA-CMS 主题，页面组件设置',
    'fields' => [
        [
            'desc' => '正文过滤器',
            'type' => 'title_1',
        ],
        [
            'title' => '使用 DOMDocument 处理器',
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
            'sub' => [
                'link' => '提示外部页面',
                'go' => '使用内链跳转',
                'false' => '直接跳转（不处理）',
            ],
            'default' => 'link',
        ],
        [
            'desc' => '内容组件',
            'type' => 'title_1',
        ],
        [
            'desc' => '设置文章列表输出的布局模板，文章列表的输出数量请在站点 [url=options-reading.php]阅读设置[/url] 中调整',
            'type' => 'message',
        ],
        [
            'title' => '文章时效性提示',
            'desc' => '在文章发布或更新时间超过天数后显示内容过期提示，留空时禁用',
            'id' => 'site_post_outdate_days_text',
            'type' => 'text',
            'default' => '0',
        ],
        [
            'title' => '文末声明',
            'desc' => '在文章末尾输出版权声明等额外文本',
            'id' => 'site_post_statement_text',
            'type' => 'textarea',
            'default' => '本站部分内容转载自网络，作品版权归原作者及来源网站所有，任何内容转载、商业用途等均须联系原作者并注明来源。',
        ],
        [
            'title' => '加载文章提示组件',
            'desc' => '创建新的分类法，在文章开头位置输出文章完成状态（更新中、已停更），或提示读者风险操作，等有用的提示',
            'id' => 'site_post_add_tips_terms_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '加载推文组件',
            'desc' => '创建新的文章类型，以及推文组件页面',
            'id' => 'site_post_add_tweets_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '首页独立分区列表',
            'desc' => '添加独立分区列表，显示不同的分类文章',
            'id' => 'site_post_home_section_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '此项为空则自动分类名称',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '分类',
                    'desc' => '选择要显示的分类',
                    'id' => 'category_ids',
                    'type' => 'checkbox',
                    'sub_mode' => 'category',
                    'default' => '',
                ],
                [
                    'title' => '查询排序方式',
                    'desc' => '选择查询的文章排序方式',
                    'id' => 'orderby',
                    'type' => 'select',
                    'sub' => [
                        'date' => '发布时间',
                        'title' => '标题',
                        'modified' => '最后修改日期',
                        'rand' => '随机',
                        'none' => '不排序',
                    ],
                    'default' => 'date',
                ],
                [
                    'title' => '查询数量',
                    'desc' => '选择查询的文章数量',
                    'id' => 'limit',
                    'type' => 'radio',
                    'sub' => [
                        '5' => '5条',
                        '10' => '10条',
                        '20' => '20条',
                    ],
                    'default' => '10',
                ],
            ],
        ],
        [
            'desc' => '轮播设置',
            'type' => 'title_1',
        ],
        [
            'title' => '轮播组件外观切换',
            'desc' => '切换或关闭首页轮播，CMS模式为至少需要5篇文章，宽幅模式为至少需要1篇文章',
            'id' => 'site_carousel_section_type',
            'type' => 'radio',
            'sub' => [
                'off' => '关闭',
                'full' => '经典（宽幅模式）',
                'mosaic' => '拼贴（三卡片）',
                'cms' => '三栏位（CMS模式）',
            ],
            'default' => 'off',
        ],
        [
            'title' => '轮播列表',
            'desc' => '添加轮播列表',
            'id' => 'site_carousel_section_item_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '链接',
                    'desc' => '填入任意URL、或填写文章ID或文章别名 [b]*Tips: 不支持识别分类或标签[/b]',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '图片',
                    'desc' => '轮播卡片的大图，留空时自动获取文章特色图片',
                    'id' => 'thumbnail',
                    'type' => 'upload',
                    'default' => '',
                ],
                [
                    'title' => '标题',
                    'desc' => '轮播卡片的标题，留空时自动获取文章标题',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '描述',
                    'id' => 'description',
                    'desc' => '轮播卡片的描述，留空时自动获取文章摘要',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'desc' => '页面广告位（AD）',
            'type' => 'title_1',
        ],
        [
            'title' => '全局顶部',
            'desc' => '广告位在页面顶部显示',
            'id' => 'site_ad_home_before_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '广告视图',
                    'desc' => '广告的图片链接 ',
                    'id' => 'view',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => '全局底部',
            'desc' => '广告位在页面底部显示',
            'id' => 'site_ad_home_after_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '广告视图',
                    'desc' => '广告的图片链接 ',
                    'id' => 'view',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => '文内顶部',
            'desc' => '广告位在文章顶部显示（仅文本链接）',
            'id' => 'site_ad_post_before_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => '文内底部',
            'desc' => '广告位在文章底部显示（仅文本链接）',
            'id' => 'site_ad_post_after_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '广告标题，作为链接文本',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => '链接',
                    'desc' => '广告的跳转链接',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
    ]
]);
