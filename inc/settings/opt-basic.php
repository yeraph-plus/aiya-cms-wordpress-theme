<?php

if (!defined('ABSPATH')) {
    exit;
}


/**
 * NOTE:
 * 
 * 多选项标记为_type
 * 单选项标记为_bool
 * 文本框标记为_text
 * 上传文件标记为_upload
 * 自增列表标记为_list
 * 
 * <del>省得前台调用时候忘判断类型</del>
 */

//创建主题设置
AYF::new_opt([
    'title' => 'AIYA-CMS',
    'page_title' => '基本设置',
    'slug' => 'basic',
    'desc' => 'AIYA-CMS 主题，首选项设置',
    'fields' => [
        [
            'desc' => '站点设置',
            'type' => 'title_1',
        ],
        /*
        [
            'title' => '静态文件加载位置',
            'desc' => '设置全站静态文件加载方式，使用CDN或从本地加载',
            'id' => 'site_scripts_load_type',
            'type' => 'radio',
            'sub'  => [
                'local' => '本地加载',
                'cdnjs' => 'CloudFlare',
                'zstatic' => 'ZstaticCDN',
                'bootcdn' => 'BootCDN',
            ],
            'default' => 'cdnjs',
        ],
        */
        [
            'title' => ' LOGO 图片',
            'desc' => ' LOGO 使用的图片地址Url',
            'id' => 'site_logo_image_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => get_template_directory_uri() . '/assets/image/logo.png',
        ],
        [
            'title' => '显示站点名称',
            'desc' => '是否在 LOGO 图片后显示站点名称',
            'id' => 'site_logo_text_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '默认文章缩略图',
            'desc' => '上传文章默认缩略图',
            'id' => 'site_default_thumb_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => get_template_directory_uri() . '/assets/image/default-thumb.png',
        ],
        [
            'title' => '默认使用主题模式',
            'desc' => '设置站点用户首次初始化时加载的配色模式',
            'id' => 'site_default_color_mode_type',
            'type' => 'radio',
            'sub'  => [
                'system' => '系统自动',
                'dark' => '暗色',
                'light' => '亮色',
            ],
            'default' => 'system',
        ],
        [
            'desc' => '合规类功能设置',
            'type' => 'title_1',
        ],
        [
            'title' => '用户 Cookie 授权弹窗',
            'desc' => '用户首次访问网站时，会弹窗提示用户确认 Cookie 权限',
            'id' => 'site_cookie_consent_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '全局禁用评论',
            'desc' => '全局禁用所有文章的评论功能',
            'id' => 'site_comment_disable_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => 'ICP备案',
            'desc' => '根据《互联网信息服务管理办法》要求的网站 ICP 备案信息',
            'id' => 'site_icp_beian_text',
            'type' => 'text',
            'default' => 'ICP没备11451419号-19', // （雾）
        ],
        [
            'title' => '公安备案',
            'desc' => '根据《计算机信息网络国际联网安全保护管理办法》要求的网站公网安备信息',
            'id' => 'site_mps_beian_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => '公安备案记录代码',
            'desc' => '由于地域备案号展示要求不同，手动填写您的公安备案号纯数字部分',
            'id' => 'site_mps_code_text',
            'type' => 'text',
            'default' => '',
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
                    'sub'  => [
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
                    'sub'  => [
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
    ]
]);
