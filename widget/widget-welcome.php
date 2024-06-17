<?php
class AYA_Widget_User_Welcome extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-welcome',
            'title' => 'AIYA-CMS 用户面板',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏搜索组件',
        );

        return $widget_args;
    }
    function widget_func()
    {
        //未登录
        if (!is_user_logged_in()) {
            //查询访客评论
            if (!empty($comment['comment_author_email'])) {
                //嗨! $user
                //您在本站有<?php echo comment_count($comment['comment_author_email'], true); 条评论
            } else {
                //嗨! 新朋友
                //登录注册
            }
        } else {
            //已登录
        }
    }
}
