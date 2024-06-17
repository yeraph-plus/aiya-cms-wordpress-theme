<?php

$sticky_top = (aya_opt('site_header_sticky_top', 'layout')) ? 'banner-size-off' : 'banner-none';
?>
<session id="banner">
    <div class="<?php e_html($sticky_top); ?>"></div>
</session>