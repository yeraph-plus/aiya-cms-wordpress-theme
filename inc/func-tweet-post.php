<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 自定义推文文章类型
 * ------------------------------------------------------------------------------
 */

//注册文章类型
add_action('after_setup_theme', 'aya_post_type_tweet_action');
//MetaBox注册
add_action('add_meta_boxes', 'aya_post_type_tweet_add_meta_box');
//归档筛选
add_action('pre_get_posts', 'aya_tweet_post_archive_filter_by_tag');

//注册推文文章类型和标签分类法
function aya_post_type_tweet_action()
{
    AYF::module(
        'Register_Post_Type',
        //'文章类型' => array('name' => '文章类型名','slug' => '别名','icon' => '图标','public' => 允许查询),
        [
            'tweet' => [
                'name' => __('推文', 'aiya-cms'),
                'slug' => 'tweet',
                'icon' => 'dashicons-format-quote',
                'public' => true,
            ],
        ]
    );
    AYF::module(
        'Register_Tax_Type',
        //'分类法类型' => array('name' => '分类法类型名','slug' => '别名','post_type' => '文章类型名','tag_mode' => 作为标签),
        [
            'tweet_tag' => [
                'name' => __('推文标签', 'aiya-cms'),
                'slug' => 'tweet_tag',
                'post_type' => ['tweet'],
                'tag_mode' => true,
            ],
        ]
    );
}

//使自定义文章类型可以操作置顶
function aya_post_type_tweet_add_meta_box()
{
    add_meta_box('aya_tweet_product_sticky', __('置顶', 'aiya-cms'), 'aya_tweet_product_sticky', 'tweet', 'side', 'high');
}

function aya_tweet_product_sticky()
{
    printf(
        '<p>
            <label for="super-sticky" class="selectit">
                <input id="super-sticky" name="sticky" type="checkbox" value="sticky" %s />
                %s
            </label>
        </p>',
        checked(is_sticky(), true, false),
        esc_html__('置顶这篇文章', 'aiya-cms')
    );
}

// 归档筛选推文标签
function aya_tweet_post_archive_filter_by_tag($query)
{
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('tweet')) {
        return;
    }

    $tag_param = wp_unslash($_GET['t_tag'] ?? '');

    if (is_array($tag_param)) {
        $tag_slugs = array_map('sanitize_title', $tag_param);
    } else {
        $tag_slugs = preg_split('/[，,\s]+/u', (string) $tag_param);
        $tag_slugs = array_map('sanitize_title', $tag_slugs);
    }

    $tag_slugs = array_values(array_filter(array_unique($tag_slugs)));

    if (empty($tag_slugs)) {
        return;
    }

    $query->set('tax_query', [
        [
            'taxonomy' => 'tweet_tag',
            'field' => 'slug',
            'terms' => $tag_slugs,
            'operator' => 'AND',
        ]
    ]);
}

// 查询推文标签列表
function aya_tweet_post_get_tags_list()
{
    $terms = get_terms([
        'taxonomy' => 'tweet_tag',
        'hide_empty' => false,
        'orderby' => 'count',
        'order' => 'DESC',
    ]);

    if (is_wp_error($terms)) {
        return false;
    }

    $items = [];
    foreach ($terms as $term) {
        if (!$term instanceof WP_Term) {
            continue;
        }
        $items[] = [
            'id' => (int) $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'count' => (int) $term->count,
        ];
    }

    return $items;
}

// 内容截断方法（用于循环中输出）
function aya_tweet_post_excerpt($content, $lines_limit = 10, $post_url = null)
{
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    $lines = explode("\n", $content);

    if (count($lines) > $lines_limit) {
        $lines = array_slice($lines, 0, $lines_limit);
        return implode("\n", $lines) . "\n\n" . '<a href="' . esc_url($post_url) . '">阅读更多</a>';
    }

    return $content;
}

/*
 * ------------------------------------------------------------------------------
 * 推文文章类型接口
 * ------------------------------------------------------------------------------
 */

// 推文格式提交过滤
function aya_tweet_post_insert_from_request(AYA_WP_REST_API $api, WP_REST_Request $request, $post = null)
{
    $tweet_content = wp_kses_post((string) $request->get_param('content'));
    $tweet_title = sanitize_text_field((string) $request->get_param('title'));
    $tweet_status = in_array($request->get_param('status'), ['publish', 'draft', 'pending', 'trash'], true) ? $request->get_param('status') : 'pending';
    $tweet_tags = aya_tweet_extract_tags_from_content($tweet_content);
    $gallery_images = aya_tweet_sanitize_gallery_images($request->get_param('gallery_images'));

    if ($tweet_content === '') {
        return $api->error_response('invalid_param', ['detail' => __('帖子内容不能为空', 'aiya-cms')]);
    }

    $post_data = [
        'post_type' => 'tweet',
        'post_status' => $tweet_status,
        'post_title' => $tweet_title,
        'post_content' => $tweet_content,
        'post_modified' => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql', true),
    ];

    if ($post instanceof WP_Post) {
        $post_data['ID'] = (int) $post->ID;
    } else {
        $post_data['post_author'] = get_current_user_id();
    }

    $post_id = wp_insert_post($post_data, true);

    if (is_wp_error($post_id) || !$post_id) {
        return $api->error_response('server_error', ['detail' => __('帖子发布失败，请稍后重试', 'aiya-cms')]);
    }

    wp_set_object_terms($post_id, $tweet_tags, 'tweet_tag', false);
    update_post_meta($post_id, 'gallery_images', $gallery_images);

    return $post_id;
}

// 验证并清理图片URL
function aya_tweet_sanitize_gallery_images($images)
{
    if (!is_array($images)) {
        return [];
    }

    $items = [];

    foreach ($images as $image) {
        $image = ltrim(wp_normalize_path(sanitize_text_field((string) $image)), '/');

        if ($image === '' || strpos($image, '..') !== false) {
            continue;
        }

        if (!preg_match('#^[0-9]{4}/[0-9]{2}/[0-9]{2}/[A-Za-z0-9_-]+\.(jpg|png|gif|webp|avif)$#', $image)) {
            continue;
        }

        $items[] = $image;
    }

    return array_values(array_unique($items));
}

// 从推文内容中提取标签
function aya_tweet_extract_tags_from_content($content)
{
    $content = wp_strip_all_tags((string) $content);

    if ($content === '') {
        return [];
    }

    preg_match_all('/#([^#\r\n]+)#/u', $content, $matches);

    if (empty($matches[1]) || !is_array($matches[1])) {
        return [];
    }

    $tag_names = array_map('sanitize_text_field', $matches[1]);
    $tag_names = array_map('trim', $tag_names);
    $tag_names = array_values(array_filter(array_unique($tag_names), function ($tag_name) {
        return $tag_name !== '';
    }));

    if (empty($tag_names)) {
        return [];
    }

    $terms = get_terms([
        'taxonomy' => 'tweet_tag',
        'hide_empty' => false,
        'name' => $tag_names,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return [];
    }

    $valid_tags = [];
    foreach ($terms as $term) {
        if (!$term instanceof WP_Term) {
            continue;
        }

        $valid_tags[] = $term->name;
    }

    return array_values(array_unique($valid_tags));
}

// 调用 Rest API 命名空间
$api = new AYA_WP_REST_API('aiya/v1');

// 创建推文
$api->register_route('tweet/create', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //验证nonce
        $nonce_check = aya_rest_api_verify_nonce($api, $request);

        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        $post_id = aya_tweet_post_insert_from_request($api, $request);

        if (is_wp_error($post_id) || !$post_id) {
            return $post_id;
        }

        return $api->response([
            'message' => __('帖子已发布', 'aiya-cms'),
            'post_id' => $post_id,
        ], 201);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'title' => [
            'required' => false,
            'type' => 'string',
        ],
        'content' => [
            'required' => true,
            'type' => 'string',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'gallery_images' => [
            'required' => false,
            'type' => 'array',
            'sanitize_callback' => function ($value) {
                return aya_tweet_sanitize_gallery_images($value);
            },
        ],
    ]
]);

// 编辑推文
$api->register_route('tweet/update', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //验证nonce
        $nonce_check = aya_rest_api_verify_nonce($api, $request);

        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        // 更新前检查流程
        $post_id = absint($request->get_param('post_id'));
        if ($post_id <= 0) {
            return $api->error_response('invalid_param', ['detail' => __('缺少帖子ID', 'aiya-cms')]);
        }

        $post = get_post($post_id);
        if (!$post instanceof WP_Post || $post->post_type !== 'tweet') {
            return $api->error_response('not_found', ['detail' => __('帖子不存在', 'aiya-cms')]);
        }

        if ((int) $post->post_author !== get_current_user_id() && !current_user_can('edit_others_posts')) {
            return $api->error_response('permission_denied', ['detail' => __('没有更新这条帖子的权限', 'aiya-cms')]);
        }

        $post_id = aya_tweet_post_insert_from_request($api, $request, $post);

        if (is_wp_error($post_id) || !$post_id) {
            return $post_id;
        }

        return $api->response([
            'message' => __('帖子已更新', 'aiya-cms'),
            'post_id' => $post_id,
        ], 201);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'post_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
        'title' => [
            'required' => false,
            'type' => 'string',
        ],
        'content' => [
            'required' => true,
            'type' => 'string',
        ],
        'status' => [
            'required' => false,
            'type' => 'string',
        ],
        'gallery_images' => [
            'required' => false,
            'type' => 'array',
            'sanitize_callback' => function ($value) {
                return aya_tweet_sanitize_gallery_images($value);
            },
        ],
    ]
]);

// 删除推文
$api->register_route('tweet/delete', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //验证nonce
        $nonce_check = aya_rest_api_verify_nonce($api, $request);

        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }

        // 删除前检查流程
        $post_id = absint($request->get_param('post_id'));
        if ($post_id <= 0) {
            return $api->error_response('invalid_param', ['detail' => __('缺少帖子ID', 'aiya-cms')]);
        }

        $post = get_post($post_id);
        if (!$post instanceof WP_Post || $post->post_type !== 'tweet') {
            return $api->error_response('not_found', ['detail' => __('帖子不存在', 'aiya-cms')]);
        }

        if ((int) $post->post_author !== get_current_user_id() && !current_user_can('delete_others_posts')) {
            return $api->error_response('permission_denied', ['detail' => __('没有删除这条帖子的权限', 'aiya-cms')]);
        }

        $deleted = wp_delete_post($post_id, true);
        if (!$deleted) {
            return $api->error_response('server_error', ['detail' => __('删除帖子失败', 'aiya-cms')]);
        }

        return $api->response([
            'message' => __('帖子已删除', 'aiya-cms'),
            'post_id' => $post_id,
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'post_id' => [
            'required' => true,
            'type' => 'numeric',
        ],
    ]
]);
