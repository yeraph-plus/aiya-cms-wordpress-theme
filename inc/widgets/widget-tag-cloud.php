<?php

//小工具：标签云

class AYA_Widget_Tag_Cloud extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-tag-cloud',
            'title' => 'AIYA-CMS 标签云',
            'classname' => 'widget-panel',
            'desc' => '标签云卡片，栏标签列表组件',
            'field_build' => array(
                [
                    'type' => 'input',
                    'id' => 'title',
                    'name' => '标题',
                    'default' => '热门标签',
                ],
                [
                    'type' => 'input',
                    'id' => 'limit',
                    'name' => '显示数量',
                    'default' => '20',
                ],
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $title = parent::widget_opt('title');
        $limit = parent::widget_opt('limit');

        $tags = get_tags(array(
            "number" => $limit,
            "orderby" => "count",
            "order" => "DESC"
        ));

        $tags_data = [];
        if (!empty($tags) && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $tags_data[] = [
                    'id' => $tag->term_id,
                    'name' => $tag->name,
                    'url' => get_tag_link($tag->term_id),
                    'count' => $tag->count
                ];
            }
        }

        aya_react_island(
            'widget-tag-cloud',
            ['tags' => $tags_data, 'widgetTitle' => $title]
        );
    }
}
