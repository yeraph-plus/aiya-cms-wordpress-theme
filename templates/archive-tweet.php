<?php

if (!defined('ABSPATH')) {
    exit;
}

$tweet_status = sanitize_text_field(wp_unslash($_GET['tweet_status'] ?? ''));
$current_tag = sanitize_title(wp_unslash($_GET['tweet_tag'] ?? ''));
$tweet_tags = get_terms([
    'taxonomy' => 'tweet_tag',
    'hide_empty' => true,
]);

if ($tweet_status !== '') {
    $status_text_map = [
        'published' => __('推文发布成功', 'aiya-cms'),
        'pending' => __('已提交，等待审核', 'aiya-cms'),
        'empty' => __('内容不能为空', 'aiya-cms'),
        'error' => __('提交失败，请稍后重试', 'aiya-cms'),
    ];
    if (isset($status_text_map[$tweet_status])) {
        echo '<div class="alert alert-info mb-4">' . esc_html($status_text_map[$tweet_status]) . '</div>';
    }
}

if (is_user_logged_in()) {
    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="mb-6 space-y-2">
        <input type="hidden" name="action" value="aya_submit_tweet" />
        <?php wp_nonce_field('aya_submit_tweet_action', 'aya_submit_tweet_nonce'); ?>
        <input type="text" name="tweet_title" class="input input-bordered w-full" placeholder="<?php esc_attr__('标题（可选）', 'aiya-cms'); ?>" />
        <textarea name="tweet_content" class="textarea textarea-bordered w-full" rows="4" placeholder="<?php esc_attr__('分享点什么…', 'aiya-cms'); ?>" required></textarea>
        <input type="text" name="tweet_tags" class="input input-bordered w-full" placeholder="<?php esc_attr__('标签，使用逗号分隔', 'aiya-cms'); ?>" />
        <button type="submit" class="btn btn-primary"><?php esc_html__('发布推文', 'aiya-cms'); ?></button>
    </form>
    <?php
}

if (!is_wp_error($tweet_tags) && !empty($tweet_tags)) {
    echo '<div class="mb-6 flex flex-wrap gap-2">';
    echo '<a class="btn btn-sm ' . ($current_tag === '' ? 'btn-primary' : 'btn-ghost') . '" href="' . esc_url(get_post_type_archive_link('tweet')) . '">' . esc_html__('全部', 'aiya-cms') . '</a>';
    foreach ($tweet_tags as $term) {
        $url = add_query_arg('tweet_tag', $term->slug, get_post_type_archive_link('tweet'));
        $active = ($current_tag === $term->slug) ? 'btn-primary' : 'btn-ghost';
        echo '<a class="btn btn-sm ' . esc_attr($active) . '" href="' . esc_url($url) . '">#' . esc_html($term->name) . '</a>';
    }
    echo '</div>';
}

if (!have_posts()) {
    aya_react_island('loop-tweet', ['posts' => []]);
    return;
}

$loop_html = '';
$loop_porps = [];

while (have_posts()) {
    the_post();
    $post_obj = new AYA_Post_In_While();

    $loop_porps[] = [
        'id' => $post_obj->id,
        'url' => (string) $post_obj->url,
        'title' => (string) $post_obj->title,
        'attr_title' => (string) $post_obj->attr_title,
        'content' => (string) $post_obj->content,
        'date' => (string) $post_obj->date,
        'date_iso' => (string) $post_obj->date_iso,
        'comments' => (string) $post_obj->comments,
        'likes' => (string) $post_obj->likes,
        'author' => [
            'name' => (string) $post_obj->author_name,
            'avatar' => (string) $post_obj->author_avatar_x32,
        ],
    ];

    $loop_html .= '<a href="' . esc_url($post_obj->url) . '" class="block h-auto w-full" title="' . esc_attr($post_obj->title) . '" rel="bookmark">' . esc_html($post_obj->attr_title) . '</a>';
}

aya_react_island('loop-tweet', ['posts' => $loop_porps, 'loopTitle' => __('推文', 'aiya-cms')], $loop_html);
aya_template_part_load('pagination', ['paged' => aya_get_pagination()]);
