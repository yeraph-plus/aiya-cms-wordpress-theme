<?php
if (!defined('ABSPATH')) exit;

//启动框架
$AYF = new AYF();

//创建主题设置
AYF::new_opt(array(
    'title' => 'AIYA-CMS',
    'page_title' => '基本设置',
    'slug' => 'theme',
    'desc' => 'AIYA-CMS 主题，功能和必要组件设置',
    'fields' => array(
        array(
            'desc' => '静态文件设置',
            'type' => 'title_2',
        ),
        array(
            'title' => '静态文件加载位置',
            'desc' => '设置全站静态文件加载方式，使用CDN或从本地加载',
            'id' => 'site_load_script',
            'type' => 'radio',
            'sub'  => array(
                'local' => '本地加载',
                'cdnjs' => 'CloudFlare',
                'zstatic' => 'Zstatic CDN',
                'bootcdn' => 'Boot CDN',
            ),
            'default' => 'local',
        ),
        array(
            'title' => '启用 jQuery.js 组件',
            'desc' => '设置是否加载 jQuery 和主题 jQ 组件',
            'id' => 'site_jquery_type',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'desc' => 'LOGO设置',
            'type' => 'title_2',
        ),
        array(
            'title' => ' LOGO 图片',
            'desc' => ' LOGO 使用的图片地址Url',
            'id' => 'site_logo_image',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/logo.png',
        ),
        array(
            'title' => '显示站点名称',
            'desc' => ' LOGO 位置显示站点名称，如果使用图标 LOGO 时，可以切换LOGO部分文字显示',
            'id' => 'site_logo_text',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'desc' => '主题默认图片设置',
            'type' => 'title_2',
        ),
        array(
            'title' => '加载动画',
            'desc' => '选择默认的 SVG 动画',
            'id' => 'site_default_selector',
            'type' => 'radio',
            'sub'  => array(
                'audio' => 'Audio',
                'bars' => 'Bars',
                'oval' => 'Oval',
                'puff' => 'Puff',
                'spinning-circles' => 'Spinning Circles',
                'tail-spin' => 'Tail Spin',
                'three-dots' => 'Three Fots',
                'custom' => '自定义',
            ),
            'default' => 'three-dots',
        ),
        array(
            'title' => '自定义加载动画',
            'desc' => '覆盖上一项设置，上传自定义的 SVG 或 GIF 动画',
            'id' => 'site_custom_selector',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => '',
        ),
        array(
            'title' => '默认文章缩略图',
            'desc' => '上传文章默认缩略图',
            'id' => 'site_thumb_default',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/thumb-default.png',
        ),
        array(
            'title' => '列表为空占位图',
            'desc' => '上传文章列表为空时卡片的占位图片',
            'id' => 'site_none_page_img',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/paper_plane.png',
        ),
        array(
            'title' => ' 404 页面占位图',
            'desc' => '上传 404 页面卡片的占位图片',
            'id' => 'site_404_page_img',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/paper_plane.png',
        ),
        array(
            'desc' => '页脚信息',
            'type' => 'title_2',
        ),
        array(
            'title' => '站长邮箱',
            'desc' => '在页脚显示站点管理员联系邮箱（自动转换为HTML实体编码格式，请勿重复操作）',
            'id' => 'site_master_email',
            'type' => 'text',
            'default' => '',
        ),
        array(
            'title' => 'ICP备案',
            'desc' => '根据《互联网信息服务管理办法》要求，设置网站 ICP 备案信息',
            'id' => 'site_icp_beian',
            'type' => 'text',
            'default' => '', // 没ICP备11451419号-0（雾）
        ),
        array(
            'title' => '公安网备',
            'desc' => '根据《计算机信息网络国际联网安全保护管理办法》要求，设置网站公网安备信息',
            'id' => 'site_mps_beian',
            'type' => 'text',
            'default' => '',
        ),
    ),
));
