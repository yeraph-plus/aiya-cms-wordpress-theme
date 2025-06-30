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
            return $api->error_response('invalid_param', ['detail' => __('该邮箱未注册', 'AIYA')]);
        }

        //生成重置密码链接
        $key = get_password_reset_key($user);
        if (is_wp_error($key)) {
            return $api->error_response('server_error', ['detail' => __('无法生成密码重置链接，请稍后再试', 'AIYA')]);
        }

        //发送重置密码邮件
        $reset_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');

        //邮件标题
        $subject = sprintf(__('[%s] 密码重置', 'AIYA'), wp_specialchars_decode(get_option('blogname')));

        //邮件内容
        $message = __('有人请求重置以下账号的密码：', 'AIYA') . "\r\n\r\n";
        $message .= network_home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('用户名: %s', 'AIYA'), $user->user_login) . "\r\n\r\n";
        $message .= __('如果这不是您本人的操作，请忽略此邮件。', 'AIYA') . "\r\n\r\n";
        $message .= __('要重置密码，请访问以下链接:', 'AIYA') . "\r\n\r\n";
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
