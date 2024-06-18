<?php
//保护后台登录
$text_one = get_option('text_one');
$text_two = get_option('text_two');
$new_login_url = 'Location:'.site_url();
add_action('login_enqueue_scripts','superfastseo_login_protection');  
function superfastseo_login_protection(){  
    if( $_GET[$text_one] != $text_two )header($new_login_url);  
}
?>