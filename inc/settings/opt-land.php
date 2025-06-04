<?php

if (!defined('ABSPATH')) {
    exit;
}


//创建主题设置
AYF::new_opt([
    'title' => '首页组件',
    'parent' => 'basic',
    'slug' => 'land',
    'desc' => 'AIYA-CMS 主题，首页组件设置',
    'fields' => [
        [
            'desc' => '横幅设置（Banner）',
            'type' => 'title_1',
        ],
        [
            'title' => '显示横幅组件',
            'desc' => '开关横幅组件显示',
            'id' => 'site_banner_section_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '横幅组件显示',
            'desc' => '切换横幅组件的模板',
            'id' => 'site_banner_template_type',
            'type' => 'radio',
            'sub' => array(
                'custom' => '自定义横幅',
                'welcome' => '营销横幅（Welcome Banner）',
                'hero' => '首页视觉区块（Hero）',
            ),
            'default' => 'custom',
        ],
        [
            'title' => '背景图片',
            'desc' => '上传页面横幅背景图片，建议图片宽度不低于 [code]1200px[/code] ',
            'id' => 'site_banner_bg_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/default-banner.png',
        ],
        [
            'title' => '背景滤镜',
            'desc' => '设置横幅组件背景滤镜，用于调整不同背景图下文字可读性，接受值范围 [code]0-100[/code]',
            'id' => 'site_banner_bg_opacity',
            'type' => 'text',
            'default' => '50',
        ],
        [
            'title' => '背景颜色',
            'desc' => '设置横幅组件背景颜色',
            'id' => 'site_banner_bg_color',
            'type' => 'color',
            'default' => '#53565C',
        ],
        [
            'title' => '自定义横幅模板',
            'desc' => '配置自定义横幅模板',
            'id' => 'site_banner_custom_group',
            'type' => 'group',
            'sub_type' => [
                [
                    'title' => '横幅高度',
                    'desc' => '横幅图片的高度，单位为像素，默认值为 [code]260px[/code]',
                    'id' => 'element_height',
                    'type' => 'text',
                    'default' => '260px',
                ],
                [
                    'title' => '随机一言',
                    'desc' => '显示随机一条一言，关闭时使用自定义文本',
                    'id' => 'hitokoto_bool',
                    'type' => 'switch',
                    'default' => true,
                ],
                [
                    'title' => '自定义文本',
                    'desc' => '横幅内容的文本，不需要时留空',
                    'id' => 'content_text',
                    'type' => 'text',
                    'default' => '',
                ],
            ],
        ],
        [
            'title' => '营销横幅模板',
            'desc' => '配置欢迎横幅模板',
            'id' => 'site_banner_welcome_group',
            'type' => 'group',
            'sub_type' => [
                [
                    'title' => '文本',
                    'desc' => '营销横幅的文本',
                    'id' => 'content_text',
                    'type' => 'text',
                    'default' => 'AIYA-CMS 主题现以 GPL v3 开源发布',
                ],
                [
                    'title' => '跳转链接',
                    'desc' => '营销横幅的链接地址，留空则不显示链接',
                    'id' => 'button_link',
                    'type' => 'text',
                    'default' => 'https://github.com/yeraph-plus/aiya-cms-wordpress-theme',
                ],

            ],
        ],
        [
            'title' => '区块横幅模板',
            'desc' => '配置区块横幅模板',
            'id' => 'site_banner_hero_group',
            'type' => 'group',
            'sub_type' => [
                [
                    'title' => '标题',
                    'desc' => '区块横幅的 H1 标题',
                    'id' => 'title_text',
                    'type' => 'text',
                    'default' => 'AIYA-CMS 主题 V2.1',
                ],
                [
                    'title' => '文本',
                    'desc' => '区块横幅的文本',
                    'id' => 'content_text',
                    'type' => 'textarea',
                    'default' => 'AIYA-CMS 主题现以 GPL v3 开源发布，全新基于 Vue 3 + Tailwind CSS 4 构建。',
                ],
                [
                    'title' => '跳转按钮 1',
                    'desc' => '区块横幅的链接地址，留空则不显示链接',
                    'id' => 'btn_text_1',
                    'type' => 'text',
                    'default' => '查看 Github 页面',
                ],
                [
                    'title' => '跳转链接 1',
                    'desc' => '区块横幅的链接地址，留空则不显示链接',
                    'id' => 'btn_link_1',
                    'type' => 'text',
                    'default' => 'https://github.com/yeraph-plus/aiya-cms-wordpress-theme',
                ],
                [
                    'title' => '跳转按钮 2',
                    'desc' => '区块横幅的链接地址，留空则不显示链接',
                    'id' => 'btn_text_2',
                    'type' => 'text',
                    'default' => '提交 Issues',
                ],
                [
                    'title' => '跳转链接 2',
                    'desc' => '区块横幅的链接地址，留空则不显示链接',
                    'id' => 'btn_link_2',
                    'type' => 'text',
                    'default' => 'https://github.com/yeraph-plus/aiya-cms-wordpress-theme/issues',
                ],
            ],
        ],
        [
            'desc' => '轮播设置（Carousel）',
            'type' => 'title_1',
        ],
        [
            'title' => '轮播组件显示',
            'desc' => '开关轮播显示',
            'id' => 'site_carousel_section_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '轮播组件外观切换',
            'desc' => '切换或关闭首页轮播，CMS模式为至少需要5篇文章，宽幅模式为至少需要1篇文章',
            'id' => 'site_carousel_template_type',
            'type' => 'radio',
            'sub' => [
                'full' => '经典（宽幅模式）',
                'cms' => '三栏位（CMS模式）',
                'mosaic' => '拼贴（四卡片）',
            ],
            'default' => 'cms',
        ],
        [
            'title' => '轮播列表',
            'desc' => '添加轮播列表',
            'id' => 'site_carousel_post_list',
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
            'desc' => '侧边栏贴片（InfoBox）',
            'type' => 'title_1',
        ],
        [
            'title' => '显示贴片',
            'desc' => '开关贴片组件显示',
            'id' => 'site_info_box_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '贴片标题',
            'desc' => '贴片组件标题',
            'id' => 'site_info_title_text',
            'type' => 'text',
            'default' => 'AIYA-CMS Pro',
        ],
        [
            'title' => '贴片内容',
            'desc' => '贴片组件描述文本，支持HTML标签',
            'id' => 'site_info_desc_text',
            'type' => 'textarea',
            'default' => '一种很新的旧 WordPress 主题',
        ],
        /*
        [
            'desc' => '首页卡片（Section）',
            'type' => 'title_2',
        ],
        [
            'title' => '卡片列表',
            'desc' => '添加卡片列表',
            'id' => 'site_home_custom_list',
            'type' => 'group_mult',
            'sub_type' => array(
                array(
                    'title' => '启用',
                    'desc' => '在首页中显示',
                    'id' => 'type',
                    'type' => 'switch',
                    'default' => true,
                ),
                array(
                    'title' => '分类ID',
                    'desc' => '填入分类或自定义分类的ID <b>注意！不支持文章ID</b>',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ),
                array(
                    'title' => '标题',
                    'desc' => '此项为空则自动分类名称',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ),
                array(
                    'title' => '模式',
                    'desc' => '切换或关闭首页轮播，CMS模式为5卡片，宽幅模式为单独卡片',
                    'id' => 'mode',
                    'type' => 'radio',
                    'sub' => array(
                        'card' => '卡片',
                        'list' => '列表',
                        'fall' => '自适应（Masonry）',
                    ),
                    'default' => 'card',
                ),
            ),
        ],
        */
    ]
]);
