

//NSFW图片遮罩初始化
export function initNSFWHandlers() {
    document.querySelectorAll(".nsfw").forEach(function (element) {
        if (element.dataset.nsfwInitialized) return;

        //标记为已初始化
        element.dataset.nsfwInitialized = true;

        //添加点击处理
        element.addEventListener("click", function (e) {
            e.stopPropagation();
            this.classList.toggle("unlocked");
        });
    });
}

//捕获自定义按钮
export function initRedirectButtons() {
    document.querySelectorAll(".btn-redirect").forEach(button => {
        if (button.dataset.redirectInitialized) return;

        //标记为已初始化
        button.dataset.redirectInitialized = true;

        //添加点击处理
        button.addEventListener("click", function (e) {
            const href = this.getAttribute("data-href");
            if (href) {
                // 检查是否为外部链接
                if (href.startsWith("http") && !href.includes(window.location.hostname)) {
                    // 外部链接使用新标签页打开
                    window.open(href, "_blank");
                } else {
                    // 内部链接直接跳转
                    window.location.href = href;
                }
            }
        });
    });
}
