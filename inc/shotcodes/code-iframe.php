<?php

if (!defined('ABSPATH')) {
    exit;
}

if (is_admin()) {

    AYA_Shortcode::shortcode_register('hidden-content', array(
        'id' => 'sc-display-hide',
        'title' => '隐藏文本段',
        'note' => '包含在此简码内的文本不会在前台显示，用于充当文章编辑时的注释，或隐藏一段文本',
        'template' => '[hide {{attributes}}]<br/> {{content}} <br/>[/hide]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '内容',
                'desc' => '在这里输入要隐藏的文本',
                'default' => '隐藏的文本段。',
            ],
            [
                'id' => 'inline',
                'type' => 'checkbox',
                'label' => '作为注释输出',
                'desc' => '使当前段落作为 html 注释输出到前台页面，或者完全不输出',
                'default' => false,
            ]
        )
    ));
}