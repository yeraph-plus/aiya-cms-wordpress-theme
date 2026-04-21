<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * REST-API方法组件
 * ------------------------------------------------------------------------------
 */

//初始化路由组件，定义命名空间
$api = new AYA_WP_REST_API('aiya/v1');

//注销
$api->register_route('logout', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('安全退出失败', 'AIYA')]);
        }
        //执行退出
        wp_logout();

        return $api->response(['message' => __('已退出登录', 'AIYA')]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => []
]);

//登录
$api->register_route('login', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('客户端已失效，请刷新页面后重试', 'AIYA')]);
        }

        //接取参数
        $email = $request->get_param('email');
        $password = $request->get_param('password');
        $remember = $request->get_param('remember');

        if (empty($email) || empty($password)) {
            return $api->error_response('invalid_param', ['detail' => '请输入用户名和密码']);
        }

        //执行登录
        $user = wp_signon([
            'user_login' => $email,
            'user_password' => $password,
            'remember' => $remember,
        ], is_ssl());

        if (is_wp_error($user)) {
            //$real_error_message = $user->get_error_message();
            return $api->error_response('permission_denied', ['detail' => __('登录邮箱或密码错误', 'AIYA')]);
        }

        //刷新页面
        return $api->response(['message' => __('欢迎回来，', 'AIYA') . $user->display_name]);
    },
    'permission_callback' => function () {
        return !is_user_logged_in();
    },
    'args' => [
        'email' => [
            'required' => true,
            'type' => 'string',
        ],
        'password' => [
            'required' => true,
            'type' => 'string',
        ],
        'remember' => [
            'required' => false,
            'type' => 'bool',
        ]
    ]
]);

//生成唯一用户名
function generate_unique_username($prefix = 'user')
{
    //提取16进制时间戳
    $time_hex = dechex(time());
    //生成随机字符串
    $random_hex = bin2hex(random_bytes(2));

    return $prefix . '_' . $time_hex . $random_hex;
}

// 存储密码重置Token
function aya_store_password_reset_payload($user_login, $key)
{
    $token = wp_generate_password(48, false, false);
    $token_key = 'aya_pwd_reset_' . hash_hmac('sha256', $token, wp_salt('nonce'));
    $payload = [
        'login' => (string) $user_login,
        'key' => (string) $key,
        'expires_at' => time() + AYA_PASSWORD_RESET_TOKEN_TTL,
    ];

    // token 定时器，默认 30 分钟，支持通过常量覆盖
    set_transient($token_key, $payload, AYA_PASSWORD_RESET_TOKEN_TTL);

    return $token;
}

// 生成密码重置Token
function aya_get_password_reset_payload($token)
{
    $token = sanitize_text_field((string) $token);
    if (empty($token)) {
        return null;
    }

    $token_key = 'user_pwd_reset_' . hash_hmac('sha256', $token, wp_salt('nonce'));
    $payload = get_transient($token_key);

    if (!is_array($payload) || empty($payload['login']) || empty($payload['key'])) {
        return null;
    }

    $expires_at = isset($payload['expires_at']) ? absint($payload['expires_at']) : 0;
    if ($expires_at > 0 && $expires_at < time()) {
        delete_transient($token_key);
        return null;
    }

    return [
        'token_key' => $token_key,
        'login' => sanitize_user(wp_unslash((string) $payload['login']), true),
        'key' => sanitize_text_field((string) $payload['key']),
    ];
}

//注册
$api->register_route('register', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('客户端已失效，请刷新页面后重试', 'AIYA')]);
        }

        //接取参数
        $register_name = $request->get_param('username');
        $email = $request->get_param('email');
        $password = $request->get_param('password');
        $password_confirm = $request->get_param('password_confirm');

        //检查参数
        if (empty($register_name) || empty($email) || empty($password)) {
            return $api->error_response('invalid_param', ['detail' => '用户名、邮箱或密码不能为空']);
        }
        //验证密码一致性
        if ($password !== $password_confirm) {
            return $api->error_response('invalid_param', ['detail' => '两次输入的密码不一致']);
        }
        //密码强度校验：至少8位，包含字母和数字
        if (mb_strlen($password) < 8) {
            return $api->error_response('invalid_param', ['detail' => __('密码长度不能少于8位', 'AIYA')]);
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return $api->error_response('invalid_param', ['detail' => __('密码必须同时包含字母和数字', 'AIYA')]);
        }
        //验证传入是否是邮箱
        if (!is_email($email)) {
            return $api->error_response('invalid_param', ['detail' => '邮箱格式不正确']);
        }
        if (email_exists($email)) {
            return $api->error_response('invalid_param', ['detail' => '此邮箱已被注册']);
        }

        //登录名方法
        $username = generate_unique_username('user');
        //校验
        while (username_exists($username)) {
            $username = generate_unique_username('user');
        }

        //注册用户
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            //$real_error_message = $user->get_error_message();
            return $api->error_response('invalid_param', ['detail' => __('无法创建用户，请重试', 'AIYA')]);
        }

        //将用户名设置为display_name
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $register_name,
        ]);

        //执行自动登录
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        //刷新页面
        return $api->response(['message' => __('欢迎你，', 'AIYA') . $register_name]);
    },
    'permission_callback' => function () {
        return !is_user_logged_in() && get_option('users_can_register');
    },
    'args' => [
        'username' => [
            'required' => true,
            'type' => 'string',
            'description' => '用户名'
        ],
        'password' => [
            'required' => true,
            'type' => 'string',
            'description' => '密码'
        ],
        'password_confirm' => [
            'required' => true,
            'type' => 'string',
            'description' => '确认密码'
        ],
        'email' => [
            'required' => true,
            'type' => 'string',
            'description' => '邮箱'
        ]
    ]
]);

//找回密码
$api->register_route('forgot_password', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('客户端已失效，请刷新页面后重试', 'AIYA')]);
        }

        //接取参数
        $email = $request->get_param('email');

        //检查参数
        if (empty($email)) {
            return $api->error_response('invalid_param', ['detail' => __('请提供注册邮箱', 'AIYA')]);
        }

        //验证传入是否是邮箱
        if (!is_email($email)) {
            return $api->error_response('invalid_param', ['detail' => __('邮箱格式不正确', 'AIYA')]);
        }

        //检查邮箱是否存在
        $user = get_user_by('email', $email);
        if (!$user) {
            return $api->error_response('invalid_param', ['detail' => __('如果邮箱存在将发送邮件', 'AIYA')]);
        }

        //生成重置密码链接
        $key = get_password_reset_key($user);
        if (is_wp_error($key)) {
            return $api->error_response('server_error', ['detail' => __('无法生成密码重置链接，请稍后再试', 'AIYA')]);
        }

        //发送重置密码邮件
        $reset_token = aya_store_password_reset_payload($user->user_login, $key);
        $reset_link = add_query_arg(['token' => $reset_token,], home_url('/reset-password/'));

        //邮件标题
        $subject = sprintf(__('[%s] 密码重置', 'AIYA'), wp_specialchars_decode(get_option('blogname')));

        //邮件内容
        $message = __('有人请求重置以下账号的密码：', 'AIYA') . "\r\n\r\n";
        $message .= network_home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('用户名: %s', 'AIYA'), $user->user_login) . "\r\n\r\n";
        $message .= __('如果这不是您本人的操作，请忽略此邮件。', 'AIYA') . "\r\n\r\n";
        $message .= __('要重置密码，请访问以下链接:', 'AIYA') . "\r\n\r\n";
        $message .= __('此链接仅在30分钟内有效。', 'AIYA') . "\r\n\r\n";
        $message .= $reset_link . "\r\n";

        //发送邮件
        $mail_sent = wp_mail($user->user_email, $subject, $message);

        if (!$mail_sent) {
            return $api->error_response('server_error', ['detail' => __('发送重置密码邮件失败，请稍后再试', 'AIYA')]);
        }

        return $api->response(['message' => __('密码重置链接已发送到您的邮箱，请查收', 'AIYA')]);
    },
    'permission_callback' => function () {
        return !is_user_logged_in();
    },
    'args' => [
        'email' => [
            'required' => true,
            'type' => 'string',
            'description' => '用户注册邮箱'
        ]
    ]
]);

//校验密码重置链接
$api->register_route('validate_password_reset', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        $token = sanitize_text_field((string) $request->get_param('token'));
        $payload = aya_get_password_reset_payload($token);

        if (!$payload) {
            return $api->error_response('invalid_param', ['detail' => __('重置链接缺少必要参数', 'AIYA')]);
        }

        $user = check_password_reset_key($payload['key'], $payload['login']);
        if (is_wp_error($user)) {
            delete_transient($payload['token_key']);
            return $api->error_response('invalid_key', ['detail' => __('重置链接无效或已过期，请重新申请找回密码', 'AIYA')]);
        }

        return $api->response([
            'message' => __('重置链接有效，请输入新密码', 'AIYA'),
            'login' => $user->user_login,
        ]);
    },
    'permission_callback' => '__return_true',
    'args' => [
        'token' => [
            'required' => true,
            'type' => 'string',
            'description' => '密码重置令牌'
        ]
    ]
]);

//通过重置链接设置新密码
$api->register_route('reset_password', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        $token = sanitize_text_field((string) $request->get_param('token'));
        $payload = aya_get_password_reset_payload($token);
        $password = (string) $request->get_param('password');
        $password_confirm = (string) $request->get_param('password_confirm');

        if (!$payload) {
            return $api->error_response('invalid_param', ['detail' => __('重置链接缺少必要参数', 'AIYA')]);
        }

        if (empty($password) || empty($password_confirm)) {
            return $api->error_response('invalid_param', ['detail' => __('请输入新密码并确认', 'AIYA')]);
        }

        if ($password !== $password_confirm) {
            return $api->error_response('invalid_param', ['detail' => __('两次输入的密码不一致', 'AIYA')]);
        }

        if (mb_strlen($password) < 8) {
            return $api->error_response('invalid_param', ['detail' => __('密码长度不能少于8位', 'AIYA')]);
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return $api->error_response('invalid_param', ['detail' => __('密码必须同时包含字母和数字', 'AIYA')]);
        }

        $user = check_password_reset_key($payload['key'], $payload['login']);
        if (is_wp_error($user)) {
            delete_transient($payload['token_key']);
            return $api->error_response('invalid_key', ['detail' => __('重置链接无效或已过期，请重新申请找回密码', 'AIYA')]);
        }

        reset_password($user, $password);
        delete_transient($payload['token_key']);

        return $api->response([
            'status' => 'done',
            'message' => __('密码已重置，请使用新密码登录', 'AIYA'),
            'redirect' => home_url('/'),
        ]);
    },
    'permission_callback' => '__return_true',
    'args' => [
        'token' => [
            'required' => true,
            'type' => 'string',
            'description' => '密码重置令牌'
        ],
        'password' => [
            'required' => true,
            'type' => 'string',
            'description' => '新密码'
        ],
        'password_confirm' => [
            'required' => true,
            'type' => 'string',
            'description' => '确认新密码'
        ]
    ]
]);

//修改用户资料
$api->register_route('update_profile', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        $nonce = $request->get_header('X-WP-Nonce');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('验证失败', 'AIYA')]);
        }

        $user_id = get_current_user_id();
        $params = $request->get_params();

        $userdata = ['ID' => $user_id];
        $errors = [];

        // Handle basic fields
        if (isset($params['first_name'])) {
            $userdata['first_name'] = sanitize_text_field($params['first_name']);
        }
        if (isset($params['last_name'])) {
            $userdata['last_name'] = sanitize_text_field($params['last_name']);
        }
        if (isset($params['nickname'])) {
            $userdata['nickname'] = sanitize_text_field($params['nickname']);
        }
        if (isset($params['description'])) {
            $userdata['description'] = sanitize_textarea_field($params['description']);
        }
        if (isset($params['user_url'])) {
            $userdata['user_url'] = esc_url_raw($params['user_url']);
        }

        // Handle email update
        if (isset($params['email'])) {
            if (!is_email($params['email'])) {
                $errors[] = __('邮箱格式不正确', 'AIYA');
            } else {
                $current_user = wp_get_current_user();
                if ($params['email'] !== $current_user->user_email) {
                    if (email_exists($params['email'])) {
                        $errors[] = __('该邮箱已被使用', 'AIYA');
                    } else {
                        $userdata['user_email'] = $params['email'];
                    }
                }
            }
        }

        if (!empty($errors)) {
            return $api->error_response('validation_error', ['detail' => implode('; ', $errors)]);
        }

        $user_id = wp_update_user($userdata);

        if (is_wp_error($user_id)) {
            return $api->error_response('update_failed', ['detail' => $user_id->get_error_message()]);
        }

        return $api->response([
            'status' => 'done',
            'message' => __('资料已更新', 'AIYA')
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'first_name' => [
            'required' => false,
            'type' => 'string',
            'description' => '用户姓名'
        ],
        'last_name' => [
            'required' => false,
            'type' => 'string',
            'description' => '用户姓氏'
        ],
        'nickname' => [
            'required' => false,
            'type' => 'string',
            'description' => '用户昵称'
        ],
        'description' => [
            'required' => false,
            'type' => 'string',
            'description' => '用户描述'
        ],
        'user_url' => [
            'required' => false,
            'type' => 'string',
            'description' => '用户个人网站URL'
        ],
        'email' => [
            'required' => false,
            'type' => 'string',
            'description' => '用户邮箱'
        ],
    ]
]);

//修改用户密码
$api->register_route('update_password', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        $nonce = $request->get_header('X-WP-Nonce');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('验证失败', 'AIYA')]);
        }

        $user_id = get_current_user_id();
        $params = $request->get_params();

        $userdata = ['ID' => $user_id];
        $errors = [];

        // Handle password update
        if (!empty($params['pass']) && !empty($params['pass_again'])) {
            if ($params['pass'] === $params['pass_again']) {
                if (mb_strlen($params['pass']) < 8) {
                    $errors[] = __('密码长度不能少于8位', 'AIYA');
                } else if (!preg_match('/[A-Za-z]/', $params['pass']) || !preg_match('/[0-9]/', $params['pass'])) {
                    $errors[] = __('密码必须同时包含字母和数字', 'AIYA');
                } else {
                    $userdata['user_pass'] = $params['pass'];
                }
            } else {
                $errors[] = __('两次输入的密码不一致', 'AIYA');
            }
        } else {
            $errors[] = __('请输入新密码', 'AIYA');
        }

        if (!empty($errors)) {
            return $api->error_response('validation_error', ['detail' => implode('; ', $errors)]);
        }

        $user_id = wp_update_user($userdata);

        if (is_wp_error($user_id)) {
            return $api->error_response('update_failed', ['detail' => $user_id->get_error_message()]);
        }

        return $api->response([
            'status' => 'done',
            'message' => __('密码已更新，请重新登录', 'AIYA')
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'pass' => [
            'required' => true,
            'type' => 'string',
            'description' => '新密码'
        ],
        'pass_again' => [
            'required' => true,
            'type' => 'string',
            'description' => '确认新密码'
        ],
    ]
]);

//点赞文章
$api->register_route('post_like', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('验证失败', 'AIYA')]);
        }

        $post_id = absint($request->get_param('post_id'));
        if (empty($post_id)) {
            return $api->error_response('invalid_param', ['detail' => '缺少参数']);
        }

        //验证文章存在且已发布
        if (get_post_status($post_id) !== 'publish') {
            return $api->error_response('invalid_param', ['detail' => '文章不存在']);
        }

        //获取当前点赞数
        $count = (int) get_post_meta($post_id, 'like_count', true);
        $count++;
        //更新
        update_post_meta($post_id, 'like_count', $count);

        return $api->response([
            'status' => 'done',
            'count' => $count
        ]);
    },
    'permission_callback' => '__return_true', // 允许所有人点赞
    'args' => [
        'post_id' => [
            'required' => true,
            'type' => 'numeric',
        ]
    ]
]);

//收藏文章
$api->register_route('post_favorite', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        $nonce = $request->get_header('X-WP-Nonce');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('验证失败', 'AIYA')]);
        }

        $post_id = $request->get_param('post_id');
        $user_id = get_current_user_id();

        //获取用户收藏列表
        $favorites = get_user_meta($user_id, 'favorite_posts', true);

        if (!is_array($favorites)) {
            $favorites = [];
        }

        //切换状态
        if (in_array($post_id, $favorites)) {
            //移除
            $favorites = array_diff($favorites, [$post_id]);
            $action = 'removed';
        } else {
            //添加
            $favorites[] = $post_id;
            $action = 'added';
        }

        //去重并更新
        $favorites = array_unique($favorites);
        update_user_meta($user_id, 'favorite_posts', $favorites);

        return $api->response([
            'status' => 'done',
            'action' => $action
        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'post_id' => [
            'required' => true,
            'type' => 'numeric',
        ]
    ]
]);

//赞助者订单自助激活逻辑
$api->register_route('sponsor_activate', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('客户端已失效，请刷新页面后重试', 'AIYA')]);
        }

        //接取参数
        $order_by = $request->get_param('order_by');
        $order = $request->get_param('order');

        //订单查询接口函数名单
        $allowed_order_by = ['code', 'afdian', 'patreon', 'mbd', 'kofi', 'gumroad'];

        if (empty($order_by) || !in_array($order_by, $allowed_order_by)) {
            return $api->error_response('invalid_param', ['detail' => __('未定义的接口', 'AIYA')]);
        }

        //调用接口函数
        $verify_func = 'aya_verify_code_by_' . $order_by;
        /**
         * TIPS 此处返回 [ 'status' => (bool), 'detail' =>'', ] 结构的数组简化定义
         */
        if (function_exists($verify_func)) {
            $verify = call_user_func($verify_func, $order);
        } else {
            $verify = ['status' => false, 'detail' => __('系统错误', 'AIYA')];
        }

        if ($verify['status']) {
            //处理完成
            return $api->response([
                'message' => __('订单激活成功', 'AIYA'),
                'description' => $verify['detail']
            ]);
        } else {
            //查询失败
            return $api->error_response('verify_failed', ['detail' => $verify['detail']]);
        }
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    },
    'args' => [
        'order_by' => [
            'required' => true,
            'type' => 'string',
            'description' => '查询订单的接口名'
        ],
        'order' => [
            'required' => true,
            'type' => 'string',
            'description' => '订单号或其他标识符'
        ],
    ]
]);

// OpenList 请求接口
$api->register_route('oplist_fs', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('安全验证失败，请刷新页面后重试', 'AIYA')]);
        }

        //接取参数
        $params = $request->get_json_params();

        $fs_method = $params['fs_method'];
        $fs_atts = [];
        switch ($fs_method) {
            case 'list':
            case 'get':
                $fs_atts['path'] = '/' . trim($params['path'], '/');
                $fs_atts['password'] = trim($params['password']);
                $fs_atts['per_page'] = isset($params['per_page']) ? intval($params['per_page']) : 0;
                $fs_atts['page'] = isset($params['page']) ? intval($params['page']) : 1;
                $fs_atts['refresh'] = filter_var($params['refresh'], FILTER_VALIDATE_BOOLEAN);
                break;
            case 'dirs':
                $fs_atts['path'] = '/' . trim($params['path'], '/');
                $fs_atts['password'] = trim($params['password']);
                $fs_atts['force_root'] = filter_var($params['force_root'], FILTER_VALIDATE_BOOLEAN);
                break;
            case 'search':
                $fs_atts['parent'] = '/' . trim($params['parent'], '/');
                $fs_atts['keywords'] = trim($params['keywords']);
                $fs_atts['scope'] = intval($params['scope']);
                $fs_atts['page'] = isset($params['page']) ? intval($params['page']) : 1;
                $fs_atts['per_page'] = isset($params['per_page']) ? intval($params['per_page']) : 0;
                $fs_atts['password'] = trim($params['password']);
                break;
            default:
                return $api->error_response('invalid_param', ['detail' => __('未定义的请求类型', 'AIYA')]);
        }

        $oplist_cli = aya_oplist_cli_init();
        $fs_content = $oplist_cli->fs_request($fs_method, $fs_atts, true);

        //错误处理
        if (!is_array($fs_content)) {
            //识别一些常见错误
            if (strpos($fs_content, 'EOF') !== false) {
                $msg = __('本地服务器发送请求失败', 'AIYA');
            } else if (strpos($fs_content, '400') !== false) {
                $msg = __('参数错误', 'AIYA');
            } else if (strpos($fs_content, '401') !== false) {
                $msg = __('令牌失效', 'AIYA');
            } else if (strpos($fs_content, '403') !== false) {
                $msg = __('文件访问被拒绝', 'AIYA');
            } else if (strpos($fs_content, '500') !== false) {
                $msg = __('文件/目录位置不存在，或搜索功能未就绪', 'AIYA');
            } else if (strpos($fs_content, 'your.openlist.server') !== false) {
                $msg = __('请先完成后台设置', 'AIYA');
            } else {
                $msg = __('访问错误', 'AIYA');
            }
            return $api->error_response('not_found', ['detail' => $msg . ' (' . $fs_content . ') ']);
        }

        //处理返回前端的数据结构
        return $api->response([
            'content' => aya_oplist_rebuild_content($fs_content, $params),
            'per_page' => intval($params['per_page']),
            'page' => intval($params['page']),
            'total' => intval($fs_content['total']),

        ]);
    },
    'permission_callback' => function () {
        return is_user_logged_in();
    }
]);
