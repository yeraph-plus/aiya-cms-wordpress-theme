import { createApp, h } from 'vue';
import ToastComponent from './ui/Toast.vue';

type ToastOptions = {
    duration?: number;
    type?: 'info' | 'success' | 'warning' | 'error'
};

//吐司消息方法函数
const createToast = (message: string, options: ToastOptions = {}) => {
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

//定义方法拓展
interface ToastFunction {
    (message: string, options?: ToastOptions): void;
    info: (message: string, options?: Omit<ToastOptions, 'type'>) => void;
    success: (message: string, options?: Omit<ToastOptions, 'type'>) => void;
    warning: (message: string, options?: Omit<ToastOptions, 'type'>) => void;
    error: (message: string, options?: Omit<ToastOptions, 'type'>) => void;
}

//套用接口定义
export const toast = createToast as ToastFunction;

toast.info = (message: string, options: Omit<ToastOptions, 'type'> = {}) => {
    createToast(message, { ...options, type: 'info' });
};

toast.success = (message: string, options: Omit<ToastOptions, 'type'> = {}) => {
    createToast(message, { ...options, type: 'success' });
};

toast.warning = (message: string, options: Omit<ToastOptions, 'type'> = {}) => {
    createToast(message, { ...options, type: 'warning' });
};

toast.error = (message: string, options: Omit<ToastOptions, 'type'> = {}) => {
    createToast(message, { ...options, type: 'error' });
};

/**
 * import { toast } from '@/scripts/toast-plugin';
 * 
 * 基本用法
 * toast('消息内容');
 * 
 * 使用类型
 * toast('成功消息', { type: 'success' });
 * 
 * 便捷方法
 * toast.success('操作成功');
 * toast.error('出现错误');
 * toast.warning('警告信息');
 * toast.info('提示信息');
 * 
 * 自定义时长
 * toast.success('长时间显示', { duration: 5000 });
 */