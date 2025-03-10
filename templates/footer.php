<?php

//设置
$settings = array(
    'siteRouter' => aya_is_where(),
    'navbarMenu' => aya_opt('site_nav_position_type', 'basic'),
    'navbarSticky' => aya_opt('site_nav_sticky_type', 'basic'),
    'colorScheme' => aya_opt('site_dark_scheme_type', 'basic'),
    'rtlClass' => (aya_opt('site_rtl_direction_bool', 'basic')) ? 'rtl' : 'ltr',
    'bodyLayout' => (aya_opt('site_boxed_layout_bool', 'basic')) ? 'boxed-layout' : 'full',
    'animation' => 'animate__' . aya_opt('site_router_trans_type', 'basic'),
    'loopGridCol' => aya_opt('site_loop_width_type', 'basic'),
    'colorSemidark' => false,
    'themeCustomizer' => (aya_opt('site_theme_customizer_bool', 'basic')) ? true : false,
);
//AJAX
$ajax_obj = array(
    'home' => home_url(),
    'url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('ajax_nonce'),
);

//页脚
aya_template_load('parts/footer');
?>
        </div>
    </div>
<?php
//其他组件
//aya_template_load('units/hover-cookie-consent');
aya_template_load('units/theme-customizer');
aya_template_load('units/top-button');
//模板底部动作钩子
aya_body_end();
?>
<?php
//The wp_footer action
wp_footer();
?>
<script type="text/javascript">
    const $settingsConfig = <?php aya_json_echo($settings); ?>;
    const $ajaxObj = <?php aya_json_echo($ajax_obj); ?>;
    const $siteNotification = <?php aya_notify_list_data() ?>;
    const $userLogindata = <?php aya_user_login_in_data() ?>;
</script>

</body>

</html>