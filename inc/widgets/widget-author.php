<?php

//小工具：作者卡片

class AYA_Widget_Author_Box extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-author',
            'title' => 'AIYA-CMS 作者卡片',
            'classname' => 'widget-card-box',
            'desc' => '作者卡片，该组件只在文章页面侧栏有效',
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
                    'default' => '0',
                ),
            ),
        );

        return $widget_args;
    }

    function widget_func()
    {
        $limit = parent::widget_opt('limit');
        $author_meta = new AYA_Plugin_Data_Template_Of_Post_Meta();
        $author_data = $author_meta->aya_get_post_author_data(0, 128);

        $args = array(
            'author__in' => $author_data['id'],
            'ignore_sticky_posts' => true,
            'posts_per_page' => $limit,
            'paged'    => '1',
            'post_type' => 'post'
        );

        $post = new AYA_Post_Query();

        $the_query = $post->query($args);
?>
        <!-- Author box -->
        <div class="panel p-6 pt-12 mt-8 grid place-items-center">
            <div class="bg-white absolute text-white-light -top-12 w-24 h-24 rounded-full shadow mb-5 mx-auto flex items-center justify-center">
                <img src="<?php aya_echo($author_data['avatar']); ?>" alt="avatar" class="w-20 h-20 rounded-full" />
            </div>
            <h5 class="text-dark text-lg font-semibold mt-4 mb-4 dark:text-white-light">
                <a href="<?php aya_echo($author_data['url']); ?>" class="text-primary font-semibold hover:underline group">
                    <?php aya_echo($author_data['name']); ?>
                </a>
            </h5>
            <p class="text-white-dark text-base font-medium mb-4">
                <?php aya_echo($author_data['desc']); ?>
            </p>
            <p class="flex items-center text-base font-medium mb-4">
                <i data-feather="edit-3" width="16" height="16" class="mr-1"></i> <span class="mr-2">文章 <?php aya_echo(count_user_posts($author_data['id'])); ?> 篇</span>
                <i data-feather="message-circle" width="16" height="16" class="mr-1"></i> <span>评论 <?php aya_echo(get_comments('count=true&user_id=' . $author_data['id'])); ?> 次</span>
            </p>
        </div>
<?php
        $html = '';
        $html .= '<div class="widget-content flex flex-col gap-4 mt-4">';
        //生成文章列表
        if ($the_query == false) {
            $html .= '<span class="text-center text-base text-gray-500 m-4">' . __('暂无文章', 'AIYA') . '</span>';
        } else {
            //循环
            foreach ($the_query as $post => $post_data) {
                $post_thumb = aya_post_thumb($post_data['thumb_url'], $post_data['id'], 200, 150);

                $html .= '<div class="relative flex items-center group">';
                $html .= '<img class="w-[100px] h-[75px] rounded-lg mr-2" src="' . $post_thumb . '" alt="thumb">';
                $html .= '<div class="flex flex-col">';
                $html .= '<a class="text-sm font-bold group-hover:text-primary transition-all duration-300 line-clamp-2 mb-2" href="' . $post_data['url'] . '" title="' . $post_data['attr_title'] . '">' . $post_data['title'] . '</a>';
                $html .= '<span class="flex items-center text-sm text-gray-500">' . aya_feather_icon('calendar', '16', 'mr-1', '') .  __('发布于', 'AIYA') . ' ' . $post_data['date'] . '</span>';
                $html .= '</div></div>';
            }
        }
        $html .= '</div>';

        aya_echo($html);
    }
}
