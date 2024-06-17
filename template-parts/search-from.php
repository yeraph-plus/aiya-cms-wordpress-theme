<?php

/**
 * AIYA-CMS
 * 
 * 通用搜索框模板
 */

?>
<form role="search" method="get" id="search-form" class="d-flex search-form" action="/" type="text">
    <input type="text" class="form-control search-input" required="required" value="" name="s" placeholder="<?php e_html(__('搜索内容', 'AIYA')); ?>">
    <button type="submit" class="search-submit trans-percent" id="search-submit"><i class="bi bi-search"></i></button>
</form>