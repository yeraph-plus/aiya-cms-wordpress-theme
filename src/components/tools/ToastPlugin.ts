import { createApp, h } from 'vue';
import ToastComponent from './Toast.vue';

type ToastOptions = { duration?: number };

export const toast = (message: string, options: ToastOptions = {}) => {

    const container = document.createElement('div');

    document.body.appendChild(container);

    //创建临时实例
    const app = createApp({
        render: () => h(ToastComponent, {
            message,
            duration: options.duration || 3000,
            // 组件自动卸载逻辑
            onVanish: () => {
                app.unmount();
                container.remove();
            }
        })
    });

    app.mount(container);
};