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
define('VITE_ENTRY_POINT', 'src/main.tsx');
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
    unset($handle);

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
        return get_template_directory_uri() . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[VITE_ENTRY_POINT]['file'];
    }
    return null;
}

function aya_vite_main_css_urls()
{
    $urls = [];
    $manifest = aya_vite_get_manifest();

    if (!empty($manifest[VITE_ENTRY_POINT]['css'])) {
        foreach ($manifest[VITE_ENTRY_POINT]['css'] as $css_file) {
            $urls[] = get_template_directory_uri() . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $css_file;
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
            if (isset($manifest[$import]['file'])) {
                $urls[] = get_template_directory_uri() . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[$import]['file'];
            }
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
//add_action('wp_footer', 'aya_debug_vite_assets');

//模板静态资源载入函数
function aya_dist_scripts_loader()
{
    //如果是开发模式，载入Vite构造文件
    if (aya_is_debug()) {
        if (aya_vite_reference()) {
            //返回client，用于支持HMR
            echo '<script type="module" src="' . VITE_HOST . '/@vite/client"></script>' . PHP_EOL;
            // 注入 React preamble
            echo '<script type="module">' . PHP_EOL;
            echo 'import RefreshRuntime from "' . VITE_HOST . '/@react-refresh";' . PHP_EOL;
            echo 'RefreshRuntime.injectIntoGlobalHook(window);' . PHP_EOL;
            echo 'window.$RefreshReg$ = () => {};' . PHP_EOL;
            echo 'window.$RefreshSig$ = () => (type) => type;' . PHP_EOL;
            echo 'window.__vite_plugin_react_preamble_installed__ = true;' . PHP_EOL;
            echo '</script>' . PHP_EOL;
            //返回入口文件
            echo '<script type="module" src="' . VITE_HOST . '/' . ltrim(VITE_ENTRY_POINT, '/') . '"></script>' . PHP_EOL;

            return;
        }
    }

    //从生产模式配置
    $res = '';

    $res .= '<script type="module" data-cfasync="false" src="' . aya_vite_main_script_url() . '" crossorigin="anonymous"></script>' . PHP_EOL;

    $preload_urls = aya_vite_imports_script_urls();
    $css_urls = aya_vite_main_css_urls();

    foreach ($preload_urls as $url) {
        $res .= '<link rel="modulepreload" as="script" href="' . $url . '" crossorigin="anonymous">' . PHP_EOL;
    }

    foreach ($css_urls as $url) {
        $res .= '<link rel="stylesheet" href="' . $url . '">' . PHP_EOL;
    }

    echo $res;

    return;
}

//在页面末尾添加log
function aya_debug_vite_assets()
{
    if (aya_is_debug()) {
        // 创建数据数组
        $debug_data = array(
            'vite_server' => array(
                'status' => aya_vite_reference() ? 'connected' : 'disconnected',
                'host' => VITE_HOST
            ),
            'config' => array(
                'entry_point' => VITE_ENTRY_POINT,
                'public_path' => VITE_PUBLIC_PATH
            )
        );

        // 检查 manifest 并添加相关信息
        $manifest_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/.vite/manifest.json';
        $manifest_exists = file_exists($manifest_path);

        $debug_data['manifest'] = array(
            'path' => $manifest_path,
            'status' => $manifest_exists ? 'found' : 'missing'
        );

        if ($manifest_exists) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
            $debug_data['manifest']['last_modified'] = human_time_diff(filemtime($manifest_path)) . ' ago';
            $debug_data['manifest']['entry_found'] = isset($manifest[VITE_ENTRY_POINT]);
            $debug_data['manifest']['size'] = size_format(filesize($manifest_path));

            // 添加主资源信息
            if (isset($manifest[VITE_ENTRY_POINT])) {
                $main_asset_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[VITE_ENTRY_POINT]['file'];
                $main_asset_exists = file_exists($main_asset_path);

                $debug_data['main_asset'] = array(
                    'path' => $main_asset_path,
                    'status' => $main_asset_exists ? 'found' : 'missing'
                );

                if ($main_asset_exists) {
                    $debug_data['main_asset']['size'] = size_format(filesize($main_asset_path));
                }

                // 导入模块信息
                if (!empty($manifest[VITE_ENTRY_POINT]['imports'])) {
                    $debug_data['imported_modules'] = array();

                    foreach ($manifest[VITE_ENTRY_POINT]['imports'] as $import) {
                        $import_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[$import]['file'];
                        $exists = file_exists($import_path);

                        $debug_data['imported_modules'][] = array(
                            'name' => basename($import_path),
                            'status' => $exists ? 'found' : 'missing',
                            'size' => $exists ? size_format(filesize($import_path)) : null
                        );
                    }
                }

                // CSS 文件信息
                if (!empty($manifest[VITE_ENTRY_POINT]['css'])) {
                    $debug_data['css_files'] = array();

                    foreach ($manifest[VITE_ENTRY_POINT]['css'] as $css) {
                        $css_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $css;
                        $exists = file_exists($css_path);

                        $debug_data['css_files'][] = array(
                            'name' => basename($css_path),
                            'status' => $exists ? 'found' : 'missing',
                            'size' => $exists ? size_format(filesize($css_path)) : null
                        );
                    }
                }
            }
        }

        //输出格式
        $html = '';

        $html .= '<div style="padding: 2rem;"><h3>Vite Debug Information: </h3><pre style="background:#f5f5f5; padding:10px; overflow:auto; max-height:500px;">';
        $html .= json_encode($debug_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $html .= '</pre></div>';

        echo $html;
    }
}

//严格编码数组到JSON（用于 HTML attribute 传递 props）
function aya_json_strict_encode($value)
{
    $json = wp_json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    if ($json === false || $json === null) {
        $json = 'null';
    }

    return htmlspecialchars((string) $json, ENT_QUOTES);
}

//构造 React 群岛节点输出
function aya_react_island($slug = null, $props = [], $server_html = '')
{
    $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $slug);

    if (empty($slug)) {
        trigger_error('Island component slug is empty or invalid.', E_USER_WARNING);

        return '';
    }

    // 生成容器ID
    $data_id = uniqid();

    $html_attrs = ' data-id="' . esc_attr($data_id) . '"' . ' data-island="' . esc_attr($slug) . '"';

    if ($props !== null && $props !== []) {
        $html_attrs .= ' data-props="' . aya_json_strict_encode($props) . '"';
    }


    echo '<div' . $html_attrs . '>' . $server_html . '</div>' . PHP_EOL;

    return;
}

/*
 * ------------------------------------------------------------------------------
 * PJAX 请求模式
 * ------------------------------------------------------------------------------
 */

add_action('init', 'aya_handle_pjax_request');

//判断PJAX请求头
function aya_is_pjax_request()
{
    return !empty($_SERVER['HTTP_X_PJAX']) ||
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
}

//请求处理逻辑
function aya_handle_pjax_request()
{
    if (!aya_is_pjax_request()) {
        return;
    }

    //TODO 缓冲逻辑异步捕获DOM截取需要返回的部分
}
