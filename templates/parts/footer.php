<div class="footer p-4">
    <div class="flex flex-wrap">
        <div class="w-full lg:w-3/4 flex flex-col justify-end">
            <?php if (aya_opt('site_master_email_text', 'basic') != ''): ?>
                <p class="h-10 flex items-center">
                    <i data-feather="mail" width="16" height="16" class="mr-1"></i><span class="mr-1">Master Email:</span>
                    <?php echo antispambot(wp_kses(aya_opt('site_master_email_text', 'basic'), 'post')); ?>
                </p>
            <?php endif; ?>
            <p class="h-15 dark:text-white-dark text-left rtl:text-right">
                AIYA-CMS &copy;<span id="footer-year">2023</span> All rights reserved. Powered by WordPress.
                <?php if (!defined('AYA_RELEASE')) {
                    aya_echo('<br />' . aya_sql_counter());
                } ?>
            </p>
        </div>
        <div class="w-full lg:w-1/4 flex flex-col justify-end items-end">
            <?php if (aya_opt('site_icp_beian_text', 'basic') != ''): ?>
                <p class="icp-beian h-10 flex items-center">
                    <i data-feather="shield" width="20" height="20" class="mr-1"></i>
                    <a href="https://beian.miit.gov.cn/" rel="noopener noreferrer" target="_blank">
                        <?php aya_echo(aya_opt('site_icp_beian_text', 'basic')); ?>
                    </a>
                </p>
            <?php endif; ?>
            <?php if (aya_opt('site_mps_beian_text', 'basic') != ''): ?>
                <p class="mps-beian h-10 flex items-center">
                    <i data-feather="shield" width="20" height="20" class="mr-1"></i>
                    <a href="http://www.beian.gov.cn/portal/registerSystemInfo" rel="noopener noreferrer" target="_blank">
                        <?php aya_echo(aya_opt('site_mps_beian_text', 'basic')); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>