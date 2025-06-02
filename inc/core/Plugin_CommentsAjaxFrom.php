<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 插件 文章评论列表和评论提交
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

if (!class_exists('AYA_Plugin_CommentsAjaxFrom')) {
    class AYA_Plugin_CommentsAjaxFrom
    {
        public static $nonce_name = 'aya_post_comment_submit';
        public static $comments_settings = [];

        public function __construct()
        {
            //前端action事件名：comments_submit
            add_action('wp_ajax_comments_submit', array($this, 'submit_comment'));
            add_action('wp_ajax_nopriv_comments_submit', array($this, 'submit_comment'));

            //前端action事件名：get_comments
            add_action('wp_ajax_get_comments', array($this, 'get_comments'));
            add_action('wp_ajax_nopriv_get_comments', array($this, 'get_comments'));

            //初始化评论设置
            self::$comments_settings = self::init_wp_comments_settings();
        }

        //获取WP评论设置
        public function init_wp_comments_settings()
        {
            return [
                //提交前检查
                'require_name_email' => get_option('require_name_email', true),
                'comment_registration' => get_option('comment_registration', false),
                'comment_max_links' => get_option('comment_max_links', 2),
                'comment_moderation' => get_option('comment_moderation', true),
                'comment_previously_approved' => get_option('comment_previously_approved', true),
                //评论嵌套设置
                'thread_comments' => get_option('thread_comments', true),
                'thread_comments_depth' => get_option('thread_comments_depth', 5),
                //评论分页设置
                'page_comments' => get_option('page_comments', false),
                'comments_per_page' => get_option('comments_per_page', 20),
                'default_comments_page' => get_option('default_comments_page', 'newest'),
                'comment_order' => get_option('comment_order', 'asc'),
            ];
        }

        //处理评论提交
        public function submit_comment()
        {
            //验证请求
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], self::$nonce_name)) {
                wp_send_json_error([
                    'message' => __('安全验证失败，请刷新页面后重试', 'AIYA'),
                    'code' => 'invalid_nonce'
                ]);
            }

            //检查文章ID
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

            if (!$post_id) {
                wp_send_json_error([
                    'message' => __('文章不存在', 'AIYA'),
                    'code' => 'missing_post_id'
                ]);
            }

            //检查评论开放状态
            if (!comments_open($post_id)) {
                wp_send_json_error([
                    'message' => __('该文章已关闭评论', 'AIYA'),
                    'code' => 'comments_closed'
                ]);
            }

            //检查是否要求用户登录才能评论
            $is_user_logged_in = is_user_logged_in();

            if ($this->comments_settings['comment_registration'] && !$is_user_logged_in) {
                wp_send_json_error([
                    'message' => __('您必须登录后才能发表评论', 'AIYA'),
                    'code' => 'login_required'
                ]);
            }

            //获取评论内容
            $comment_content = isset($_POST['comment']) ? wp_kses_post(trim($_POST['comment'])) : '';

            if (empty($comment_content)) {
                wp_send_json_error([
                    'message' => __('评论内容不能为空', 'AIYA'),
                    'code' => 'empty_comment'
                ]);
            }

            // 添加链接数量检查
            $max_links = $this->comments_settings['comment_max_links'];
            if ($max_links) {
                $num_links = preg_match_all('/<a [^>]*href/i', $comment_content, $out) +
                    preg_match_all('/\s+href\s*=/i', $comment_content, $out);

                if ($num_links > $max_links) {
                    wp_send_json_error([
                        'message' => sprintf(__('评论中包含太多链接。最多允许 %d 个链接。', 'AIYA'), $max_links),
                        'code' => 'comment_flood'
                    ]);
                }
            }
            //准备评论数据
            $comment_data = [
                'comment_post_ID' => $post_id,
                'comment_content' => $comment_content,
                'comment_parent' => isset($_POST['comment_parent']) ? intval($_POST['comment_parent']) : 0,
            ];

            //如果用户已登录
            if ($is_user_logged_in) {

                $user = wp_get_current_user();

                $comment_data['user_id'] = $user->ID;
                $comment_data['comment_author'] = $user->display_name;
                $comment_data['comment_author_email'] = $user->user_email;
                $comment_data['comment_author_url'] = $user->user_url;
            } else {

                //检查游客提交时的必要字段
                if ($this->comments_settings['require_name_email']) {
                    if (!isset($_POST['author']) || empty($_POST['author'])) {
                        wp_send_json_error([
                            'message' => __('请填写昵称', 'AIYA'),
                            'code' => 'missing_author'
                        ]);
                    }

                    if (!isset($_POST['email']) || empty($_POST['email']) || !is_email($_POST['email'])) {
                        wp_send_json_error([
                            'message' => __('请填写有效的邮箱', 'AIYA'),
                            'code' => 'invalid_email'
                        ]);
                    }
                }

                $comment_data['comment_author'] = sanitize_text_field($_POST['author']);
                $comment_data['comment_author_email'] = sanitize_email($_POST['email']);
            }

            //应用WordPress评论过滤器
            $comment_data = wp_filter_comment($comment_data);

            //检查评论审核状态设置
            $is_approved = 1;

            //评论必须经人工批准
            if ($this->comments_settings['comment_moderation']) {
                $is_approved = 0;
            }
            //评论者先前须有评论通过了审核
            else if ($this->comments_settings['comment_previously_approved'] && !$is_user_logged_in) {
                //查询此邮箱之前是否有通过审核的评论
                $previous_comments = get_comments([
                    'author_email' => $comment_data['comment_author_email'],
                    'status' => 'approve',
                    'count' => true
                ]);
                //没有已批准的评论时
                if ($previous_comments == 0) {
                    $is_approved = 0;
                }
            }

            $comment_data['comment_approved'] = $is_approved;

            //插入评论
            $comment_id = wp_insert_comment($comment_data);

            if (!$comment_id) {
                wp_send_json_error([
                    'message' => __('评论提交失败，请稍后重试', 'AIYA'),
                    'code' => 'insert_failed'
                ]);
            }

            //获取新创建的评论对象
            $comment = get_comment($comment_id);
            $comment_status = wp_get_comment_status($comment_id);

            //发送评论通知邮件
            wp_notify_postauthor($comment_id);

            //返回的评论数据
            $comment_data = $this->format_comment_data($comment);

            wp_send_json_success([
                'message' => ($comment_status == 'unapproved' || $comment_status == 'hold') ? __('等待审核后显示', 'AIYA') : __('评论提交成功', 'AIYA'),
                'status' => $comment_status,
                'comment_data' => $comment_data,
                'parent_id' => $comment_data['comment_parent']
            ]);
        }

        //评论列表 GET 
        public function get_comments()
        {
            //检查文章ID
            $post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;

            if (!$post_id) {
                wp_send_json_error([
                    'message' => __('文章不存在', 'AIYA'),
                    'code' => 'missing_post_id'
                ]);
            }

            //获取分页和排序参数
            $page = isset($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
            $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : null;

            //使用设置或参数确定排序顺序
            if ($order === null) {
                $comment_order = $this->comments_settings['comment_order']; // 默认设置
            } else {
                $comment_order = ($order === 'desc') ? 'desc' : 'asc';
            }

            //分页和嵌套设置
            $page_comments = $this->comments_settings['page_comments'];
            $comments_per_page = $this->comments_settings['comments_per_page'];
            $thread_comments = $this->comments_settings['thread_comments'];

            //未启用分页
            if (!$page_comments) {
                $comments_per_page = 0;
            }

            //查询参数
            $args = array(
                'post_id' => $post_id,
                'status' => 'approve',
                'order' => $comment_order,
            );

            //模拟WP原生逻辑处理嵌套评论分页
            if ($page_comments) {
                //获取文章所有评论数量
                $comments_count = get_comments_number($post_id);
                //启用了嵌套评论
                if ($thread_comments) {
                    $args['parent'] = 0;
                    $args['number'] = $comments_per_page;
                    $args['offset'] = ($page - 1) * $comments_per_page;

                    //获取顶级评论
                    $top_comments = get_comments($args);

                    //计算顶级评论总数和最大页数
                    $count_args = array_merge($args, ['count' => true]);

                    unset($count_args['number']);
                    unset($count_args['offset']);

                    //计算顶级评论总数
                    $top_comments_count = get_comments($count_args);
                    $max_pages = ceil($top_comments_count / $comments_per_page);

                    //如果有顶级评论，获取它们的所有回复
                    $all_comments = [];
                    if (!empty($top_comments)) {
                        //合并顶级评论
                        $all_comments = $top_comments;

                        //获取顶级评论的ID列表
                        $parent_ids = array_map(function ($comment) {
                            return $comment->comment_ID;
                        }, $top_comments);

                        //递归获取所有回复
                        $child_comments = $this->get_all_comment_replies($parent_ids, $post_id, $comment_order);

                        //合并所有评论
                        $all_comments = array_merge($all_comments, $child_comments);
                    }

                    $comments = $all_comments;
                } else {
                    $args['number'] = $comments_per_page;
                    $args['offset'] = ($page - 1) * $comments_per_page;

                    $comments = get_comments($args);

                    //使顶级评论数等于总评论数
                    $top_comments_count = $comments_count;
                    $max_pages = ceil($comments_count / $comments_per_page);
                }
            } else {
                $comments = get_comments($args);
                $comments_count = count($comments);
                $max_pages = 1;

                //计算顶级评论数
                $top_args = array_merge($args, ['parent' => 0, 'count' => true]);
                $top_comments_count = get_comments($top_args);
            }

            //构建评论的嵌套结构
            $comments_data = $this->build_comments_tree($comments);

            // 返回评论数据和分页信息
            wp_send_json_success([
                'comments' => $comments_data,
                'pagination' => [
                    'current_page' => $page,
                    'max_pages' => $max_pages,
                    'total_comments' => $comments_count,
                    'top_level_comments' => $top_comments_count,
                    'per_page' => $comments_per_page,
                    'comment_order' => $comment_order,
                    'page_comments' => $page_comments,
                    'thread_comments' => $thread_comments
                ]
            ]);
        }

        //递归查询评论的所有回复
        private function get_all_comment_replies($parent_ids, $post_id, $comment_order = 'asc')
        {
            if (empty($parent_ids)) {
                return [];
            }

            //获取指定父评论的直接回复
            $args = array(
                'post_id' => $post_id,
                'parent__in' => $parent_ids,
                'status' => 'approve',
                'order' => $comment_order
            );

            $replies = get_comments($args);

            //如果有回复，继续获取这些回复的子回复
            if (!empty($replies)) {
                $child_ids = array_map(function ($comment) {
                    return $comment->comment_ID;
                }, $replies);

                $child_replies = $this->get_all_comment_replies($child_ids, $post_id, $comment_order);

                // 合并所有回复
                $replies = array_merge($replies, $child_replies);
            }

            return $replies;
        }

        //构建评论嵌套树结构
        private function build_comments_tree($comments)
        {
            // 首先将所有评论格式化并按ID索引
            $comments_by_id = [];
            foreach ($comments as $comment) {
                $comments_by_id[$comment->comment_ID] = $this->format_comment_data($comment);
                $comments_by_id[$comment->comment_ID]['replies'] = []; // 添加replies数组存储子评论
                $comments_by_id[$comment->comment_ID]['depth'] = 1; // 初始深度为1
            }

            // 计算每个评论的深度
            foreach ($comments_by_id as $id => $comment_data) {
                $current_id = $id;
                $current_depth = 1;
                $parent_id = $comment_data['parent_id'];

                // 找到评论的所有父评论来计算深度
                while ($parent_id > 0 && isset($comments_by_id[$parent_id])) {
                    $current_depth++;
                    $parent_id = $comments_by_id[$parent_id]['parent_id'];
                }

                $comments_by_id[$id]['depth'] = $current_depth;
            }

            // 然后构建树结构
            $top_level_comments = [];
            foreach ($comments_by_id as $id => $comment_data) {
                if ($comment_data['parent_id'] == 0) {
                    // 这是顶级评论
                    $top_level_comments[] = &$comments_by_id[$id];
                } else {
                    // 这是回复评论，添加到父评论的replies数组
                    if (isset($comments_by_id[$comment_data['parent_id']])) {
                        $comments_by_id[$comment_data['parent_id']]['replies'][] = &$comments_by_id[$id];
                    } else {
                        // 如果父评论不在当前页中，将其作为顶级评论处理
                        // 但保留其parent_id信息以便前端识别
                        $top_level_comments[] = &$comments_by_id[$id];
                    }
                }
            }

            return $top_level_comments;
        }

        //定义格评论数据格式
        private function format_comment_data($comment)
        {
            // 获取评论作者头像
            $avatar = get_avatar_url($comment, ['size' => 32]);

            // 构建评论数据
            $data = [
                'id' => $comment->comment_ID,
                'parent_id' => $comment->comment_parent,
                'post_id' => $comment->comment_post_ID,
                'author' => [
                    'name' => $comment->comment_author,
                    'avatar' => $avatar,
                    'url' => $comment->comment_author_url,
                    'is_user' => !empty($comment->user_id)
                ],
                'content' => wpautop($comment->comment_content),
                'date' => [
                    'raw' => $comment->comment_date,
                    'human' => human_time_diff(strtotime($comment->comment_date_gmt), current_time('timestamp', true)) . __('前', 'AIYA')
                ]
            ];

            return $data;
        }
    }

    //实例化
    new AYA_Plugin_CommentsAjaxFrom();

    //为前端组件返回nonce参数
    function aya_nonce_comments_submit()
    {
        return wp_create_nonce(AYA_Plugin_CommentsAjaxFrom::$nonce_name);
    }
    //为前端组件返回评论设置
    function aya_get_comments_settings()
    {
        return AYA_Plugin_CommentsAjaxFrom::$comments_settings;
    }
}
