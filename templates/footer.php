<?php

if (!defined('ABSPATH')) {
  exit;
}

?>
                <!-- Ad Space -->
                <?php
                // 获取广告位
                $post_ads = aya_opt('site_ad_home_after_mult', 'land');

                if (!empty($post_ads) && is_array($post_ads)) {
                    aya_react_island('content-ad-space', ['ads' => array_values($post_ads), 'col' => 2]);
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
    <!-- Scroll Top -->
    <?php aya_react_island('ui-scroll-top', []); ?>
    <!-- Cookie Consent -->
    <?php
    if (aya_opt('site_cookie_consent_bool', 'basic')) {
        aya_react_island('ui-cookie-consent', ['policyUrl' => get_privacy_policy_url()]);
    }
    ?>
    <?php wp_footer(); ?>
    <footer class="bg-neutral-900/95 backdrop-blur supports-[backdrop-filter]:bg-neutral/60 text-neutral-400 py-12 transition-all duration-300 ease-in-out">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center md:justify-between gap-6">
            <div class="flex flex-wrap justify-center md:justify-start gap-x-6 gap-y-2">
                <?php
                // 获取页脚菜单
                $menu_items = aya_get_menu('footer-menu');
                // 确保返回的是菜单项数组且没有错误
                if (!empty($menu_items) && is_array($menu_items) && !isset($menu_items['error'])) {
                    // 限制最多输出7个菜单项
                    $count = 0;
                    foreach ($menu_items as $item) {
                        if ($count >= 7) break;

                        aya_echo('<a href="' . esc_url($item['url']) . '" class="hover:text-white transition-colors duration-200" title="' . esc_attr($item['label']) . '" target="' . esc_attr($item['target']) . '" >' . esc_html($item['label']) . '</a>');

                        $count++;
                    }
                }
                ?>
            </div>
            <div class="text-sm flex justify-center md:justify-end">
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
