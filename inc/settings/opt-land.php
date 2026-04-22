<?php

if (!defined('ABSPATH')) {
    exit;
}

//创建主题设置
AYF::new_opt([
    'title' => __('页面组件', 'aiya-cms'),
    'page_title' => __('页面组件', 'aiya-cms'),
    'parent' => 'basic',
    'slug' => 'land',
    'desc' => __('AIYA-CMS 主题，页面组件设置', 'aiya-cms'),
    'fields' => [
        [
            'desc' => __('正文过滤器', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'title' => __('使用 DOMDocument 处理器', 'aiya-cms'),
            'desc' => __(' HTML 属性匹配更精准，但性能略差 [del]什么年代了还在用传统 preg_match_all()[/del]', 'aiya-cms'),
            'id' => 'site_content_dom_handler_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => __('添加图片描述', 'aiya-cms'),
            'desc' => __('文章正文内的图片标签自动添加 alt 属性和懒加载属性', 'aiya-cms'),
            'id' => 'site_content_img_filter_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => __('添加链接内链', 'aiya-cms'),
            'desc' => __('给文章正文内链接标签自动添加 [code]rel="nofollow"[/code] 和 [code]target="_blank"[/code] 属性（仅对外部链接生效）', 'aiya-cms'),
            'id' => 'site_content_link_filter_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => __('内链格式设置', 'aiya-cms'),
            'desc' => __('接上一项设置，可选跳转方式', 'aiya-cms'),
            'id' => 'site_content_link_jump_page_type',
            'type' => 'radio',
            'sub' => [
                'link' => __('提示外部页面', 'aiya-cms'),
                'go' => __('使用内链跳转', 'aiya-cms'),
                'false' => __('直接跳转（不处理）', 'aiya-cms'),
            ],
            'default' => 'link',
        ],
        [
            'desc' => __('内容组件', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'desc' => __('设置文章列表输出的布局模板，文章列表的输出数量请在站点 [url=options-reading.php]阅读设置[/url] 中调整', 'aiya-cms'),
            'type' => 'message',
        ],
        [
            'title' => __('文章时效性提示', 'aiya-cms'),
            'desc' => __('在文章发布或更新时间超过天数后显示内容过期提示，留空时禁用', 'aiya-cms'),
            'id' => 'site_post_outdate_days_text',
            'type' => 'text',
            'default' => '0',
        ],
        [
            'title' => __('文末声明', 'aiya-cms'),
            'desc' => __('在文章末尾输出版权声明等额外文本', 'aiya-cms'),
            'id' => 'site_post_statement_text',
            'type' => 'textarea',
            'default' => __('本站部分内容转载自网络，作品版权归原作者及来源网站所有，任何内容转载、商业用途等均须联系原作者并注明来源。', 'aiya-cms'),
        ],
        [
            'title' => __('加载文章提示组件', 'aiya-cms'),
            'desc' => __('创建新的分类法，在文章开头位置输出文章完成状态（更新中、已停更），或提示读者风险操作，等有用的提示', 'aiya-cms'),
            'id' => 'site_post_add_tips_terms_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => __('加载推文组件', 'aiya-cms'),
            'desc' => __('创建新的文章类型，以及推文组件页面', 'aiya-cms'),
            'id' => 'site_post_add_tweets_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => __('首页独立分区列表', 'aiya-cms'),
            'desc' => __('添加独立分区列表，显示不同的分类文章', 'aiya-cms'),
            'id' => 'site_post_home_section_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => __('标题', 'aiya-cms'),
                    'desc' => __('此项为空则自动分类名称', 'aiya-cms'),
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('分类', 'aiya-cms'),
                    'desc' => __('选择要显示的分类', 'aiya-cms'),
                    'id' => 'category_ids',
                    'type' => 'checkbox',
                    'sub_mode' => 'category',
                    'default' => '',
                ],
                [
                    'title' => __('查询排序方式', 'aiya-cms'),
                    'desc' => __('选择查询的文章排序方式', 'aiya-cms'),
                    'id' => 'orderby',
                    'type' => 'select',
                    'sub' => [
                        'date' => __('发布时间', 'aiya-cms'),
                        'title' => __('标题', 'aiya-cms'),
                        'modified' => __('最后修改日期', 'aiya-cms'),
                        'rand' => __('随机', 'aiya-cms'),
                        'none' => __('不排序', 'aiya-cms'),
                    ],
                    'default' => 'date',
                ],
                [
                    'title' => __('查询数量', 'aiya-cms'),
                    'desc' => __('选择查询的文章数量', 'aiya-cms'),
                    'id' => 'limit',
                    'type' => 'radio',
                    'sub' => [
                        '5' => __('5条', 'aiya-cms'),
                        '10' => __('10条', 'aiya-cms'),
                        '20' => __('20条', 'aiya-cms'),
                    ],
                    'default' => '10',
                ],
            ],
        ],
        [
            'desc' => __('轮播设置', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'title' => __('轮播组件外观切换', 'aiya-cms'),
            'desc' => __('切换或关闭首页轮播，CMS模式为至少需要5篇文章，宽幅模式为至少需要1篇文章', 'aiya-cms'),
            'id' => 'site_carousel_section_type',
            'type' => 'radio',
            'sub' => [
                'off' => __('关闭', 'aiya-cms'),
                'full' => __('经典（宽幅模式）', 'aiya-cms'),
                'mosaic' => __('拼贴（三卡片）', 'aiya-cms'),
                'cms' => __('三栏位（CMS模式）', 'aiya-cms'),
            ],
            'default' => 'off',
        ],
        [
            'title' => __('轮播列表', 'aiya-cms'),
            'desc' => __('添加轮播列表', 'aiya-cms'),
            'id' => 'site_carousel_section_item_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => __('链接', 'aiya-cms'),
                    'desc' => __('填入任意URL、或填写文章ID或文章别名 [b]*Tips: 不支持识别分类或标签[/b]', 'aiya-cms'),
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('图片', 'aiya-cms'),
                    'desc' => __('轮播卡片的大图，留空时自动获取文章特色图片', 'aiya-cms'),
                    'id' => 'thumbnail',
                    'type' => 'upload',
                    'default' => '',
                ],
                [
                    'title' => __('标题', 'aiya-cms'),
                    'desc' => __('轮播卡片的标题，留空时自动获取文章标题', 'aiya-cms'),
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('描述', 'aiya-cms'),
                    'desc' => __('轮播卡片的描述，留空时自动获取文章摘要', 'aiya-cms'),
                    'id' => 'description',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'desc' => __('页面广告位（AD）', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'title' => __('全局顶部', 'aiya-cms'),
            'desc' => __('广告位在页面顶部显示', 'aiya-cms'),
            'id' => 'site_ad_home_before_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => __('标题', 'aiya-cms'),
                    'desc' => __('广告标题，作为链接文本', 'aiya-cms'),
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('链接', 'aiya-cms'),
                    'desc' => __('广告的跳转链接', 'aiya-cms'),
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('广告视图', 'aiya-cms'),
                    'desc' => __('广告的图片链接 ', 'aiya-cms'),
                    'id' => 'view',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => __('全局底部', 'aiya-cms'),
            'desc' => __('广告位在页面底部显示', 'aiya-cms'),
            'id' => 'site_ad_home_after_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => __('标题', 'aiya-cms'),
                    'desc' => __('广告标题，作为链接文本', 'aiya-cms'),
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('链接', 'aiya-cms'),
                    'desc' => __('广告的跳转链接', 'aiya-cms'),
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('广告视图', 'aiya-cms'),
                    'desc' => __('广告的图片链接 ', 'aiya-cms'),
                    'id' => 'view',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => __('文内顶部', 'aiya-cms'),
            'desc' => __('广告位在文章顶部显示（仅文本链接）', 'aiya-cms'),
            'id' => 'site_ad_post_before_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => __('标题', 'aiya-cms'),
                    'desc' => __('广告标题，作为链接文本', 'aiya-cms'),
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('链接', 'aiya-cms'),
                    'desc' => __('广告的跳转链接', 'aiya-cms'),
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => __('文内底部', 'aiya-cms'),
            'desc' => __('广告位在文章底部显示（仅文本链接）', 'aiya-cms'),
            'id' => 'site_ad_post_after_mult',
            'type' => 'group_mult',
            'sub_type' => [
                [
                    'title' => __('标题', 'aiya-cms'),
                    'desc' => __('广告标题，作为链接文本', 'aiya-cms'),
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'title' => __('链接', 'aiya-cms'),
                    'desc' => __('广告的跳转链接', 'aiya-cms'),
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
    ]
]);
