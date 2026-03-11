import { createRoot } from 'react-dom/client';
import { flushSync } from 'react-dom';
import { createPortal } from 'react-dom';
import React, { useState, useRef, useCallback } from 'react';
import Providers from './contexts/providers';
import { type IslandInfo, IslandComponent, scanIslands, islandsEqual } from './island';
import { usePJAXFetch, useLinkInterception } from './lib/pjax';
import { initViewer } from './lib/viewer-plugin';
import { initPrism } from './lib/prism-plugin';
import { initClipboard } from './lib/clipboard-plugin';
import './tailwind.css';

let viewerInstance: ReturnType<typeof initViewer> = null;

function IslandManager() {
  const [islands, setIslands] = useState<IslandInfo[]>(() => {
    const rootEl = document.getElementById('react-root');
    return rootEl ? scanIslands(rootEl) : [];
  });

  const { fetchPage } = usePJAXFetch();

  // 滚动位置存储
  const scrollPositions = useRef<Record<string, number>>({});

  // 清理不再需要的旧条目
  React.useEffect(() => {
    const cleanup = () => {
      const keys = Object.keys(scrollPositions.current);
      if (keys.length > 20) {
        // 保留最近 10 条
        const sorted = keys.sort((a, b) => scrollPositions.current[b] - scrollPositions.current[a]);
        sorted.slice(10).forEach(k => delete scrollPositions.current[k]);
      }
    };
    const id = setInterval(cleanup, 30000);
    return () => clearInterval(id);
  }, []);

  const navigate = useCallback(async (url: string) => {
    // 1. Dispatch pjax:send
    const sendEvent = new CustomEvent('pjax:send', { detail: { url }, cancelable: true });
    if (!window.dispatchEvent(sendEvent)) return;

    // 保存当前滚动位置
    scrollPositions.current[location.href] = window.scrollY;

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
      // 使用 flushSync 确保 React 立即卸载旧岛屿（避免批处理延迟）
      flushSync(() => {
        setIslands((prev) => prev.filter((i) => {
          const inMain = mainContainer && i.element.closest('[data-pjax-container="main"]');
          const inSidebar = sidebarContainer && i.element.closest('[data-pjax-container="sidebar"]');
          return !inMain && !inSidebar;
        }));
      });

      // 5. 替换容器内容
      if (mainContainer && containers.main) {
        mainContainer.innerHTML = containers.main;
      }
      if (sidebarContainer && containers.sidebar) {
        sidebarContainer.innerHTML = containers.sidebar;
      }

      // 6. 扫描新容器内的岛屿
      const newIslands = [
        ...(mainContainer ? scanIslands(mainContainer) : []),
        ...(sidebarContainer ? scanIslands(sidebarContainer) : [])
      ];

      // 7. 重新挂载新岛屿
      setIslands((prev) => {
        const merged = [...prev, ...newIslands];
        return islandsEqual(prev, merged) ? prev : merged;
      });

      // 8. 恢复滚动位置（新页面）
      const savedY = scrollPositions.current[url] || 0;
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

      // 9. 触发自定义事件
      window.dispatchEvent(new CustomEvent('pjax:complete', { detail: { url } }));
      window.dispatchEvent(new Event('app:reload'));

    } catch (err) {
      // 发生错误时恢复样式
      setOpacity(mainContainer, '');
      setOpacity(sidebarContainer, '');
      
      console.error('PJAX Error:', err);
      window.dispatchEvent(new CustomEvent('pjax:error', { detail: { url, error: err } }));
      
      // Fallback to full reload
      window.location.href = url;
    }
  }, [fetchPage]);

  // 拦截链接点击
  useLinkInterception(navigate);

  // 渲染所有岛屿
  return (
    <>
      {islands.map(({ element, componentIsland, instanceId, props }) => {
        return createPortal(
          <IslandComponent key={instanceId} name={componentIsland} props={props} />,
          element
        );
      })}
    </>
  );
}

async function bootstrap() {
  const rootEl = document.getElementById('react-root');
  if (!rootEl || (window as any).__bootstrapped__) return;
  (window as any).__bootstrapped__ = true;

  // 创建隐藏容器作为 React 根
  let reactRootEl = document.getElementById('react-root-portal');
  if (!reactRootEl) {
    reactRootEl = document.createElement('div');
    reactRootEl.id = 'react-root-portal';
    document.body.appendChild(reactRootEl);
  }

  createRoot(reactRootEl).render(
    <Providers>
      <IslandManager />
    </Providers>
  );

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

  const reloadViewer = () => {
    if (viewerInstance) {
      viewerInstance.destroy();
      viewerInstance = null;
    }
    viewerInstance = initViewer({ selector: '#article-content', force: true });
  };

  window.addEventListener('app:reload', reloadViewer);
  reloadViewer();

  const reloadPrism = () => {
    initPrism({ selector: '#article-content', force: true });
  };

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
