import { hydrateAllIslands, unmountIslandsIn } from './island';
import { fetchPage, setupLinkInterception } from './lib/pjax';
import { initViewer } from './lib/viewer-plugin';
import { initPrism } from './lib/prism-plugin';
import { initClipboard } from './lib/clipboard-plugin';
import './tailwind.css';

let viewerInstance: ReturnType<typeof initViewer> = null;

// 滚动位置存储
const scrollPositions: Record<string, number> = {};

// 清理滚动位置存储
function cleanupScrollPositions() {
    const keys = Object.keys(scrollPositions);
    if (keys.length > 20) {
        // 保留最近 10 条
        const sorted = keys.sort((a, b) => scrollPositions[b] - scrollPositions[a]);
        sorted.slice(10).forEach(k => delete scrollPositions[k]);
    }
}

// 设置定时清理
setInterval(cleanupScrollPositions, 30000);

async function navigate(url: string) {
    // 1. Dispatch pjax:send
    const sendEvent = new CustomEvent('pjax:send', { detail: { url }, cancelable: true });
    if (!window.dispatchEvent(sendEvent)) return;

    // 保存当前滚动位置
    scrollPositions[location.href] = window.scrollY;

    // 获取当前容器并添加淡出效果
    const mainContainer = document.querySelector('[data-pjax-container="main"]') as HTMLElement;
    const sidebarContainer = document.querySelector('[data-pjax-container="sidebar"]') as HTMLElement;

    const setOpacity = (el: HTMLElement | null, opacity: string) => {
        if (el) {
            el.style.transition = 'opacity 300ms ease-in-out';
            el.style.opacity = opacity;
        }
    };

    setOpacity(mainContainer, '0.5');
    setOpacity(sidebarContainer, '0.5');

    try {
        // 2. 获取新页面数据
        const { title, containers } = await fetchPage(url);

        // 3. 更新页面标题
        if (title) document.title = title;

        // 4. 卸载当前容器内的所有岛屿
        unmountIslandsIn(mainContainer);
        unmountIslandsIn(sidebarContainer);

        // 5. 替换容器内容
        if (mainContainer && containers.main) {
            mainContainer.innerHTML = containers.main;
        }
        if (sidebarContainer && containers.sidebar) {
            sidebarContainer.innerHTML = containers.sidebar;
        }

        // 6. 扫描并挂载新岛屿
        if (mainContainer) hydrateAllIslands(mainContainer);
        if (sidebarContainer) hydrateAllIslands(sidebarContainer);

        // 7. 恢复滚动位置（新页面）
        const savedY = scrollPositions[url] || 0;
        window.scrollTo(0, savedY);

        // 恢复透明度
        requestAnimationFrame(() => {
            setOpacity(mainContainer, '1');
            setOpacity(sidebarContainer, '1');
            setTimeout(() => {
                if (mainContainer) {
                    mainContainer.style.transition = '';
                    mainContainer.style.opacity = '';
                }
                if (sidebarContainer) {
                    sidebarContainer.style.transition = '';
                    sidebarContainer.style.opacity = '';
                }
            }, 300);
        });

        // 8. 触发自定义事件
        window.dispatchEvent(new CustomEvent('pjax:complete', { detail: { url } }));
        window.dispatchEvent(new Event('app:reload'));

        // 9. 重新初始化插件
        reloadViewer();
        reloadPrism();
        initClipboard();

    } catch (err) {
        // 发生错误时恢复样式
        setOpacity(mainContainer, '');
        setOpacity(sidebarContainer, '');

        console.error('PJAX Error:', err);
        window.dispatchEvent(new CustomEvent('pjax:error', { detail: { url, error: err } }));

        // Fallback to full reload
        window.location.href = url;
    }
}

function reloadViewer() {
    if (viewerInstance) {
        viewerInstance.destroy();
        viewerInstance = null;
    }
    viewerInstance = initViewer({ selector: '#article-content', force: true });
}

function reloadPrism() {
    initPrism({ selector: '#article-content', force: true });
}

async function bootstrap() {
    const rootEl = document.getElementById('react-root');
    if (!rootEl || (window as any).__bootstrapped__) return;
    (window as any).__bootstrapped__ = true;

    // 直接扫描并挂载所有岛屿
    hydrateAllIslands(rootEl);

    // 设置链接拦截
    setupLinkInterception(navigate);

    // 移除全屏遮罩
    const mask = document.getElementById('screen-loading-mask');
    if (mask) {
        // 使用 requestAnimationFrame 确保在下一帧开始过渡
        requestAnimationFrame(() => {
            mask.style.opacity = '0';
            // 这里的 500ms 对应 CSS 中的 duration-500
            setTimeout(() => {
                mask.remove();
            }, 500);
        });
    }

    // 初始化插件
    window.addEventListener('app:reload', reloadViewer);
    reloadViewer();

    window.addEventListener('app:reload', reloadPrism);
    reloadPrism();

    initClipboard();

    console.log(
        "\n\n %c AIYA-CMS %c https://www.yeraph.com",
        "color:#f1ab0e;background:#222;padding:5px;",
        "background:#eee;padding:5px;"
    );
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
} else {
    bootstrap();
}
