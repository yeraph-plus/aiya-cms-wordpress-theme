//主题切换
export function initThemeSwitch(defaultDarkMode = false) {
    // 可用主题列表
    const availableThemes = [
        "light",
        "dark"
    ];

    // 应用主题
    const applyTheme = (theme) => {
        // 根元素添加 data-theme 属性（用于 DaisyUI）
        document.documentElement.setAttribute("data-theme", theme);

        // 处理深色模式 class
        if (theme === "dark") {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }

        // 保存当前主题到全局变量，便于组件访问
        window.currentTheme = theme;

        // 触发主题变化事件，通知组件
        window.dispatchEvent(new CustomEvent('theme-changed'));
    };

    // 检查系统暗色模式
    const isSystemDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches;

    // 从 localStorage 获取用户保存的主题
    const savedTheme = localStorage.getItem("theme");

    // 应用主题：优先使用保存的主题，其次使用 defaultDarkMode 设置，最后使用系统主题
    if (savedTheme && availableThemes.includes(savedTheme)) {
        applyTheme(savedTheme);
    } else {
        // 如果没有保存的主题或保存的主题无效，使用 defaultDarkMode 设置或系统默认
        const defaultTheme = defaultDarkMode ? "dark" : (isSystemDarkMode ? "dark" : "light");
        applyTheme(defaultTheme);
    }

    // 监听系统颜色方案变化
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", (e) => {
        // 如果没有保存的主题，则跟随系统变化
        if (!localStorage.getItem("theme")) {
            applyTheme(e.matches ? "dark" : "light");
        }
    });

    // 全局主题切换函数，供组件调用
    window.toggleTheme = () => {
        const currentTheme = window.currentTheme || "dark";
        const currentIndex = availableThemes.findIndex((t) => t === currentTheme);
        const nextIndex = (currentIndex + 1) % availableThemes.length;
        const nextTheme = availableThemes[nextIndex];

        // 保存到 localStorage
        localStorage.setItem("theme", nextTheme);
        applyTheme(nextTheme);
    };
}

//NSFW遮罩事件初始化
export function initNSFWHandlers() {
    document.querySelectorAll(".nsfw").forEach(function (element) {
        if (element.dataset.nsfwInitialized) return;

        // 标记为已初始化
        element.dataset.nsfwInitialized = "true";

        // 添加点击处理
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

        // 标记为已初始化
        button.dataset.redirectInitialized = "true";

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