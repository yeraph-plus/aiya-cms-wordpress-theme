<?php if (is_home() && aya_opt('site_banner_load_bool', 'homepage')): ?>
    <!-- Banner image -->
    <div class="featured-banner">
        <div class="bg-cover bg-center bg-dark/10 flex items-center justify-center" style="
        background-image: url('<?php aya_echo(aya_opt('site_banner_image_upload', 'homepage')); ?>');
        height: <?php aya_echo(aya_opt('site_banner_height_text', 'homepage')); ?>
        ">
            <?php
            switch (aya_opt('site_banner_content_type', 'homepage')):
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
            <span class="text-2xl md:text-3xl text-white font-medium uppercase"><?php aya_echo($banner_content); ?></span>
        </div>
    </div>
<?php endif; ?>