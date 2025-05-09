/**
 * 入口文件
 * 
 */

import 'vite/modulepreload-polyfill'

//获取组件
import "./styles/tailwind.css"
//import './scripts.js'

//Vue
import { createApp } from 'vue'

//createApp(App).mount('#app')

//单独导入Vue组件
import HelloWorld from './components/HelloWorld.vue'
const components = {
    HelloWorld
}

/*
//导入所有Vue组件
const components = {}
const modules = import.meta.glob('./components/*.vue', { eager: true })

for (const path in modules) {
    components[modules[path].default.__name] = modules[path].default
}

*/
for (const el of document.getElementsById('main')) {
    //实例化
    createApp({
        template: el.innerHTML,
        components
    }).mount(el)
}

//当DOM完全加载时
document.addEventListener("DOMContentLoaded", function () {
    console.log('js executed...');
});