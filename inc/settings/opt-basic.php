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
    'title' => __('AIYA-CMS', 'aiya-cms'),
    'page_title' => __('基本设置', 'aiya-cms'),
    'slug' => 'basic',
    'desc' => __('AIYA-CMS 主题，首选项设置'),
    'fields' => [
        [
            'desc' => __('站点设置'),
            'type' => 'title_1',
        ],
        [
            'title' => __(' LOGO 图片'),
            'desc' => __(' LOGO 使用的图片地址Url'),
            'id' => 'site_logo_image_upload',
            'type' => 'upload',
            'button_text' => __('上传', 'aiya-cms'),
            'default' => get_template_directory_uri() . '/assets/image/logo.png',
        ],
        [
            'title' => __('显示站点名称'),
            'desc' => __('是否在 LOGO 图片后显示站点名称'),
            'id' => 'site_logo_text_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => __('默认文章缩略图'),
            'desc' => __('上传文章默认缩略图'),
            'id' => 'site_default_thumb_upload',
            'type' => 'upload',
            'button_text' => __('上传', 'aiya-cms'),
            'default' => get_template_directory_uri() . '/assets/image/default-thumb.png',
        ],
        [
            'title' => __('默认使用主题模式'),
            'desc' => __('设置站点用户首次初始化时加载的配色模式'),
            'id' => 'site_default_color_mode_type',
            'type' => 'radio',
            'sub'  => [
                'system' => __('系统自动', 'aiya-cms'),
                'dark' => __('暗色', 'aiya-cms'),
                'light' => __('亮色', 'aiya-cms'),
            ],
            'default' => 'system',
        ],
        [
            'desc' => __('合规类功能设置', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'title' => __('用户 Cookie 授权弹窗', 'aiya-cms'),
            'desc' => __('用户首次访问网站时，会弹窗提示用户确认 Cookie 权限', 'aiya-cms'),
            'id' => 'site_cookie_consent_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => __('全局禁用评论', 'aiya-cms'),
            'desc' => __('全局禁用所有文章的评论功能', 'aiya-cms'),
            'id' => 'site_comment_disable_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => __('ICP备案', 'aiya-cms'),
            'desc' => __('根据《互联网信息服务管理办法》要求的网站 ICP 备案信息', 'aiya-cms'),
            'id' => 'site_icp_beian_text',
            'type' => 'text',
            'default' => 'ICP没备11451419号-19', // （雾）
        ],
        [
            'title' => __('公安备案', 'aiya-cms'),
            'desc' => __('根据《计算机信息网络国际联网安全保护管理办法》要求的网站公网安备信息', 'aiya-cms'),
            'id' => 'site_mps_beian_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => __('公安备案记录代码', 'aiya-cms'),
            'desc' => __('由于地域备案号展示要求不同，手动填写您的公安备案号纯数字部分', 'aiya-cms'),
            'id' => 'site_mps_code_text',
            'type' => 'text',
            'default' => '',
        ],
    ]
]);
