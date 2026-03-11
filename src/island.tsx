import React, { lazy, Suspense, type ComponentType, type ReactNode } from 'react';
import { Skeleton } from '@/components/ui/skeleton';

// 类型定义
export interface IslandInfo {
    element: HTMLElement;
    componentIsland: string;
    instanceId: string;
    props: any;
}

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
class IslandErrorBoundary extends React.Component<{ children: ReactNode; id: string }, { hasError: boolean }> {
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
function loadIslandComponent(name: string, retries = 2): ComponentType<any> | null {
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

// 扫描岛屿
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

        // 清空原始 HTML 内容，避免重复渲染
        el.innerHTML = '';

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
