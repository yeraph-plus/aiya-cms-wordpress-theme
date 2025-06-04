<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 处理WP的文章数据结构到数组
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

if (!class_exists('AYA_WP_Post_Object')) {
    class AYA_WP_Post_Object
    {
        private $post;

        //替代一些WP原来的the_post方法
        public function __construct($post_id = 0)
        {
            $this->post = $this->get_post($post_id);
        }
        //获取当前POST对象
        public function this_post()
        {
            return $this->post;
        }
        //定位文章
        public function get_post($post = 0)
        {
            //已经是对象直接返回
            if (is_object($post)) {

                $this->post = $post;

                return $post;
            }
            //检查是否为数字ID
            $post_id = absint($post);

            //检查当前是否在 have_posts 内，是则返回
            if (empty($post_id) && isset($GLOBALS['post'])) {

                $post = $GLOBALS['post'];
            }
            //尝试获取 WP_Post 对象
            if ($post_id > 0) {

                $post = get_post($post_id);
            }
            //获取成功
            if (!empty($post)) {

                $this->post = $post;

                return $post;
            }

            return false;
        }
        //获取ID
        public function get_post_id()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            return $post->ID;
        }
        //获取URL
        public function get_post_url()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_url = get_permalink($post);

            return esc_url($the_url);
        }
        //获取文章类型
        public function get_post_type()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_type = $post->post_type;

            //如果是文章类型，则返回文章格式类型
            if ('post' == $the_type) {
                return get_post_format($post);
            }

            return $the_type;
        }
        //获取标题
        public function get_post_title($attribute = false)
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_title = $post->post_title;

            //检查文章标题
            if (strlen($the_title) === 0) {
                $the_title = __('无标题', 'AIYA');
            }
            //清理HTML输出
            if ($attribute == true) {
                $the_title = esc_attr(strip_tags($the_title));
            }

            return esc_html($the_title);
        }
        //获取状态
        public function get_post_status()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $status = $post->post_status;
            $post_id = $post->ID;

            $the_status = [];

            //基本状态
            switch ($status) {
                case 'pending':
                    $the_status['pending'] = __('待审', 'AIYA');
                    break;
                case 'future':
                    $the_status['future'] = __('定时发布', 'AIYA');
                    break;
                case 'private':
                    $the_status['private'] = __('私密', 'AIYA');
                    break;
                case 'draft':
                    $the_status['draft'] = __('草稿', 'AIYA');
                    break;
                case 'auto-draft':
                    $the_status['auto-draft'] = __('自动草稿', 'AIYA');
                    break;
                case 'inherit':
                    $the_status['inherit'] = __('修订版本', 'AIYA');
                    break;
                case 'trash':
                    $the_status['trash'] = __('已删除', 'AIYA');
                    break;
                case 'publish':
                default:
                    //$the_status[] = ['publish' => __('已发布', 'AIYA')];
                    break;
            }
            //是置顶文章
            if (is_sticky($post_id)) {
                $the_status['sticky'] = __('置顶', 'AIYA');
            }
            //是密码保护
            if (!empty($post->post_password)) {
                $the_status['password'] = __('密码保护', 'AIYA');
            }
            //是最近发布
            $publish_time = get_post_time('U', false, $post, true);
            if (date('U') - $publish_time < 86400) {
                $the_status['newest'] = __('最新', 'AIYA');
            }

            return $the_status;
        }
        //获取用户摘要
        public function get_post_excerpt()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_excerpt = $post->post_excerpt;

            return wp_kses_post($the_excerpt);
        }
        //获取密码保护
        public function get_post_password()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_password = $post->post_password;

            return empty($the_password) ? false : $the_password;
        }
        //获取摘要
        public function get_post_preview($size = 225)
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            //如果文章加密
            if (!empty($post->post_password)) {

                return __('这篇文章受密码保护，输入密码才能阅读。', 'AIYA');
            }
            //如果设置了用户摘要，则直接输出摘要内容
            if (!empty($post->post_excerpt)) {

                return wp_kses_post($post->post_excerpt);
            }
            //没有摘要，截取正文内容
            if (!empty($post->post_content)) {

                $the_content = $post->post_content;
                $the_content = wp_strip_all_tags(strip_shortcodes($the_content));

                $the_preview = wp_trim_words($the_content, $size);
                //DEBUG：有时候WP原生的摘要函数好像完全没法判断中文长度，改用PHP判断
                //$the_preview = mb_strimwidth($the_content, 0, $size, '...');

                return wp_kses_post($the_preview);
            }

            return __('这篇文章没有摘要内容。', 'AIYA');
        }
        //获取评论数
        public function get_post_comments($modified = false)
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_comment_count = $post->comment_count;
            $the_comment_status = $post->comment_status;

            if ($modified == true) {
                //关闭评论
                if (empty($the_comment_status)) {
                    return __('评论已关闭', 'AIYA');
                }
                //计数评论
                if ($the_comment_count > 0) {
                    return $the_comment_count . __('&nbsp;条评论', 'AIYA');
                } else {
                    return __('无人评论', 'AIYA');
                }
            }

            return empty($the_comment_count) ? '0' : $the_comment_count;
        }
        //计算为几天前
        public function diff_timeago($time)
        {
            //更新：使用WordPress内置方法
            return human_time_diff($time, current_time('timestamp')) . __('前', 'AIYA');
        }
        //获取发布时间
        public function get_post_date($date_mod = 'publish_date')
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            //获取WP时间格式设置
            static $date_format = get_option('date_format');

            //获取不同的时间
            switch ($date_mod) {
                case 'publish_date':
                    $publish_time = get_post_time('U', false, $post, true);
                    return date($date_format, $publish_time);
                case 'modified_date':
                    $modified_time = get_post_modified_time('U', false, $post, true);
                    return date($date_format, $modified_time);
                case 'publish_timeago':
                    $publish_time = get_post_time('U', false, $post, true);
                    return $this->diff_timeago($publish_time);
                case 'modified_timeago':
                    $modified_time = get_post_modified_time('U', false, $post, true);
                    return $this->diff_timeago($modified_time);
                case 'iso':
                default:
                    //返回ISO-8601标准格式时间
                    return get_post_time('c', false, $post, true);
            }
        }
        //获取文章访问量
        public function get_post_views($modified = false)
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_views = $post->view_count;

            if (empty($the_views)) {
                $the_views = '0';
            }

            //计算为千位
            if ($the_views >= 1000) {
                $the_views = round($the_views / 1000, 1) . 'K';
            }

            if ($modified == true) {
                return $the_views . __('&nbsp;次浏览', 'AIYA');
            }

            return $the_views;
        }
        //获取文章点赞数
        public function get_post_likes($modified = false)
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_likes = $post->like_count;

            if (empty($the_likes)) {
                $the_likes = '0';
            }

            if ($modified == true) {
                return $the_likes . __('&nbsp;人喜欢', 'AIYA');
            }

            return $the_likes;
        }
        //获取正文
        public function get_post_content()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_content = get_the_content(null, false, $post);

            //执行 the_content 的过滤器
            $the_content = apply_filters('the_content', $the_content);
            $the_content = str_replace(']]>', ']]&gt;', $the_content);

            return $the_content;
        }
        //获取附件
        public function get_post_media($media = 'image')
        {
            //可以获取 WP 定义的 image video audio text application 附件类型
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $the_attachments = get_attached_media($media, $post);

            return $the_attachments; //返回查询到的对象
        }
        //获取特色图片
        public function get_post_thumbnail($deepl = false, $extract_url = false, $size = 'full')
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $post_id = $post->ID;

            //尝试获取特色图片
            $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
            //如果没有特色图片
            if (empty($thumbnail_id) && $deepl === true) {
                //尝试获取第一张图片
                $the_attachments = get_attached_media('image', $post);

                if (!empty($the_attachments)) {
                    //提取第一个附件
                    $first_attachment = reset($the_attachments);

                    if ($first_attachment) {
                        $thumbnail_id = $first_attachment->ID;
                    }
                }
            }
            //没有找到
            if (empty($thumbnail_id)) {
                return false;
            }
            //尝试获取媒体库
            $image = wp_get_attachment_image_src($thumbnail_id, $size, false);
            //图片数据
            if (!$image) {
                return false;
            }
            //如果只需要URL
            if ($extract_url === true) {
                return esc_url($image[0]);
            }
            //返回图片数据
            return array(
                'url' => esc_url($image[0]),
                'width' => $image[1],
                'height' => $image[2]
            );
        }
        //获取分类和标签
        public function get_post_terms($taxonomy = 'category')
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $post_id = $post->ID;
            $post_type = $post->post_type;

            $term_data = [];

            //此文章类型注册的所有分类法
            $taxonomies = get_object_taxonomies($post_type);
            //检查是否适用于当前文章类型
            if (!in_array($taxonomy, $taxonomies)) {
                return $term_data;
            }

            $terms = get_the_terms($post_id, $taxonomy);
            //直接检查WP报错，跳过类型验证
            if (is_wp_error($terms) || empty($terms)) {
                return $term_data;
            }

            //遍历Term对象返回新的数组
            foreach ($terms as $term) {

                $link = get_term_link($term, $taxonomy);

                if (is_wp_error($link)) {
                    continue;
                }

                $term_data[] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'url' => esc_url($link),
                ];
            }

            return $term_data;
        }
        //获取作者ID
        public function get_post_author_id()
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            return $post->post_author;
        }
        //获取作者头像
        public function get_author_avatar($avatar_size = 32)
        {
            $author_id = $this->get_post_author_id();

            return get_avatar_url($author_id, $avatar_size);
        }
        //获取作者名字
        public function get_post_author_name()
        {
            $author_id = $this->get_post_author_id();

            return get_the_author_meta('display_name', $author_id);
        }
        //获取作者链接
        public function get_post_author_url()
        {
            $author_id = $this->get_post_author_id();

            return get_author_posts_url($author_id);
        }
        //获取作者描述
        public function get_post_author_desc()
        {
            $author_id = $this->get_post_author_id();

            return get_the_author_meta('description', $author_id);
        }
        //获取PostMeta中的设置
        public function get_post_meta($meta_key = '')
        {
            $post = is_object($this->post) ? $this->post : $this->get_post();

            $post_id = $post->ID;

            return get_post_meta($post_id, $meta_key, true);
        }
        //获取当前文章作者是否于登录用户一致
        public function user_is_post_author()
        {
            if (!is_user_logged_in()) {
                return false;
            }
            //获取当前登录用户ID
            $current_user_id = get_current_user_id();

            //获取文章作者ID
            $post_author_id = $this->get_post_author_id();

            //判断是否一致
            return ($current_user_id == $post_author_id);
        }
        //获取文章时效性
        public function the_post_is_outdated($out_day = 30)
        {
            $out_day = intval($out_day);

            //设置为0时
            if ($out_day == 0) {
                return false;
            }

            $post = is_object($this->post) ? $this->post : $this->get_post();

            $publish_time = get_post_time('U', false, $post, true);
            $modified_time = get_post_modified_time('U', false, $post, true);
            //判断更新时间取最近
            $last_time = ($modified_time > $publish_time) ? $modified_time : $publish_time;

            //时间30天
            return (time() > $last_time + 86400 * $out_day);
        }
    }
}

if (!class_exists('AYA_Post_In_While')) {
    class AYA_Post_In_While extends AYA_WP_Post_Object
    {
        private $data = [];

        //初始化父类
        public function __construct($post = 0)
        {
            parent::__construct($post);

            // 预加载基本数据
            $this->data['id'] = $this->get_post_id();
            $this->data['url'] = $this->get_post_url();
            $this->data['title'] = $this->get_post_title();
            $this->data['attr_title'] = $this->get_post_title(true);
            $this->data['type'] = $this->get_post_type();

            // 如果文章对象已经带有浏览量和点赞数据则预加载
            $post = $this->this_post();
            if ($post && property_exists($post, 'view_count')) {
                $this->data['views'] = $this->get_post_views();
            }
            if ($post && property_exists($post, 'like_count')) {
                $this->data['likes'] = $this->get_post_likes();
            }
        }
        //定义魔术方法预加载属性
        public function __get($name)
        {
            // 如果属性已存在直接返回
            if (array_key_exists($name, $this->data)) {
                return $this->data[$name];
            }
            // 按需加载各种属性
            switch ($name) {
                case 'status':
                    $this->data['status'] = $this->get_post_status();
                    break;
                case 'excerpt':
                    $this->data['excerpt'] = $this->get_post_excerpt();
                    break;
                case 'password':
                    $this->data['password'] = $this->get_post_password();
                    break;
                case 'preview':
                    $this->data['preview'] = $this->get_post_preview();
                    break;
                case 'comments':
                    $this->data['comments'] = $this->get_post_comments();
                    break;
                case 'comments_text':
                    $this->data['comments_text'] = $this->get_post_comments(true);
                    break;
                case 'date':
                    $this->data['date'] = $this->get_post_date('publish_date');
                    break;
                case 'date_ago':
                    $this->data['date_ago'] = $this->get_post_date('publish_timeago');
                    break;
                case 'datetime':
                    $this->data['date_iso'] = $this->get_post_date('iso');
                    break;
                case 'modified':
                    $this->data['modified'] = $this->get_post_date('modified_date');
                    break;
                case 'modified_ago':
                    $this->data['modified_ago'] = $this->get_post_date('modified_timeago');
                    break;
                case 'views':
                    $this->data['views'] = $this->get_post_views();
                    break;
                case 'views_text':
                    $this->data['views_text'] = $this->get_post_views(true);
                    break;
                case 'likes':
                    $this->data['likes'] = $this->get_post_likes();
                    break;
                case 'likes_text':
                    $this->data['likes_text'] = $this->get_post_likes(true);
                    break;
                case 'content':
                    $this->data['content'] = $this->get_post_content();
                    break;
                case 'thumbnail':
                    $this->data['thumbnail'] = $this->get_post_thumbnail();
                    break;
                case 'thumbnail_url':
                    $this->data['thumbnail_url'] = $this->get_post_thumbnail(false, true);
                    break;
                case 'cat_list':
                    $this->data['cat_list'] = $this->get_post_terms('category');
                    break;
                case 'tag_list':
                    $this->data['tag_list'] = $this->get_post_terms('post_tag');
                    break;
                case 'author_id':
                    $this->data['author_id'] = $this->get_post_author_id();
                    break;
                case 'author_avatar_x32':
                    $this->data['author_avatar_x32'] = $this->get_author_avatar(32);
                    break;
                case 'author_avatar_x64':
                    $this->data['author_avatar_x64'] = $this->get_author_avatar(64);
                    break;
                case 'author_name':
                    $this->data['author_name'] = $this->get_post_author_name();
                    break;
                case 'author_url':
                    $this->data['author_url'] = $this->get_post_author_url();
                    break;
                case 'author_desc':
                    $this->data['author_desc'] = $this->get_post_author_desc();
                    break;
                case 'is_author':
                    $this->data['is_post_author'] = $this->user_is_post_author();
                    break;
                case '30_dyas_outdated':
                    $this->data['is_outdated'] = $this->the_post_is_outdated(30);
                    break;
                default:
                    return true;
            }

            return $this->data[$name];
        }
        //检查属性是否存在
        public function __isset($name)
        {
            //尝试加载属性
            if (!array_key_exists($name, $this->data)) {
                $this->__get($name);
            }

            return isset($this->data[$name]);
        }
        //上一篇文章
        public function prev_post()
        {
            $prev_post = get_adjacent_post(false, '', true, 'category');

            return $prev_post ? new self($prev_post) : null;
        }
        //下一篇文章
        public function next_post()
        {
            $next_post = get_adjacent_post(false, '', false, 'category');

            return $next_post ? new self($next_post) : null;
        }
    }
}