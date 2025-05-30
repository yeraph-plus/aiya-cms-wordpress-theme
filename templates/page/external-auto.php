<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 * 自动跳转模板
 * ------------------------------------------------------------------------------
 */

//验证来源
if (aya_home_url_referer_check() && isset($_GET['url'])) {
    //获取URL参数解码base64
    $decoded_url = base64_decode($_GET['url'], false);
    // 检查解码是否成功
    if ($decoded_url !== false) {

        $external_url = trim($decoded_url);

        //判断是否是网址
        if (filter_var($external_url, FILTER_VALIDATE_URL)) {
            $jump_url = $external_url;
            $title = __('页面加载中，请稍候...', 'AIYA');
        }
    } else {
        $jump_url = home_url();
        $title = __('跳转格式错误，返回...', 'AIYA');
    }
} else {
    return wp_redirect(home_url());
}

?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="refresh" content="1;url='<?php echo $jump_url; ?>';">
    <title>
        <?php echo $title; ?>
    </title>
</head>
<style>
body{background:#000}.spinner-wrapper{position:absolute;top:0;left:0;z-index:300;height:100%;min-width:100%;min-height:100%;background:rgba(255,255,255,0.93)}.spinner{position:absolute;top:50%;left:50%;margin-top:-50px}.spinner-text{position:absolute;top:50%;left:44%;color:#53565C;margin-top:100px;font-family:Arial;letter-spacing:1px;font-weight:700;font-size:36px}
</style>
<div class="spinner-wrapper">
    <svg xmlns="http://www.w3.org/2000/svg" class="spinner" width="105" height="105" style="box-sizing: border-box;" viewBox="0 0 105 105" fill="#53565C">
        <circle cx="12.5" cy="12.5" r="12.5">
            <animate attributeName="fill-opacity" begin="0s" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="12.5" cy="52.5" r="12.5" fill-opacity=".5">
            <animate attributeName="fill-opacity" begin="100ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="52.5" cy="12.5" r="12.5">
            <animate attributeName="fill-opacity" begin="300ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="52.5" cy="52.5" r="12.5">
            <animate attributeName="fill-opacity" begin="600ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="92.5" cy="12.5" r="12.5">
            <animate attributeName="fill-opacity" begin="800ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="92.5" cy="52.5" r="12.5">
            <animate attributeName="fill-opacity" begin="400ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="12.5" cy="92.5" r="12.5">
            <animate attributeName="fill-opacity" begin="700ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="52.5" cy="92.5" r="12.5">
            <animate attributeName="fill-opacity" begin="500ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
        <circle cx="92.5" cy="92.5" r="12.5">
            <animate attributeName="fill-opacity" begin="200ms" dur="1s" values="1;.2;1" calcMode="linear" repeatCount="indefinite" />
        </circle>
    </svg>
    <span class="spinner-text"><?php echo $title; ?></span>
</div>