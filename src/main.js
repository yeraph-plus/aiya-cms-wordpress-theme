/**
 * 入口文件
 * 
 */
import "vite/modulepreload-polyfill";
import "./styles/tailwind.css";
//lozad
import lozad from 'lozad';
//masonry
import "./scripts/masonry-init";
//Vue
import { createApp } from "vue";
import { createI18n } from 'vue-i18n';
//Vue 翻译文件
import messages from './i18n';

//标记页面加载状态
document.body.classList.add('loading');

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
    components[modules[path].default.__name] = modules[path].default;
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
const app = createApp({
    template: document.getElementById('vue-app').innerHTML,
    components,
    //Vue初始化
    data() {
        return {
            isMobile: isMobile(),
            sidebarToggle: !isMobile(),
            mobileMenuToggle: false,
        }
    },
    created() {
        window.addEventListener('resize', () => {
            const sidebarOpen = isMobile();

            // 在移动设备上自动收起侧边栏，在大屏幕上自动展开
            if (sidebarOpen && this.sidebarToggle) {
                this.sidebarToggle = false;
            } else if (!sidebarOpen && !this.sidebarToggle) {
                this.sidebarToggle = true;
            }
        });
    },
    //Vue挂载完成
    mounted() {
        setTimeout(() => {
            handleLoading();
            // 初始化 Lozad
            lozadObserver = initLozad();
            //分发一个自定义事件用于触发二级逻辑
            document.dispatchEvent(new CustomEvent('vue-initialized'));
        }, 100);
    }
});

app.use(i18n);
app.mount('#vue-app');

//IF Load
window.addEventListener("load", function () {
    setTimeout(function () {
        document.body.classList.remove('loading');
    }, 300);

    if (!document.getElementById('vue-app')) {
        lozadObserver = initLozad();
    }
});

//DOM Loaded
document.addEventListener("DOMContentLoaded", function () { });

//Vue Loaded
document.addEventListener("vue-initialized", function () {
    console.log("\n\n %c AIYA-CMS %c https://www.yeraph.com", "color:#f1ab0e;background:#222222;padding:5px;", "background:#eee;padding:5px;");
});
