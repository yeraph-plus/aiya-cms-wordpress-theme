<?php

/**
 * 
 * NOTE: All wp-head info tag:
 * 
 * bloginfo('html_type');
 * bloginfo('charset');
 * bloginfo('description');
 * bloginfo('version');
 * bloginfo('pingback_url');
 * 
 * NOTE: 模板文件加载结构
 * cards / loop卡片组件
 * contents / 正文内容组件
 * parts / 页面复用组件
 * units / 可选组件
 * 其余为页面模板
 * 
 */

aya_template_load('header');

//aya_template_load('part/navbar');
?>
<div class="vue-app">
    <hello-world msg="header"></hello-world>
</div>
<!-- Page content here -->
<?php
aya_template_load('footer');
?>