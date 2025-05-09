<?php

//小工具：最近评论

class AYA_Widget_Comments extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-comment-newest',
            'title' => 'AIYA-CMS 最近评论',
            'classname' => 'widget-panel',
            'desc' => '评论列表卡片，显示最近评论',
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

        $args = array(
            'number' => $limit,
            'status' => 'approve',
            'author__not_in' => 1
        );

        $html = '';
        $html .= '<div class="space-y-4">';

        global $comment;

        $comments = get_comments($args);

        //生成评论列表
        foreach ($comments as $key => $comment) {
            $html .= '<div class="relative flex overflow-hidden">';
            $html .= '<div class="w-12 h-12 rounded-full bg-white shadow dark:bg-[#0e1726] dark:shadow-none p-1 mr-2"><img class="rounded-full" src="' . get_avatar_url($comment, 32) . '" alt="avatar"></div>';
            $html .= '<div class="flex-1 rounded-md bg-white shadow dark:bg-[#0e1726] dark:shadow-none p-4">';
            $html .= '<div class="flex items-center justify-between">';
            $html .= '<h2 class="text-sm font-medium">' . $comment->comment_author . '</h2>';
            $html .= '<small class="text-xs text-gray-600">' . __('发布于', 'AIYA') . human_time_diff(get_comment_date('U'), current_time('timestamp')) . __('前', 'AIYA') . '</small>';
            $html .= '</div>';
            $html .= '<p class="mt-2 text-sm text-white-dark">' . $comment->comment_content . '&nbsp;[<a href="' . get_comment_link($comment->comment_ID) . '" rel="nofollow">' . __('查看文章', 'AIYA') . '</a>]</p>';
            $html .= '</div></div>';
        }

        $html .= '</div>';

        aya_echo($html);
    }
}
