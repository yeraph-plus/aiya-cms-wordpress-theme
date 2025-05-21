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

    if (!empty($manifest[VITE_ENTRY_POINT]['css'])) {
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
            if (isset($manifest[$import]['file'])) {
                $urls[] = AYA_URI . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[$import]['file'];
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
add_action('wp_footer', 'aya_debug_vite_assets');

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

//在页面末尾添加log
function aya_debug_vite_assets()
{
    echo '<div style="background:#f5f5f5; border:1px solid #ddd; padding:15px; margin:15px; font-family:monospace;">';
    echo '<h3>Vite 资源调试信息</h3>';

    // 检查 manifest 文件
    $manifest_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/.vite/manifest.json';
    echo "<p>Manifest 路径: {$manifest_path}</p>";
    echo "<p>Manifest 存在: " . (file_exists($manifest_path) ? '是' : '否') . "</p>";

    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);
        echo "<p>Manifest 入口点: " . (isset($manifest[VITE_ENTRY_POINT]) ? '找到' : '未找到') . "</p>";

        // 检查资源文件
        if (!empty($manifest[VITE_ENTRY_POINT]['imports'])) {
            echo "<h4>导入的模块:</h4><ul>";
            foreach ($manifest[VITE_ENTRY_POINT]['imports'] as $import) {
                $import_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $manifest[$import]['file'];
                echo "<li>{$import_path} - " . (file_exists($import_path) ? '存在' : '<strong style="color:red">不存在</strong>') . "</li>";
            }
            echo "</ul>";
        }

        // 检查CSS文件
        if (!empty($manifest[VITE_ENTRY_POINT]['css'])) {
            echo "<h4>CSS 文件:</h4><ul>";
            foreach ($manifest[VITE_ENTRY_POINT]['css'] as $css) {
                $css_path = AYA_PATH . ltrim(VITE_PUBLIC_PATH, '.') . '/' . $css;
                echo "<li>{$css_path} - " . (file_exists($css_path) ? '存在' : '<strong style="color:red">不存在</strong>') . "</li>";
            }
            echo "</ul>";
        }
    }

    echo '</div>';

}

//严格编码数组到JSON
function aya_vue_json_encode($value)
{
    if (is_array($value)) {
        $value = json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    return htmlspecialchars($value, ENT_QUOTES);
}

//简化 Vue 标签拼接
function aya_vue_load($slug = null, $attrs = [])
{
    //清理组件名
    $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $slug);

    if (empty($slug)) {
        //在PHP注册一个报错
        trigger_error('Vue component slug is empty or invalid.', E_USER_WARNING);

        return '';
    }

    //驼峰转换
    //$slug = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $slug));

    $props = '';

    if (!empty($attrs)) {
        //检查关联数组
        $is_assoc = is_array($attrs) && !array_is_list($attrs);

        //处理参数，作为多个独立props传递
        if ($is_assoc) {
            foreach ($attrs as $key => $value) {
                //清理字符
                $prop_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

                if ($prop_name === '') {
                    continue;
                }

                //限定props支持绑定的数据类型判断布尔、数字、数组、字符串
                if ($value === null) {
                    $props .= ' :' . $prop_name . '="null"';
                } elseif (is_bool($value)) {
                    $props .= ' :' . $prop_name . '=' . ($value ? '"true"' : '"false"');
                } elseif (is_numeric($value)) {
                    $props .= ' :' . $prop_name . '="' . $value . '"';
                } elseif (is_array($value)) {
                    $props .= ' :' . $prop_name . '="' . aya_vue_json_encode($value) . '"';
                } else {
                    //不是以上类型则作为静态绑定
                    $props .= ' ' . $prop_name . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
                }
            }

        }
        //直接编码
        else {
            $props .= ' :data="' . aya_vue_json_encode($attrs) . '"';
        }
    }

    echo sprintf('<%1$s%2$s></%1$s>', $slug, $props);

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

    //使用缓冲逻辑异步捕获DOM截取需要返回的部分
    add_action('template_redirect', 'aya_start_pjax_buffer', 1);
    add_action('wp_footer', 'aya_end_pjax_buffer', 999);
}


//开始缓冲触发点
function aya_start_pjax_buffer()
{
    ob_start();
}

//结束输出缓冲并返回所需内容
function aya_end_pjax_buffer()
{
    $content = ob_get_clean();

    // 解析HTML，提取所需内容
    $dom = new DOMDocument();

    // 禁用错误报告，避免由于HTML5标签引起的警告
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // 提取标题
    $titleNodes = $xpath->query('//title');
    $title = '';
    if ($titleNodes->length > 0) {
        $title = $titleNodes->item(0)->nodeValue;
    }

    // 提取主内容区域 - 调整为与前端JS中的选择器匹配
    $contentNodes = $xpath->query('//div[contains(@class, "pjax-container")]');
    $content = '';
    if ($contentNodes->length > 0) {
        $contentNode = $contentNodes->item(0);
        $content = $dom->saveHTML($contentNode);
    }

    // 如果找不到内容，回退到完整页面
    if (empty($content)) {
        echo $content;
        exit;
    }

    // 创建新文档结构
    $newDom = new DOMDocument();
    $newDom->loadHTML('<!DOCTYPE html><html><head><title>' . htmlspecialchars($title) . '</title></head><body>' . $content . '</body></html>');

    // 输出最终HTML
    echo $newDom->saveHTML();
    exit;
}