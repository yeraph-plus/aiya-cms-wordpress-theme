<?php
aya_home_end();
return;
?>
<!-- start footer section -->
<footer class="mt-auto p-6 text-center dark:text-white-dark sm:ltr:text-left sm:rtl:text-right">
    © <span id="footer-year">2022</span>. Aiya-CMS All rights reserved.
</footer>
</div>
<script type="text/javascript">
    // 设置body为加载状态
    document.body.classList.add('loading');

    // 页面完全加载后移除body的loading类
    window.addEventListener('load', function () {
        setTimeout(function () {
            document.body.classList.remove('loading');
        }, 300);
    });
</script>