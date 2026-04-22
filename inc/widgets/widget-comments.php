<?php

//小工具：最近评论

class AYA_Widget_Comments extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-comment-newest',
            'title' => __('AIYA-CMS 最近评论', 'aiya-cms'),
            'classname' => 'widget-panel',
            'desc' => __('评论列表卡片，显示最近评论', 'aiya-cms'),
            'field_build' => array(
                array(
                    'type' => 'input',
                    'id' => 'title',
                    'name' => __('标题', 'aiya-cms'),
                    'default' => __('最近评论', 'aiya-cms'),
                ),
                array(
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => __('显示数量', 'aiya-cms'),
                    'default' => '10',
                ),
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $title = parent::widget_opt('title');
        $limit = parent::widget_opt('limit');

        $args = array(
            'number' => $limit,
            'status' => 'approve',
            'author__not_in' => true
        );

        $comments_data = [];
        $comments = get_comments($args);
//您在本站有<?php echo comment_count($comment['comment_author_email'], true); 条评论
        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $comments_data[] = [
                    'id' => $comment->comment_ID,
                    'author' => get_comment_author($comment),
                    'avatar' => get_avatar_url($comment, ['size' => 64]),
                    'content' => wp_trim_words(get_comment_text($comment), 20),
                    'date' => human_time_diff(get_comment_date('U', $comment), current_time('timestamp')) . '前',
                    'url' => get_comment_link($comment),
                    'post_title' => get_the_title($comment->comment_post_ID),
                ];
            }
        }

        aya_react_island(
            'widget-comments',
            ['comments' => $comments_data, 'widgetTitle' => $title]
        );
    }
}
