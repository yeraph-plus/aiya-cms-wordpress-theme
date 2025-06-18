import lozad from 'lozad';

//lozadJS观察器初始化逻辑
export function initLozad() {
    //将所有图片的src地址先处理为data-src
    const images = document.querySelectorAll('img:not(.ignore-lazy)') as NodeListOf<HTMLImageElement>;

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

    // 添加类型定义以防止window.lozadObserver的类型错误
    (window as any).lozadObserver = observer;

    return observer;
}