/**
 * 入口文件
 * 
 */
import "vite/modulepreload-polyfill";
import "./styles/tailwind.css";
//Vue
import { createApp } from "vue";
import { createI18n } from 'vue-i18n';
//Vue 翻译文件
import messages from "./i18n";
//Debug工具
import { VueDebugTools } from "./scripts/debug";
//页面挂载
import { initPrism } from './scripts/prismjs-plugin';
import { initLozad } from './scripts/lozad-plugin';
import { initViewer } from './scripts/viewerjs-plugin';
import {
    initThemeSwitch,
    initNSFWHandlers,
    initRedirectButtons,
} from './init';

//标记页面加载状态
document.body.classList.add('loading');

//从HTML读取初始化配置
function getAppConfig() {
    const defaultConfig = {
        defaultDarkMode: false,
        defaultSitebarClose: false,
    };

    if (window.AIYACMS_CONFIG) {
        return { ...defaultConfig, ...window.AIYACMS_CONFIG };
    } else {
        return defaultConfig;
    }
}

//页面加载遮罩
function removeLoadingMask() {
    const loadingMask = document.getElementById('page-loading-mask');
    const vueAppElement = document.getElementById('vue-app');

    // 显示Vue应用
    if (vueAppElement) {
        vueAppElement.style.visibility = 'visible';
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
//导入语言包
const i18n = createI18n({
    legacy: false,
    locale: (() => {
        //检查浏览器语言设置
        const htmlLang = document.documentElement.lang;

        if (htmlLang) {
            return htmlLang.split('-')[0];
        }

        //默认使用 'zh'
        return 'zh';
    })(),
    messages
});
//读取所有Vue组件
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

/*
//循环实例化
for (const el of document.getElementsByClassName('vue-app')) {
    //实例化
    createApp({
        template: el.innerHTML,
        components
    }).mount(el);
}
*/

//实例化
const vueAppElement = document.getElementById('vue-app');

if (vueAppElement) {
    const app = createApp({
        template: vueAppElement.innerHTML,
        components,
        data() {
            //读取配置
            const appConfig = getAppConfig();
            //打印配置参数
            console.log('appConfig:', appConfig);
            //检测移动端
            const isMobile = window.innerWidth < 1024;
            //初始化主题切换
            initThemeSwitch(appConfig.defaultDarkMode);
            return {
                isMobile: isMobile,
                sidebarToggle: (isMobile) ? false : appConfig.defaultSitebarClose,
            }
        },
        mounted() {
            //创建Vue加载完成事件
            setTimeout(() => {
                document.dispatchEvent(new CustomEvent('vue-initialized'));
            }, 100);
            //将图片懒加载逻辑挂载到全局
            window.lozadObserver = initLozad();
        },
        created() {
            //...
        }
    });

    //创建了用于捕获错误的工具类
    // app.use(VueDebugTools);
    // app.debug();

    app.use(i18n);
    app.mount('#vue-app');
}

//IF Load
window.addEventListener("load", function () {
    //Screen Loading Mask
    removeLoadingMask();

    setTimeout(function () {
        document.body.classList.remove('loading');
    }, 300);

    //NSFW Click Unlock
    setTimeout(function () {
        initNSFWHandlers();

        //MutationObserver
        const observer = new MutationObserver(function () {
            initNSFWHandlers();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

    }, 1000);
});

//DOM Loaded
document.addEventListener("DOMContentLoaded", function () {
    //Custom Redirect Buttons
    initRedirectButtons();
    //初始化PrismJS
    initPrism();
    //初始化ViewerJS
    initViewer();
});

//Vue Loaded
document.addEventListener("vue-initialized", function () {
    console.log("\n\n %c AIYA-CMS %c https://www.yeraph.com", "color:#f1ab0e;background:#222222;padding:5px;", "background:#eee;padding:5px;");
});
