<?php

//小工具：推文

class AYA_Widget_Tweet_Posts extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'sidebar-tweet',
            'title' => 'AIYA-CMS 推文列表',
            'classname' => 'widget-card-box',
            'desc' => '展示近期评论列表',
            'field_build' => array(
                array(
                    'type' => 'input',
                    'id' => 'author',
                    'name' => '用户ID',
                    'default' => '1',
                ),
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
        $author = parent::widget_opt('author');
        $limit = parent::widget_opt('limit');
        //自定义Query查询
        $args = array(
            'author__in' => $author,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'paged'    => '1',
            'post_type' => 'tweet'
        );
        $the_query = new WP_Query($args);
        //生成文章列表
        if ($the_query->have_posts()) {
            echo '<ul class="widget-tweet p-0 m-0">';
            while ($the_query->have_posts()) : $the_query->the_post();
                echo '<li>
                    <div class="avatar">' . get_avatar(get_the_author_meta('ID'), '32') . get_the_author() . '</div>
                    <div class="concent">
                        <p>' . mb_strimwidth(aya_clear_text(apply_shortcodes(get_the_content()), 1), 0, 300, '...') . '</p>
                        <span><i class="bi bi-clock"></i>&nbsp;发表于' . get_the_date() . '&nbsp;<a href="' . get_permalink() . '"><i class="bi bi-arrow-return-right"></i>&nbsp;查看全文</a></span>
                    </div>
                </li>';
            endwhile;
            wp_reset_query(); //重置Query查询
            echo '</ul>';
        } else {
            echo '<ul><li class="post-link">没有内容</li></ul>';
        }
    }
}
