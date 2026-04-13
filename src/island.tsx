import React, { lazy, Suspense, type ComponentType, type ReactNode } from 'react';
import { hydrateRoot, createRoot, type Root } from 'react-dom/client';
import { Skeleton } from '@/components/ui/skeleton';
import Providers from './contexts/providers';

// 类型定义
export interface IslandInfo {
    element: HTMLElement;
    componentIsland: string;
    instanceId: string;
    props: any;
}

// 全局 Root 注册表
const activeRoots = new Map<HTMLElement, Root>();

let autoIslandSeq = 0;

function ensureIslandInstanceId(el: HTMLElement, componentIsland: string) {
    const existing = el.getAttribute('data-id');
    if (existing) {
        return existing;
    }

    autoIslandSeq += 1;
    const generated = `${componentIsland}-${autoIslandSeq}`;
    el.setAttribute('data-id', generated);
    return generated;
}

type IslandModule = Record<string, unknown> & { default?: unknown };

// 错误边界组件
export class IslandErrorBoundary extends React.Component<{ children: ReactNode; id: string }, { hasError: boolean }> {
    constructor(props: any) {
        super(props);
        this.state = { hasError: false };
    }
    static getDerivedStateFromError() {
        return { hasError: true };
    }
    componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
        console.error(`Island ${this.props.id} crashed:`, error, errorInfo);
    }
    render() {
        if (this.state.hasError) {
            return <div data-island-error={`组件 ${this.props.id} 渲染失败`} />;
        }
        return this.props.children;
    }
}

// 默认骨架屏
function DefaultSkeleton() {
    return (
        <div className="space-y-2 p-4">
            <Skeleton className="h-4 w-[250px]" />
            <Skeleton className="h-4 w-[200px]" />
        </div>
    );
}

// 构建组件名到动态导入函数的映射
const islandLoaders = import.meta.glob<IslandModule>([
    './components/**/*.{tsx,ts,jsx,js}',
    '!./components/ui/**/*',
    '!./components/**/*.skeleton.{tsx,ts,jsx,js}',
]);
const loaders = Object.fromEntries(
    Object.entries(islandLoaders).map(([path, loader]) => {
        const id = path.split('/').pop()?.replace(/\.[^.]+$/, '') ?? '';
        return [id, loader];
    })
    .filter(([id]) => id !== null) as [string, () => Promise<IslandModule>][]
);

// 构建骨架屏映射 (eager loading)
const skeletonModules = import.meta.glob<{ default: ComponentType<any> }>('./components/**/*.skeleton.{tsx,ts,jsx,js}', { eager: true });
const skeletons = Object.fromEntries(
    Object.entries(skeletonModules).map(([path, mod]) => {
        const filename = path.split('/').pop() ?? '';
        // 移除 .skeleton.ext 后缀获取组件ID
        const id = filename.replace(/\.skeleton\.[^.]+$/, '');
        return [id, mod.default];
    })
);

// 加载组件（带简单重试）
export function loadIslandComponent(name: string, retries = 2): ComponentType<any> | null {
    const loader = loaders[name];
    if (!loader) return null;

    const attempt = (attemptsLeft: number): Promise<{ default: ComponentType<any> }> => {
        return loader()
            .then((mod) => {
                const Component = mod.default ?? mod[name];
                if (!Component) {
                    console.warn(`Island component "${name}" not found in module`, mod);
                }
                return { default: Component as ComponentType<any> };
            })
            .catch((err) => {
                if (attemptsLeft > 0) {
                    return attempt(attemptsLeft - 1);
                }
                throw err;
            });
    };

    return lazy(() => attempt(retries));
}

// 岛屿组件
export const IslandComponent = React.memo(({ name, props = {} }: { name: string; props?: any }) => {
    const Component = loadIslandComponent(name);
    const SkeletonComponent = skeletons[name] || DefaultSkeleton;

    if (!Component) {
        return <div data-island-error={`组件 ${name} 未找到`} />;
    }

    return (
        <IslandErrorBoundary id={name}>
            <Suspense fallback={<SkeletonComponent />}>
                <Component {...props} />
            </Suspense>
        </IslandErrorBoundary>
    );
});

// 扫描岛屿（不再清空 innerHTML，由 mountIsland 决定）
export function scanIslands(container: ParentNode = document): IslandInfo[] {
    const elements = container.querySelectorAll<HTMLElement>('[data-island]');
    return Array.from(elements).map((el) => {
        let props = {};
        const rawProps = el.getAttribute('data-props');

        const componentIsland = el.getAttribute('data-island') || 'unknown';
        const instanceId = ensureIslandInstanceId(el, componentIsland);

        if (rawProps) {
            try {
                props = JSON.parse(rawProps) || {};
            } catch (e) {
                console.warn(`Failed to parse props for island ${componentIsland}:`, rawProps, e);
            }
        }

        // 不再在这里清空 innerHTML，由 mountIsland 根据 data-hydrate 决定

        return {
            element: el,
            componentIsland,
            instanceId,
            props,
        };
    });
}

// 辅助：比较两个岛屿列表是否相等（可选，用于性能优化）
export function islandsEqual(a: IslandInfo[], b: IslandInfo[]): boolean {
    if (a.length !== b.length) return false;
    return a.every((item, index) => {
        const other = b[index];
        return (
            item.element === other.element &&
            item.componentIsland === other.componentIsland &&
            item.instanceId === other.instanceId &&
            JSON.stringify(item.props) === JSON.stringify(other.props)
        );
    });
}

// ============================================
// Per-Island Root 架构
// ============================================

/**
 * 挂载岛屿到独立 React Root
 * 根据 data-hydrate 属性决定使用 hydrateRoot 或 createRoot
 */
export function mountIsland(el: HTMLElement, component: React.ReactElement): Root {
    // 如果已有 root，先卸载
    const existingRoot = activeRoots.get(el);
    if (existingRoot) {
        existingRoot.unmount();
    }

    const shouldHydrate = el.hasAttribute('data-hydrate');
    let root: Root;

    if (shouldHydrate) {
        root = hydrateRoot(el, component, {
            onRecoverableError: (error) => {
                console.warn(`[Island Hydration] mismatch:`, error);
            },
        });
        el.removeAttribute('data-hydrate');
    } else {
        // 非水合模式：清空现有内容
        el.innerHTML = '';
        root = createRoot(el);
        root.render(component);
    }

    activeRoots.set(el, root);
    return root;
}

/**
 * 卸载指定岛屿
 */
export function unmountIsland(el: HTMLElement): void {
    const root = activeRoots.get(el);
    if (root) {
        root.unmount();
        activeRoots.delete(el);
    }
}

/**
 * 卸载容器内所有岛屿
 */
export function unmountIslandsIn(container: Element | null | undefined): void {
    if (!container) return;
    container.querySelectorAll<HTMLElement>('[data-island]').forEach(unmountIsland);
}

/**
 * 扫描并挂载容器内所有岛屿
 */
export function hydrateAllIslands(container: ParentNode): void {
    const islands = scanIslands(container);
    islands.forEach(({ element, componentIsland, props }) => {
        const Component = loadIslandComponent(componentIsland);
        if (!Component) return;

        const SkeletonComponent = skeletons[componentIsland] || DefaultSkeleton;

        mountIsland(
            element,
            <Providers>
                <IslandErrorBoundary id={componentIsland}>
                    <Suspense fallback={<SkeletonComponent />}>
                        <Component {...props} />
                    </Suspense>
                </IslandErrorBoundary>
            </Providers>
        );
    });
}
