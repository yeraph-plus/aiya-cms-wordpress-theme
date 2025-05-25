import Masonry from 'masonry-layout';
import "../styles/masonry.css";

/**
 * 初始化瀑布流布局
 */
document.addEventListener("DOMContentLoaded", function() {
    initMasonry();
});

// 也监听Vue初始化完成事件
document.addEventListener("vue-initialized", function() {
    initMasonry();
});

// 保存全局的masonry实例
let masonryInstance = null;

function initMasonry() {
    const masonryGrid = document.querySelector('.masonry-grid');
    
    if (!masonryGrid || masonryGrid.classList.contains('masonry-initialized')) return;
    
    // 标记已初始化，避免重复执行
    masonryGrid.classList.add('masonry-initialized');

    // 先隐藏容器，添加加载状态
    masonryGrid.style.opacity = "0";
    masonryGrid.classList.add('is-loading');

    // 在容器上方添加加载指示器
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'masonry-loading-indicator';
    loadingIndicator.innerHTML = '<div class="loading-spinner"></div>';
    masonryGrid.parentNode.insertBefore(loadingIndicator, masonryGrid);

    // 获取列数，默认为4
    const columnsAttr = masonryGrid.getAttribute('data-columns');
    const columns = columnsAttr ? parseInt(columnsAttr.replace(/['"]/g, '')) : 4;

    // 初始化Masonry
    masonryInstance = new Masonry(masonryGrid, {
        itemSelector: 'article.card',
        columnWidth: '.masonry-sizer',
        percentPosition: true,
        gutter: 16, // 卡片之间的间距
        transitionDuration: '0.3s',
        initLayout: true
    });

    // 根据columns动态设置CSS变量，用于控制列宽
    const columnWidth = `calc(${100 / columns}% - ${(columns - 1) * 16 / columns}px)`;
    document.documentElement.style.setProperty('--masonry-column-width', columnWidth);

    // 监听lozad加载的图片事件，每当图片加载完成时重新布局
    document.addEventListener('lozad-loaded', function(e) {
        if (masonryInstance) {
            masonryInstance.layout();
        }
    });

    // 使用MutationObserver检测图片加载状态变化
    const imageObserver = new MutationObserver((mutations) => {
        let shouldLayout = false;
        
        mutations.forEach(mutation => {
            if (mutation.type === 'attributes' && 
                mutation.attributeName === 'class' && 
                mutation.target.tagName === 'IMG' &&
                mutation.target.classList.contains('loaded')) {
                shouldLayout = true;
            }
        });
        
        if (shouldLayout && masonryInstance) {
            masonryInstance.layout();
        }
    });
    
    // 观察瀑布流容器内的所有图片元素
    const images = masonryGrid.querySelectorAll('img');
    images.forEach(img => {
        imageObserver.observe(img, { attributes: true });
    });
    
    // 设置一个延迟显示布局的定时器，给图片一些加载时间
    setTimeout(() => {
        if (masonryInstance) {
            masonryInstance.layout();
        }
        
        // 显示布局
        masonryGrid.style.opacity = "1";
        masonryGrid.classList.remove('is-loading');
        
        // 移除加载指示器
        if (loadingIndicator && loadingIndicator.parentNode) {
            loadingIndicator.parentNode.removeChild(loadingIndicator);
        }
    }, 800); // 给足够的时间让lozad加载一些初始可见的图片

    // 窗口大小改变时重新布局
    window.addEventListener('resize', () => {
        if (masonryInstance) {
            masonryInstance.layout();
        }
    });

    // 处理异步加载的内容
    const contentObserver = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // 新内容加载时先隐藏
                const newItems = Array.from(mutation.addedNodes).filter(node =>
                    node.nodeType === Node.ELEMENT_NODE &&
                    node.matches('article.card')
                );

                newItems.forEach(item => {
                    item.style.opacity = "0";
                });

                // 监听新添加的元素中的图片
                newItems.forEach(item => {
                    const newImages = item.querySelectorAll('img');
                    newImages.forEach(img => {
                        imageObserver.observe(img, { attributes: true });
                    });
                });

                // 通过自定义事件通知lozad观察新元素
                document.dispatchEvent(new CustomEvent('content-added'));
                
                // 重新布局并显示新项目
                setTimeout(() => {
                    if (masonryInstance) {
                        masonryInstance.layout();
                    }
                    
                    newItems.forEach(item => {
                        item.style.opacity = "1";
                    });
                }, 300);
            }
        }
    });

    contentObserver.observe(masonryGrid, { childList: true });
    
    // 为防止初始加载时布局不正确，添加一个额外的重新布局调用
    document.addEventListener('vue-initialized', () => {
        setTimeout(() => {
            if (masonryInstance) {
                masonryInstance.layout();
            }
        }, 1000);
    });
}

// 添加一个自定义事件，让Main.js中的lozad实例可以在初始化后观察新添加的元素
document.addEventListener('content-added', () => {
    // 主文件中的lozadObserver会处理这个逻辑
    if (window.lozadObserver && typeof window.lozadObserver.observe === 'function') {
        window.lozadObserver.observe();
    }
});

// 创建一个自定义事件，用于通知masonry布局图片已加载
// 在main.js中的lozad配置中调用此事件
export function notifyImageLoaded() {
    document.dispatchEvent(new CustomEvent('lozad-loaded'));
}