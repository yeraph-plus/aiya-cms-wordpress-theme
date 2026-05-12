<?php

if (!defined('ABSPATH')) {
    exit;
}

?>
<!-- Ad Space -->
<?php
// 获取广告位
$post_ads = aya_opt('site_ad_home_after_mult', 'land');

if (!aya_is_sponsor() && !empty($post_ads) && is_array($post_ads)) {
    aya_react_island('content-ad-space', ['ads' => array_values($post_ads)]);
}
?>
</main>
<div class="layout w-full lg:w-1/5">
    <div class="w-full h-full px-4 bg-secondary/50 backdrop-blur supports-[backdrop-filter]:bg-secondary/50">
        <?php aya_widget_sidebar(); ?>
    </div>
</div>
</div>
</div>
</div>
<?php wp_footer(); ?>
<!-- Cookie Consent -->
<?php
// Cookie 同意提示
if (aya_opt('site_cookie_consent_bool', 'basic')) {
    aya_react_island('footer-consent', [
        'slug' => 'cookie',
        'text' => [
            'title' => __('Cookie 使用提示', 'aiya-cms'),
            'description' => __('我们使用 Cookie 来提升您的浏览体验，分析网站流量并提供个性化内容。继续使用本网站即表示您同意我们使用 Cookie。', 'aiya-cms'),
            'moreUrl' => get_privacy_policy_url(),
            'moreText' => __('了解更多', 'aiya-cms'),
            'declineText' => __('拒绝', 'aiya-cms'),
            'acceptText' => __('同意', 'aiya-cms'),
        ],
    ]);
}

// 自定义弹窗
$consent_message = aya_consent_message();
foreach ($consent_message as $key => $note) {
    aya_react_island('footer-consent', [
        'slug' => $note['slug'],
        'text' => [
            'title' => $note['title'],
            'description' => $note['content'],
        ],
    ]);
}
?>
<!-- Scroll Top -->
<?php aya_react_island('footer-scroll-top', []); ?>
<footer class="bg-neutral-900/95 backdrop-blur supports-[backdrop-filter]:bg-neutral/60 text-neutral-400 py-12 transition-all duration-300 ease-in-out">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center md:justify-between gap-6">
        <div class="flex flex-wrap justify-center md:justify-start gap-x-6 gap-y-2 text-sm">
            <?php
            // 获取页脚菜单
            $menu_items = aya_get_menu('footer-menu');
            // 确保返回的是菜单项数组且没有错误
            if (!empty($menu_items) && is_array($menu_items) && !isset($menu_items['error'])) {
                // 限制最多输出7个菜单项
                $count = 0;
                foreach ($menu_items as $item) {
                    if ($count >= 7) break;

                    echo '<a href="' . esc_url($item['url']) . '" class="hover:text-white transition-colors duration-200" title="' . esc_attr($item['label']) . '" target="' . esc_attr($item['target']) . '" >' . esc_html($item['label']) . '</a>';

                    $count++;
                }
            }
            ?>
        </div>
        <div class="flex justify-center md:justify-end text-sm">
            <div class="flex flex-col items-center md:items-end gap-2">
                <div class="flex flex-wrap items-center justify-center md:justify-end gap-x-4 gap-y-2">
                    <?php if (aya_opt('site_icp_beian_text', 'basic') !== ''): ?>
                        <!-- ICP -->
                        <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors duration-200">
                            <?php aya_echo(aya_opt('site_icp_beian_text', 'basic')); ?>
                        </a>
                    <?php endif; ?>
                    <?php if (aya_opt('site_mps_beian_text', 'basic') !== ''): ?>
                        <!-- MPS -->
                        <a href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=<?php aya_echo(aya_opt('site_mps_code_text', 'basic')); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors duration-200">
                            <?php aya_echo(aya_opt('site_mps_beian_text', 'basic')); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto px-4 mt-6">
        <p class="text-neutral-500 text-xs text-right">Copyright © 2026 Aiya-CMS All rights reserved. <?php aya_echo(aya_is_debug() ? aya_sql_counter() : ''); ?>
        </p>
    </div>
</footer>
</body>

</html>