/**
 * 入口文件
 * 
 */

import "vite/modulepreload-polyfill";

//获取组件
import "./styles/tailwind.css";
//import './scripts.js'

//Vue
import { createApp } from "vue";
import { createI18n } from 'vue-i18n';

//导入所有Vue组件
/*
const components = {};
const modules = import.meta.glob('./components/*.vue', { eager: true });

for (const path in modules) {
    components[modules[path].default.__name] = modules[path].default;
}

//循环实例化
for (const el of document.getElementsByClassName('vue-app')) {
    //实例化
    createApp({
        template: el.innerHTML,
        components
    }).mount(el);
}
*/

//导入翻译文件
import messages from './i18n';

const i18n = createI18n({
    legacy: false,
    locale: 'zh',
    messages
});

//单独导入Vue组件
import NotifyList from "./components/units/NotifyList.vue";
import SearchForm from "./components/units/SearchForm.vue";
import UserMenu from "./components/units/UserMenu.vue";
import LoginPop from "./components/units/LoginPop.vue";

//需要全局注册的组件
const components = {
    NotifyList,
    SearchForm,
    LoginPop,
    UserMenu
}

//实例化
const app = createApp({
    template: document.getElementById('vue-app').innerHTML,
    components,
    data() {
        return {
            mobileNavOpen: false
        }
    },
});

app.use(i18n);
app.mount('#vue-app');

//DOM Loaded
document.addEventListener("DOMContentLoaded", function () {
    console.log('js executed...');
});
