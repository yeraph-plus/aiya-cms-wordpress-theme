import Viewer from 'viewerjs';

//初始化 ViewerJS 图片查看器
export function initViewer() {
    const container = document.querySelector('.article-content') as HTMLElement;

    //检查ViewerJS是否需要初始化
    if (!container || container.dataset.viewerInitialized === "done") {
        return;
    }

    //查询所有图片
    const images = container.querySelectorAll('img');

    if (images.length === 0) {
        return;
    }

    try {
        //标记已初始化
        container.dataset.viewerInitialized = "done";

        const viewer = new Viewer(container, {
            url: 'data-src',
            inline: false,
            keyboard: false,
            toolbar: false,
            viewed() {
                viewer.zoomTo(0.8);
            },
            //完成时添加一条Log
            ready() {
                console.log('ViewerJS wrapper is activated.');
            },
            //显示时检查图片加载状态
            show() {
                const currentImage = (viewer as any).image;

                if (currentImage && !currentImage.complete) {
                    currentImage.onload = () => {
                        viewer.zoomTo(0.8);
                    };
                }
            }
        });
    } catch (error) {
        console.error('Failed to load ViewerJS:', error);
        /*
        //降级方法：打开新窗口查看图片
        images.forEach(img => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function () {
                window.open(this.src, '_blank');
            });
        });
        */
    }
}