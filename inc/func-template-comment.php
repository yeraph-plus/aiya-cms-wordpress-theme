<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 评论组件
 * ------------------------------------------------------------------------------
 */

//获取评论模板
function aya_comments_template()
{
    //检查评论开启
    if (is_attachment() || is_404() || post_password_required()) return;

    if (aya_opt('site_comment_disable_bool', 'basic', true) === false) return;

    if (comments_open() || get_comments_number()) {
        //输出（定义这个位置必须包含"/"）
        comments_template('/templates/comments.php', false);
    }
}

//强制排除评论表单站点字段
add_filter('comment_form_default_fields', function ($fields) {
    if (isset($fields['url'])) {
        unset($fields['url']);
    }
    return $fields;
});
