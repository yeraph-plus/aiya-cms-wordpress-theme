<?php

/**
 * AIYA-CMS
 * 
 * 评论表单模板 comments.php
 */

//获取主题设置
$input_from_url = false;

//评论信息表单
if ($input_from_url) :
    $comments_from_fields = array(
        'author' => '<span class="comment-form-author"><input id="author" class="blog-form-input" placeholder="昵称" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" /></span>',
        'email' => '<span class="comment-form-email"><input id="email" class="blog-form-input" placeholder="Email " name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" /></span>',
        'url' => '<span class="comment-form-url"><input id="url" class="blog-form-input" placeholder="网站地址" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></span>',
    );
else :
    $comments_from_fields = array(
        'author' => '<span class="comment-form-author"><input id="author" class="blog-form-input" placeholder="昵称" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" /></span>',
        'email' => '<span class="comment-form-email"><input id="email" class="blog-form-input" placeholder="Email " name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" /></span>',
    );
endif;
//评论表单
$comments_form_args = array(
    'label_submit' => '发布',
    'title_reply' => '',
    'comment_form_top' => '',
    'comment_notes_before' => '',
    'comment_notes_after' => '',
    'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true"></textarea></p>',
    'fields' => apply_filters('comment_form_default_fields', $comments_from_fields),
);
//评论列表
$comments_list_args = array(
    'style'         => 'ul',
    'short_ping'    => false,
    'reply_text'    => '回复',
    'avatar_size'   => 34,
    'format'        => 'html5',
);

?>
<!--Comments-->
<div class="comments-title d-flex flex-row align-items-center justify-content-start">
    <h3><i class="bi bi-pen me-2"></i> 发布评论</h3>
</div>
<div id="respond" class="comments-from card p-3 mb-2">
    <?php comment_form($comments_form_args); ?>
</div>
<div class="comments-title d-flex flex-row align-items-center justify-content-start">
    <h3><i class="bi bi-filter me-2"></i>评论<small>( <?php e_html(number_format_i18n(get_comments_number())); ?> )</small></h3>
</div>
<div id="comments" class="comments-area card p-3 mb-2">
    <?php
    if (have_comments() && get_comments_number() != 0) :
        e_html('<ul class="comment-list">');

        wp_list_comments($comments_list_args);

        e_html('</ul>');

        the_comments_pagination(array('prev_text' => '上一页', 'next_text' => '下一页', 'prev_next' => false,));
    else :
        e_html('<p class="no-comments">暂无评论</p>');
    endif;
    ?>
</div>