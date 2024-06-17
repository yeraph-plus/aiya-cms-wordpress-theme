<?php
if (!defined('ABSPATH')) exit;

//主题外观设置
AYF::new_opt(array(
    'title' => '外观设置',
    'parent' => 'theme',
    'slug' => 'color',
    'desc' => 'AIYA-CMS 主题，外观样式和配色切换',
    'fields' => array(
        array(
            'desc' => '配色设置（Color）',
            'type' => 'title_2',
        ),
        array(
            'title' => '自动配色',
            'desc' => '设置页面中主要组件的颜色，层叠样式自动匹配',
            'id' => 'site_color_element',
            'type' => 'color',
            'default' => '#0d6efd',
        ),
        array(
            'desc' => '手动配色参数需为完整的主题参数，请见主题文档',
            'type' => 'message',
        ),
        array(
            'title' => '手动配色',
            'desc' => '自定义方式设置所有组件配色',
            'id' => 'site_color_custom_type',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'title' => '自定义样式表',
            'desc' => '自定义方式设置所有组件配色',
            'id' => 'site_color_custom',
            'type' => 'code_editor',
            'default' => aya_theme_head_css_custom_rule(1),
        ),
        array(
            'desc' => '正文样式设置',
            'type' => 'title_2',
        ),
        array(
            'title' => '正文文字颜色',
            'desc' => '设置正文文字的颜色，仅影响文内',
            'id' => 'site_main_color',
            'type' => 'color',
            'default' => '#222222',
        ),
        array(
            'title' => '正文文字大小',
            'desc' => '设置正文文字大小，仅影响文内，默认值<code>1rem</code>',
            'id' => 'site_main_size',
            'type' => 'text',
            'default' => '1rem',
        ),
        array(
            'title' => '正文文字行间距',
            'desc' => '设置正文文字行间距，仅影响文内，默认值<code>10px</code>',
            'id' => 'site_main_width',
            'type' => 'text',
            'default' => '10px',
        ),
        array(
            'desc' => '页面背景（Background）',
            'type' => 'title_2',
        ),
        array(
            'title' => '页面背景色',
            'desc' => '设置页面中主要组件的颜色，层叠样式自动匹配',
            'id' => 'site_background_color',
            'type' => 'color',
            'default' => '#ededed',
        ),
        array(
            'title' => '启用背景图片',
            'desc' => '自定义方式设置所有组件配色',
            'id' => 'site_background_type',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'title' => '全局背景图片',
            'desc' => '上传页面背景图片，不需要背景图时此项留空即可',
            'id' => 'site_background_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/bg-dp.png',
        ),
        array(
            'title' => '背景图平铺',
            'desc' => '开关页面背景图片自动拉伸（开启后自动铺满背景）',
            'id' => 'site_background_center',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'title' => '背景图遮罩',
            'desc' => '开关页面背景图片的遮罩层',
            'id' => 'site_background_after',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'desc' => '夜间模式（DarkMode）',
            'type' => 'title_2',
        ),
        array(
            'title' => '默认暗色模式',
            'desc' => '默认页面配色为暗色模式',
            'id' => 'site_dark_mode_type',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'desc' => '页面特效',
            'type' => 'title_2',
        ),
        array(
            'title' => '全局灰色特效',
            'desc' => '开关页面全局灰色特效',
            'id' => 'site_gray_mode_type',
            'type' => 'switch',
            'default' => false,
        ),
    ),
));
