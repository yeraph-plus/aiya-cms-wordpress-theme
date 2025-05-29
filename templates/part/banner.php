<?php
if (aya_is_where() === 'home' && get_query_var('page_slug_vars') == '' && aya_opt('site_banner_load_bool', 'land')):
    $style = '';
    $style .= 'background-image: url(' . aya_opt('site_banner_image_upload', 'land') . ');';
    $style .= 'height: ' . aya_opt('site_banner_height_text', 'land') . ';';

    switch (aya_opt('site_banner_content_type', 'land')):
        case 'false':
            $banner_content = '';
            break;
        case 'hitokoto':
            $banner_content = aya_curl_get_hitokoto();
            break;
        case 'custom':
            $banner_content = aya_opt('site_banner_content_text', 'homepage');
            break;
        default:
            $banner_content = '';
            break;
    endswitch;
    ?>
    <!-- Banner Image -->
    <div class="mb-4">
        <div class="bg-cover bg-center bg-dark/10 flex items-center justify-center" style="<?php aya_echo($style); ?>">
            <span class="text-2xl md:text-3xl text-white font-medium uppercase"><?php aya_echo($banner_content); ?></span>
        </div>
    </div>
<?php endif; ?>