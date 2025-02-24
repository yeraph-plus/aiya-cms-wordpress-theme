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

//获取AJAX位置
function aya_ajax_url($action, $args = array())
{
    $url = admin_url('admin-ajax.php?action=') . $action;

    if (!empty($args)) {
        $url .= '&' . http_build_query($args);
    }

    return $url;
}

//访问原始 POST 数据
function aya_get_req_body()
{
    //使用PHP伪协议方法
    $body = @file_get_contents('php://input');

    return json_decode($body, true);
}

/*
 * ------------------------------------------------------------------------------
 * 主题AJAX功能
 * ------------------------------------------------------------------------------
 */

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
function aya_ajax_comment_submit() {}
