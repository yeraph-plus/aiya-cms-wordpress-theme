import { createApp, h } from 'vue';
import ModalComponent from './ui/Modal.vue';

type ModalOptions = {
    title?: string;
    Refresh?: boolean;
};

//创建模态框方法
const createModal = (message: string, options: ModalOptions = {}) => {
    const container = document.createElement('div');
    document.body.appendChild(container);

    // 创建临时实例
    const app = createApp({
        render: () => h(ModalComponent, {
            message,
            title: options.title || 'INFO',
            Refresh: options.Refresh || false,
            onClose: () => {
                app.unmount();
                container.remove();
            },
            onRefresh: () => {
                window.location.reload();
            }
        })
    });

    app.mount(container);
};

export const showModal = (
    message: string,
    options: ModalOptions = {}
) => {
    createModal(message, options);
};

/**
 * 使用示例:
 * import { showModal } from '@/scripts/modal-plugin';
 * 
 * 基本用法
 * showModal('消息内容');
 * 
 * 带标题
 * showModal('消息内容', { title: '操作成功' });
 * 
 * 显示带刷新按钮的模态框
 * showModal('操作已完成，请刷新页面查看最新状态', { Refresh: true });
 * 
 */
