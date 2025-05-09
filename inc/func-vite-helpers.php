<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 
 * 使用 Vite 的开发服务器配置
 * 
 * Tips: Vite 服务器地址和端口配置，应该和 vite.config.js 中的配置完全一致
 * 
 * 参考来源：
 * https://github.com/wp-bond/bond/blob/master/src/Tooling/Vite.php
 * https://github.com/wp-bond/boilerplate/tree/master/app/themes/boilerplate
 * 
 * ------------------------------------------------------------------------------
 */

define('VITE_HOST', 'http://localhost:5173');
define('VITE_ENTRY_POINT', 'src/main.js');
define('VITE_PUBLIC_PATH', './build');

//检查Vite启动状态
function aya_vite_reference()
{
    //创建curl请求对象
    $handle = curl_init(VITE_HOST . '/' . VITE_ENTRY_POINT);

    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_NOBODY, true);

    curl_exec($handle);

    //检查请求是否成功
    $error = curl_errno($handle);
    //关闭请求
    curl_close($handle);

    //返回
    return ($error === 0) ? true : false;
}

//读取manifest.json
function aya_vite_get_manifest()
{
    //缓存读取结果
    static $exists = null;

    if ($exists !== null) {
        return $exists;
    }

    //定位manifest.json
    $manifest_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/.vite/manifest.json';

    if (file_exists($manifest_path)) {

        $manifest_content = file_get_contents($manifest_path);

        $exists = json_decode($manifest_content, true);

        return $exists;

    }
    //产生报错
    else {
        if (!is_admin()) {
            $error_title = __('AIYA-CMS 启动错误', 'AIYA');
            $error_msg = __('没有找到生产环境文件，请检查 Vite 配置是否正确。', 'AIYA');

            wp_die($error_msg, $error_title, array('response' => 500));

            exit;
        }

        return false;
    }

}

//解析静态资源位置
function aya_vite_main_script_url()
{
    $manifest = aya_vite_get_manifest();

    if (isset($manifest[VITE_ENTRY_POINT])) {
        return AYA_URI . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[VITE_ENTRY_POINT]['file'];
    }
    return null;
}

function aya_vite_main_css_urls()
{
    $urls = [];
    $manifest = aya_vite_get_manifest();

    if (!empty($manifest[VITE_ENTRY_POINT])) {
        foreach ($manifest[VITE_ENTRY_POINT]['css'] as $css_file) {
            $urls[] = AYA_URI . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $css_file;
        }
    }

    return $urls;
}

function aya_vite_imports_script_urls()
{
    $urls = [];
    $manifest = aya_vite_get_manifest();

    if (!empty($manifest[VITE_ENTRY_POINT]['imports'])) {
        foreach ($manifest[VITE_ENTRY_POINT]['imports'] as $import) {
            $urls[] = AYA_URI . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[$import]['file'];
        }
    }

    return $urls;
}

/*
 * ------------------------------------------------------------------------------
 * 载入模板构造文件
 * ------------------------------------------------------------------------------
 */

add_action('wp_head', 'aya_dist_scripts_loader');

//模板静态资源载入函数
function aya_dist_scripts_loader()
{
    //如果是开发模式，载入Vite构造文件
    if (aya_is_dev_mode()) {
        if (aya_vite_reference()) {
            //返回client，用于支持HMR
            echo '<script type="module" src="' . VITE_HOST . '/@vite/client"></script>';
            //返回入口文件
            echo '<script type="module" src="' . VITE_HOST . '/' . ltrim(VITE_ENTRY_POINT, '/') . '"></script>';
            return;
        }
    }

    //从生产模式配置
    $res = '';
    $res .= '<script type="module" src="' . aya_vite_main_script_url() . '"></script>';

    $preload_urls = aya_vite_imports_script_urls();
    $css_urls = aya_vite_main_css_urls();

    foreach ($preload_urls as $url) {
        $res .= '<link rel="modulepreload" href="' . $url . '">';
    }

    foreach ($css_urls as $url) {
        $res .= '<link rel="stylesheet" href="' . $url . '">';
    }

    echo $res;

    return;
}
