<?php

/**
 * 空内容
 */

?>
<div class="post-not-found">
    <div class="card">
        <div class="card-body text-center">
            <?php aya_get_404_page_img('card-mt-box card-in-img img-fluid'); ?>
            <h3 class="card-mt-box">
                <?php e_html(__('你访问的资源不存在！', 'AIYA')); ?>
            </h3>
            <p class="card-mt-box card-text">
                <span id="time_count_down">5</span>&nbsp;<?php e_html(__('秒后自动', 'AIYA')); ?>
                <a class="home-url" href="<?php e_html(home_url()); ?>"><?php e_html(__('返回首页', 'AIYA')); ?></a>
            </p>
            <script>
                var timeCountDownS = 5;
                var timeCountDownVal = 5;
                timeCountDownVal = setInterval(function() {
                    document.querySelector("#time_count_down").innerHTML = (--timeCountDownS);
                }, 1000);
                setTimeout(function() {
                    window.clearInterval(timeCountDownVal);
                    window.location = '/';
                }, 3000);
            </script>
        </div>
    </div>
</div>