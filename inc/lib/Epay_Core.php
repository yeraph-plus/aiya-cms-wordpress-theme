<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 彩虹版易支付兼容 SDK 
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @version 1.0
 * 
 **/

if (!class_exists('EPAY_SDK')) {
    class EPAY_SDK
    {
        private $pid;
        private $key;
        private $plat_from_url;
        private $sign_type;

        //构造时应用配置
        function __construct($config)
        {
            $this->pid = $config['pid'];
            $this->key = $config['key'];

            //如果URL没有以"/"结尾
            if (substr($config['url'], -1) != '/') {
                $config['url'] .= '/';
            }

            $this->plat_from_url = $config['url'];

            //指定签名方法
            $this->sign_type = (isset($config['sign_type'])) ? $config['sign_type'] : 'MD5';
        }

        //Curl方式请求体结构
        private function curl_http_response($url, $post = false, $http_header = [], $timeout = 10)
        {
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            if (empty($http_header)) {
                $http_header = [
                    "Accept: */*",
                    "Accept-Language: zh-CN,zh;q=0.8",
                    "Connection: close"
                ];
            }

            curl_setopt($curl, CURLOPT_HTTPHEADER, $http_header);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            if ($post) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            }

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;
        }

        //计算签名
        private function md5_sign($param)
        {
            //通用签名方法 md5( a=b&c=d&e=f + KEY )
            $signstr = '';

            //排序参数
            ksort($param);
            reset($param);

            //使 sign 和 sign_type 值为空或0时，不参与签名
            foreach ($param as $k => $v) {

                if ($k != "sign" && $k != "sign_type" && $v != '' && $v !== '0') {
                    $signstr .= $k . '=' . $v . '&';
                }
            }

            $signstr = substr($signstr, 0, -1) . $this->key;

            return md5($signstr);
        }

        //用户IP地址
        private function get_client_ip()
        {
            if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
                $ip = getenv('REMOTE_ADDR');
            } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
        }

        //用户设备类型
        private function get_client_is_mobile()
        {
            if (!isset($_SERVER['HTTP_USER_AGENT'])) {
                return false;
            }

            $agent = $_SERVER['HTTP_USER_AGENT'];
            $agent = strtolower($agent);

            //常见移动设备标识
            $mobile_keywords = ['iphone', 'ipad', 'android', 'mobile', 'phone'];

            //循环匹配
            foreach ($mobile_keywords as $keyword) {

                if (strpos($agent, $keyword) !== false) {
                    return true;
                }
            }

            return false;
        }

        //签名请求参数
        private function sign_param($param)
        {
            $param['sign'] = $this->md5_sign($param);
            $param['sign_type'] = $this->sign_type;

            return $param;
        }

        //使用POST发起支付（表单HTML+JS跳转）
        public function pay_auto_redirect($param_tmp, $from_id = 'bill')
        {
            //合并必要参数
            $param_tmp['pid'] = $this->pid;

            if (!isset($param_tmp['type'])) {
                $param_tmp['type'] = 'alipay';
            }

            $param = $this->sign_param($param_tmp);

            $submit_url = $this->plat_from_url . 'submit.php';

            $html = '<form id="' . $from_id . '" action="' . $submit_url . '" method="post">';

            foreach ($param as $k => $v) {
                $html .= '<input type="hidden" name="' . $k . '" value="' . $v . '"/>';
            }

            $html .= '<input type="submit" value="LOADING..."/></form>';
            $html .= '<script> document.getElementById("' . $from_id . '").submit(); </script>';

            return $html;
        }

        //API接口支付
        public function pay_mapi($param_tmp)
        {
            //合并必要参数
            $param_tmp['pid'] = $this->pid;

            if (!isset($param_tmp['type'])) {
                $param_tmp['type'] = 'cashier';
            }

            if (!isset($param_tmp['out_trade_no'])) {
                //拼接一个临时订单号
                $param_tmp['out_trade_no'] = time() . mt_rand(100000, 999999);
            }

            $param_tmp['clientip'] = $this->get_client_ip();

            if (!isset($param_tmp['device'])) {
                $param_tmp['device'] = ($this->get_client_is_mobile() ? 'mobile' : 'pc');
            }

            $param = $this->sign_param($param_tmp);

            $mapi_url = $this->plat_from_url . 'mapi.php';

            $response = $this->curl_http_response($mapi_url, http_build_query($param));

            $result = json_decode($response, true);

            //判断请求是否成功
            return ($result['code'] == 1) ? $result : $result['msg'];
        }

        //API接口退款
        public function pay_refund($trade_no, $money)
        {
            //合并必要参数
            $param['pid'] = $this->pid;
            $param['trade_no'] = $trade_no;
            $param['money'] = $money;

            $param = $this->sign_param($param);

            $api_url = $this->plat_from_url . '?act=refund';

            $response = $this->curl_http_response($api_url, http_build_query($param));

            $result = json_decode($response, true);

            //判断请求是否成功
            return ($result['code'] == 1) ? $result : $result['msg'];
        }

        //回调验证逻辑
        public function verify_callback($param)
        {
            //接收$_GET参数（notify_url、return_url）
            if (empty($param)) {
                return false;
            }

            $sign = $this->md5_sign($param);

            return ($sign === $param['sign']) ? true : false;
        }

        //回调数据
        public function callback_data()
        {
            if (empty($_GET)) {
                return false;
            }

            return $_GET;
        }

        //API查询单个订单
        public function query_order($trade_no)
        {
            //GET接口点 api.php?act=order&pid={商户ID}&trade_no={系统订单号}&sign={32位签名字符串}

            //合并必要参数
            $param['act'] = 'order';
            $param['pid'] = $this->pid;
            $param['trade_no'] = $trade_no;

            $param = $this->sign_param($param);

            $api_url = $this->plat_from_url . 'api.php?' . http_build_query($param);

            $response = $this->curl_http_response($api_url);

            $result = json_decode($response, true);

            //判断请求是否成功
            return ($result['code'] == 1) ? $result : $result['msg'];
        }

        //API查询订单列表
        public function query_order_list($offset = 0, $limit = 20)
        {
            //GET接口点 api.php?act=orders&pid={商户ID}&sign={32位签名字符串}

            //合并必要参数
            $param['act'] = 'orders';
            $param['pid'] = $this->pid;
            $param['offset'] = $offset;
            $param['limit'] = $limit;

            $param = $this->sign_param($param);

            $api_url = $this->plat_from_url . 'api.php?' . http_build_query($param);

            $response = $this->curl_http_response($api_url);

            $result = json_decode($response, true);

            //判断请求是否成功
            return ($result['code'] == 1) ? $result : $result['msg'];
        }

        //API查询当前商户
        public function query_merchant()
        {
            //GET接口点 api.php?act=query&pid={商户ID}&sign={32位签名字符串}

            //合并必要参数
            $param['act'] = 'query';
            $param['pid'] = $this->pid;

            $param = $this->sign_param($param);

            $api_url = $this->plat_from_url . 'api.php?' . http_build_query($param);

            $response = $this->curl_http_response($api_url);

            $result = json_decode($response, true);

            //判断请求是否成功
            return ($result['code'] == 1) ? $result : $result['msg'];
        }

        //API查询当前商户T1结算记录
        public function query_settlement()
        {
            //GET接口点 api.php?act=settle&pid={商户ID}&sign={32位签名字符串}

            //合并必要参数
            $param['act'] = 'settle';
            $param['pid'] = $this->pid;

            $param = $this->sign_param($param);

            $api_url = $this->plat_from_url . 'api.php?' . http_build_query($param);

            $response = $this->curl_http_response($api_url);

            $result = json_decode($response, true);

            //判断请求是否成功
            return ($result['code'] == 1) ? $result : $result['msg'];
        }
    }
}