<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 标准 Curl 请求类
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @version 1.0
 * 
 **/

if (!class_exists('AYA_HTTP_Request')) {
    class AYA_HTTP_Request
    {
        private static $options;
        private $curl;

        public function __construct()
        {
            //初始化
            $this->curl = curl_init();

            //默认参数
            self::$options = [
                //CURLOPT_URL => $url,
                //CURLOPT_USERAGENT => ['User-Agent: AIYA-CMS-CURL/1.0', 'Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                //CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 10,
                //CURLOPT_ENCODING => 'gzip',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 30,
            ];
        }

        public function __destruct()
        {
            //结束请求
            curl_close($this->curl);
        }

        //设置请求头
        public function set_header($headers = [])
        {
            if (!is_array($headers)) {
                $headers = [$headers];
            }
            self::$options[CURLOPT_HTTPHEADER] = $headers;
        }
        //设置参数
        public function set_referer($referer = '')
        {
            self::$options[CURLOPT_REFERER] = $referer;
            self::$options[CURLOPT_AUTOREFERER] = true;
        }
        //设置Cookie
        public function set_cookie($cookie = '')
        {
            self::$options[CURLOPT_COOKIE] = $cookie;
        }
        //设置代理
        public function set_proxy($proxy = '')
        {
            self::$options[CURLOPT_PROXY] = rtrim($proxy, '/');
        }
        //设置UA
        public function set_useragent($useragent = '')
        {
            if (empty($useragent)) {
                $useragent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)';
            }
            self::$options[CURLOPT_USERAGENT] = $useragent;
        }

        //发起HTTP请求
        private function request()
        {
            //检查响应头格式
            $ress_headers = [];

            self::$options[CURLOPT_HEADERFUNCTION] = function ($curl, $header) use (&$ress_headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);

                if (count($header) < 2) {
                    return $len;
                }
                $ress_headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            };

            //加载设置
            curl_setopt_array($this->curl, self::$options);
            //获取响应
            $response = curl_exec($this->curl);

            if (curl_errno($this->curl)) {
                $http_status = curl_error($this->curl);
            } else {
                $http_status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
            }

            //转换为一个对象打包返回
            return new AYA_HTTP_Response($http_status, $ress_headers, $response);
        }

        //GET方法
        public function get($url, $params = [])
        {
            $param = '';

            if (!empty($params)) {
                $param = '?' . http_build_query($params);
            }
            self::$options[CURLOPT_URL] = $url . $param;
            self::$options[CURLOPT_CUSTOMREQUEST] = 'GET';

            return $this->request();
        }
        //POST方法
        public function post($url, $post_data = [])
        {
            self::$options[CURLOPT_URL] = $url;
            self::$options[CURLOPT_CUSTOMREQUEST] = 'POST';

            self::$options[CURLOPT_POST] = true;
            if (is_array($post_data)) {
                self::$options[CURLOPT_POSTFIELDS] = http_build_query($post_data);
            } else {
                self::$options[CURLOPT_POSTFIELDS] = $post_data;
            }

            return $this->request();
        }
        //PUT方法
        public function put($url, $from_data)
        {
            self::$options[CURLOPT_URL] = $url;
            self::$options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            self::$options[CURLOPT_POSTFIELDS] = $from_data;

            return $this->request();
        }
        //DELETE方法
        public function delete($url, $from_data)
        {
            self::$options[CURLOPT_URL] = $url;
            self::$options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            self::$options[CURLOPT_POSTFIELDS] = $from_data;

            return $this->request();
        }
    }

    class AYA_HTTP_Response
    {
        public $status, $headers, $data;

        public function __construct($status, $headers, $data)
        {
            $this->status = $status;
            $this->headers = $headers;
            $this->data = $data;
        }
    }
}
