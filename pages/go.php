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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>加载动画示例</title>

    <style>
        /* 全局加载动画样式 */
        #loadingOverlay {
            display: none;
            /* 默认不显示 */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
        }

        #loadingSpinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 5px solid #f3f3f3;
            /* 灰色 */
            border-top: 5px solid #3498db;
            /* 蓝色 */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }

        /* 动画 */
        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div id="loadingOverlay">
        <div id="loadingSpinner"></div>
    </div>

    <!-- 页面内容 -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 显示加载动画
            var loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'block';

            // 假设页面加载需要一段时间，这里使用setTimeout模拟
            setTimeout(function() {
                // 页面加载完成，隐藏加载动画
                loadingOverlay.style.display = 'none';
            }, 3000); // 假设加载需要3秒钟
        });
    </script>
</body>

</html>