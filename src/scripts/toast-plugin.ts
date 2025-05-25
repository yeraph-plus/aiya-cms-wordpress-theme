import { createApp, h } from 'vue';
import ToastComponent from './Toast.vue';

type ToastOptions = {
    duration?: number;
    type?: 'info' | 'success' | 'warning' | 'error'
};

export const toast = (message: string, options: ToastOptions = {}) => {
    const container = document.createElement('div');
    document.body.appendChild(container);

    // 创建临时实例
    const app = createApp({
        render: () => h(ToastComponent, {
            message,
            duration: options.duration || 3000,
            type: options.type || 'info',
            onVanish: () => {
                app.unmount();
                container.remove();
            }
        })
    });

    app.mount(container);
};

/**
 * 使用示例：
 * import { toast } from '@/scripts/toast-plugin';
 * toast('消息内容', { type: 'success', duration: 5000 });
 */