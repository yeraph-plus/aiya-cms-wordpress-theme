<?php

$objectcache = WP_CONTENT_DIR.'/object-cache.php';
$objectcachesource = SUPERFASTSEO_PLUGIN_DIR.'object-cache.php';
$advancedcache = WP_CONTENT_DIR.'/advanced-cache.php';
$advancedcachesource = SUPERFASTSEO_PLUGIN_DIR.'advanced-cache.php';

//生成
if( !file_exists( $objectcache ) ){
    if( copy( $objectcachesource,$objectcache ) ){
        echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible" style="color:red;font-weight: bold;"><p>成功生成缓存！</p></div>';
    }
}
if( !file_exists( $advancedcache ) ){
    if( copy( $advancedcachesource,$advancedcache ) ){
        echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible" style="color:red;font-weight: bold;"><p>成功生成缓存！</p></div>';
    }
}

?>