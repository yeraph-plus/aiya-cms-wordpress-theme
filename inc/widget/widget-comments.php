<?php

//小工具：最近评论

class AYA_Widget_Comments extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-comments',
            'title' => 'AIYA-CMS 最近评论',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏近期评论组件',
            'field_build' => array(
                array(
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => '显示数量',
                    'default' => '5',
                ),
            ),
        );

        return $widget_args;
    }
    function widget_func()
    {
        $limit = parent::widget_opt('limit');
        echo '<ul class="widget-comments p-0 m-0">';
        $args = array(
            'number' => $limit,
            'status' => 'approve',
            'author__not_in' => 1
        );
        $comments = get_comments($args);
        global $comment;
        //生成文章列表
        foreach ($comments as $key => $comment) {
            echo '<li>' . get_avatar($comment, 32) . '
                    <div class="comment-loop">
                        <span>' . $comment->comment_author . '&nbsp;发布于' . human_time_diff(get_comment_date('U'), current_time('timestamp')) . '前</span>
                        <div class="comment"><a href="' . get_comment_link($comment->comment_ID) . '" rel="nofollow">' . $comment->comment_content . '</a></div>
                    </div></li>';
        }
        echo '</ul>';
    }
}
