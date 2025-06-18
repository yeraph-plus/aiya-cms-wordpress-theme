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

//检测屏幕宽度
export function mobileDetector() {
    // 当前状态
    let _isMobile = window.innerWidth < 1024;

    // 回调函数列表
    const callbacks = [];

    // 监听窗口大小变化
    window.addEventListener('resize', () => {
        const newState = window.innerWidth < 1024;

        // 只有状态变化时才触发回调
        if (newState !== _isMobile) {
            _isMobile = newState;
            // 执行所有注册的回调
            callbacks.forEach(callback => callback(_isMobile));
        }
    });

    // 返回增强的函数对象
    const mobileDetector = () => _isMobile;

    // 添加监听方法
    mobileDetector.onChange = (callback) => {
        if (typeof callback === 'function') {
            callbacks.push(callback);
        }
        return mobileDetector; // 支持链式调用
    };

    // 移除监听方法
    mobileDetector.offChange = (callback) => {
        const index = callbacks.indexOf(callback);
        if (index !== -1) {
            callbacks.splice(index, 1);
        }
        return mobileDetector; // 支持链式调用
    };

    return mobileDetector;
}

//页面加载遮罩
export function handleLoading() {
    const loadingMask = document.getElementById('page-loading-mask');
    const vueAppEl = document.getElementById('vue-app');

    // 显示Vue应用
    if (vueAppEl) {
        vueAppEl.style.visibility = 'visible';
    }

    // 隐藏加载遮罩
    if (loadingMask) {
        loadingMask.style.opacity = '0';
        loadingMask.style.transition = 'opacity 0.3s ease-out';

        setTimeout(() => {
            loadingMask.remove();
            document.body.classList.remove('loading');
        }, 300);
    } else {
        document.body.classList.remove('loading');
    }
}

//切换默认初始化配置
export function getAppConfig(element) {
    const defaultConfig = {
        defaultDarkMode: false,
        defaultSitebarClose: false,
    };

    if (!element) return defaultConfig;

    try {
        //解析 data-config 属性
        const dataConfig = element.dataset.config;
        if (!dataConfig) return defaultConfig;

        const parsedConfig = JSON.parse(dataConfig);

        return { ...defaultConfig, ...parsedConfig };
    } catch (e) {
        console.error('配置解析错误:', e);
        return defaultConfig;
    }
}