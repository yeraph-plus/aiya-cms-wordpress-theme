<?php

/**
 * AIYA-CMS
 * 
 * 页脚
 */

?>
<?php aya_footer_scroll_button(); ?>
</div>
<!--pjax-container-end-->
<!-- Footer -->
<footer id="footer" class="site-footer">
    <div class="container">
        <div class="row row-lg-eq-height">
            <div class="col-lg-9">
                <div class="footer-content">
                    <?php aya_header_logo(); ?>
                    <nav class="navbar navbar-expand-lg">
                        <?php aya_footer_menu_toggle(); ?>
                    </nav>
                    <p class="loadtime">
                        <?php aya_sql_counter(); ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="subscribe-background"></div>
                <div class="subscribe-content">
                    <?php aya_footer_beian(); ?>
                    <p>
                        Powered by AIYA-CMS .
                    </p>
                    <p class="copyright">
                        Copyright &copy;<script>
                            document.write(new Date().getFullYear());
                        </script> All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>