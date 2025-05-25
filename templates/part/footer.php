            </div>
        </main>
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
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M8.5 1.709a.75.75 0 0 0-1 0 8.963 8.963 0 0 1-4.84 2.217.75.75 0 0 0-.654.72 10.499 10.499 0 0 0 5.647 9.672.75.75 0 0 0 .694-.001 10.499 10.499 0 0 0 5.647-9.672.75.75 0 0 0-.654-.719A8.963 8.963 0 0 1 8.5 1.71Zm2.34 5.504a.75.75 0 0 0-1.18-.926L7.394 9.17l-1.156-.99a.75.75 0 1 0-.976 1.138l1.75 1.5a.75.75 0 0 0 1.078-.106l2.75-3.5Z" clip-rule="evenodd" />
                                </svg>
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