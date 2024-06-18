<?php

$t_url = preg_replace('/^url=(.*)$/i', '$1', $_SERVER["QUERY_STRING"]);

if (!empty($t_url)) {
    //判断取值是否加密
    if ($t_url == base64_encode(base64_decode($t_url))) {
        $t_url =  base64_decode($t_url);
    }
    //对取值进行网址校验和判断
    preg_match('/(http|https):\/\//', $t_url, $matches);
    if ($matches) {
        $url = $t_url;
        $title = '页面加载中,请稍候...';
    } else {
        preg_match('/\./i', $t_url, $matche);
        if ($matche) {
            $url = 'http://' . $t_url;
            $title = '页面加载中,请稍候...';
        } else {
            $url = home_url();
            $title = '参数错误，正在返回首页...';
        }
    }
} else {
    $title = '参数缺失，正在返回首页...';
    $url = home_url();
}
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="refresh" content="1;url='<?php echo $url; ?>';">
    <title><?php echo $title; ?></title>
</head>

<body>
    <div class="loading">
        <div class="spinner-wrapper">
            <span class="spinner-text">页面加载中,请稍候...</span>
            <span class="spinner"></span>
        </div>
    </div>
</body>

</html>