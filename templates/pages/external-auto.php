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
if (function_exists('aya_home_url_referer_check') && aya_home_url_referer_check() && isset($_GET['url'])) {
    //获取URL参数解码base64
    $decoded_url = base64_decode($_GET['url'], false);
    // 检查解码是否成功
    if ($decoded_url !== false) {

        $external_url = trim($decoded_url);

        //判断是否是网址
        if (filter_var($external_url, FILTER_VALIDATE_URL)) {
            $jump_url = $external_url;
            $title = __('页面加载中，即将跳转...', 'AIYA');
        } else {
            $jump_url = home_url();
            $title = __('链接格式错误，返回首页...', 'AIYA');
        }
    } else {
        $jump_url = home_url();
        $title = __('解码失败，返回首页...', 'AIYA');
    }
} else {
    return wp_redirect(home_url());
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="1;url=<?php echo $jump_url; ?>"> -->
    <title><?php echo $title; ?></title>
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --primary-color: #0f172a;
            --border-radius: 16px;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #0f172a;
                --card-bg: #1e293b;
                --text-primary: #f1f5f9;
                --text-secondary: #94a3b8;
                --primary-color: #ffffff;
                --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .card {
            background: var(--card-bg);
            padding: 2.5rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            width: 90%;
            max-width: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            animation: fadeIn 0.5s ease-out;
        }

        .content h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .content p {
            font-size: 0.925rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .btn {
            margin-top: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 2rem;
            background-color: var(--primary-color);
            color: var(--card-bg);
            text-decoration: none;
            border-radius: 9999px;
            font-size: 0.925rem;
            font-weight: 500;
            transition: transform 0.2s, opacity 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .btn:active {
            transform: translateY(0);
        }

        .url-preview {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
            opacity: 0.7;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="content">
            <h1><?php echo $title; ?></h1>
            <p><?php _e('我们将带您前往外部链接，请注意账号安全。', 'AIYA'); ?></p>
            <p style="font-size: 0.8rem; margin-top: 0.25rem;"><?php _e('如果没有自动跳转，请点击下方按钮', 'AIYA'); ?></p>
        </div>

        <a href="<?php echo $jump_url; ?>" class="btn">
            <?php _e('立即前往', 'AIYA'); ?>
        </a>

        <div class="url-preview" title="<?php echo esc_attr($jump_url); ?>">
            <?php echo parse_url($jump_url, PHP_URL_HOST) ?: 'External Link'; ?>
        </div>
    </div>
</body>

</html>