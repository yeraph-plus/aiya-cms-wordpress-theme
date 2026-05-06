<?php

if (!defined('ABSPATH')) {
    exit;
}

aya_react_island('content-not-found', [
    'title' => __('页面未找到', 'aiya-cms'),
    'description' => __('抱歉，您访问的页面不存在或已被移除', 'aiya-cms'),
]);
