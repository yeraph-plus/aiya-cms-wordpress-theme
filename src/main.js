/**
 * 入口文件
 * 
 */
import "vite/modulepreload-polyfill";
import "./styles/tailwind.css";
//lozad
import lozad from 'lozad';
//Vue
import { createApp } from "vue";
import { createI18n } from 'vue-i18n';
//Vue 翻译文件
import messages from "./i18n";
//Debug工具
import VueDebugTools from "./scripts/debug";

//标记页面加载状态
document.body.classList.add('loading');

//主题管理
function initThemeSwitch(defaultDarkMode = false) {
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

//lozad观察器初始化逻辑
function initLozad() {
    //将所有图片的src地址先处理为data-src
    const images = document.querySelectorAll('img:not(.ignore-lazy)');

    images.forEach(img => {
        // 如果图片已经有data-src，则跳过
        if (!img.getAttribute('data-src') && img.src) {
            // 保存原始src
            img.setAttribute('data-src', img.src);
            // 清除原始src以防止立即加载
            img.removeAttribute('src');
            // 添加lozad需要的class
            img.classList.add('lozad');
        }
    });

    //lozad init
    const observer = lozad('.lozad', {
        rootMargin: '10px 0px', // 图片进入视口10px时加载
        threshold: 0.1, // 当图片可见10%时加载
        loaded: function (el) {
            // 图片加载后添加淡入效果
            el.classList.add('loaded');
        }
    });

    observer.observe();

    window.lozadObserver = observer;

    return observer;
}

//检测屏幕宽度
function isMobile() {
    return window.innerWidth < 1024;
}

//页面加载遮罩
function handleLoading() {
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

//单独导入Vue组件
/*
import HelloWorld from "./components/HelloWorld.vue";
*/

//导入所有Vue组件
const components = {};
const modules = import.meta.glob('./components/*.vue', { eager: true });

for (const path in modules) {
    const component = modules[path].default;

    if (component.__name) {
        components[component.__name] = component;
    } else if (component.name) {
        components[component.name] = component;
    } else {
        // 从路径提取名称作为后备
        const fileName = path.split('/').pop().replace('.vue', '');
        components[fileName] = component;
    }
}

//导入语言包
const i18n = createI18n({
    legacy: false,
    locale: 'zh',
    messages
});

//循环实例化
/*
for (const el of document.getElementsByClassName('vue-app')) {
    //实例化
    createApp({
        template: el.innerHTML,
        components
    }).mount(el);
}
*/

//实例化
const vueAppEl = document.getElementById('vue-app');

if (vueAppEl) {
    const app = createApp({
        template: vueAppEl.innerHTML,
        components,
        data() {
            return {
                isMobile: isMobile(),
                sidebarToggle: window.appConfig.sidebarToggle,
                defaultDarkMode: window.appConfig.defaultDarkMode,
            }
        },
        created() {
            window.addEventListener('resize', () => {
                this.isMobile = isMobile();

                // 在移动设备上自动收起侧边栏，在大屏幕上自动展开
                if (this.isMobile && this.sidebarToggle) {
                    this.sidebarToggle = false;
                } else if (!this.isMobile && !this.sidebarToggle) {
                    this.sidebarToggle = true;
                }
            });
        },
        mounted() {
            //初始化主题切换
            initThemeSwitch(this.defaultDarkMode);

            //先声明 lozadObserver 对象确保正确定义
            let lozadObserver;
            setTimeout(() => {
                handleLoading();
                document.dispatchEvent(new CustomEvent('vue-initialized'));
                lozadObserver = initLozad();
                // 全局变量
                window.lozadObserver = lozadObserver;
            }, 100);
        }
    });

    app.use(VueDebugTools);
    app.debug();

    app.use(i18n);
    app.mount('#vue-app');
}


//IF Load
window.addEventListener("load", function () {
    setTimeout(function () {
        document.body.classList.remove('loading');
    }, 300);

    if (!document.getElementById('vue-app')) {
        // 确保变量被正确声明
        window.lozadObserver = initLozad();
    }
});

//DOM Loaded
document.addEventListener("DOMContentLoaded", function () { });

//Vue Loaded
document.addEventListener("vue-initialized", function () {
    console.log("\n\n %c AIYA-CMS %c https://www.yeraph.com", "color:#f1ab0e;background:#222222;padding:5px;", "background:#eee;padding:5px;");
});
