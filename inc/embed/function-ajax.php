<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 注册异步方法
 * ------------------------------------------------------------------------------
 */

//点赞 +1 计数器
function add_zan()
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
add_action('wp_ajax_nopriv_add_zan', 'add_zan');
add_action('wp_ajax_add_zan', 'add_zan');
//点赞 -1 计数器
function sub_zan()
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
add_action('wp_ajax_nopriv_sub_zan', 'sub_zan');
add_action('wp_ajax_sub_zan', 'sub_zan');
//评论AJAX翻页
function ajax_comment_page_nav()
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
    wp_list_comments('page=' . $pageid . '&per_page=' . get_option('comments_per_page'), $comments); //如果你的主题使用了回调函数，则要设置
    echo '</ol>';
    echo '<nav class="commentnav" data-fuck="' . $postid . '">';
    paginate_comments_links('total=' . get_comment_pages_count($comments) .  '¤t=' . $pageid . '&prev_text=«&next_text=»');
    echo '</nav>';
    die;
}
add_action('wp_ajax_nopriv_ajax_comment_page_nav', 'ajax_comment_page_nav');
add_action('wp_ajax_ajax_comment_page_nav', 'ajax_comment_page_nav');
