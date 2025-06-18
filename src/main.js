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
import { initPrism } from './scripts/prismjs-plugin';
import { initLozad } from './scripts/lozad-plugin';
//导入初始化函数
import {
    initThemeSwitch,
    initNSFWHandlers,
    initRedirectButtons,
    mobileDetector,
    handleLoading,
    getAppConfig
} from './init';

//标记页面加载状态
document.body.classList.add('loading');

//创建页面宽度监听单例
const isMobile = mobileDetector();
window.isMobile = isMobile;

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

//读取配置
const config = getAppConfig(vueAppEl);

window.appConfig = config;

if (vueAppEl) {
    const app = createApp({
        template: vueAppEl.innerHTML,
        components,
        data() {
            const currentIsMobile = isMobile();
            //计算初始侧边栏状态
            const initialSidebarState = currentIsMobile ? false : !config.defaultSitebarClose;
            //打印配置参数调试
            //console.log('config:', config);

            return {
                isMobile: currentIsMobile,
                sidebarToggle: initialSidebarState,
                defaultDarkMode: config.defaultDarkMode,
                defaultSitebarClose: config.defaultSitebarClose,
            }
        },
        created() {
            //监听 resize 变化
            isMobile.onChange(mobileState => {
                this.isMobile = mobileState;
                //移动端始终折叠
                if (mobileState) {
                    this.sidebarToggle = false;
                } else {
                    this.sidebarToggle = !this.defaultSitebarClose;
                }
            });
        },
        mounted() {
            //初始化主题切换
            initThemeSwitch(this.defaultDarkMode);

            setTimeout(() => {
                //操作页面加载器遮罩
                handleLoading();

                //创建Vue加载完成事件
                document.dispatchEvent(new CustomEvent('vue-initialized'));

                //将图片懒加载逻辑挂载到全局
                window.lozadObserver = initLozad();
            }, 100);
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
});

//Vue Loaded
document.addEventListener("vue-initialized", function () {
    console.log("\n\n %c AIYA-CMS %c https://www.yeraph.com", "color:#f1ab0e;background:#222222;padding:5px;", "background:#eee;padding:5px;");
});
