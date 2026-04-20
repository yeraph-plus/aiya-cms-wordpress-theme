<?php

if (!defined('ABSPATH')) {
    exit;
}

//获取文章数据
$post_obj = new AYA_Post_In_While();

?>
<article>
    <?php
    //获取特色图片生成裁剪
    $thumbnail = aya_get_post_thumb($post_obj->thumbnail_url, '', 1200, 0);
    //获取过期状态
    $post_outdated_days = aya_opt('site_post_outdate_days_text', 'land');
    $post_is_outdated = $post_obj->the_post_is_outdated($post_outdated_days);

    aya_template_part_load('content-detail', [
        'post_id' => $post_obj->id,
        'title' => $post_obj->title,
        'author' => [
            'name' => $post_obj->author_name,
            'url' => $post_obj->author_url,
            'avatar' => $post_obj->author_avatar_x32,
        ],
        'date' => $post_obj->date,
        'date_iso' => $post_obj->date_iso,
        'status' => $post_obj->status,
        'modified_ago' => $post_obj->modified_ago,
        'views' => $post_obj->views,
        'disallow_toggle' => false,
        'comments' => $post_obj->comments_text,
        'likes' => $post_obj->likes,
        'is_favorite' => aya_user_get_favorite_posts_check($post_obj->id),
        'thumbnail' => ($post_obj->thumbnail_url !== false) ? $thumbnail : '',
        'categories' => $post_obj->cat_list,
        'tags' => $post_obj->tag_list,
        'is_outdated' => $post_is_outdated,
        'outdated_text' => $post_obj->date_ago,
        'alert_tips' => aya_get_post_tips($post_obj->id),
    ]);
    ?>
    <!-- Content Start -->
    <div id="article-content" class=" prose prose-base lg:prose-lg max-w-none">
        <?php aya_echo($post_obj->content); ?>
    </div>
    <!-- Content End -->
    <?php
    //使权限计数器工作一次
    // TODO 应该提前注册此方法使其在restapi中也可用
    aya_sponsor_user_auto_trigger_count();

    //加载文件列表组件
    $oplist_props = function_exists('aya_oplist_cli_get_props') ? aya_oplist_cli_get_props() : null;

    if (is_array($oplist_props) && $oplist_props !== []) {
        aya_react_island('open-list-client', $oplist_props);
    }

    //获取声明文本
    $statement_content = aya_opt('site_post_statement_text', 'land');
    //获取上下篇文章
    $prev_post = $post_obj->prev_post();
    $next_post = $post_obj->next_post();

    aya_template_part_load('content-footer', [
        'endDivider' => true,
        'statementText' => $statement_content,
        'prevPost' => !empty($prev_post) ? [
            'title' => $prev_post->title,
            'url' => $prev_post->url,
        ] : null,
        'nextPost' => !empty($next_post) ? [
            'title' => $next_post->title,
            'url' => $next_post->url,
        ] : null,
    ]);
    ?>
</article>
