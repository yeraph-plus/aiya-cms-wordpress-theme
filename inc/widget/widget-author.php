<?php

//小工具：作者卡片

class AYA_Widget_Author_Box extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-author',
            'title' => 'AIYA-CMS 作者卡片',
            'classname' => 'widget-card-box',
            'desc' => '侧边栏作者卡片组件，该组件只在文章页面侧栏有效',
            'field_build' => array(
                array(
                    'type' => 'select',
                    'id' => 'limit',
                    'name' => '显示作者最新文章',
                    'sub' => array(
                        '0' => '不显示',
                        '3' => '显示3篇',
                        '5' => '显示5篇',
                    ),
                    'default' => '',
                ),
            ),
        );

        return $widget_args;
    }
    function widget_func()
    {
        $limit = parent::widget_opt('limit');
        $author = get_the_author_meta('ID');
        echo '<div class="author-info">
                ' . get_avatar($author, 80) . '
                <h5 class="name">' . get_the_author_meta('display_name') . '</h5>
                <p class="des">' . get_the_author_meta('description') . '</p>
                <p class="mate">
                    <span><i class="bi bi-book"></i>&nbsp;文章&nbsp;' . count_user_posts($author) . '</span>
                    <span><i class="bi bi-chat-dots"></i>&nbsp;评论&nbsp;' . get_comments('count=true&user_id=' . $author) . '</span>
                </p>
            </div>';
        $args = array(
            'author__in' => $author,
            'ignore_sticky_posts' => true,
            'posts_per_page' => $limit,
            'paged'    => '1',
            'post_type' => 'post'
        );
        $the_query = new WP_Query($args);
        //生成文章列表
        if ($the_query->have_posts()) {
            echo '<ul class="widget-loop-li p-0 m-0">';
            while ($the_query->have_posts()) : $the_query->the_post();
                echo '<li>
                    <img loading="lazy" src="' . get_post_thumb() . '" >
                    <div class="loop">
                        <h5><a class="stretched-link" href="' . get_permalink() . '">' . get_post_title() . '</a></h5>
                        <p><i class="bi bi-clock"></i>&nbsp;' . get_the_date() . '</p>
                    </div>
                </li>';
            endwhile;
            wp_reset_query(); //重置Query查询
            echo '</ul>';
        }
    }
}
