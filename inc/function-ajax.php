<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * AJAX方法
 * ------------------------------------------------------------------------------
 */

//注册异步方法
function aya_ajax_register($name, $callback, $public = false)
{
    add_action('wp_ajax_' . $name, $callback);

    if ($public) {
        add_action('wp_ajax_nopriv_' . $name, $callback);
    }
}
//点赞 +1 计数器
function aya_ajax_add_like()
{
    global $wpdb, $post;

    $id = $_POST["um_id"];
    $action = $_POST["um_action"];

    if ($action == 'ding') {
        $specs_raters = get_post_meta($id, 'specs_zan', true);
        $expire = time() + 99999999;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost
        setcookie('specs_zan_' . $id, $id, $expire, '/', $domain, false);
        if (!$specs_raters || !is_numeric($specs_raters)) {
            update_post_meta($id, 'specs_zan', 1);
        } else {
            update_post_meta($id, 'specs_zan', ($specs_raters + 1));
        }
        return get_post_meta($id, 'specs_zan', true);
    }
    die;
}
//aya_ajax_register('add_like', 'aya_ajax_add_like');
//点赞 -1 计数器
function aya_ajax_sub_like()
{
    global $wpdb, $post;
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ($action == 'duang') {
        $specs_raters = get_post_meta($id, 'specs_zan', true);
        $expire = time() - 99999999;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost
        setcookie('specs_zan_' . $id, $id, $expire, '/', $domain, false);
        if (!$specs_raters || !is_numeric($specs_raters)) {
            update_post_meta($id, 'specs_zan', 1);
        } else {
            update_post_meta($id, 'specs_zan', ($specs_raters - 1));
        }
        return get_post_meta($id, 'specs_zan', true);
    }
    die;
}
//aya_ajax_register('sub_like', 'aya_ajax_sub_like');
//评论AJAX翻页
function aya_ajax_comment_page_nav()
{
    global $post, $wp_query, $wp_rewrite;

    $postid = $_POST["um_post"];
    $pageid = $_POST["um_page"];

    $comments = get_comments('post_id=' . $postid);
    $post = get_post($postid);

    if ('desc' != get_option('comment_order')) {
        $comments = array_reverse($comments);
    }

    $wp_query->is_singular = true;

    $baseLink = '';

    if ($wp_rewrite->using_permalinks()) {
        $baseLink = '&base=' . user_trailingslashit(get_permalink($postid) . 'comment-page-%#%', 'commentpaged');
    }

    echo '<ol class="commentlist" >';
    //如果你的主题使用了回调函数，则要设置
    wp_list_comments('page=' . $pageid . '&per_page=' . get_option('comments_per_page'), $comments);

    echo '</ol>';
    echo '<nav class="commentnav" data-fuck="' . $postid . '">';

    paginate_comments_links('total=' . get_comment_pages_count($comments) .  '¤t=' . $pageid . '&prev_text=«&next_text=»');

    echo '</nav>';

    die;
}
aya_ajax_register('comment_page_nav', 'aya_ajax_comment_page_nav');
//评论AJAX提交
function aya_ajax_comment_submit()
{
}
