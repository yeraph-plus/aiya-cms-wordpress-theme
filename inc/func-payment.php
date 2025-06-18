<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 激活码逻辑
 * ------------------------------------------------------------------------------
 */

//预设系统可用的赞助者激活码来源
function aya_payment_allowable_activation_code()
{
    return [
        'afdian',
        //'code',
        //'mbd',
        //'gumroad',
        //'patreon',
        //'kofi',
    ];
}

/*
 * ------------------------------------------------------------------------------
 * 爱发电接口逻辑
 * ------------------------------------------------------------------------------
 */

//在REST-API中响应爱发电的 WebHook 接口
add_action('rest_api_init', 'aya_register_afdian_api_routes', 10, 0);
add_filter('rest_authentication_errors', 'aya_disable_afdian_nonce_check', 99);
//在系统中添加爱发电赞助计划
add_filter('aya_sponsor_plan_add', 'aya_add_afdian_sponsor_plans', 10);

//爱发电链接
function aya_get_afdian_link()
{
    return 'https://afdian.com/';
}

//爱发电主页链接
function aya_get_afdian_home_link()
{
    //获取爱发电主页 URL
    $afdian_home_slug = aya_opt('stie_afdian_homepage_text', 'access');

    //验证URL结构是否符合预期
    if (empty($afdian_home_slug)) {
        return false;
    }

    return aya_get_afdian_link() . 'a/' . $afdian_home_slug;
}

//爱发电API实例
function aya_inst_afdian_api()
{
    //获取爱发电用户ID和Token
    $afdian_userid = aya_opt('stie_afdian_userid_text', 'access');
    $afdian_token = aya_opt('stie_afdian_token_text', 'access');

    //未设置
    if (empty($afdian_userid) || empty($afdian_token)) {
        return false;
    }

    //创建接口实例
    return new Afdian_API($afdian_userid, $afdian_token);
}

//测试爱发电接口可用性
function aya_afdian_ping_server()
{
    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    return $afdian_api->ping_server();
}

//爱发电即时查询（订单号）
function aya_afdian_query_order($order = '')
{
    $order = trim($order);

    //为空&不是数字时返回
    if (empty($order) || !preg_match('/^[0-9]+$/', $order)) {
        return false;
    }

    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取订单信息
    $result = $afdian_api->query_order($order);

    if (empty($result['data']['list'])) {
        return false;
    }

    //返回查询订单详情
    return $result['data']['list'][0];

}

//爱发电即时查询（用户）
function aya_afdian_query_sponsor($sponsor = '')
{
    //为空时返回
    if (empty($sponsor)) {
        return false;
    }

    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取用户信息
    $result = $afdian_api->query_sponsor($sponsor);

    if (empty($result['data']['list'])) {
        return false;
    }

    //返回查询结果
    return $result['data']['list'][0];
}

//爱发电即时查询（订单列表）
function aya_afdian_query_order_list($page = 1, $per_page = 50)
{
    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取订单列表
    $result = $afdian_api->get_orders($page, $per_page);

    return $result;
}

//爱发电即时查询（赞助者列表）
function aya_afdian_query_sponsor_list($page = 1, $per_page = 50)
{
    //获取API
    $afdian_api = aya_inst_afdian_api();

    if ($afdian_api === false) {
        return false;
    }

    //获取赞助者列表
    $result = $afdian_api->get_sponsors($page, $per_page);

    return $result;
}

//爱发电订单创建系统内赞助者激活
function aya_afdian_callback_auto_activation($order)
{
    //查询成功，创建系统内赞助者激活
    $activate_order = 'afd_' . $order['out_trade_no']; //order id
    $activate_days = intval($order['month']) * 31; //days

    $activate_user_token = aya_token_decode($order['custom_order_id'], 8);
    $activate_user = intval($activate_user_token); //user

    //检查订单是否已存在
    if (!aya_sponsor_order_exists($activate_order)) {
        $activate_result = aya_sponsor_add_order($activate_user, $activate_order, $activate_days, 'paid', 'afdian');

        //处理完成
        if ($activate_result === true) {
            return 'system id:' . $activate_user . ' is activated.';
        } else {
            return 'system id:' . $activate_user . ' activation failed.';

        }
    }

    return 'The order:"' . $order['out_trade_no'] . '" has been entered repeatedly.';
}

//用于回调爱发电 WebHook 数据的 REST-API 路由
function aya_register_afdian_api_routes()
{
    register_rest_route('afdian', 'response', [
        'methods' => 'POST',
        'callback' => function ($request) {
            // 接收POST数据
            $post_data = $request->get_body();
            //是否配置日志
            $need_save_log = aya_opt('site_afdian_savelog_bool', 'access', true);

            //记录接收到的数据以便调试
            if ($need_save_log) {
                //日志目录
                $log_dir = aya_local_mkdir('/afdian_logs');
                //补充文件名随机编码
                $rand_name = wp_generate_password(6, false);

                $log_file = $log_dir . '/webhook-' . date('Y-m-d') . '-' . $rand_name . '.log';
                $log_content = date('[Y-m-d H:i:s]') . " Afdian API received data:\n" . $post_data . "\n\n";

                file_put_contents($log_file, $log_content, FILE_APPEND);
            }

            //处理数据
            if (!empty($post_data)) {
                //解析JSON数据
                $json_decode = json_decode($post_data, true);
                $order_data = $json_decode['data']['order'] ?? [];

                //如果设置了订单回调ID，处理激活
                if (isset($order_data['custom_order_id'])) {
                    $activation_to = aya_afdian_callback_auto_activation($order_data);

                    if ($need_save_log) {
                        $log_content = date('[Y-m-d H:i:s]') . $activation_to . "\n\n";
                        file_put_contents($log_file, $log_content, FILE_APPEND);
                    }
                }
            }

            //返回成功响应
            return new WP_REST_Response([
                'ec' => 200,
                'em' => 'done'
            ], 200);

        },
        'permission_callback' => function () {
            return true;
        }
    ]);
}

//取消爱发电回调接口的Cookie验证
function aya_disable_afdian_nonce_check($errors)
{
    //获取当前请求的路径
    $request_uri = $_SERVER['REQUEST_URI'];

    //检查请求
    if (strpos($request_uri, '/api/afdian/response') !== false) {
        //对爱发电发回的webhook请求，跳过Cookie认证
        return true;
    }

    return $errors;
}

//爱发电赞助方案数据
function aya_add_afdian_sponsor_plans()
{
    //启用爱发电订阅接入
    $afd_home = aya_get_afdian_home_link();

    $afd_order_plan = [];

    //只有设置了主页时启用接口
    if ($afd_home !== false) {

        //爱发电主页链接
        $afd_order_plan['afdian'] = [
            'name' => __('查看爱发电创作者主页', 'AIYA'),
            'color' => '#946ce6',
            'alt' => 'afdian homepage url',
            'href' => $afd_home,
            'title' => __('支持创作，用爱发电', 'AIYA'),
            'triggered_msg' => __('在当前页面输入爱发电平台订单号激活本站订阅即可查看专属内容', 'AIYA'),
            'refresh' => false,
        ];

        //预设方案切换
        $afd_plan_type = aya_opt('site_afdian_plan_type', 'access');

        //配置回调时需求的数据
        $site_name = get_bloginfo('name');
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $user_name = $current_user->display_name;
        //生成自定义订单ID
        $custom_id = aya_token_encode($user_id, 8);
        //方案详情页
        $afd_order_create = aya_get_afdian_link() . 'order/create?';
        //创建留言消息
        $remark_format = __('来自 「%s」 用户 %s 发送的赞助订单~', 'AIYA');

        $remark_msg = urlencode(sprintf($remark_format, $site_name, $user_name));

        //跳转自选金额页面
        if ($afd_plan_type === 'optional') {
            $afd_user_id = aya_opt('stie_afdian_userid_text', 'access');
            //拼接回调链接
            $plan_url = "{$afd_order_create}user_id={$afd_user_id}&custom_order_id={$custom_id}&remark={$remark_msg}";

            $afd_order_plan['afdian_optional'] = [
                'name' => __('在爱发电支持我', 'AIYA'),
                'color' => '#946ce6',
                'alt' => 'afdian homepage url',
                'href' => $plan_url,
                'title' => __('自选金额赞助', 'AIYA'),
                'triggered_msg' => __('已经支持，稍后并刷新页面即可查看专属内容', 'AIYA'),
                'refresh' => true,
            ];
        }
        //跳转预设方案页面
        else if ($afd_plan_type === 'preset') {
            $afd_preset_url = aya_opt('site_afdian_preset_plan_url', 'access');
            //提取方案ID
            $afd_plan_id = aya_extract_url_query($afd_preset_url, 'plan_id');

            if (!empty($afd_plan_id)) {
                //拼接回调链接
                $plan_url = "{$afd_order_create}plan_id={$afd_plan_id}&custom_order_id={$custom_id}&remark={$remark_msg}";

                $afd_order_plan['afdian_preset'] = [
                    'name' => __('订阅我的赞助方案', 'AIYA'),
                    'color' => '#946ce6',
                    'alt' => 'afdian homepage url',
                    'href' => $plan_url,
                    'title' => __('赞助方案订阅', 'AIYA'),
                    'triggered_msg' => __('已经支持，稍后并刷新页面即可查看专属内容', 'AIYA'),
                    'refresh' => true,
                ];
            }
        }
    }

    return $afd_order_plan;
}