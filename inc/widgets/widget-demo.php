<?php

//Demo

class AYA_Demo_Widget extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'demo-widget',
            'title' => 'Demo Widget',
            'classname' => 'demo-widget',
            'desc' => '',
            'field_build' => array(
                array(
                    'type' => 'input',
                    'id' => 'input',
                    'name' => 'input field',
                    'default' => '',
                ),
                array(
                    'type' => 'textarea',
                    'id' => 'textarea',
                    'name' => 'textarea field',
                    'default' => '',
                ),
                array(
                    'type' => 'checkbox',
                    'id' => 'checkbox',
                    'name' => 'checkbox field',
                    'default' => true,
                ),
                array(
                    'type' => 'select',
                    'id' => 'select',
                    'name' => 'select field',
                    'sub' => array(
                        '0' => 'off',
                        '1' => 'on',
                    ),
                    'default' => '',
                ),
            ),
        );

        return $widget_args;
    }
    function widget_func()
    {
        echo parent::widget_opt('input');
        echo parent::widget_opt('textarea');
        echo parent::widget_opt('checkbox'); //this field will return string 'true' or '', is not bool
        echo parent::widget_opt('select');
    }
}
