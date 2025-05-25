<?php
if (!defined('ABSPATH'))
    exit;

//创建主题设置
AYF::new_opt([
    'title' => '首页轮播',
    'parent' => 'basic',
    'slug' => 'land',
    'desc' => 'AIYA-CMS 主题，首页组件设置',
    'fields' => [
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
            'desc' => '贴片组件描述文本',
            'id' => 'site_info_desc_text',
            'type' => 'textarea',
            'default' => '一种很新的旧 WordPress 主题',
        ],
        [
            'desc' => '横幅设置（Banner）',
            'type' => 'title_1',
        ],
        [
            'title' => '横幅组件显示',
            'desc' => '开关Banner图片显示',
            'id' => 'site_banner_load_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '横幅图片',
            'desc' => '上传页面横幅图片，建议图片宽度不低于 [code]1200px[/code] ',
            'id' => 'site_banner_image_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/default-banner.png',
        ],
        [
            'title' => '横幅高度',
            'desc' => '横幅图片的高度，单位为像素，默认值为 [code]260px[/code]',
            'id' => 'site_banner_height_text',
            'type' => 'text',
            'default' => '260px',
        ],
        [
            'title' => '横幅文本设置',
            'desc' => '设置横幅显示的文本，设置为一言或自定义',
            'id' => 'site_banner_content_type',
            'type' => 'radio',
            'sub' => array(
                'false' => '禁用',
                'hitokoto' => '一言',
                'custom' => '自定义',
            ),
            'default' => 'hitokoto',
        ],
        [
            'title' => '自定义文本',
            'desc' => '横幅标题的文本，仅设置为自定义时生效',
            'id' => 'site_banner_content_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'desc' => '轮播设置（Carousel）',
            'type' => 'title_1',
        ],
        [
            'title' => '轮播组件显示',
            'desc' => '开关轮播显示',
            'id' => 'site_carousel_load_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '轮播组件外观切换',
            'desc' => '切换或关闭首页轮播，CMS模式为至少需要5篇文章，宽幅模式为至少需要1篇文章',
            'id' => 'site_carousel_layout_type',
            'type' => 'radio',
            'sub' => array(
                'off' => '关闭（不显示）',
                'cms' => '三栏位（CMS模式）',
                'full' => '经典（宽幅模式）',
            ),
            'default' => 'off',
        ],
        [
            'title' => '轮播列表',
            'desc' => '添加轮播列表',
            'id' => 'site_carousel_post_list',
            'type' => 'group_mult',
            'sub_type' => array(
                array(
                    'title' => 'ID / URL',
                    'desc' => '填入任意URL，或文章ID',
                    'id' => 'url',
                    'type' => 'text',
                    'default' => '',
                ),
                array(
                    'title' => '标题',
                    'desc' => '设置轮播卡片的标题，使用文章ID时自动获取文章标题，可覆盖',
                    'id' => 'title',
                    'type' => 'text',
                    'default' => '',
                ),
                array(
                    'title' => '图片',
                    'desc' => '设置轮播卡片的图片，使用文章ID时自动获取文章图片',
                    'id' => 'img',
                    'type' => 'upload',
                    'default' => 'https://',
                ),
            ),
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
