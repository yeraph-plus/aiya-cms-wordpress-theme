<?php
class AYA_Widget_User_Welcome extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-welcome',
            'title' => 'AIYA-CMS 用户面板',
            'classname' => 'widget-card-box',
            'desc' => '用户面板组件',
        );

        return $widget_args;
    }
    function widget_func()
    {
        // 使用 aya_user_get_login_data 获取用户数据
        $user_data = aya_user_get_login_data();

        aya_react_island(
            'widget-user-welcome',
            $user_data
        );
    }
}
