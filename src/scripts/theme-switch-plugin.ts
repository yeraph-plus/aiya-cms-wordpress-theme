//引用 Window 接口
declare global {
    interface Window {
        currentTheme?: string;
        toggleTheme?: () => void;
    }
}

//主题切换
export function initThemeSwitch(defaultDarkMode = false) {
    const availableThemes = [
        "light",
        "dark"
    ];

    const applyTheme = (theme) => {
        document.documentElement.setAttribute("data-theme", theme);

        if (theme === "dark") {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }

        //保存到全局变量
        window.currentTheme = theme;

        window.dispatchEvent(new CustomEvent('theme-changed'));
    };

    //检查系统Media属性
    const isSystemDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches;

    //从 localStorage 获取用户保存的主题
    const savedTheme = localStorage.getItem("theme");

    //使用 defaultDarkMode 设置
    if (savedTheme && availableThemes.includes(savedTheme)) {
        applyTheme(savedTheme);
    } else {
        // 如果没有保存的主题或保存的主题无效，使用 defaultDarkMode 设置或系统默认
        const defaultTheme = defaultDarkMode ? "dark" : (isSystemDarkMode ? "dark" : "light");
        applyTheme(defaultTheme);
    }

    //监听系统Media属性
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", (e) => {
        //跟随系统变化
        applyTheme(e.matches ? "dark" : "light");
    });

    window.toggleTheme = () => {
        const currentTheme = window.currentTheme || "dark";
        const currentIndex = availableThemes.findIndex((t) => t === currentTheme);
        const nextIndex = (currentIndex + 1) % availableThemes.length;
        const nextTheme = availableThemes[nextIndex];

        //保存到 localStorage
        localStorage.setItem("theme", nextTheme);
        applyTheme(nextTheme);
    };
}