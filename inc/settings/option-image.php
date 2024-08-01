<?php
if (!defined('ABSPATH')) exit;

//主题外观设置
AYF::new_opt(array(
    'title' => '图像设置',
    'parent' => 'theme',
    'slug' => 'image',
    'desc' => 'AIYA-CMS 主题，图像生成器的相关设置',
    'fields' => array(
        array(
            'desc' => '正文格式',
            'type' => 'title_2',
        ),
    ),
));
