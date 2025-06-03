<?php

if (!defined('ABSPATH')) {
    exit;
}

$banner_type = aya_opt('site_banner_template_type', 'land');

//仅在首页显示横幅组件
if (aya_is_where() !== 'home' || $banner_type == 'off') {
    return '';
}

//背景图设置
$bg_image = aya_opt('site_banner_bg_upload', 'land');
$bg_color = aya_opt('site_banner_bg_color', 'land');

$overlay_opacity = aya_opt('site_banner_bg_opacity', 'land');
$overlay_style = 'opacity: ' . (intval($overlay_opacity) / 100) . ';';

$style = '';
$style .= ($bg_image !== '') ? 'background-image: url(' . esc_url($bg_image) . '); ' : '';
$style .= ($bg_color !== '') ? 'background-color: ' . esc_attr($bg_color) . '; ' : '';

?>
<!-- Banner Container -->
<?php if ($banner_type === 'custom'):
    $opt_group = aya_opt('site_banner_custom_group', 'land');

    //横幅高度
    $style .= 'height: ' . ($opt_group['element_height'] ?? '') . ';';

    //横幅文本
    if (!empty($opt_group['hitokoto_bool'])) {
        $content = aya_curl_get_hitokoto();
    } else {
        $content = $opt_group['content_text'] ?? '';
    }
    ?>
    <div class="banner-wrap mb-4 relative w-full bg-cover bg-center " style="<?php aya_echo($style); ?>">
        <div class="absolute inset-0 bg-black" style="<?php aya_echo($overlay_style); ?>"></div>
        <div class="relative z-10 h-full flex items-center justify-center">
            <span class="text-2xl md:text-3xl text-white font-medium uppercase"><?php aya_echo($content); ?></span>
        </div>
    </div>
<?php elseif ($banner_type === 'welcome'):
    $opt_group = aya_opt('site_banner_welcome_group', 'land');
    //横幅文本
    $content = (!empty($opt_group['content_text'])) ? $opt_group['content_text'] : '';
    //横幅链接
    if (!empty($opt_group['button_link'])) {
        $button_link = '<a href="' . esc_url($opt_group['button_link']) . '" class="btn btn-primary" target="_blank" rel="noopener">' . __('立即查看', 'AIYA') . '</a>';
    }
    ?>
    <div class="banner-wrap mb-4 relative">
        <div class="absolute inset-0 bg-cover bg-center" style="<?php aya_echo($style); ?>"></div>
        <div class="absolute inset-0 bg-black" style="<?php aya_echo($overlay_style); ?>"></div>
        <div class="relative z-10 py-4 flex items-center justify-center w-full">
            <div class="container mx-auto px-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between text-white">
                    <div class="mb-6 md:mb-0">
                        <p class="text-lg opacity-90 max-w-xl"><?php aya_echo($content); ?></p>
                    </div>
                    <div class="flex justify-start md:justify-end">
                        <?php aya_echo($button_link ?? ''); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php elseif ($banner_type === 'hero'):
    $opt_group = aya_opt('site_banner_hero_group', 'land');
    //横幅标题
    $hero_title = (!empty($opt_group['title_text'])) ? $opt_group['title_text'] : '';
    //横幅文本
    $hero_content = (!empty($opt_group['content_text'])) ? $opt_group['content_text'] : '';
    //横幅链接
    $button_html = '';
    if (!empty($opt_group['btn_link_1'])) {
        $button_name = (!empty($opt_group['btn_text_1'])) ? $opt_group['btn_text_1'] : __('立即查看', 'AIYA');
        $button_html .= '<a href="' . esc_url($opt_group['btn_link_1']) . '" class="btn btn-primary" target="_blank" rel="noopener">' . esc_html($button_name) . '</a>';
    }
    if (!empty($opt_group['btn_link_2'])) {
        $button_name = (!empty($opt_group['btn_text_2'])) ? $opt_group['btn_text_2'] : __('了解更多', 'AIYA');
        $button_html .= '<a href="' . esc_url($opt_group['btn_link_2']) . '" class="btn btn-secondary btn-outline" target="_blank" rel="noopener">' . esc_html($button_name) . '</a>';
    }

    ?>
    <div class="hero min-h-screen mb-4" style="<?php aya_echo($style); ?>">
        <div class="hero-overlay" style="<?php aya_echo($overlay_style); ?>"></div>
        <div class="hero-content text-neutral-content text-center">
            <div class="max-w-md">
                <h1 class="mb-6 text-4xl font-bold">
                    <?php aya_echo($hero_title); ?>
                </h1>
                <p class="mb-6">
                    <?php aya_echo($hero_content); ?>
                </p>
                <div class="flex justify-center gap-4">
                    <?php aya_echo($button_html ?? ''); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>