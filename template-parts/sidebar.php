<?php

/**
 * AIYA-CMS
 * 
 * 小工具栏
 */
// 判断是否开启侧边栏
?>
<div class="sidebar-background"></div>
<div id="sidebar" class="sidebar my-4">
    <?php
    if (aya_page_type('home') || aya_page_type('archive')) :
        dynamic_sidebar('index-sitebar');
    else :
        dynamic_sidebar('single-sitebar');
    endif;
    ?>
</div>