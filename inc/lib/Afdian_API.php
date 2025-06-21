<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 爱发电平台 API 
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @version 2.0
 * 
 **/

if (!class_exists('Afdian_API')) {
    class Afdian_API
    {
        private $user_id, $token, $http;

        private $api_root_url = 'https://afdian.com/api/open/%s';

        //初始化
        public function __construct($user_id, $token)
        {
            $this->user_id = $user_id;
            $this->token = $token;
            $this->http = new AYA_HTTP_Request();
        }

        //计算 API 请求签名
        private function get_signature($params, $time)
        {
            return md5("{$this->token}params{$params}ts{$time}user_id{$this->user_id}");
        }

        //查询入口
        public function query_server($api, $params)
        {
            if (!isset($api) || empty($api)) {
                return null;
            }

            $params = json_encode($params);
            //请求格式
            $query_data = json_encode([
                'user_id' => $this->user_id,
                'params' => $params,
                'ts' => time(),
                'sign' => $this->get_signature($params, time())
            ]);

            //设置请求头
            $this->http->set_header(['Content-Type: application/json']);

            //发送请求
            $result = $this->http->post(sprintf($this->api_root_url, $api), $query_data);

            //请求没有正常响应
            if ($result->status !== 200) {
                //合并错误信息返回
                return new AYA_HTTP_Response(
                    400,
                    ['Content-Type: application/text'],
                    "[Afdian API] Client request failed: {$result->data} ({$result->status})"
                );
            }

            //解析返回数据
            $response_json = json_decode($result->data, true);

            //解析成功
            if ($response_json && is_array($response_json)) {
                //返回数据交给调用请求的函数处理
                return $response_json;
            }
            //解析失败
            else {
                return new AYA_HTTP_Response(
                    400,
                    ['Content-Type: application/text'],
                    "[Afdian API] Response data: {$result->data} ({$result->status})"
                );
            }
        }

        //Ping 方法
        public function ping_server()
        {
            $result = $this->query_server('ping', ['ping' => 'hello world']);

            //返回报错
            if (is_object($result)) {
                return $result->data;
            }

            return (isset($result['ec']) && $result['ec'] == 200);
        }

        //查询订单号
        public function query_order($order = '')
        {
            $result = $this->query_server('query-order', ['out_trade_no' => $order]);

            //返回报错
            if (is_object($result)) {
                return $result->data;
            }

            return (isset($result['ec']) && $result['ec'] == 200) ? $result : $result['em'];
        }

        //查询赞助者
        public function query_sponsor($sponsor = '')
        {
            $result = $this->query_server('query-sponsor', ['user_id' => $sponsor]);

            //返回报错
            if (is_object($result)) {
                return $result->data;
            }

            return (isset($result['ec']) && $result['ec'] == 200) ? $result : $result['em'];
        }

        //返回订单列表
        public function get_orders($page = 1, $per_page = 50)
        {
            $result = $this->query_server('query-order', ['page' => $page, 'per_page' => $per_page]);

            //返回报错
            if (is_object($result)) {
                return $result->data;
            }

            return (isset($result['ec']) && $result['ec'] == 200) ? $result : $result['em'];
        }

        //返回赞助者列表
        public function get_sponsors($page = 1, $per_page = 50)
        {
            $result = $this->query_server('query-sponsor', ['page' => $page, 'per_page' => $per_page]);

            //返回报错
            if (is_object($result)) {
                return $result->data;
            }

            return (isset($result['ec']) && $result['ec'] == 200) ? $result : $result['em'];
        }

        //Tips：以下方法通过循环查询获取后合并全部列表，所以不会返回 total_page 参数

        //获取所有订单（递归）
        public function get_all_orders()
        {
            $orders = ["data" => ["list" => []]];

            $result = $this->get_orders(1);

            //不是查询数组
            if (!is_array($result)) {

                return $result;
            }

            if (isset($result['data']['list'], $result['data']['total_page'])) {

                foreach ($result['data']['list'] as $order) {
                    $orders['data']['list'][] = $order;
                }

                for ($i = 2; $i <= $result['data']['total_page']; $i++) {

                    $result = $this->get_orders($i);

                    if (isset($result['data']['list'])) {

                        foreach ($result['data']['list'] as $order) {
                            $orders['data']['list'][] = $order;
                        }
                    }
                }
            }

            return $orders;
        }

        //获取所有赞助者名单（递归）
        public function get_all_sponsors()
        {
            $sponsors = ["data" => ["list" => []]];

            $result = $this->get_sponsors(1);

            //不是查询数组
            if (!is_array($result)) {

                return $result;
            }

            if (isset($result['data']['list'], $result['data']['total_page'])) {

                foreach ($result['data']['list'] as $order) {
                    $sponsors['data']['list'][] = $order;
                }

                for ($i = 2; $i <= $result['data']['total_page']; $i++) {

                    $result = $this->get_sponsors($i);

                    if (isset($result['data']['list'])) {

                        foreach ($result['data']['list'] as $order) {
                            $sponsors['data']['list'][] = $order;
                        }
                    }
                }
            }
            return $sponsors;
        }

        //从取得列表中搜索订单
        public function serach_order($result, $order_id)
        {
            if (isset($result['data']['list'])) {

                foreach ($result['data']['list'] as $order) {

                    if ($order['out_trade_no'] == $order_id) {
                        return $order;
                    }
                }
            }

            return false;
        }

        //从取得列表中搜索用户
        public function serach_user($result, $user_id)
        {
            if (isset($result['data']['list'])) {

                foreach ($result['data']['list'] as $sponsor) {

                    if (isset($sponsor['user'], $sponsor['user']['user_id']) && $sponsor['user']['user_id'] == $user_id) {
                        return $sponsor;
                    }
                }
            }

            return false;
        }

        //从取得列表中搜索用户名
        public function serach_user_name($result, $user_name)
        {
            if (isset($result['data']['list'])) {

                foreach ($result['data']['list'] as $sponsor) {

                    if (isset($sponsor['user'], $sponsor['user']['name']) && $sponsor['user']['name'] == $user_name) {
                        return $sponsor;
                    }
                }
            }
            return false;
        }

        //从取得列表中查询指定方案的订单列表
        public function list_plan_order($result, $plan_id)
        {
            $orders = [];

            if (isset($result['data']['list'])) {

                foreach ($result['data']['list'] as $order) {

                    if ($order['plan_id'] == $plan_id) {
                        $orders[] = $order;
                    }
                }
            }

            return $orders;
        }

        //从取得列表中查询指定用户的订单列表
        public function list_user_order($result, $user_id)
        {
            $orders = [];

            if (isset($result['data']['list'])) {

                foreach ($result['data']['list'] as $order) {
                    if ($order['user_id'] == $user_id) {
                        $orders[] = $order;
                    }
                }
            }

            return $orders;
        }
    }
}