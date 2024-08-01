<?php
if (!defined('ABSPATH')) exit;

//AIYA-CMS 短代码组件：隐藏文字段

function hide_shortcode($atts = array(), $content = null)
{
    return '';
}

add_shortcode('hide', 'hide_shortcode');
