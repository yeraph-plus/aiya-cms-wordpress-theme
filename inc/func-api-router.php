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

//注销登录
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
    }
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

        //检查参数
        if (empty($register_name) || empty($email) || empty($password)) {
            return $api->error_response('invalid_param', ['detail' => '用户名、邮箱或密码不能为空']);
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
        'email' => [
            'required' => true,
            'type' => 'string',
            'description' => '邮箱'
        ]
    ]
]);

//匿名注册
$api->register_route('anonymous-register', [
    'methods' => 'POST',
    'callback' => function (WP_REST_Request $request) use ($api) {
        //获取nonce参数
        $nonce = $request->get_header('X-WP-Nonce');
        //验证nonce
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return $api->error_response('permission_denied', ['detail' => __('非法操作', 'AIYA')]);
        }

        //接取参数
        // 生成随机邮箱和密码
        $random_str = bin2hex(random_bytes(6));
        $email = $random_str . '@' . home_url();
        $password = wp_generate_password(10, false, false);
        $display_name = '匿名用户_' . substr($random_str, 0, 6);

        // 确保邮箱唯一
        while (email_exists($email)) {
            $random_str = bin2hex(random_bytes(6));
            $email = 'anon_' . $random_str . '@example.com';
            $display_name = '匿名用户_' . substr($random_str, 0, 6);
        }

        // 生成唯一用户名
        $username = generate_unique_username('anon');
        while (username_exists($username)) {
            $username = generate_unique_username('anon');
        }

        // 创建用户
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            return $api->error_response('invalid_param', ['detail' => __('匿名注册失败', 'AIYA')]);
        }

        // 设置 display_name
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $display_name,
        ]);

        // 自动登录
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        return $api->response([
            'message' => __('匿名注册成功', 'AIYA'),
            'email' => $email,
            'password' => $password,
            'display_name' => $display_name,
        ]);
    },
    'permission_callback' => function () {
        return !is_user_logged_in();
    },
]);