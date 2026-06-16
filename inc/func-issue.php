<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 独立 Issue 社区 CURD 方法
 * ------------------------------------------------------------------------------
 */

add_action('aya_install', 'aya_install_issue_db');

function aya_install_issue_db()
{
    global $wpdb;

    $issues_table = $wpdb->prefix . 'aya_issues';
    $comments_table = $wpdb->prefix . 'aya_issue_comments';
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $issues_table)) != $issues_table) {
        $sql = "CREATE TABLE $issues_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            user_id BIGINT UNSIGNED NOT NULL,
            type VARCHAR(32) NOT NULL DEFAULT 'issue',
            status VARCHAR(32) NOT NULL DEFAULT 'open',
            title VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            comment_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
            last_comment_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            last_comment_user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            last_comment_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY type (type),
            KEY status (status),
            KEY last_comment_at (last_comment_at),
            KEY created_at (created_at)
        ) $charset_collate;";

        dbDelta($sql);
    }

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $comments_table)) != $comments_table) {
        $sql = "CREATE TABLE $comments_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            issue_id BIGINT UNSIGNED NOT NULL,
            post_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            user_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(32) NOT NULL DEFAULT 'publish',
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY issue_id (issue_id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        dbDelta($sql);
    }
}

// 定义允许的 Issue 类型
function aya_issue_get_allowed_types()
{
    return apply_filters('aya_issue_allowed_types', [
        'issue',
        'discussion',
        'question',
        'feedback',
    ]);
}

// 定义允许的 Issue 状态
function aya_issue_get_allowed_statuses()
{
    return apply_filters('aya_issue_allowed_statuses', [
        'open',
        'closed',
        'progress',
        'accepted',
        'resolved',
        'pending',
    ]);
}

// 定义允许的回复状态
function aya_issue_get_allowed_comment_statuses()
{
    return apply_filters('aya_issue_allowed_comment_statuses', [
        'publish',
        // 'pending',
        // 'hidden',
    ]);
}

// 验证管理员用户或当前用户
function aya_issue_can_manage_user($user_id)
{
    $current_user_id = get_current_user_id();

    if ($current_user_id <= 0) {
        return false;
    }

    if (current_user_can('edit_pages')) {
        return true;
    }

    // TODO 当前仅允许管理员用户
    return false;
    //return intval($current_user_id) === intval($user_id);
}

// 验证 Issue 类型
function aya_issue_normalize_type($type)
{
    $type = sanitize_key((string) $type);

    if ($type === '') {
        $type = 'issue';
    }

    if (!in_array($type, aya_issue_get_allowed_types(), true)) {
        return new WP_Error('invalid_issue_type', __('无效的议题类型', 'aiya-cms'));
    }

    return $type;
}

// 验证 Issue 状态
function aya_issue_normalize_status($status)
{
    $status = sanitize_key((string) $status);

    if ($status === '') {
        $status = 'open';
    }

    if (!in_array($status, aya_issue_get_allowed_statuses(), true)) {
        return new WP_Error('invalid_issue_status', __('无效的议题状态', 'aiya-cms'));
    }

    return $status;
}

// 判断议题当前是否允许回复
function aya_issue_can_reply($issue)
{
    if (!$issue) {
        return false;
    }

    $status = isset($issue->status) ? sanitize_key((string) $issue->status) : '';

    return !in_array($status, ['closed', 'accepted'], true);
}

// 定义允许的 Issue 排序字段
function aya_issue_get_allowed_orderby_fields()
{
    return apply_filters('aya_issue_allowed_orderby_fields', [
        'id' => 'id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'last_comment_at' => 'last_comment_at',
        'comment_count' => 'comment_count',
    ]);
}

// 验证 Issue 排序字段
function aya_issue_normalize_orderby($orderby)
{
    $orderby = sanitize_key((string) $orderby);
    $allowed_orderby = aya_issue_get_allowed_orderby_fields();

    return isset($allowed_orderby[$orderby]) ? $allowed_orderby[$orderby] : 'updated_at';
}

// 验证排序方向
function aya_issue_normalize_order($order)
{
    return strtoupper(sanitize_text_field((string) $order)) === 'ASC' ? 'ASC' : 'DESC';
}

// 验证回复状态
function aya_issue_normalize_comment_status($status)
{
    $status = sanitize_key((string) $status);

    if ($status === '') {
        $status = 'publish';
    }

    if (!in_array($status, aya_issue_get_allowed_comment_statuses(), true)) {
        return new WP_Error('invalid_comment_status', __('无效的回复状态', 'aiya-cms'));
    }

    return $status;
}

// 验证标题
function aya_issue_sanitize_title($title)
{
    return sanitize_text_field(wp_unslash((string) $title));
}

// 验证正文内容
function aya_issue_sanitize_content($content)
{
    return trim(wp_kses_post(wp_unslash((string) $content)));
}

// 验证关联文章 ID
function aya_issue_validate_post_id($post_id)
{
    $post_id = absint($post_id);

    if ($post_id === 0) {
        return 0;
    }

    $post = get_post($post_id);

    if (!$post instanceof WP_Post) {
        return new WP_Error('invalid_post', __('关联文章不存在', 'aiya-cms'));
    }

    return $post_id;
}

// 验证时允许空的 post_id 参数
function aya_issue_validate_nullable_post_param($value)
{
    if ($value === null) {
        return true;
    }

    if (is_string($value)) {
        $value = trim(wp_unslash($value));

        if ($value === '' || strtolower($value) === 'null') {
            return true;
        }
    }

    return is_numeric($value);
}

// 验证空的关联文章 ID
function aya_issue_sanitize_nullable_post_param($value)
{
    if ($value === null) {
        return 0;
    }

    if (is_string($value)) {
        $value = trim(wp_unslash($value));

        if ($value === '' || strtolower($value) === 'null') {
            return 0;
        }
    }

    return absint($value);
}

// 提取 Issue 行数据
function aya_issue_get_row($issue_id)
{
    global $wpdb;

    $issue_id = absint($issue_id);

    if ($issue_id <= 0) {
        return null;
    }

    $table = $wpdb->prefix . 'aya_issues';

    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE id = %d LIMIT 1",
        $issue_id
    ));
}

// 提取 Issue 回复行数据
function aya_issue_comment_get_row($comment_id)
{
    global $wpdb;

    $comment_id = absint($comment_id);

    if ($comment_id <= 0) {
        return null;
    }

    $table = $wpdb->prefix . 'aya_issue_comments';

    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE id = %d LIMIT 1",
        $comment_id
    ));
}

// 提取 Issue 回复数量
function aya_issue_comment_count($issue_id, $status = 'publish')
{
    global $wpdb;

    $issue_id = absint($issue_id);

    if ($issue_id <= 0) {
        return 0;
    }

    $table = $wpdb->prefix . 'aya_issue_comments';

    if ($status === '' || $status === null) {
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM $table WHERE issue_id = %d",
            $issue_id
        )));
    }

    return intval($wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(id) FROM $table WHERE issue_id = %d AND status = %s",
        $issue_id,
        $status
    )));
}

// 同步 Issue 回复统计信息
function aya_issue_sync_comment_stats($issue_id)
{
    global $wpdb;

    $issue = aya_issue_get_row($issue_id);

    if (!$issue) {
        return false;
    }

    $issues_table = $wpdb->prefix . 'aya_issues';
    $comments_table = $wpdb->prefix . 'aya_issue_comments';
    $comment_count = aya_issue_comment_count($issue_id, 'publish');

    $last_comment = $wpdb->get_row($wpdb->prepare(
        "SELECT id, user_id, created_at
         FROM $comments_table
         WHERE issue_id = %d AND status = %s
         ORDER BY id DESC
         LIMIT 1",
        absint($issue_id),
        'publish'
    ));

    $wpdb->update(
        $issues_table,
        [
            'comment_count' => $comment_count,
            'last_comment_id' => $last_comment ? intval($last_comment->id) : 0,
            'last_comment_user_id' => $last_comment ? intval($last_comment->user_id) : 0,
            'last_comment_at' => $last_comment ? $last_comment->created_at : null,
            'updated_at' => current_time('mysql', true),
        ],
        ['id' => absint($issue_id)],
        ['%d', '%d', '%d', '%s', '%s'],
        ['%d']
    );

    return true;
}

// 查询 Issue 行数据
function aya_issue_query_rows($args = [])
{
    global $wpdb;

    $defaults = [
        'post_id' => 0,
        'user_id' => 0,
        'type' => '',
        'status' => '',
        'paged' => 1,
        'per_page' => 20,
        'orderby' => 'updated_at',
        'order' => 'DESC',
    ];

    $args = wp_parse_args($args, $defaults);
    $table = $wpdb->prefix . 'aya_issues';
    $where = ['1=1'];
    $params = [];

    if (!empty($args['post_id'])) {
        $where[] = 'post_id = %d';
        $params[] = absint($args['post_id']);
    }

    if (!empty($args['user_id'])) {
        $where[] = 'user_id = %d';
        $params[] = absint($args['user_id']);
    }

    if ($args['type'] !== '') {
        $type = aya_issue_normalize_type($args['type']);

        if (is_wp_error($type)) {
            return $type;
        }

        $where[] = 'type = %s';
        $params[] = $type;
    }

    if ($args['status'] !== '') {
        $status = aya_issue_normalize_status($args['status']);

        if (is_wp_error($status)) {
            return $status;
        }

        $where[] = 'status = %s';
        $params[] = $status;
    }

    $orderby = aya_issue_normalize_orderby($args['orderby']);
    $order = aya_issue_normalize_order($args['order']);
    $per_page = max(1, min(100, absint($args['per_page'])));
    $paged = max(1, absint($args['paged']));
    $offset = ($paged - 1) * $per_page;

    $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $where) . " ORDER BY $orderby $order LIMIT %d OFFSET %d";
    $params[] = $per_page;
    $params[] = $offset;

    return $wpdb->get_results($wpdb->prepare($sql, $params));
}

// 统计 Issue 总数
function aya_issue_count_rows($args = [])
{
    global $wpdb;

    $defaults = [
        'post_id' => 0,
        'user_id' => 0,
        'type' => '',
        'status' => '',
    ];

    $args = wp_parse_args($args, $defaults);
    $table = $wpdb->prefix . 'aya_issues';
    $where = ['1=1'];
    $params = [];

    if (!empty($args['post_id'])) {
        $where[] = 'post_id = %d';
        $params[] = absint($args['post_id']);
    }

    if (!empty($args['user_id'])) {
        $where[] = 'user_id = %d';
        $params[] = absint($args['user_id']);
    }

    if ($args['type'] !== '') {
        $type = aya_issue_normalize_type($args['type']);

        if (is_wp_error($type)) {
            return $type;
        }

        $where[] = 'type = %s';
        $params[] = $type;
    }

    if ($args['status'] !== '') {
        $status = aya_issue_normalize_status($args['status']);

        if (is_wp_error($status)) {
            return $status;
        }

        $where[] = 'status = %s';
        $params[] = $status;
    }

    $sql = "SELECT COUNT(id) FROM $table WHERE " . implode(' AND ', $where);

    if (empty($params)) {
        return intval($wpdb->get_var($sql));
    }

    return intval($wpdb->get_var($wpdb->prepare($sql, $params)));
}

// 查询 Issue 回复行数据
function aya_issue_query_comment_rows($issue_id, $args = [])
{
    global $wpdb;

    $issue_id = absint($issue_id);

    if ($issue_id <= 0) {
        return [];
    }

    $defaults = [
        'status' => 'publish',
        'paged' => 1,
        'per_page' => 50,
        'order' => 'ASC',
    ];

    $args = wp_parse_args($args, $defaults);
    $table = $wpdb->prefix . 'aya_issue_comments';
    $where = ['issue_id = %d'];
    $params = [$issue_id];

    if ($args['status'] !== '' && $args['status'] !== null) {
        $status = aya_issue_normalize_comment_status($args['status']);

        if (is_wp_error($status)) {
            return $status;
        }

        $where[] = 'status = %s';
        $params[] = $status;
    }

    $order = strtoupper((string) $args['order']) === 'DESC' ? 'DESC' : 'ASC';
    $per_page = max(1, min(200, absint($args['per_page'])));
    $paged = max(1, absint($args['paged']));
    $offset = ($paged - 1) * $per_page;

    $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $where) . " ORDER BY id $order LIMIT %d OFFSET %d";
    $params[] = $per_page;
    $params[] = $offset;

    return $wpdb->get_results($wpdb->prepare($sql, $params));
}

// 格式化日期时间
function aya_issue_format_datetime($datetime)
{
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return null;
    }

    return mysql_to_rfc3339($datetime);
}

// 提取用户摘要信息
function aya_issue_get_user_summary($user_id)
{
    $user_id = absint($user_id);

    if ($user_id <= 0) {
        return null;
    }

    $user = get_userdata($user_id);
    if (!$user instanceof WP_User) {
        return null;
    }

    return [
        'id' => $user_id,
        'name' => $user->display_name ?: $user->user_login,
        'avatar' => get_avatar_url($user_id, ['size' => 64]),
        'url' => get_author_posts_url($user_id),
    ];
}

// 提取关联文章摘要信息
function aya_issue_get_post_summary($post_id)
{
    $post_id = absint($post_id);

    if ($post_id <= 0) {
        return null;
    }

    $post = get_post($post_id);
    if (!$post instanceof WP_Post) {
        return null;
    }

    return [
        'id' => $post_id,
        'title' => get_the_title($post),
        'url' => get_permalink($post),
        'type' => $post->post_type,
    ];
}

// 格式化 Issue 数据
function aya_issue_prepare_issue_data($issue)
{
    if (!$issue) {
        return null;
    }

    $issue_id = isset($issue->id) ? absint($issue->id) : 0;

    return [
        'id' => $issue_id,
        'post_id' => isset($issue->post_id) ? absint($issue->post_id) : 0,
        'user_id' => isset($issue->user_id) ? absint($issue->user_id) : 0,
        'type' => isset($issue->type) ? (string) $issue->type : 'issue',
        'status' => isset($issue->status) ? (string) $issue->status : 'open',
        'title' => isset($issue->title) ? (string) $issue->title : '',
        'content' => isset($issue->content) ? (string) $issue->content : '',
        'comment_count' => isset($issue->comment_count) ? intval($issue->comment_count) : 0,
        'last_comment_id' => isset($issue->last_comment_id) ? absint($issue->last_comment_id) : 0,
        'last_comment_user_id' => isset($issue->last_comment_user_id) ? absint($issue->last_comment_user_id) : 0,
        'last_comment_at' => aya_issue_format_datetime($issue->last_comment_at ?? null),
        'created_at' => aya_issue_format_datetime($issue->created_at ?? null),
        'updated_at' => aya_issue_format_datetime($issue->updated_at ?? null),
        'permalink' => add_query_arg(['issue' => $issue_id], home_url('/issues/')),
        'user' => aya_issue_get_user_summary($issue->user_id ?? 0),
        'post' => aya_issue_get_post_summary($issue->post_id ?? 0),
        'last_comment_user' => aya_issue_get_user_summary($issue->last_comment_user_id ?? 0),
        'can_edit' => aya_issue_can_manage_user($issue->user_id ?? 0),
        'can_delete' => aya_issue_can_manage_user($issue->user_id ?? 0),
        'can_reply' => aya_issue_can_reply($issue),
    ];
}

// 格式化 Issue 回复数据
function aya_issue_prepare_issue_comment_data($comment)
{
    if (!$comment) {
        return null;
    }

    return [
        'id' => isset($comment->id) ? absint($comment->id) : 0,
        'issue_id' => isset($comment->issue_id) ? absint($comment->issue_id) : 0,
        'post_id' => isset($comment->post_id) ? absint($comment->post_id) : 0,
        'user_id' => isset($comment->user_id) ? absint($comment->user_id) : 0,
        'status' => isset($comment->status) ? (string) $comment->status : 'publish',
        'content' => isset($comment->content) ? (string) $comment->content : '',
        'created_at' => aya_issue_format_datetime($comment->created_at ?? null),
        'updated_at' => aya_issue_format_datetime($comment->updated_at ?? null),
        'user' => aya_issue_get_user_summary($comment->user_id ?? 0),
        'can_edit' => aya_issue_can_manage_user($comment->user_id ?? 0),
        'can_delete' => aya_issue_can_manage_user($comment->user_id ?? 0),
    ];
}

/*
 * ------------------------------------------------------------------------------
 * 独立 Issue 社区 REST-API
 * ------------------------------------------------------------------------------
 */

$issue_api = new AYA_WP_REST_API('aiya/v1');

// 创建议题
$issue_api->register_route('issue/create', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {
        global $wpdb;

        $nonce_check = aya_rest_api_verify_nonce($issue_api, $request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return $issue_api->error_response('permission_denied', ['detail' => __('请先登录后再操作', 'aiya-cms')]);
        }

        $post_id = aya_issue_validate_post_id($request->get_param('post_id'));
        if (is_wp_error($post_id)) {
            return $issue_api->error_response('invalid_param', ['detail' => $post_id->get_error_message()]);
        }

        $type = aya_issue_normalize_type($request->get_param('type'));
        if (is_wp_error($type)) {
            return $issue_api->error_response('invalid_param', ['detail' => $type->get_error_message()]);
        }

        $status = aya_issue_normalize_status($request->get_param('status'));
        if (is_wp_error($status)) {
            return $issue_api->error_response('invalid_param', ['detail' => $status->get_error_message()]);
        }

        $title = aya_issue_sanitize_title($request->get_param('title'));
        if ($title === '') {
            return $issue_api->error_response('invalid_param', ['detail' => __('议题标题不能为空', 'aiya-cms')]);
        }

        $content = aya_issue_sanitize_content($request->get_param('content'));

        $table = $wpdb->prefix . 'aya_issues';
        $now = current_time('mysql', true);

        $result = $wpdb->insert($table, [
            'post_id' => $post_id,
            'user_id' => $user_id,
            'type' => $type,
            'status' => $status,
            'title' => $title,
            'content' => $content,
            'created_at' => $now,
            'updated_at' => $now,
        ], ['%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s']);

        if ($result === false) {
            return $issue_api->error_response('invalid_param', ['detail' => $wpdb->last_error ?: __('议题创建失败', 'aiya-cms')]);
        }

        return $issue_api->response([
            'message' => __('议题已创建', 'aiya-cms'),
            'issue' => aya_issue_prepare_issue_data(aya_issue_get_row($wpdb->insert_id)),
        ], 201);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'post_id' => [
            'required' => false,
            'type' => 'numeric',
            'validate_callback' => function ($value) {
                return aya_issue_validate_nullable_post_param($value);
            },
            'sanitize_callback' => function ($value) {
                return aya_issue_sanitize_nullable_post_param($value);
            },
        ],
        'type' => [
            'required' => false,
            'type' => 'string',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'title' => [
            'required' => true,
            'type' => 'string',
        ],
        'content' => [
            'required' => false,
            'type' => 'string',
        ],
    ],
]);

// 获取议题详情
$issue_api->register_route('issue/get', [
    'methods' => 'GET',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {

        $issue_id = absint($request->get_param('issue_id'));
        if ($issue_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少议题 ID', 'aiya-cms')]);
        }

        $issue = aya_issue_get_row($issue_id);
        if (!$issue) {
            return $issue_api->error_response('not_found', ['detail' => __('议题不存在', 'aiya-cms')]);
        }

        return $issue_api->response([
            'issue' => aya_issue_prepare_issue_data($issue),
        ]);
    },
    'permission_callback' => '__return_true',
    'args' => [
        'issue_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
    ],
]);

// 获取议题列表
$issue_api->register_route('issue/list', [
    'methods' => 'GET',
    'callback' =>  function (WP_REST_Request $request) use ($issue_api) {

        $query_args = [
            'post_id' => absint($request->get_param('post_id')),
            'user_id' => absint($request->get_param('user_id')),
            'type' => sanitize_key((string) $request->get_param('type')),
            'status' => sanitize_key((string) $request->get_param('status')),
            'paged' => absint($request->get_param('paged')) ?: 1,
            'per_page' => absint($request->get_param('per_page')) ?: 20,
            'orderby' => aya_issue_normalize_orderby($request->get_param('orderby')),
            'order' => aya_issue_normalize_order($request->get_param('order')),
        ];

        $issues = aya_issue_query_rows($query_args);

        if (is_wp_error($issues)) {
            return $issue_api->error_response('invalid_param', ['detail' => $issues->get_error_message()]);
        }

        $total = aya_issue_count_rows($query_args);
        if (is_wp_error($total)) {
            return $issue_api->error_response('invalid_param', ['detail' => $total->get_error_message()]);
        }

        return $issue_api->response([
            'items' => array_values(array_filter(array_map('aya_issue_prepare_issue_data', $issues))),
            'count' => count($issues),
            'total' => $total,
            'paged' => intval($query_args['paged']),
            'per_page' => intval($query_args['per_page']),
            'orderby' => $query_args['orderby'],
            'order' => $query_args['order'],
        ]);
    },
    'permission_callback' => '__return_true',
    'args' => [
        'post_id' => [
            'required' => false,
            'type' => 'numeric',
            'validate_callback' => function ($value) {
                return aya_issue_validate_nullable_post_param($value);
            },
            'sanitize_callback' => function ($value) {
                return aya_issue_sanitize_nullable_post_param($value);
            },
        ],
        'user_id' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'type' => [
            'required' => false,
            'type' => 'string',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'paged' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'per_page' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'orderby' => [
            'required' => false,
            'type' => 'string',
        ],
        'order' => [
            'required' => false,
            'type' => 'string',
        ],
    ],
]);

// 获取文章下的议题列表
$issue_api->register_route('issue/by-post', [
    'methods' => 'GET',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {

        $post_id = aya_issue_validate_post_id($request->get_param('post_id'));
        if (is_wp_error($post_id)) {
            return $issue_api->error_response('invalid_param', ['detail' => $post_id->get_error_message()]);
        }

        if ($post_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少文章 ID', 'aiya-cms')]);
        }

        $query_args = [
            'post_id' => $post_id,
            'user_id' => absint($request->get_param('user_id')),
            'type' => sanitize_key((string) $request->get_param('type')),
            'status' => sanitize_key((string) $request->get_param('status')),
            'paged' => absint($request->get_param('paged')) ?: 1,
            'per_page' => absint($request->get_param('per_page')) ?: 20,
            'orderby' => aya_issue_normalize_orderby($request->get_param('orderby')),
            'order' => aya_issue_normalize_order($request->get_param('order')),
        ];

        $issues = aya_issue_query_rows($query_args);

        if (is_wp_error($issues)) {
            return $issue_api->error_response('invalid_param', ['detail' => $issues->get_error_message()]);
        }

        $total = aya_issue_count_rows($query_args);
        if (is_wp_error($total)) {
            return $issue_api->error_response('invalid_param', ['detail' => $total->get_error_message()]);
        }

        return $issue_api->response([
            'post_id' => $post_id,
            'items' => array_values(array_filter(array_map('aya_issue_prepare_issue_data', $issues))),
            'count' => count($issues),
            'total' => $total,
            'paged' => intval($query_args['paged']),
            'per_page' => intval($query_args['per_page']),
            'orderby' => $query_args['orderby'],
            'order' => $query_args['order'],
        ]);
    },
    'permission_callback' => '__return_true',
    'args' => [
        'post_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
        'user_id' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'type' => [
            'required' => false,
            'type' => 'string',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'paged' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'per_page' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'orderby' => [
            'required' => false,
            'type' => 'string',
        ],
        'order' => [
            'required' => false,
            'type' => 'string',
        ],
    ],
]);

// 更新议题
$issue_api->register_route('issue/update', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {
        global $wpdb;

        $nonce_check = aya_rest_api_verify_nonce($issue_api, $request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        $issue_id = absint($request->get_param('issue_id'));
        if ($issue_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少议题 ID', 'aiya-cms')]);
        }

        $issue = aya_issue_get_row($issue_id);
        if (!$issue) {
            return $issue_api->error_response('not_found', ['detail' => __('议题不存在', 'aiya-cms')]);
        }

        if (!aya_issue_can_manage_user($issue->user_id)) {
            return $issue_api->error_response('permission_denied', ['detail' => __('无权修改该议题', 'aiya-cms')]);
        }

        $data = [];
        $format = [];

        if ($request->has_param('post_id')) {
            $post_id = aya_issue_validate_post_id($request->get_param('post_id'));
            if (is_wp_error($post_id)) {
                return $issue_api->error_response('invalid_param', ['detail' => $post_id->get_error_message()]);
            }

            $data['post_id'] = $post_id;
            $format[] = '%d';
        }

        if ($request->has_param('type')) {
            $type = aya_issue_normalize_type($request->get_param('type'));
            if (is_wp_error($type)) {
                return $issue_api->error_response('invalid_param', ['detail' => $type->get_error_message()]);
            }

            $data['type'] = $type;
            $format[] = '%s';
        }

        if ($request->has_param('status')) {
            $status = aya_issue_normalize_status($request->get_param('status'));
            if (is_wp_error($status)) {
                return $issue_api->error_response('invalid_param', ['detail' => $status->get_error_message()]);
            }

            $data['status'] = $status;
            $format[] = '%s';
        }

        if ($request->has_param('title')) {
            $title = aya_issue_sanitize_title($request->get_param('title'));
            if ($title === '') {
                return $issue_api->error_response('invalid_param', ['detail' => __('议题标题不能为空', 'aiya-cms')]);
            }

            $data['title'] = $title;
            $format[] = '%s';
        }

        if ($request->has_param('content')) {
            $content = aya_issue_sanitize_content($request->get_param('content'));
            if ($content === '') {
                return $issue_api->error_response('invalid_param', ['detail' => __('议题内容不能为空', 'aiya-cms')]);
            }

            $data['content'] = $content;
            $format[] = '%s';
        }

        if (empty($data)) {
            return $issue_api->response([
                'message' => __('没有需要更新的字段', 'aiya-cms'),
                'issue' => $issue,
            ]);
        }

        $data['updated_at'] = current_time('mysql', true);
        $format[] = '%s';

        $table = $wpdb->prefix . 'aya_issues';
        $result = $wpdb->update($table, $data, ['id' => $issue_id], $format, ['%d']);

        if ($result === false) {
            return $issue_api->error_response('invalid_param', ['detail' => $wpdb->last_error ?: __('议题更新失败', 'aiya-cms')]);
        }

        if (isset($data['post_id'])) {
            $comments_table = $wpdb->prefix . 'aya_issue_comments';
            $wpdb->update(
                $comments_table,
                ['post_id' => absint($data['post_id'])],
                ['issue_id' => $issue_id],
                ['%d'],
                ['%d']
            );
        }

        return $issue_api->response([
            'message' => __('议题已更新', 'aiya-cms'),
            'issue' => aya_issue_prepare_issue_data(aya_issue_get_row($issue_id)),
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'issue_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
        'post_id' => [
            'required' => false,
            'type' => 'numeric',
            'validate_callback' => function ($value) {
                return aya_issue_validate_nullable_post_param($value);
            },
            'sanitize_callback' => function ($value) {
                return aya_issue_sanitize_nullable_post_param($value);
            },
        ],
        'type' => [
            'required' => false,
            'type' => 'string',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'title' => [
            'required' => false,
            'type' => 'string',
        ],
        'content' => [
            'required' => false,
            'type' => 'string',
        ],
    ],
]);

// 删除议题
$issue_api->register_route('issue/delete', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {
        global $wpdb;

        $nonce_check = aya_rest_api_verify_nonce($issue_api, $request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        $issue_id = absint($request->get_param('issue_id'));
        if ($issue_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少议题 ID', 'aiya-cms')]);
        }

        $issue = aya_issue_get_row($issue_id);
        if (!$issue) {
            return $issue_api->error_response('not_found', ['detail' => __('议题不存在', 'aiya-cms')]);
        }

        if (!aya_issue_can_manage_user($issue->user_id)) {
            return $issue_api->error_response('permission_denied', ['detail' => __('无权删除该议题', 'aiya-cms')]);
        }

        $issues_table = $wpdb->prefix . 'aya_issues';
        $comments_table = $wpdb->prefix . 'aya_issue_comments';

        $deleted_comments = $wpdb->delete($comments_table, ['issue_id' => $issue_id], ['%d']);
        if ($deleted_comments === false) {
            return $issue_api->error_response('invalid_param', ['detail' => $wpdb->last_error ?: __('议题回复删除失败', 'aiya-cms')]);
        }

        $deleted = $wpdb->delete($issues_table, ['id' => $issue_id], ['%d']);
        if ($deleted === false) {
            return $issue_api->error_response('invalid_param', ['detail' => $wpdb->last_error ?: __('议题删除失败', 'aiya-cms')]);
        }

        return $issue_api->response([
            'message' => __('议题已删除', 'aiya-cms'),
            'issue_id' => $issue_id,
            'deleted_comments' => intval($deleted_comments),
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'issue_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
    ],
]);

// 创建议题回复
$issue_api->register_route('issue/comment/create', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {
        global $wpdb;

        $nonce_check = aya_rest_api_verify_nonce($issue_api, $request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        $issue_id = absint($request->get_param('issue_id'));
        if ($issue_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少议题 ID', 'aiya-cms')]);
        }

        $issue = aya_issue_get_row($issue_id);
        if (!$issue) {
            return $issue_api->error_response('not_found', ['detail' => __('议题不存在', 'aiya-cms')]);
        }

        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return $issue_api->error_response('permission_denied', ['detail' => __('请先登录后再操作', 'aiya-cms')]);
        }

        if (!aya_issue_can_reply($issue)) {
            return $issue_api->error_response('invalid_param', ['detail' => __('当前议题状态不允许回复', 'aiya-cms')]);
        }

        $status = aya_issue_normalize_comment_status($request->get_param('status'));
        if (is_wp_error($status)) {
            return $issue_api->error_response('invalid_param', ['detail' => $status->get_error_message()]);
        }

        $content = aya_issue_sanitize_content($request->get_param('content'));
        if ($content === '') {
            return $issue_api->error_response('invalid_param', ['detail' => __('回复内容不能为空', 'aiya-cms')]);
        }

        $table = $wpdb->prefix . 'aya_issue_comments';
        $now = current_time('mysql', true);

        $result = $wpdb->insert($table, [
            'issue_id' => $issue_id,
            'post_id' => intval($issue->post_id),
            'user_id' => $user_id,
            'status' => $status,
            'content' => $content,
            'created_at' => $now,
            'updated_at' => $now,
        ], ['%d', '%d', '%d', '%s', '%s', '%s', '%s']);

        if ($result === false) {
            return $issue_api->error_response('invalid_param', ['detail' => $wpdb->last_error ?: __('回复创建失败', 'aiya-cms')]);
        }

        aya_issue_sync_comment_stats($issue_id);

        return $issue_api->response([
            'message' => __('回复已创建', 'aiya-cms'),
            'comment' => aya_issue_prepare_issue_comment_data(aya_issue_comment_get_row($wpdb->insert_id)),
        ], 201);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'issue_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'content' => [
            'required' => true,
            'type' => 'string',
        ],
    ],
]);

// 获取议题回复详情
$issue_api->register_route('issue/comment/get', [
    'methods' => 'GET',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {

        $comment_id = absint($request->get_param('comment_id'));
        if ($comment_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少回复 ID', 'aiya-cms')]);
        }

        $comment = aya_issue_comment_get_row($comment_id);
        if (!$comment) {
            return $issue_api->error_response('not_found', ['detail' => __('回复不存在', 'aiya-cms')]);
        }

        return $issue_api->response([
            'comment' => aya_issue_prepare_issue_comment_data($comment),
        ]);
    },
    'permission_callback' => '__return_true',
    'args' => [
        'comment_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
    ],
]);

// 获取议题回复列表
$issue_api->register_route('issue/comment/list', [
    'methods' => 'GET',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {

        $issue_id = absint($request->get_param('issue_id'));
        if ($issue_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少议题 ID', 'aiya-cms')]);
        }

        $issue = aya_issue_get_row($issue_id);
        if (!$issue) {
            return $issue_api->error_response('not_found', ['detail' => __('议题不存在', 'aiya-cms')]);
        }

        $query_args = [
            'status' => $request->has_param('status') ? sanitize_key((string) $request->get_param('status')) : 'publish',
            'paged' => absint($request->get_param('paged')) ?: 1,
            'per_page' => absint($request->get_param('per_page')) ?: 50,
            'order' => sanitize_text_field((string) $request->get_param('order')),
        ];

        $comments = aya_issue_query_comment_rows($issue_id, $query_args);

        if (is_wp_error($comments)) {
            return $issue_api->error_response('invalid_param', ['detail' => $comments->get_error_message()]);
        }

        $total = aya_issue_comment_count($issue_id, $query_args['status']);

        return $issue_api->response([
            'issue_id' => $issue_id,
            'items' => array_values(array_filter(array_map('aya_issue_prepare_issue_comment_data', $comments))),
            'count' => count($comments),
            'total' => $total,
            'paged' => intval($query_args['paged']),
            'per_page' => intval($query_args['per_page']),
        ]);
    },
    'permission_callback' => '__return_true',
    'args' => [
        'issue_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'paged' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'per_page' => [
            'required' => false,
            'type' => 'numeric',
        ],
        'order' => [
            'required' => false,
            'type' => 'string',
        ],
    ],
]);

// 更新议题回复
$issue_api->register_route('issue/comment/update', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {
        global $wpdb;

        $nonce_check = aya_rest_api_verify_nonce($issue_api, $request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        $comment_id = absint($request->get_param('comment_id'));
        if ($comment_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少回复 ID', 'aiya-cms')]);
        }

        $comment = aya_issue_comment_get_row($comment_id);
        if (!$comment) {
            return $issue_api->error_response('not_found', ['detail' => __('回复不存在', 'aiya-cms')]);
        }

        if (!aya_issue_can_manage_user($comment->user_id)) {
            return $issue_api->error_response('permission_denied', ['detail' => __('无权修改该回复', 'aiya-cms')]);
        }

        $data = [];
        $format = [];

        if ($request->has_param('status')) {
            $status = aya_issue_normalize_comment_status($request->get_param('status'));
            if (is_wp_error($status)) {
                return $issue_api->error_response('invalid_param', ['detail' => $status->get_error_message()]);
            }

            $data['status'] = $status;
            $format[] = '%s';
        }

        if ($request->has_param('content')) {
            $content = aya_issue_sanitize_content($request->get_param('content'));
            if ($content === '') {
                return $issue_api->error_response('invalid_param', ['detail' => __('回复内容不能为空', 'aiya-cms')]);
            }

            $data['content'] = $content;
            $format[] = '%s';
        }

        if (empty($data)) {
            return $issue_api->response([
                'message' => __('没有需要更新的字段', 'aiya-cms'),
                'comment' => $comment,
            ]);
        }

        $data['updated_at'] = current_time('mysql', true);
        $format[] = '%s';

        $table = $wpdb->prefix . 'aya_issue_comments';
        $result = $wpdb->update($table, $data, ['id' => $comment_id], $format, ['%d']);

        if ($result === false) {
            return $issue_api->error_response('invalid_param', ['detail' => $wpdb->last_error ?: __('回复更新失败', 'aiya-cms')]);
        }

        aya_issue_sync_comment_stats($comment->issue_id);

        return $issue_api->response([
            'message' => __('回复已更新', 'aiya-cms'),
            'comment' => aya_issue_prepare_issue_comment_data(aya_issue_comment_get_row($comment_id)),
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'comment_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'content' => [
            'required' => false,
            'type' => 'string',
        ],
    ],
]);

// 删除议题回复
$issue_api->register_route('issue/comment/delete', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($issue_api) {
        global $wpdb;

        $nonce_check = aya_rest_api_verify_nonce($issue_api, $request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        $comment_id = absint($request->get_param('comment_id'));
        if ($comment_id <= 0) {
            return $issue_api->error_response('invalid_param', ['detail' => __('缺少回复 ID', 'aiya-cms')]);
        }

        $comment = aya_issue_comment_get_row($comment_id);
        if (!$comment) {
            return $issue_api->error_response('not_found', ['detail' => __('回复不存在', 'aiya-cms')]);
        }

        if (!aya_issue_can_manage_user($comment->user_id)) {
            return $issue_api->error_response('permission_denied', ['detail' => __('无权删除该回复', 'aiya-cms')]);
        }

        $table = $wpdb->prefix . 'aya_issue_comments';
        $deleted = $wpdb->delete($table, ['id' => $comment_id], ['%d']);

        if ($deleted === false) {
            return $issue_api->error_response('invalid_param', ['detail' => $wpdb->last_error ?: __('回复删除失败', 'aiya-cms')]);
        }

        aya_issue_sync_comment_stats($comment->issue_id);

        return $issue_api->response([
            'message' => __('回复已删除', 'aiya-cms'),
            'comment_id' => $comment_id,
            'issue_id' => intval($comment->issue_id),
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'comment_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
    ],
]);
