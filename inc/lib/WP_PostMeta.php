<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 处理WP的文章数据结构到数组
 * 
 **/

class AYA_WP_Post_ObjectInArray
{
    //替代一些WP原来的the_方法
    //private static $post;

    //定位文章
    public function aya_get_post($post_id = 0)
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

    //定位文章URL
    public function aya_get_post_url($post = null)
    {
        return get_permalink($post);
    }

    //获取文章ID
    public function aya_get_post_id($post = null)
    {
        if (!is_object($post)) {
            $post = get_post();
        }

        return !empty($post) ? $post->ID : false;
    }

    //获取文章标题检查是否为空
    public function aya_get_post_title($post = NULL, $attribute = false)
    {
        if (!is_object($post)) {
            $the_title = get_the_title($post);
        } else {
            $the_title = $post->post_title;
        }

        //检查文章标题
        if (strlen($the_title) === 0) {
            $the_title = __('无标题', 'AIYA_FRAMEWORK');
        }
        //是否转义
        if ($attribute == true) {
            $the_title = esc_attr(strip_tags($the_title));
        }
        //返回标题
        return $the_title;
    }

    //获取文章用户摘要
    public function aya_get_post_excerpt($post = NULL)
    {
        if (!is_object($post)) {
            $the_excerpt = get_the_excerpt($post);
        } else {
            $the_excerpt = $post->post_excerpt;
        }
        return $the_excerpt;
    }

    //获取文章状态
    public function aya_get_post_status($post = NULL)
    {
        if (!is_object($post)) {
            $the_status = get_post_status($post);
        } else {
            $the_status = $post->post_status;
        }

        //返回文本
        switch ($the_status) {
            case 'publish':
                //return __('已发布', 'AIYA_FRAMEWORK');
                return '';
            case 'pending':
                return __('待审', 'AIYA_FRAMEWORK');
            case 'future':
                return __('定时发布', 'AIYA_FRAMEWORK');
            case 'private':
                return __('私密文章', 'AIYA_FRAMEWORK');
            case 'draft':
                return __('草稿', 'AIYA_FRAMEWORK');
            case 'auto-draft':
                return __('自动保存的草稿', 'AIYA_FRAMEWORK');
            case 'inherit':
                return __('修订版本', 'AIYA_FRAMEWORK');
            case 'trash':
                return __('已删除', 'AIYA_FRAMEWORK');
            default:
                return '';
        }
    }

    //获取文章访问量
    public function aya_get_post_views($post = NULL)
    {
        if (!is_object($post)) {
            $post = get_post($post);
        }

        $the_views = $post->view_count;

        //计算为千位
        if ($the_views >= 1000) {
            return round($the_views / 1000, 1) . 'K';
        } else {
            return $the_views;
        }
    }

    //获取文章点赞数
    function aya_get_post_likes($post = NULL)
    {
        if (!is_object($post)) {
            $post = get_post($post);
        }

        $the_likes = $post->like_count;

        if ($the_likes > 0) {
            return $the_likes;
        } else {
            return '0';
        }
    }

    //作者信息数组
    public function aya_get_post_author_data($post = NULL, $avatar_size = 32)
    {
        if (!is_object($post)) {
            $post = get_post($post);
            $the_author = get_post_field('post_author', $post);
        } else {
            $the_author = $post->post_author;
        }

        $author_data = array(
            'id' => $the_author,
            'name' => get_the_author_meta('display_name', $the_author),
            'desc' => get_the_author_meta('description', $the_author),
            'url' => get_author_posts_url($the_author),
            'avatar' => get_avatar_url($the_author, $avatar_size),
            'is_submit' => (user_can($the_author, 'publish_posts')) ? true : false,
        );

        return $author_data;
    }

    //模板方法

    //获取文章分类
    public function aya_get_post_cat_list($post = NULL, $before = '<em>', $sep = '</em><em>', $after = '</em>')
    {
        if (!is_object($post)) {
            $post = get_post($post);
        }

        //判断类型是否是文章
        if ($post && $post->post_type == 'post') {
            $post_id = $post->ID;

            $the_cat_list = get_the_term_list($post_id, 'category', $before, $sep, $after);

            //省去判断文章类型是否支持标签，直接检查WP是否报错
            if (!is_wp_error($the_cat_list)) {
                return $the_cat_list;
            }
        }
        return false;
    }

    //获取文章标签
    public function aya_get_post_tag_list($post = NULL, $before = '<em>', $sep = '</em><em>', $after = '</em>')
    {
        if (!is_object($post)) {
            $post = get_post($post);
        }

        //判断类型是否是文章
        if ($post && $post->post_type == 'post') {
            $post_id = $post->ID;

            $the_tag_list = get_the_term_list($post_id, 'post_tag', $before, $sep, $after);

            //省去判断文章类型是否支持标签，直接检查WP是否报错
            if (!is_wp_error($the_tag_list)) {
                return $the_tag_list;
            }
        }
        return false;
    }

    //获取文章摘要
    public function aya_get_post_preview($post = NULL, $size = 225)
    {
        if (!is_object($post)) {
            $post = get_post($post);
        }

        //如果文章加密
        if (post_password_required($post)) {
            return __('这篇文章受密码保护，输入密码才能阅读。', 'AIYA_FRAMEWORK');
        }

        //如果用户设置了摘要，则直接输出摘要内容
        if (has_excerpt($post)) {
            $the_preview = $post->post_excerpt;
        }
        //没有摘要，截取正文内容
        else {
            //如果内容为空，则返回空
            $the_content = $post->post_content;
            $the_content = wp_strip_all_tags(strip_shortcodes($the_content));
            if ($the_content === null) {
                $the_preview = '';
            } else {
                //$the_preview = wp_trim_words($the_content, $size);
                //DEBUG：WP原生的摘要函数好像完全没法判断中文长度，改用PHP判断
                $the_preview = mb_strimwidth($the_content, 0, $size, '...');
            }
        }

        //再次检查摘要是否为空
        if (empty($the_preview)) {
            return __('这篇文章没有摘要内容。', 'AIYA_FRAMEWORK');
        } else {
            return $the_preview;
        }
    }

    //计算已发布时间
    public function aya_diff_timeago($time)
    {
        //更新：使用WordPress内置方法
        return human_time_diff($time, current_time('timestamp')) . __('前', 'AIYA_FRAMEWORK');
    }

    //获取文章发布时间
    public function aya_get_post_date($post = NULL, $modified = 'short')
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
                return date($date_format, $publish_time) . __(' [ 上次更新于 ', 'AIYA_FRAMEWORK') . self::aya_diff_timeago($modified_time) . __(' ]', 'AIYA_FRAMEWORK');
            case 'short':
                return date($date_format, $publish_time);
            case 'timeago':
                return self::aya_diff_timeago($publish_time);
            default:
                return date($date_format, $publish_time);
        }
    }

    //获取文章评论数
    public function aya_get_post_comments($post = NULL, $modified = false)
    {
        if (!is_object($post)) {
            $the_comment_count = get_comments_number($post);
        } else {
            $the_comment_count = $post->comment_count;
        }

        if ($modified == true) {
            if ($the_comment_count > 0) {
                return $the_comment_count . __('条评论', 'AIYA_FRAMEWORK');
            } else {
                return __('无人评论', 'AIYA_FRAMEWORK');
            }
        } else {
            return empty($the_comment_count) ? 0 : $the_comment_count;
        }
    }

    //获取文章正文
    public function aya_get_post_content($post = NULL)
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
    public function aya_get_post_media($post = NULL, $media = '')
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
    public function aya_get_post_thumbnail($post = NULL)
    {
        if (!is_object($post)) {
            $post = get_post($post);
        }
        //如果存在特色图片
        if (has_post_thumbnail($post)) {
            return get_the_post_thumbnail_url($post);
        }
        //返回空
        return false;
    }

    //获取文章缩略图
    public function aya_get_post_thumb($post = NULL)
    {
        if (!is_object($post)) {
            $post = get_post($post);
        }

        $post_id = $post->ID;

        //如果存在特色图片
        if (has_post_thumbnail($post_id)) {
            //返回
            $img_url = get_the_post_thumbnail_url($post_id);
        }
        //获取文章中的第一张图片
        else {
            //查询附件
            $media = self::aya_get_post_media($post, 'image');
            //弹出数组中的第一个元素
            $media = array_shift($media);

            //只取URL，忽略width和height
            $img_url = (empty($media)) ? NULL : wp_get_attachment_image_src($media->ID, '', false)[0];
        }

        //如果存在图片
        return ($img_url !== NULL) ? $img_url : false;
    }

    //查询方法

    //原查询方法
    public function aya_get_query($args = array())
    {
        //检查参数
        if (!is_array($args) || count($args) == 0) return;

        /**
         * WP_Query()参数指南：
         * https://developer.wordpress.org/reference/classes/wp_query/
         * 
         * 如需使用query_posts()方法：
         * 
         * //新建查询
         * if (!is_main_query()) $the_query = query_posts($args);
         * 
         * //重置查询
         * wp_reset_query();
         * 
         * Tips: 由于query_posts可以替代主查询，并不建议这样使用，可以直接new WP_Query()，防止互相影响。
         */

        //新建查询
        $post_query = get_posts($args);

        //重置查询
        wp_reset_postdata();

        //输出查询结果
        if (!empty($post_query)) {
            return $post_query;
        }
        //没有数据
        else {
            return false;
        }
    }

    //自定义单联查询函数
    public function aya_generate_post_li_query($type = '', $query_key = '', $query_value = '', $limit = 5, $order_by = 'date')
    {
        //检查输入是否为字符串
        if (!is_string($type) || !is_string($query_key) || !is_string($query_value)) return;

        //配置查询类型
        switch ($type) {
            case 'post':
                $args = array(
                    'p' => $query_key, //文章ID
                    'name' => $query_value, //文章'sulg'
                );
                break;
            case 'page':
                $args = array(
                    'page_id' => $query_key, //页面ID
                    'pagename' => $query_value, //页面'sulg'
                );
                break;
            case 'meta':
                $args = array(
                    'meta_key' => $query_key, //Meta键名
                    'meta_value' => $query_value, //Meta键值
                );
                break;
            case 'cat':
                $args = array(
                    'cat' => $query_key, //分类ID, 整数型或为'1,2,3,'
                    'category_name' => $query_value, //此参数仅检索分类'sulg'
                );
                break;
            case 'tag':
                $args = array(
                    'tag' => $query_key, //此参数仅检索标签'sulg'
                    'tag_id' => $query_value, //标签ID, 整数型或为'1,2,3,'
                );
                break;
            case 'author':
                $args = array(
                    'author' => $query_key, //作者ID, 整数型或为'1,2,3,'
                    'author_name' => $query_value, //此参数用于检索'user_nicename'，不是用户名称ID
                );
                break;
            case 'search':
                $args = array(
                    's' => $query_key,
                );
                break;
            case 'year':
                $args = array(
                    'year' => $query_key,
                );
                break;
            case 'month':
                $args = array(
                    'monthnum' => $query_key,
                );
                break;
            case 'day':
                $args = array(
                    'day' => $query_key,
                );
                break;
            default:
                echo 'ERROR: Query $args[type] is null.';
                return;
        }

        //配置默认查询参数
        $defalt_args = array(
            'order' => 'DESC', //可选值：'ASC'（升序）'DESC'（降序）
            'orderby' => $order_by, //排序依据，默认为'date'，可选值：none'（不排序）, 'ID', 'rand'（随机）, 'author', 'title', 'date', 'modified', 'parent', 'comment_count', 'post__in'
            'ignore_sticky_posts' => false, //是否忽略置顶文章
            'posts_per_page' => $limit, //每页显示的文章数
            //'post_type' => array('post', 'page',), //可添加值：'post'（文章）, 'page'（页面）, 'attachment'（媒体附件）, 'nav_menu_item'（导航菜单项）, 'revision'（修订版本）, 'custom_post_type'（自定义文章类型）
            //'post_status' => 'publish', //指定文章状态，可选值：'','publish'（已发布）, 'pending'（等待审核）, 'draft'（草稿）, 'auto-draft'（自动草稿）, 'future'（定时发布）, 'private'（私有）, 'inherit'（继承）
            //'offset' => 0, //偏移，跳过的文章数量
            //'perm' => 'readable', //可用的值有：'readable', 'editable'
        );

        //合并参数
        $query_args = array_merge($args, $defalt_args);

        return ($query_args);
    }

    //自定义分类法查询函数
    public function aya_generate_tax_terms_query($taxonomy = '', $terms = array(), $limit = 5, $field = 'id')
    {
        //检查数据
        if (!is_string($taxonomy) || !is_array($terms)) return;

        //Tips: tax_query 使用多维数组，但是此处没有写关联查询的方法
        $query_taxs = array(
            'taxonomy' => $taxonomy, //自定义分类法
            'field' => $field, //查询方式，可选'id'或'slug'
            'terms' => $terms, //分类名称
            'include_children' => true, //是否包含子分类
            'operator' => 'IN',
        );
        $query_args = array(
            'order' => 'DESC',
            'orderby' => 'date',
            'ignore_sticky_posts' => false,
            'posts_per_page' => $limit,
            'tax_query' => array(
                'relation' => 'AND', //SQL参数，可用'AND', 'OR'
                $query_taxs,
            ),
        );

        return ($query_args);
    }

    //自定义文章类型查询函数
    public function aya_generate_post_type_query($post_type, $limit = 5, $paged = 0)
    {
        //允许输入'1,2,3,'或直接输入数组
        if (is_string($post_type)) {
            $post_type = explode(", ", $post_type);
        }
        //检查数据
        if (!is_array($post_type)) return;

        //Tips: post_type 是数组
        $query_args = array(
            'order' => 'DESC',
            'orderby' => 'date',
            'ignore_sticky_posts' => false,
            'posts_per_page' => $limit,
            'paged' => ($paged == 0) ? '' : $paged, //判断分页
            'post_type' => $post_type,
        );

        return ($query_args);
    }
}

class AYA_Post_Meta extends AYA_Plugin_Data_Template_Of_Post_Meta
{
    public $post, $id, $url, $title, $attr_title, $status, $views, $likes, $date, $author, $comments, $thumb_url, $preview, $content;

    public function __construct($post_id = 0, $date_mod = 'short', $avatar_size = 32, $preview_size = 255)
    {
        //获取原始 POST 对象
        $post = parent::aya_get_post($post_id);
        //报错时直接退出
        if (!$post) return NULL;

        //获取数据存入当前对象
        $this->post = $post;
        $this->id = parent::aya_get_post_id($post);
        $this->url = parent::aya_get_post_url($post);
        $this->title = parent::aya_get_post_title($post, false);
        $this->attr_title = parent::aya_get_post_title($post, true);
        $this->status = parent::aya_get_post_status($post);
        $this->views = parent::aya_get_post_views($post);
        $this->likes = parent::aya_get_post_likes($post);
        $this->date = parent::aya_get_post_date($post, $date_mod);
        $this->author = parent::aya_get_post_author_data($post, $avatar_size);
        $this->comments = parent::aya_get_post_comments($post);
        $this->thumb_url = parent::aya_get_post_thumb($post);
        $this->preview = parent::aya_get_post_preview($post, $preview_size);
        $this->content = parent::aya_get_post_content($post);
    }
}

class AYA_Post_Content extends AYA_Plugin_Data_Template_Of_Post_Meta
{
    public $post, $id, $title, $status, $cat_list, $tag_list, $author, $views, $likes, $date, $comments, $excerpt, $thumbnail, $content;

    public function __construct($post_id = 0, $avatar_size = 128)
    {
        //获取原始 POST 对象
        $post = parent::aya_get_post($post_id);
        //报错时直接退出
        if (!$post) return NULL;

        //获取数据存入当前对象
        $this->post = $post;
        $this->id = parent::aya_get_post_id($post);
        $this->title = parent::aya_get_post_title($post, false);
        $this->status = parent::aya_get_post_status($post);
        //$this->cat_list = parent::aya_get_post_cat_list($post, '<em>', '</em><em>', '</em>');
        //$this->tag_list = parent::aya_get_post_tag_list($post, '<em>', '</em><em>', '</em>');
        $this->author = parent::aya_get_post_author_data($post, $avatar_size);
        $this->views = parent::aya_get_post_views($post);
        $this->likes = parent::aya_get_post_likes($post);
        $this->date = parent::aya_get_post_date($post, 'full');
        $this->comments = parent::aya_get_post_comments($post, true);
        $this->excerpt = parent::aya_get_post_excerpt($post);
        $this->thumbnail = parent::aya_get_post_thumbnail($post);
        $this->content = parent::aya_get_post_content($post);
    }
}

class AYA_Post_Query extends AYA_Plugin_Data_Template_Of_Post_Meta
{
    //列表查询
    public function query($query_array)
    {
        $post_query = parent::aya_get_query($query_array);

        if ($post_query === false) return NULL;

        $content = array();

        //循环输出
        foreach ($post_query as $post) {
            //使用WP方法返回完整调用
            //setup_postdata($post);

            //报错时直接退出
            if (!$post) continue;

            //使用主题定义的方法返回数据
            $content[$post->ID] = array(
                'id' => parent::aya_get_post_id($post),
                'url' => parent::aya_get_post_url($post),
                'title' => parent::aya_get_post_title($post, false),
                'attr_title' => parent::aya_get_post_title($post, true),
                'views' => parent::aya_get_post_views($post),
                'likes' => parent::aya_get_post_likes($post),
                'date' => parent::aya_get_post_date($post, 'short'),
                //'author' => parent::aya_get_post_author_data($post, 32),
                'comments' => parent::aya_get_post_comments($post),
                'thumb_url' => parent::aya_get_post_thumb($post),
            );
        }

        //返回结果
        return $content;
    }

    //列表查询
    public function li_query($type = '', $query_key = '', $query_value = '', $limit = 5, $order_by = 'date')
    {
        $query_array = parent::aya_generate_post_li_query($type, $query_key, $query_value, $limit, $order_by);

        return self::query($query_array);
    }

    //分类和自定义分类查询
    public function tax_terms_query($taxonomy = '', $terms = array(), $limit = 5, $field = 'id')
    {
        $query_array = parent::aya_generate_tax_terms_query($taxonomy, $terms, $limit, $field);

        return self::query($query_array);
    }

    //自定义文章类型查询
    public function post_type_query($post_type, $limit = 5, $paged = 0)
    {
        $query_array = parent::aya_generate_post_type_query($post_type, $limit, $paged);

        return self::query($query_array);
    }
}
