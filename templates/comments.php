<?php

if (!defined('ABSPATH')) {
    exit;
}

//仅在文章或页面中显示评论
if (!is_singular(['post', 'page']) || is_attachment() || is_preview()) {
    return '';
}
//获取用户登录状态
$user = wp_get_current_user();
$current_user = $user->exists() ? [
    'name' => $user->display_name,
    'email' => $user->user_email,
    'avatar' => get_avatar_url($user->ID, ['size' => 32]),
] : null;
//获取当前文章信息
$post = get_post();
$post_id = $post->ID;

$comment_settings = aya_get_comments_settings();
$comments_number = get_comments_number($post);
$comments_open = comments_open($post_id);
?>
<div id="comments" class="comments-area bg-base-100 border border-base-300 rounded-lg p-4">
    <h3 class="comments-title text-lg font-bold mb-4">
        <?php aya_echo(__('评论区', 'AIYA')); ?>
    </h3>
    <?php if (post_password_required($post)): ?>
        <div class="comments-protected flex justify-center p-6 bg-base-200 rounded-lg">
            <p class="text-center text-base-content/70">
                <?php aya_echo(__('该内容受密码保护，请输入密码查看评论', 'AIYA')); ?>
            </p>
        </div>
    <?php else: ?>
        <!-- Comment Submit -->
        <?php if (!$comments_open && $comments_number == 0): ?>
            <div class="comments-closed flex justify-center p-6 mb-8 bg-base-200 rounded-lg">
                <p class="text-center text-base-content/70">
                    <?php aya_echo(__('评论已关闭', 'AIYA')); ?>
                </p>
            </div>
        <?php elseif (
            $current_user === null &&
            $comment_settings['comment_registration']
        ): ?>
            <?php $comments_open = false; ?>
            <div class="comments-must-login flex flex-wrap items-center justify-center gap-4 p-6 mb-8 bg-base-200 rounded-lg">
                <p class="text-center text-base-content/70">
                    <?php aya_echo(__('您必须登录后才能发表评论', 'AIYA')); ?>
                </p>
                <button type="button" class="btn btn-primary" onclick="window.LoginAction && window.LoginAction.showLogin()">
                    <?php aya_echo(__('登录', 'AIYA')); ?>
                </button>
            </div>
        <?php else: ?>
        <?php endif; ?>
        <?php aya_vue_load('comment', [
            'ajax-url' => aya_ajax_url(),
            'post-id' => $post_id,
            'comments-num' => $comments_number,
            'comments-open' => $comments_open,
            'current-user' => $current_user,
            'nonce' => aya_nonce_comments_submit(),
            'settings' => $comment_settings,
        ]); ?>
    <?php endif; ?>
</div>