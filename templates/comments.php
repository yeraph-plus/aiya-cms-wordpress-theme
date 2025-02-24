<?php

/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 * 
 */

?>

<div id="comments" x-data="comments">
    <?php if (comments_open()): ?>
        <div id="respond" class="comments-respond panel w-full py-4 px-6 mt-4">
            <?php
            //评论表单字段
            $comments_args = array(
                'fields' => array(
                    'author' => '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" class="form-input" placeholder="' . __('昵称', 'AIYA') . ($req ? '*' : '') . '" required />',
                    'email'  => '<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" class="form-input" placeholder="' . __('Email', 'AIYA') . ($req ? '*' : '') . '" required />',
                    'cookies' => '<label class="flex items-center cursor-pointer"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" class="form-checkbox" value="yes" ' . empty($commenter['comment_author_email']) ? '' : 'checked="checked"' . ' /><span class="text-white-dark"">' . __('保存昵称、邮箱到浏览器，以便下次评论时使用。', 'AIYA') . '</span></label>',
                ),
                'comment_field' => '<span class="flex col-span-full"><textarea id="comment" name="comment" rows="3" class="form-textarea" placeholder="' . __('留下你的评论...', 'AIYA') . '" required ></textarea></span>',
                'must_log_in' => '<p class="flex col-span-full items-center font-medium text-base">' . __('要发表评论，请先登录。', 'AIYA') . '<a class="btn btn-primary btn-sm mx-1" href="' . wp_login_url(apply_filters('the_permalink', get_permalink())) . '">' . __('登录', 'AIYA') . '</a></p>',
                'logged_in_as'         => '<p class="flex col-span-full items-center font-medium text-base">' . __('已登录', 'AIYA') . '<a class="font-semibold mx-1" href="' . admin_url('profile.php') . '">' . $user_identity . '</a>' . __('，评论将使用当前用户信息。', 'AIYA') . '</p>',
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
                'id_form'              => 'comment',
                'id_submit'            => 'submit',
                'title_reply'          => __('发送评论', 'AIYA'),
                'title_reply_to'       => __('回复给 %s', 'AIYA'),
                'cancel_reply_link'    => '<span class="font-bold text-danger mx-2 hover:underline">' . __('取消回复', 'AIYA') . '</span>',
                'label_submit'         => __('发送', 'AIYA'),
            );
            //评论表单
            comment_form($comments_args);
            ?>
        </div>
    <?php endif; ?>
    <div id="comment-area" class="comments-list panel w-full py-4 px-6 mt-4">
        <h3 class="comment-list-title">
            <?php aya_echo(__('评论列表 ', 'AIYA')); ?>
            <span class="badge bg-primary text-white dark:bg-white-light dark:text-black mx-2">
                <?php aya_echo(number_format_i18n(get_comments_number())); ?>
            </span>
        </h3>
        <?php if (have_comments()): ?>
            <ol class="comment-list space-y-4">
                <?php
                //评论列表字段
                $comments_list_args = array(
                    'style' => 'ol',
                    'short_ping' => true,
                    'avatar_size' => 32,
                    'reply_text' => __('回复', 'AIYA'),
                );
                //评论列表
                wp_list_comments($comments_list_args);
                ?>
            </ol>
            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>
                <nav class="nav-pagination flex items-center" aria-label="Pagination">
                    <div class="flex flex-1 justify-start">
                        <?php aya_comment_pagination_item_link('prev'); ?>
                    </div>
                    <div class="flex flex-1 justify-end">
                        <?php aya_comment_pagination_item_link('next'); ?>
                    </div>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-center text-gray-500 dark:text-gray-400">
                <?php aya_echo(__('暂无评论', 'AIYA')); ?>
            </p>
        <?php endif; ?>
    </div>
</div>
<script>
</script>