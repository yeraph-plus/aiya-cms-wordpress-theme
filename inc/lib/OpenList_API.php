<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * OpenList API 类
 * 
 * Author Yeraph.
 * Version 1.1
 * 
 * https://www.yeraph.com/
 * 
 * OpenList文档：
 * https://oplist.org/zh/
 * OpenList API 文档：
 * https://openlist.apifox.cn/
 */

class OpenList_API
{
    private $token, $server, $http;

    //初始化
    public function __construct($server, $token)
    {
        $this->server = rtrim($server, '/');
        $this->token = $token;
        $this->http = new AYA_HTTP_Request();
    }

    //Ping检测
    public function ping()
    {
        //接口位置
        $api_url = $this->server . '/ping';
        //请求头
        $this->http->set_header(['User-Agent: AIYA-CMS-CLI/1.0']);

        //发送请求
        $response = $this->http->get($api_url, []);

        //返回
        if ($response->status == 200 && $response->data == "pong") {

            return true;
        }

        return false;
    }

    //获取token
    public function get_token($username, $password, $otp_code = null)
    {
        //接口位置
        $api_url = $this->server . '/api/auth/login';
        //请求头
        $this->http->set_header(['User-Agent: AIYA-CMS-CLI/1.0', 'Content-Type: application/json']);
        //请求参数
        $query_data = json_encode([
            'username' => $username,
            'password' => $password,
            'otp_code' => $otp_code, //二步验证码
        ]);

        //发送请求
        $response = $this->http->post($api_url, $query_data);

        //返回
        if ($response->status == 200) {

            $data = json_decode($response->data, true);

            if ($data['code'] == 200) {
                return $data['data']['token'];
            } else {
                return 'ERROR:' . $data['message'];
            }
        }

        return false;
    }
    //获取token（hash）
    public function get_token_hash($username, $password, $otp_code = null)
    {
        //接口位置
        $api_url = $this->server . '/api/auth/login/hash';
        //请求头
        $this->http->set_header(['User-Agent: AIYA-CMS-CLI/1.0', 'Content-Type: application/json']);
        //请求参数
        $query_data = json_encode([
            'username' => $username,
            'password' => hash('sha256', $password . '-https://github.com/alist-org/alist'),
            'otp_code' => $otp_code, //二步验证码
        ]);

        //发送请求
        $response = $this->http->post($api_url, $query_data);

        //返回
        if ($response->status == 200) {

            $data = json_decode($response->data, true);

            if ($data['code'] == 200) {
                return $data['data']['token'];
            } else {
                return 'ERROR:' . $data['code'] . '-' . $data['message'];
            }
        }

        return false;
    }
    //获取当前用户信息
    public function get_me()
    {
        //接口位置
        $api_url = $this->server . '/api/me';
        //请求头
        $this->http->set_header(['User-Agent: AIYA-CMS-CLI/1.0', 'Authorization:' . $this->token]);

        //发送请求
        $response = $this->http->get($api_url, []);

        //返回
        if ($response->status == 200) {

            $data = json_decode($response->data, true);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return 'ERROR:' . $data['code'] . '-' . $data['message'];
            }
        }

        return false;
    }
    //获取站点设置
    public function get_settings()
    {
        //接口位置
        $api_url = $this->server . '/api/public/settings';
        //请求头
        $this->http->set_header(['User-Agent: AIYA-CMS-CLI/1.0']);

        //发送请求
        $response = $this->http->get($api_url, []);

        //返回
        if ($response->status == 200) {

            $data = json_decode($response->data, true);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return 'ERROR:' . $data['code'] . '-' . $data['message'];
            }
        }

        return false;
    }
    //文件方法
    public function fs_request($address, $query_data, $en_code = true)
    {
        //接口位置
        switch ($address) {
            case 'list':
                //列出文件目录
                $api = '/api/fs/list';
                break;
            case 'get':
                //获取文件信息
                $api = '/api/fs/get';
                break;
            case 'dirs':
                //获取目录
                $api = '/api/fs/dirs';
                break;
            case 'search':
                //搜索文件或文件夹
                $api = '/api/fs/search';
                break;
            case 'mkdir':
                //新建文件夹
                $api = '/api/fs/mkdir';
                break;
            case 'rename':
                //重命名
                $api = '/api/fs/rename';
                break;
            case 'batch_rename':
                //批量重命名
                $api = '/api/fs/batch_rename';
                break;
            case 'regex_rename':
                //正则重命名
                $api = '/api/fs/regex_rename';
                break;
            case 'move':
                //移动文件
                $api = '/api/fs/move';
                break;
            case 'recursive_move':
                //递归移动文件
                $api = '/api/fs/recursive_move';
                break;
            case 'copy':
                //复制文件
                $api = '/api/fs/copy';
                break;
            case 'remove':
                //删除文件或文件夹
                $api = '/api/fs/remove';
                break;
            case 'remove_empty_directory':
                //删除空文件夹
                $api = '/api/fs/remove_empty_directory';
                break;
            case 'add_offline_download':
                //添加离线下载
                $api = '/api/fs/add_offline_download';
                break;
            default:
                return false;
        }
        //编码为JSON
        if ($en_code) {
            $query_data = json_encode($query_data);
        }
        //请求头
        $this->http->set_header(['User-Agent: AIYA-CMS-CLI/1.0', 'Content-Type: application/json', 'Authorization: ' . $this->token]);
        //发送请求
        $response = $this->http->post($this->server . $api, $query_data);

        //返回
        if ($response->status == 200) {
            //解码JSON
            $data = json_decode($response->data, true);
            //请求成功时
            if ($data['code'] == 200) {
                //如果为文件操作，返回提示，否则返回数据
                if ($data['data'] === null) {
                    return $data['message'];
                } else {
                    return $data['data'];
                }
            } else {
                return 'ERROR:' . $data['code'] . '-' . $data['message'];
            }
        } else {
            return 'ERROR:' . $response->status . '-' . $response->data;
        }
    }

    /**
     * --------------------
     * 以下为操作方法别名
     * --------------------
     */

    //列出文件目录
    public function fs_list($path, $password = '', $page = 1, $per_page = 0, $refresh = false)
    {
        //请求参数
        $query_data = [
            'path' => $path, //路径
            'password' => $password, //密码
            'page' => $page,
            'per_page' => $per_page,
            'refresh' => $refresh //是否强制刷新
        ];

        //发送请求
        $response = $this->fs_request('list', $query_data);

        if (!is_array($response)) {
            return $response;
        }

        return $response;
    }
    //获取某个文件/目录信息
    public function fs_get($path, $password = '', $page = 1, $per_page = 0, $refresh = false)
    {
        //请求参数
        $query_data = [
            'path' => $path, //路径
            'password' => $password, //密码
            'page' => $page,
            'per_page' => $per_page,
            'refresh' => $refresh //是否强制刷新
        ];

        //发送请求
        $response = $this->fs_request('get', $query_data);

        if (!is_array($response)) {
            return $response;
        }

        return $response;
    }
    //获取目录
    public function fs_dir($path, $password = '', $force_root = false)
    {
        //请求参数
        $query_data = [
            'path' => $path,
            'password' => $password,
            'force_root' => $force_root
        ];

        //发送请求
        $response = $this->fs_request('dirs', $query_data);

        if (!is_array($response)) {
            return $response;
        }

        return $response;
    }
    //搜索文件或文件夹
    public function fs_search($parent, $keywords, $scope = 0, $page = 1, $per_page = 0, $password = '')
    {
        //请求参数
        $query_data = [
            'parent' => $parent, //搜索目录
            'keywords' => $keywords, //关键词
            'scope' => $scope, //0-全部 1-文件夹 2-文件
            'page' => $page,
            'per_page' => $per_page,
            'password' => $password //密码
        ];

        //发送请求
        $response = $this->fs_request('search', $query_data);

        if (!is_array($response)) {
            return $response;
        }

        return $response;
    }
    //新建文件夹
    public function fs_mkdir($path)
    {
        //请求参数
        $query_data = [
            'path' => $path, //路径
        ];

        //发送请求
        $response = $this->fs_request('mkdir', $query_data);

        return $response;
    }
    //重命名文件
    public function fs_rename($name, $path)
    {
        //请求参数
        $query_data = [
            'name' => $name, //重命名
            'path' => $path, //完整路径
        ];

        //发送请求
        $response = $this->fs_request('rename', $query_data);

        return $response;
    }
    //批量重命名
    public function fs_batch_rename($src, $src_name = [], $re_name = [])
    {
        //验证两个数组长度一致
        if (count($src_name) !== count($re_name)) {
            return 'error';
        }

        //重新列出
        $objects = array_map(function ($src_name, $re_name) {
            return [
                'src_name' => $src_name, //源文件
                'new_name' => $re_name, //新文件名
            ];
        }, $src_name, $re_name);

        //请求参数
        $query_data = [
            'src_dir' => $src, //源文件夹
            'rename_objects' => $objects,
        ];

        //发送请求
        $response = $this->fs_request('batch_rename', $query_data);

        return $response;
    }
    //正则重命名
    public function fs_regex_rename($src, $src_regex = '', $new_regex = '')
    {
        //请求参数
        $query_data = [
            'src_dir' => $src, //源文件夹
            'src_name_regex' => $src_regex, //源文件匹配正则
            'new_name_regex' => $new_regex, //新文件名正则
        ];

        //发送请求
        $response = $this->fs_request('regex_rename', $query_data);

        return $response;
    }
    //移动文件
    public function fs_move($src, $dst, $names = [])
    {
        //请求参数
        $query_data = [
            'src_dir' => $src, //源文件夹
            'dst_dir' => $dst, //目标文件夹
            'names' => $names, //文件名（数组）
        ];

        //发送请求
        $response = $this->fs_request('move', $query_data);

        return $response;
    }
    //聚合移动（移动文件夹内的所有文件）
    public function fs_move_all($src, $dst)
    {
        //请求参数
        $query_data = [
            'src_dir' => $src, //源文件夹
            'dst_dir' => $dst, //目标文件夹
        ];

        //发送请求
        $response = $this->fs_request('recursive_move', $query_data);

        return $response;
    }
    //复制文件
    public function fs_copy($src, $dst, $names = [])
    {
        //请求参数
        $query_data = [
            'src_dir' => $src, //源文件夹
            'dst_dir' => $dst, //完整路径
            'names' => $names, //文件名（数组）
        ];

        //发送请求
        $response = $this->fs_request('copy', $query_data);

        return $response;
    }
    //删除文件或文件夹
    public function fs_remove($src, $names = [])
    {
        //请求参数
        $query_data = [
            'names' => $names, //文件名（数组）
            'dir' => $src, //文件夹
        ];

        //发送请求
        $response = $this->fs_request('remove', $query_data);

        return $response;
    }
    //删除空文件夹
    public function fs_remove_empty_dir($src)
    {
        //请求参数
        $query_data = [
            'src_dir' => $src, //文件夹
        ];

        //发送请求
        $response = $this->fs_request('remove_empty_directory', $query_data);

        return $response;
    }

    /**
     * --------------------
     * 以下PUT方法
     * 
     * Tips：上传文件需要指定已存在的目录
     * --------------------
     */

    //表单上传文件 
    public function fs_from_upload($path, $file)
    {
        //接口位置
        $api_url = $this->server . '/api/fs/form';
        //请求头
        $this->http->set_header([
            'User-Agent: AIYA-CMS-CLI/1.0',
            'Authorization:' . $this->token,
            'Content-Type: multipart/form-data', //表单上传
            'Content-Length: ' . filesize($file), //文件大小
            'File-Path: ' . urlencode($path . $file['name']), //文件路径
            'As-Task: true'
        ]);

        //发送请求
        $response = $this->http->put($api_url, $file);
        //返回响应
        $data = json_decode($response->data, true);

        if ($data['code'] == 200) {
            return $data['data']['task'];
        }

        return false;
    }
    //流式上传文件
    public function fs_upload($path, $file)
    {
        //接口位置
        $api_url = $this->server . '/api/fs/stream';
        //请求头
        $this->http->set_header([
            'User-Agent: AIYA-CMS-CLI/1.0',
            'Authorization:' . $this->token,
            'Content-Type: application/octet-stream', //流式上传
            'Content-Length: ' . filesize($file),
            'File-Path: ' . urlencode($path . $file['name']),
            'As-Task: true'
        ]);

        //发送请求
        $response = $this->http->put($api_url, $file);
        //返回响应
        $data = json_decode($response->data, true);

        if ($data['code'] == 200) {
            return $data['data']['task'];
        }

        return false;
    }
}
