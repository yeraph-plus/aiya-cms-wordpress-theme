import Viewer from 'viewerjs';
import 'viewerjs/dist/viewer.css';

export default class ViewerJSPlugin {
    constructor(container?: string | Element) {
        //定位根元素
        const root = container
            ? (typeof container === 'string' ? document.querySelector(container) : container)
            : document;
        if (!root) return;

        this.init(root);
    }

    init(root) {
        //初始化Viewer
        const viewer = new Viewer(root, {
            url: 'data-src', 
            inline: false,
            keyboard: false,
            toolbar: false,
            viewed() {
                viewer.zoomTo(0.8);
            },
        });
        console.log(viewer);
        //viewer.show();
    }
}

// 导出一个函数，用于初始化查看器
export function initViewer(): void {
    //只在文章页面触发初始化
    if (document.getElementsByClassName('.article-content')) {
        new ViewerJSPlugin('.article-content');
    }
}