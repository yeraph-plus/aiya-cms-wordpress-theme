<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 激活码逻辑
 * ------------------------------------------------------------------------------
 */

//收集可用的赞助者激活码来源
function aya_payment_sponsor_activation_code()
{
    $order_code_from = [];
    $order_code_from = apply_filters('aya_add_sponsor_from', $order_code_from);

    //清理重复
    $order_code_from = array_unique($order_code_from);

    return $order_code_from;
}

//收集可用的赞助方案列表
function aya_payment_sponsor_order_plan()
{
    $order_plan = [];

    $order_plan = apply_filters('aya_add_sponsor_plan', $order_plan);

    //防止过滤器操作出错，返回空数组阻止报错
    if (!is_array($order_plan)) {
        $order_plan = [];
    }

    return $order_plan;
}

//写入日志文件
function aya_payment_callback_save_log($log_name, $log_data)
{
    //日志目录
    $log_dir = aya_local_mkdir('/webhook_logs');
    $today = date('Y-m-d');

    //文件名加盐
    $rand_name = substr(md5($today . AUTH_SALT), 0, 6);

    $log_file = $log_dir . '/webhook-' . $today . '-' . $rand_name . '.log';

    $log_content = date('[Y-m-d H:i:s]') . ' ' . $log_name . PHP_EOL . $log_data . PHP_EOL . PHP_EOL;

    file_put_contents($log_file, $log_content, FILE_APPEND);
}

/*
 * ------------------------------------------------------------------------------
 * 爱发电接口逻辑
 * ------------------------------------------------------------------------------
 */

//在REST-API中响应爱发电的 WebHook 接口
add_action('rest_api_init', 'aya_register_afdian_api_routes', 10, 0);
//在系统中添加爱发电赞助计划
add_filter('aya_add_sponsor_from', 'aya_add_afdian_order_activate', 10);
add_filter('aya_add_sponsor_plan', 'aya_add_afdian_sponsor_plans', 10);

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

//用于回调爱发电 WebHook 数据的 REST-API 接口
function aya_register_afdian_api_routes()
{
    register_rest_route('afdian', 'callbacks', [
        'methods' => 'POST',
        'callback' => function ($request) {
            // 接收POST数据
            $post_data = $request->get_body();
            //是否配置日志
            $need_save_log = aya_opt('site_afdian_savelog_bool', 'access', true);

            //记录接收到的数据以便调试
            if ($need_save_log) {
                //日志目录
                aya_payment_callback_save_log('Afdian API received data:', $post_data);
            }

            //处理数据
            if (!empty($post_data)) {
                //解析JSON数据
                $json_decode = json_decode($post_data, true);
                $order_data = $json_decode['data']['order'] ?? [];

                //如果设置了订单回调ID，处理激活
                if (isset($order_data['custom_order_id'])) {
                    //order id
                    $activate_order = 'afd_' . $order_data['out_trade_no'];
                    //days
                    $activate_days = intval($order_data['month']) * 31;
                    //user
                    $activate_user_token = aya_token_decode($order_data['custom_order_id'], 8);
                    $activate_user = intval($activate_user_token);

                    //检查订单是否已存在
                    if (!aya_sponsor_order_exists($activate_order)) {
                        //查询成功，创建系统内赞助者激活
                        $activate_result = aya_sponsor_add_order($activate_user, $activate_order, $activate_days, 'paid', 'afdian');
                    }

                    if ($need_save_log) {
                        //处理完成
                        $activation_to = 'The order:"' . $activate_order . '" User id:' . $activate_user;

                        if ($activate_result === true) {
                            $activation_to .= ' activation completed .';
                        } else {
                            $activation_to .= ' activation failed .';
                        }
                        //日志目录
                        aya_payment_callback_save_log('', $activation_to);
                    }
                }
            }

            //任何情况下都返回成功响应
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
add_filter('rest_authentication_errors', function ($errors) {
    //获取当前请求的路径
    $request_uri = $_SERVER['REQUEST_URI'];
    //需要跳过的请求路径
    $pos_uri = '/' . aya_rewrite_rest_api_url_prefix() . '/afdian/callbacks';
    //检查请求
    if (strpos($request_uri, $pos_uri) !== false) {
        //始终响应爱发电发回的webhook请求，跳过Cookie认证
        return true;
    }

    return $errors;
}, 99);

//允许爱发电赞助订单号激活
function aya_add_afdian_order_activate($code_from)
{
    if (aya_opt('stie_afdian_homepage_text', 'access') !== '') {

        $code_from[] = 'afdian';
    }

    return $code_from;
}

//爱发电赞助方案数据
function aya_add_afdian_sponsor_plans($order_plan)
{
    //启用爱发电订阅接入
    if (aya_opt('site_afdian_convert_bool', 'access')) {
        //爱发电主页
        $afd_home = aya_get_afdian_home_link();

        //爱发电主页方案
        $order_plan['afdian'] = [
            'name' => __('查看爱发电创作者主页', 'AIYA'),
            'color' => '#946ce6',
            'alt' => 'afdian homepage url',
            'href' => $afd_home,
            'title' => __('支持创作，用爱发电', 'AIYA'),
            'triggered_msg' => __('在当前页面输入爱发电平台订单号激活本站订阅即可查看专属内容', 'AIYA'),
            'refresh' => false,
        ];

        //自动回调时预设方案
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

            $order_plan['afdian_optional'] = [
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

                $order_plan['afdian_preset'] = [
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

    return $order_plan;
}

//爱发电接口订单查询逻辑
function aya_verify_code_by_afdian($order_id)
{
    //接口地址不可用
    if (aya_afdian_ping_server() === false) {
        return [
            'status' => false,
            'detail' => __('爱发电接口不可用，请联系管理员', 'AIYA'),
        ];
    }

    //检查订单是否存在
    if (aya_sponsor_order_exists('afd_' . $order_id)) {
        return [
            'status' => false,
            'detail' => __('此订单已被激活过了，请查看订单记录', 'AIYA'),
        ];
    }

    //发起查询
    $afd_order = aya_afdian_query_order($order_id);

    //查询失败
    if ($afd_order === false) {
        return [
            'status' => false,
            'detail' => __('没有查询到订单，请确认订单号是否正确，或于爱发电平台私信作者询问', 'AIYA'),
        ];
    }

    //查询成功
    $activate_order = 'afd_' . $afd_order['out_trade_no']; //order id

    //创建系统内赞助者激活
    $activate_days = intval($afd_order['month']) * 31; //days

    $result = aya_sponsor_key_activation($activate_order, $activate_days, 'afdian');

    //合并订单信息
    $order_info = '<br>';
    $order_info .= '订单号：' . $afd_order['out_trade_no'] . '<br>';
    $order_info .= '赞助方案：' . $afd_order['plan_title'] . '<br>';
    $order_info .= '赞助周期：' . $afd_order['month'] . '个月<br>';
    $order_info .= '金额：' . $afd_order['show_amount'] . '（折后 ' . $afd_order['total_amount'] . ' ）<br>';
    $order_info .= '留言：' . (!empty($afd_order['remark']) ? $afd_order['remark'] : '无') . '<br>';
    $order_info .= '兑换码：' . (!empty($afd_order['redeem_id']) ? $afd_order['redeem_id'] : '无') . '<br>';

    $success = ($result) ? __('刷新页面即可查看激活记录', 'AIYA') : __('当前无法创建激活，稍后重试', 'AIYA');

    //返回订单信息
    return [
        'status' => $result,
        'detail' => $success . $order_info,
    ];
}