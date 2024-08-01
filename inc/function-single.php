<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 文章信息
 * ------------------------------------------------------------------------------
 */

//合并文章信息查询
function aya_the_loop_meta_data($post_id = 0, $date_mod = 'short', $preview_size = 255)
{
    $post = aya_get_post($post_id);

    //直接退出
    if (!$post) return '';

    $post_data = array();

    //转换$post对象存入当前数组
    $post_data['id'] = aya_get_post_id($post);
    $post_data['url'] = aya_get_post_url($post);
    $post_data['title'] = aya_get_post_title($post, 0);
    $post_data['attr_title'] = aya_get_post_title($post, 1);
    $post_data['status'] = aya_get_post_status($post);
    $post_data['date'] = aya_get_post_date($post, $date_mod);
    $post_data['author'] = aya_get_post_author($post);
    $post_data['comments'] = aya_get_post_comments($post);
    $post_data['preview'] = aya_get_post_preview($post, $preview_size);

    //获取自定义字段
    $post_data['views'] = aya_get_post_views($post_data['id']);
    $post_data['likes'] = aya_get_post_likes($post_data['id']);

    return $post_data;
}
//合并文章正文查询
function aya_the_content_meta_data($post_id = 0)
{
    $post = aya_get_post($post_id);

    //直接退出
    if (!$post) return '';

    $post_data = array();

    //转换$post对象
    $post_data['id'] = aya_get_post($post);
    $post_data['title'] = aya_get_post_title($post, 1);
    $post_data['status'] = aya_get_post_status($post);
    $post_data['author'] = aya_get_post_author($post, 1);
    $post_data['date'] = aya_get_post_date($post, 'full');
    $post_data['comments'] = aya_get_post_comments($post, 1);
    $post_data['excerpt'] = aya_get_post_excerpt($post);
    $post_data['thumbnail'] = aya_get_post_thumbnail($post);
    $post_data['content'] = aya_get_post_content($post);

    //获取自定义字段
    $post_data['views'] = aya_get_post_views($post_data['id']);
    $post_data['likes'] = aya_get_post_likes($post_data['id']);

    return $post_data;
}
//获取文章缩略图
function aya_the_loop_thumb($post_id = 0, $thumb_w = 400, $thumb_h = 300, $thumb_def = true)
{
    //检查主题设置
    $get_thumb = aya_get_post_thumb($post_id);

    if (!empty($get_thumb)) {
        return $get_thumb;
    }

    //不存在则开始查询

    //如果存在特色图片
    if (has_post_thumbnail($post_id)) {
        //返回
        $img_url = get_the_post_thumbnail_url($post_id);
    }
    //获取文章中的第一张图片
    else {
        //查询附件
        $media = aya_get_post_media('image', $post_id);
        //弹出数组中的第一个元素
        $media = array_shift($media);
        //只取URL，忽略width和height
        $img_url = (empty($media)) ? NULL : wp_get_attachment_image_src($media->ID, '', false)[0];
    }
    //如果存在图片
    if ($img_url !== NULL) {
        //返回裁剪
        return get_bfi_thumb($img_url, $thumb_w, $thumb_h);
    }
    //不存在则返回主题默认
    else if ($thumb_def) {
        return aya_get_default_thumbnail();
    }
    //返回空
    return NULL;
}

//定位文章
function aya_get_post($post_id = 0)
{
    //检查当前是否在have_posts()内，是则返回
    if (empty($post_id) && isset($GLOBALS['post'])) {
        $post = $GLOBALS['post'];
    }
    //尝试获取WP_Post
    else {
        $post = get_post($post_id);
    }

    return (!empty($post)) ? $post : false;
}
//获取文章点赞数
function aya_get_post_likes($post_id = 0)
{
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }

    $the_likes = get_metadata('post', $post_id, 'likes', true);

    if ($the_likes > 0) {
        return $the_likes;
    } else {
        return '0';
    }
}
//获取文章访问量
function aya_get_post_views($post_id = 0)
{
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }

    $the_views = get_metadata('post', $post_id, 'views', true);

    if ($the_views > 0) {
        //计算为千位
        if ($the_views >= 1000) {
            return round($the_views / 1000, 2) . 'K';
        } else {
            return $the_views;
        }
    } else {
        return '0';
    }
};
//获取文章缩略图
function aya_get_post_thumb($post_id = 0)
{
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }

    $the_thumbnail = get_metadata('post', $post_id, 'thumbnail', true);

    return $the_thumbnail;
}

/*
 * ------------------------------------------------------------------------------
 * 替代一些WP原来的The_方法
 * ------------------------------------------------------------------------------
 */

//定位文章URL
function aya_get_post_url($post = null)
{
    return get_permalink($post);
}
//获取文章ID
function aya_get_post_id($post = null)
{
    if (!is_object($post)) {
        $post = get_post();
    }

    return !empty($post) ? $post->ID : false;
}
//获取文章标题检查是否为空
function aya_get_post_title($post = NULL, $attribute = false)
{
    if (!is_object($post)) {
        $the_title = get_the_title($post);
    } else {
        $the_title = $post->post_title;
    }

    //检查文章标题
    if (strlen($the_title) === 0) {
        $the_title = __('无标题', 'AIYA');
    }
    //是否转义
    if ($attribute == true) {
        $the_title = esc_attr(strip_tags($the_title));
    }
    //返回标题
    return $the_title;
}
//获取文章状态
function aya_get_post_status($post = NULL)
{
    if (!is_object($post)) {
        $the_status = get_post_status($post);
    } else {
        $the_status = $post->post_status;
    }

    //返回文本
    switch ($the_status) {
        case 'publish':
            //return __('已发布', 'AIYA');
            return '';
        case 'pending':
            return __('待审', 'AIYA');
        case 'future':
            return __('定时发布', 'AIYA');
        case 'private':
            return __('私密文章', 'AIYA');
        case 'draft':
            return __('草稿', 'AIYA');
        case 'auto-draft':
            return __('自动保存的草稿', 'AIYA');
        case 'inherit':
            return __('修订版本', 'AIYA');
        case 'trash':
            return __('已删除', 'AIYA');
        default:
            return '';
    }
}
//获取文章用户摘要
function aya_get_post_excerpt($post = NULL)
{
    if (!is_object($post)) {
        $the_excerpt = get_the_excerpt($post);
    } else {
        $the_excerpt = $post->post_excerpt;
    }
    return $the_excerpt;
}
//获取文章摘要
function aya_get_post_preview($post = NULL, $size = 225)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    //如果文章加密
    if (post_password_required($post)) {
        return __('这篇文章受密码保护，输入密码才能阅读。', 'AIYA');
    }
    //如果用户设置了摘要，则直接输出摘要内容
    if (has_excerpt($post)) {
        $the_preview = has_excerpt($post);
    }
    //没有摘要，截取正文内容
    else {
        $the_content = $post->post_content;

        //$the_preview = wp_trim_words($the_content, $size);
        //DEBUG：WP原生的摘要函数好像完全没法判断中文长度，改用PHP判断
        $the_preview = mb_strimwidth(aya_clear_text(strip_shortcodes($the_content)), 0, $size, '...');
    }
    //再次检查摘要是否为空
    if ($the_preview != '') {
        return $the_preview;
    } else {
        return __('这篇文章没有摘要内容。', 'AIYA');
    }
}
//获取文章作者
function aya_get_post_author($post = NULL, $modified = false)
{
    if (!is_object($post)) {
        $post = get_post($post);
        $the_author = get_post_field('post_author', $post);
    } else {
        $the_author = $post->post_author;
    }

    $author_name = get_the_author_meta('display_name', $the_author);

    if ($modified == true) {
        $author_status = (user_can($the_author, 'publish_posts')) ? __(' 发布', 'AIYA') : __(' 投稿', 'AIYA');
        return  __('由 ', 'AIYA') . $author_name . $author_status;
    } else {
        return $author_name;
    }
}
//计算已发布时间
function aya_diff_timeago($time)
{
    //更新：使用WordPress内置方法
    return human_time_diff($time, current_time('timestamp')) . __(' 前 ', 'AIYA');
}
//获取文章发布时间
function aya_get_post_date($post = NULL, $modified = 'short')
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    $publish_time = get_post_time('U', false, $post, true);

    //获取WP时间格式设置
    $date_format = get_option('date_format');

    switch ($modified) {
        case 'full':
            $modified_time = get_post_modified_time('U', false, $post, true);
            return  __('发布时间 ', 'AIYA') . date($date_format, $publish_time) . __(' [ 上次更新于 ', 'AIYA') . aya_diff_timeago($modified_time) . __(' ] ', 'AIYA');
        case 'short':
            return date($date_format, $publish_time);
        case 'timeago':
            return aya_diff_timeago($publish_time);
        default:
            return date($date_format, $publish_time);
    }
}
//获取文章评论数
function aya_get_post_comments($post = NULL, $modified = false)
{
    if (!is_object($post)) {
        $the_comment_count = get_comments_number($post);
    } else {
        $the_comment_count = $post->comment_count;
    }

    if ($modified == true) {
        if ($the_comment_count > 0) {
            return $the_comment_count . __('条评论', 'AIYA');
        } else {
            return __('无人评论', 'AIYA');
        }
    } else {
        return empty($the_comment_count) ? 0 : $the_comment_count;
    }
}
//获取文章正文
function aya_get_post_content($post = NULL)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }

    $the_content = get_the_content(null, false, $post);

    //执行 the_content 的过滤器
    $the_content = apply_filters('the_content', $the_content);
    $the_content = str_replace(']]>', ']]&gt;', $the_content);
    //返回
    return $the_content;
}
//获取文章附件
function aya_get_post_media($post = NULL, $media = '')
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    //可以获取 image video audio text application 等
    $the_attachments = get_attached_media($media, $post);
    //返回查询到的Object
    return $the_attachments;
}
//获取文章特色图片
function aya_get_post_thumbnail($post = NULL)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    //如果存在特色图片
    if (has_post_thumbnail($post)) {
        return get_the_post_thumbnail_url($post);
    }
    //返回空
    return NULL;
}

/*
 * ------------------------------------------------------------------------------
 * 一些自定义方法
 * ------------------------------------------------------------------------------
 */

//遍历法提取内容中第 1 个链接
function aya_match_post_first_url($post = NULL, $callback = false)
{
    if (!is_object($post)) {
        $post = get_post($post);
        $the_content = get_post_field('post_content', $post);
    } else {
        $the_content = $post->post_content;
    }

    ob_start();
    ob_end_clean();

    //遍历内容提取链接
    $match_all = preg_match_all('/<a.*?href=[\'"](.*?)[\'"].*?>/i', $the_content, $matches);

    if (isset($matches[1][0])) {
        return $matches[1][0];
    }
    //返回空
    return NULL;
}
//遍历法提取内容中第 1 张图片
function aya_match_post_first_image($post = NULL, $callback = false)
{
    if (!is_object($post)) {
        $post = get_post($post);
        $the_content = get_post_field('post_content', $post);
    } else {
        $the_content = $post->post_content;
    }

    ob_start();
    ob_end_clean();

    //遍历内容提取图片
    $match_all = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $the_content, $matches);
    //判断图片是否存在
    if (isset($matches[1][0])) {
        //返回全部
        if ($callback == true) {
            //返回全部数组，交给其他函数处理
            return $matches[1];
        }
        //返回第一张
        return $matches[1][0];
    }
    //返回空
    return NULL;
}
//检查文章是否已过时
function aya_verify_post_is_outdated($post = NULL, $out_time = 30)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    $publish_time = get_post_time('U', false, $post, true);
    $modified_time = get_post_modified_time('U', false, $post, true);

    //判断更新时间取最近
    $last_time = ($modified_time > $publish_time) ? $modified_time : $publish_time;

    //时间30天
    if (time() > $last_time + 86400 * $out_time) {
        return true;
    }
    return false;
}
//截取标题关键词
function aya_match_post_first_words($post = NULL)
{
    if (!is_object($post)) {
        $the_title = get_the_title($post);
    } else {
        $the_title = $post->post_title;
    }

    ob_start();
    ob_end_clean();

    //遍历标题匹配括号
    $match_all = preg_match_all('/(\[.*?\])|(\<.*?\>)|(\().*?(\))/', $the_title, $matches);
    var_dump($matches);

    //返回取到的第一个
    if (isset($matches[1][0])) {
        return $matches[1][0];
    }
    //返回空
    return NULL;
}
