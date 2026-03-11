<?php

if (!defined('ABSPATH')) {
    exit;
}

//如果全局禁用评论，不使用评论
if (aya_opt('site_comment_disable_bool', 'basic')) {
    return '';
}

//仅在文章或页面中显示评论
if (is_attachment() || is_preview()) {
    return '';
}

//获取当前文章信息
$post = get_post();
$post_id = $post->ID;

//如果文章设置了密码，不使用评论
if (post_password_required($post)) {
    return;
}

//获取用户登录状态
$user = wp_get_current_user();
$current_user = $user->exists() ? [
    'name' => $user->display_name,
    'email' => $user->user_email,
    'avatar' => get_avatar_url($user->ID, ['size' => 32]),
    'id' => $user->ID
] : null;

?>
<div id="comments" class="comments-area my-8">
    <?php aya_react_island('comment', [
        'postId' => $post_id,
        'commentsOpen' => comments_open($post_id),
        'commentsCount' => (int) get_comments_number($post),
        'currentUser' => $current_user,
        'settings' => aya_get_comments_settings(),
    ]); ?>
</div>