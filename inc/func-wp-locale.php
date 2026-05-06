<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 主题语言包加载方法
 * ------------------------------------------------------------------------------
 */

add_filter('determine_locale', 'ayar_locale_auto_convert_filter', 1);
add_action('after_setup_theme', 'aya_theme_textdomain_load');

// 加载主题语言包
function aya_theme_textdomain_load()
{
    load_theme_textdomain(AYA_THEME_TEXTDOMAIN, get_template_directory() . '/languages');
    load_theme_textdomain(AYA_FRAMEWORK_TEXTDOMAIN, get_template_directory() . '/plugins/languages/');
}

// 获取浏览器语言
function aya_get_browser_locale()
{
    $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower((string) $_SERVER['HTTP_ACCEPT_LANGUAGE']) : '';

    if ($accept_language === '') {
        return false;
    }

    if (strpos($accept_language, 'zh-hk') !== false || strpos($accept_language, 'zh-mo') !== false || strpos($accept_language, 'zh-hant-hk') !== false) {
        return 'zh_HK';
    }

    if (strpos($accept_language, 'zh-tw') !== false || strpos($accept_language, 'zh-hant') !== false) {
        return 'zh_TW';
    }

    if (strpos($accept_language, 'zh-cn') !== false || strpos($accept_language, 'zh-sg') !== false || strpos($accept_language, 'zh-hans') !== false) {
        return 'zh_CN';
    }

    if (strpos($accept_language, 'en') !== false) {
        return 'en_US';
    }

    return false;
}

// 约束语言标记字段
function aya_normalize_locale($locale)
{
    $locale = str_replace('-', '_', (string) $locale);

    if (stripos($locale, 'zh_HK') === 0 || stripos($locale, 'zh_MO') === 0) {
        return 'zh_HK';
    }

    if (stripos($locale, 'zh_TW') === 0) {
        return 'zh_TW';
    }

    if (stripos($locale, 'en') === 0) {
        return 'en_US';
    }

    return 'zh_CN';
}

// 为用户判断主题的语言
function aya_get_user_locale()
{
    if (is_user_logged_in()) {
        return aya_normalize_locale(get_user_locale(get_current_user_id()));
    }

    $browser_locale = aya_get_browser_locale();

    if ($browser_locale !== false) {
        return aya_normalize_locale($browser_locale);
    }

    return aya_normalize_locale(get_locale());
}

// 过滤前台请求用于切换语言
function ayar_locale_auto_convert_filter($locale)
{
    if (is_admin()) {
        return $locale;
    }

    $normalized = aya_normalize_locale($locale);

    if ($normalized !== 'zh_CN') {
        return $normalized;
    }

    return aya_get_user_locale();
}

/*
 * ------------------------------------------------------------------------------
 * 提供给前端的语言字典数据
 * ------------------------------------------------------------------------------
 */

add_action('wp_enqueue_scripts', 'aya_theme_localize_translations', 99);

// 向前端传递翻译字典
function aya_theme_localize_translations()
{
    $config = [
        'locale' => aya_get_user_locale(),
        'translations' => aya_theme_frontend_translations_map(),
    ];

    if (!wp_script_is('aya-theme-i18n', 'registered')) {
        wp_register_script('aya-theme-i18n', '', [], null, false);
    }

    wp_enqueue_script('aya-theme-i18n');
    wp_localize_script('aya-theme-i18n', 'AIYACMS_I18N', $config);
}

function aya_theme_frontend_translations_map()
{
    return [
        //'保存' => __('保存', 'aiya-cms'),
        //'共 %d 条数据' => __('共 %d 条数据', 'aiya-cms'),
        'success' => __('成功', 'aiya-cms'),
        'info' => __('提示', 'aiya-cms'),
        'warning' => __('警告', 'aiya-cms'),
        'error' => __('错误', 'aiya-cms'),
        'message' => __('消息', 'aiya-cms'),
        'loading' => __('加载中...', 'aiya-cms'),
        'search' => __('搜索...', 'aiya-cms'),
        'mobile_navigation' => __('移动导航', 'aiya-cms'),
        'no_available_navigation_menu' => __('暂无可用导航菜单', 'aiya-cms'),
        'site_notification' => __('站点通知', 'aiya-cms'),
        'no_new_site_notification' => __('当前没有新的站点通知', 'aiya-cms'),
        'menu' => __('菜单', 'aiya-cms'),
        'cancel' => __('取消', 'aiya-cms'),
        'administrator' => __('管理员', 'aiya-cms'),
        'editor' => __('编辑', 'aiya-cms'),
        'author' => __('作者', 'aiya-cms'),
        'contributor' => __('贡献者', 'aiya-cms'),
        'subscriber' => __('用户', 'aiya-cms'),
        'guest' => __('访客', 'aiya-cms'),
        'sponsor' => __('会员', 'aiya-cms'),
        'return_to_home' => __('返回首页', 'aiya-cms'),
        // comment.tsx
        'name' => __('名称', 'aiya-cms'),
        'write_comment' => __('写下评论...', 'aiya-cms'),
        'submit' => __('提交', 'aiya-cms'),
        'reply' => __('回复', 'aiya-cms'),
        'replying_to' => __('回复给', 'aiya-cms'),
        'fetch_comments_failed' => __('获取评论失败', 'aiya-cms'),
        'load_comments_failed' => __('加载评论失败', 'aiya-cms'),
        'comment_content_required' => __('评论内容不能为空', 'aiya-cms'),
        'name_email_required' => __('名称和邮箱为必填项', 'aiya-cms'),
        'submit_comment_failed' => __('提交评论失败', 'aiya-cms'),
        'comment_submitted_pending' => __('评论已提交，等待审核。', 'aiya-cms'),
        'comment_submitted_success' => __('评论提交成功', 'aiya-cms'),
        'comments' => __('评论', 'aiya-cms'),
        'login_required_for_comment' => __('您必须登录后才能发表评论。', 'aiya-cms'),
        'no_comments_yet' => __('还没有评论，来抢沙发吧！', 'aiya-cms'),
        'leave_comment' => __('发表评论', 'aiya-cms'),
        'comments_closed' => __('评论已关闭。', 'aiya-cms'),
        // dialog-forgot-password.tsx
        'forgot_password' => __('忘记密码', 'aiya-cms'),
        'reset_password' => __('重置密码', 'aiya-cms'),
        'confirm_password' => __('确认密码', 'aiya-cms'),
        'page_expired' => __('页面已过期，请刷新页面重试', 'aiya-cms'),
        'request_failed' => __('请求失败', 'aiya-cms'),
        'reset_link_sent' => __('密码重置链接已发送到您的邮箱', 'aiya-cms'),
        'error_retry_later' => __('发生错误，请稍后重试', 'aiya-cms'),
        'forgot_password_title' => __('找回密码', 'aiya-cms'),
        'email_sent_success' => __('邮件发送成功', 'aiya-cms'),
        'forgot_password_description' => __('请输入您的注册邮箱，我们将向您发送重置密码的链接。', 'aiya-cms'),
        'reset_link_sent_to' => __('重置密码链接已发送至：', 'aiya-cms'),
        'check_email_instruction' => __('请查收邮件并按照提示重置密码。如果没有收到，请检查垃圾邮件箱。', 'aiya-cms'),
        'back_to_login' => __('返回登录', 'aiya-cms'),
        'enter_email' => __('请输入邮箱', 'aiya-cms'),
        'send_reset_link' => __('发送重置链接', 'aiya-cms'),
        // user-reset-password.tsx
        'reset_token_missing' => __('重置链接缺少验证令牌，请重新申请找回密码。', 'aiya-cms'),
        'api_config_missing_reset_link_validation' => __('接口配置缺失，无法校验重置链接。', 'aiya-cms'),
        'reset_link_invalid_or_expired' => __('重置链接无效或已过期', 'aiya-cms'),
        'enter_and_confirm_new_password' => __('请输入新密码并确认', 'aiya-cms'),
        'api_config_missing_reset_submit' => __('接口配置缺失，无法提交重置请求', 'aiya-cms'),
        'password_reset_failed' => __('密码重置失败', 'aiya-cms'),
        'password_reset_success_login_new_password' => __('密码已重置，请使用新密码登录', 'aiya-cms'),
        'reset_password_description_security_requirements' => __('请输入新的登录密码。密码至少需要 8 位，并同时包含字母和数字。', 'aiya-cms'),
        'validating_link' => __('正在验证链接', 'aiya-cms'),
        'checking_reset_link_validity' => __('请稍候，我们正在检查密码重置链接是否有效。', 'aiya-cms'),
        'link_unavailable' => __('链接不可用', 'aiya-cms'),
        'reset_link_invalid_or_expired_request_again' => __('重置链接无效或已过期，请重新申请找回密码。', 'aiya-cms'),
        'password_updated' => __('密码已更新', 'aiya-cms'),
        'return_home_login_with_new_password' => __('您现在可以返回首页，通过新的密码重新登录。', 'aiya-cms'),
        'enter_new_password' => __('请输入新密码', 'aiya-cms'),
        'please_confirm_password' => __('请确认密码', 'aiya-cms'),
        'please_enter_new_password_again' => __('请再次输入新密码', 'aiya-cms'),
        'save_new_password' => __('保存新密码', 'aiya-cms'),
        'back_to_home' => __('返回首页', 'aiya-cms'),
        // user-settings.tsx
        'update_failed' => __('更新失败', 'aiya-cms'),
        'profile_updated' => __('资料已更新', 'aiya-cms'),
        'update_error' => __('更新发生错误', 'aiya-cms'),
        'password_updated_relogin' => __('密码已更新，请重新登录', 'aiya-cms'),
        'user_settings' => __('用户设置', 'aiya-cms'),
        'edit_profile' => __('修改个人资料', 'aiya-cms'),
        'update_profile_and_account_settings' => __('更新您的个人信息和账户设置', 'aiya-cms'),
        'last_name' => __('姓氏', 'aiya-cms'),
        'last_name_placeholder' => __('姓', 'aiya-cms'),
        'first_name' => __('名字', 'aiya-cms'),
        'first_name_placeholder' => __('名', 'aiya-cms'),
        'nickname' => __('昵称', 'aiya-cms'),
        'display_nickname' => __('显示的昵称', 'aiya-cms'),
        'interface_language' => __('界面语言', 'aiya-cms'),
        'select_language' => __('选择语言', 'aiya-cms'),
        'email_address' => __('邮箱地址', 'aiya-cms'),
        'personal_website' => __('个人网站', 'aiya-cms'),
        'bio' => __('个人说明', 'aiya-cms'),
        'please_introduce_yourself' => __('请介绍一下自己...', 'aiya-cms'),
        'save_profile' => __('保存资料', 'aiya-cms'),
        'change_password' => __('修改密码', 'aiya-cms'),
        'change_password_security_tip' => __('为了您的账户安全，建议定期更换密码', 'aiya-cms'),
        'new_password' => __('新密码', 'aiya-cms'),
        'confirm_new_password' => __('确认新密码', 'aiya-cms'),
        'enter_new_password_again' => __('再次输入新密码', 'aiya-cms'),
        // dialog-login.tsx
        'login_success' => __('登录成功', 'aiya-cms'),
        'login_error' => __('登录发生错误', 'aiya-cms'),
        'login' => __('登录', 'aiya-cms'),
        'login_description' => __('请输入您的邮箱和密码登录。', 'aiya-cms'),
        'email' => __('邮箱', 'aiya-cms'),
        'login_email' => __('登录邮箱', 'aiya-cms'),
        'forgot_password_question' => __('忘记密码？', 'aiya-cms'),
        'login_password' => __('登录密码', 'aiya-cms'),
        'remember_me' => __('记住我', 'aiya-cms'),
        // dialog-logout.tsx
        'logout_success' => __('退出登录成功', 'aiya-cms'),
        'logout_failed' => __('退出登录失败', 'aiya-cms'),
        'confirm_logout' => __('确认退出', 'aiya-cms'),
        'logout_confirm_text' => __('确定要退出登录吗？', 'aiya-cms'),
        'logout' => __('退出登录', 'aiya-cms'),
        // dialog-register.tsx
        'password_mismatch' => __('两次输入的密码不一致', 'aiya-cms'),
        'register_failed' => __('注册失败', 'aiya-cms'),
        'register_success' => __('注册成功', 'aiya-cms'),
        'register_error' => __('注册发生错误', 'aiya-cms'),
        'register' => __('注册', 'aiya-cms'),
        'register_description' => __('请输入您的信息以创建新账户。', 'aiya-cms'),
        'username' => __('用户名', 'aiya-cms'),
        'your_username' => __('您的用户名', 'aiya-cms'),
        'your_email' => __('您的邮箱', 'aiya-cms'),
        'password' => __('密码', 'aiya-cms'),
        'confirm_your_password' => __('确认您的密码', 'aiya-cms'),
        // content-pagination.tsx
        'page_of_total' => __('第 %d 页，共 %d 页', 'aiya-cms'),
        // content-button-group.tsx
        'cancel_favorite' => __('已取消收藏', 'aiya-cms'),
        'favorite' => __('收藏', 'aiya-cms'),
        'favorited' => __('已收藏', 'aiya-cms'),
        'like' => __('点赞', 'aiya-cms'),
        'liked' => __('已赞', 'aiya-cms'),
        'login_first' => __('请先登录', 'aiya-cms'),
        'operation_failed' => __('操作失败', 'aiya-cms'),
        // user-favorites.tsx
        'cover' => __('封面', 'aiya-cms'),
        'none' => __('无', 'aiya-cms'),
        'time' => __('时间', 'aiya-cms'),
        'operation' => __('操作', 'aiya-cms'),
        'view_article' => __('查看文章', 'aiya-cms'),
        'view' => __('查看', 'aiya-cms'),
        'confirm_remove_selected_favorites' => __('确定要取消收藏选中的文章吗？', 'aiya-cms'),
        'removed_favorites_count_success' => __('成功取消收藏 %d 篇文章', 'aiya-cms'),
        'removed_favorites_count_failed' => __('取消收藏 %d 篇文章失败', 'aiya-cms'),
        'my_favorites' => __('我的收藏夹', 'aiya-cms'),
        'view_or_manage_favorites' => __('查看或管理收藏列表', 'aiya-cms'),
        'view_or_manage_your_favorites' => __('查看或管理您收藏的文章', 'aiya-cms'),
        'selected_items_count' => __('已选择 %d 项', 'aiya-cms'),
        'no_favorites' => __('暂无收藏', 'aiya-cms'),
        // navbar-mode-toggle.tsx
        'switch_to_dark_mode' => __('切换到深色模式', 'aiya-cms'),
        'switch_to_follow_system' => __('切换到跟随系统', 'aiya-cms'),
        'switch_to_light_mode' => __('切换到浅色模式', 'aiya-cms'),
        // footer-scroll-top.tsx
        'back_to_top' => __('返回顶部', 'aiya-cms'),
        // ad-space.tsx
        'external_links' => __('外部链接', 'aiya-cms'),
        'ad_space' => __('广告', 'aiya-cms'),
        // clipboard-selector.tsx
        'click_to_copy' => __('点击复制', 'aiya-cms'),
        'copied' => __('已复制', 'aiya-cms'),
        'copy_failed' => __('复制失败', 'aiya-cms'),
        // tweet-editor.tsx
        'api_config_missing' => __('接口配置缺失', 'aiya-cms'),
        'tweet_content_required' => __('帖子内容不能为空', 'aiya-cms'),
        'tweet_update_failed' => __('帖子更新失败', 'aiya-cms'),
        'tweet_publish_failed' => __('帖子发布失败', 'aiya-cms'),
        'tweet_updated' => __('帖子已更新', 'aiya-cms'),
        'tweet_published' => __('帖子已发布', 'aiya-cms'),
        'save_failed' => __('保存失败', 'aiya-cms'),
        'confirm_delete_tweet' => __('确定要删除这条推文吗？', 'aiya-cms'),
        'delete_tweet_failed' => __('删除帖子失败', 'aiya-cms'),
        'tweet_deleted' => __('帖子已删除', 'aiya-cms'),
        'all_tags' => __('全部', 'aiya-cms'),
        'title' => __('标题', 'aiya-cms'),
        'what_is_happening' => __('有什么新鲜事？', 'aiya-cms'),
        'insert_image' => __('插入图片', 'aiya-cms'),
        'upload_image' => __('上传图片', 'aiya-cms'),
        'insert_tag' => __('插入标签', 'aiya-cms'),
        'tag' => __('标签', 'aiya-cms'),
        'save_as_draft' => __('保存为草稿', 'aiya-cms'),
        'delete' => __('删除', 'aiya-cms'),
        'update' => __('更新', 'aiya-cms'),
        'publish' => __('发布', 'aiya-cms'),
        'collapse' => __('收起', 'aiya-cms'),
        'expand' => __('展开', 'aiya-cms'),
        // user-sponsor-activate.tsx
        'activate_success' => __('激活成功', 'aiya-cms'),
        'activate_failed' => __('激活失败', 'aiya-cms'),
        'check_network_and_retry' => __('请检查网络连接后重试', 'aiya-cms'),
        'redeem_code' => __('兑换码', 'aiya-cms'),
        'afdian' => __('爱发电', 'aiya-cms'),
        'patreon' => __('Patreon', 'aiya-cms'),
        'self_service_activation' => __('自助激活', 'aiya-cms'),
        'activate_sponsor_with_order_or_code' => __('使用您获得的订单号或激活码激活赞助者身份', 'aiya-cms'),
        'select_method' => __('选择方式', 'aiya-cms'),
        'enter_order_or_activation_code' => __('请输入您的订单号 / 激活码', 'aiya-cms'),
        'activate_now' => __('立即激活', 'aiya-cms'),
        // user-sponsor-dashboard.tsx
        'force_canceled' => __('被强制取消', 'aiya-cms'),
        'active' => __('生效中', 'aiya-cms'),
        'not_activated' => __('未激活', 'aiya-cms'),
        'online_payment' => __('在线支付', 'aiya-cms'),
        'gifted' => __('赠送', 'aiya-cms'),
        'other' => __('其他', 'aiya-cms'),
        'notice' => __('注意', 'aiya-cms'),
        'sponsor_access_force_canceled_contact_support' => __('您的赞助权限已被管理员强制取消，如有疑问请联系客服。', 'aiya-cms'),
        'current_subscription' => __('当前订阅', 'aiya-cms'),
        'used_access' => __('已使用访问', 'aiya-cms'),
        'used_sponsor_access_count_times' => __('已使用赞助权限访问 %d 次', 'aiya-cms'),
        'remaining_days' => __('剩余天数', 'aiya-cms'),
        'day_unit' => __('天', 'aiya-cms'),
        'valid_until_s' => __('有效期至 %s', 'aiya-cms'),
        'total_sponsorship' => __('累计赞助', 'aiya-cms'),
        'thank_you_for_support' => __('感谢您的支持', 'aiya-cms'),
        'historical_order_records' => __('历史订单记录', 'aiya-cms'),
        'view_all_sponsor_and_redeem_history' => __('查看您的所有赞助与兑换历史记录', 'aiya-cms'),
        'toggle' => __('切换', 'aiya-cms'),
        'order_number' => __('订单号', 'aiya-cms'),
        'order_source' => __('订单来源', 'aiya-cms'),
        'duration' => __('时长', 'aiya-cms'),
        'order_status' => __('订单状态', 'aiya-cms'),
        'order_created_time' => __('订单创建时间', 'aiya-cms'),
        'paid' => __('已支付', 'aiya-cms'),
        'no_order_records' => __('暂无订单记录', 'aiya-cms'),
        // user-sponsor-subscribe.tsx
        'user_sponsorship_plans' => __('用户赞助计划', 'aiya-cms'),
        'no_user_sponsorship_plans' => __('暂无用户赞助计划', 'aiya-cms'),
        'no_available_sponsorship_plans_please_check_later' => __('当前没有可用的赞助订阅方案，请稍后再来看看吧。', 'aiya-cms'),
        'go_to_third_party_payment_interface' => __('跳转到第三方支付接口', 'aiya-cms'),
        'go_to' => __('跳转', 'aiya-cms'),
        'processing' => __('正在处理', 'aiya-cms'),
        'please_complete_in_new_page' => __('请在新打开的页面中完成操作...', 'aiya-cms'),
        'close' => __('关闭', 'aiya-cms'),
        'refresh_page' => __('刷新页面', 'aiya-cms'),
        // widget.tsx
        'browse_articles_with_tag' => __('浏览和 %s 有关的文章', 'aiya-cms'),
        'welcome_user' => __('嗨！新朋友', 'aiya-cms'),
        'login_to_unlock_features' => __('登录以解锁更多功能，体验完整服务', 'aiya-cms'),
    ];
}
