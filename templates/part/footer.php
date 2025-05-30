            <!-- Scroll Top -->
            <?php aya_vue_load('scroll-to-top'); ?>
            <!-- Footer -->
            <footer class="bg-base-200 text-base-content transition-all duration-300 ease-in-out">
                <div class="container mx-auto p-4 flex flex-col md:flex-row items-center md:justify-between gap-4">
                    <div class="flex justify-center md:justify-start">
                        <?php aya_vue_load('nav-menu', ['menu' => aya_get_menu('end-menu'), 'dorpdown' => false]); ?>
                    </div>
                    <div class="text-sm flex justify-center md:justify-end">
                        <div class="flex flex-col items-center md:items-end gap-2">
                            <p>Copyright © {{new Date().getFullYear()}} Aiya-CMS All rights reserved.</p>
                            <?php if (aya_is_dev_mode()): ?>
                                <p class="text-xs"><?php aya_echo(aya_sql_counter()); ?></p>
                            <?php endif; ?>
                            <?php if (aya_opt('site_icp_beian_text', 'basic') !== ''): ?>
                                <!-- ICP -->
                                <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    <icon name="shield-check" class="size-4"></icon>
                                    <?php aya_echo(aya_opt('site_icp_beian_text', 'basic')); ?>
                                </a>
                            <?php endif; ?>
                            <?php if (aya_opt('site_mps_beian_text', 'basic') !== ''): ?>
                                <!-- MPS -->
                                <a href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=<?php aya_echo(aya_opt('site_mps_code_text', 'basic')); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">
                                    <?php aya_echo(aya_opt('site_mps_beian_text', 'basic')); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?php aya_home_end(); ?>
    <?php wp_footer(); ?>
</body>

</html>