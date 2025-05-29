import { App, ComponentInternalInstance } from 'vue';

/**
 * Vue 应用调试工具类
 */
export class VueDebugTools {
    private app: App;
    private originalSetAttribute: Function;
    private enabled: boolean = false;

    /**
     * @param app Vue App
     */
    constructor(app: App) {
        this.app = app;
        this.originalSetAttribute = Element.prototype.setAttribute;
    }

    enable(): void {
        if (this.enabled) return;
        this.enabled = true;

        this.setupAttributeInterceptor();
        this.setupVueGlobalHelpers();
        this.setupErrorHandler();
        this.setupPerformanceMonitoring();

        console.log('[DEBUG] Vue调试工具已启用');
    }

    disable(): void {
        if (!this.enabled) return;

        //恢复原始的setAttribute
        Element.prototype.setAttribute = this.originalSetAttribute;
        this.enabled = false;

        console.log('[DEBUG] Vue调试工具已禁用');
    }

    private setupAttributeInterceptor(): void {
        const self = this;

        Element.prototype.setAttribute = function (name: string, value: string) {
            // 检查属性名是否有效
            if (typeof name !== 'string' || /^\d/.test(name)) {
                // 创建一个错误对象以获取堆栈信息
                const error = new Error(`尝试设置无效的属性名: "${name}"，值为: "${value}"`);

                console.warn(`尝试设置无效的属性名: "${name}"，值为: "${value}"`);
                console.warn('DOM元素:', this);
                console.warn('调用堆栈:', error.stack);

                // 记录受影响的DOM元素的上下文
                console.warn('元素HTML:', this.outerHTML);
                console.warn('父元素:', this.parentElement);

                // 尝试记录父级Vue组件（如果可能）
                if (window.__VUE__) {
                    try {
                        const instance = window.__VUE__.findNearestComponentInstance(this);
                        if (instance) {
                            console.warn('关联的Vue组件:', instance.$options?.name || '未命名组件');
                        }
                    } catch (e) {
                        console.error('尝试获取Vue组件信息时出错:', e);
                    }
                }

                return;
            }
            return self.originalSetAttribute.call(this, name, value);
        };
    }

    private setupVueGlobalHelpers(): void {
        // 添加全局Vue引用以帮助调试
        if (typeof window !== 'undefined') {
            window.__VUE__ = {
                findNearestComponentInstance(el: Element): any {
                    let current = el;
                    while (current) {
                        const instance = (current as any).__vue_app__;
                        if (instance) return instance;
                        current = current.parentElement;
                    }
                    return null;
                }
            };
        }
    }

    private setupPerformanceMonitoring(): void {
        // 如果在开发环境中，启用性能监控
        if (process.env.NODE_ENV !== 'production') {
            this.app.config.performance = true;
        }
    }

    private setupErrorHandler(): void {
        // 捕获Vue错误并进行处理
        this.app.config.errorHandler = (err: Error, vm: ComponentInternalInstance, info: string) => {
            console.error('Vue Error:', err);
            console.error('Component:', (vm as any)?.$options?.name || 'Anonymous');
            console.error('Error Info:', info);

            // 特别针对属性名错误
            if (err.message && err.message.includes('attribute name')) {
                console.error('检测到属性名称错误，可能是某个组件尝试设置数字作为属性名');

                // 尝试检查组件的props和data
                if (vm) {
                    console.error('组件props:', (vm as any).$props);
                    console.error('组件data:', (vm as any).$data);
                    console.error('组件实例:', vm);
                    console.error('组件渲染上下文:', (vm as any).$);
                }
            }
        };
    }
}

/**
 * Vue应用调试插件
 * @param app Vue应用实例
 */
export default {
    install: (app: App) => {
        const debugTools = new VueDebugTools(app);

        // 将debug方法添加到app全局属性中
        app.config.globalProperties.$debug = debugTools;

        // 将调试工具添加到app实例上
        app.debug = () => {
            debugTools.enable();
            return app;
        };

        // 添加禁用调试的方法
        app.disableDebug = () => {
            debugTools.disable();
            return app;
        };

        // 将调试工具添加到window对象，方便控制台访问
        if (typeof window !== 'undefined') {
            window.__VUE_DEBUG_TOOLS__ = debugTools;
        }
    }
}

// 类型扩展声明
declare module '@vue/runtime-core' {
    export interface App {
        debug: () => App;
        disableDebug: () => App;
    }

    export interface ComponentCustomProperties {
        $debug: VueDebugTools;
    }
}

declare global {
    interface Window {
        __VUE__: any;
        __VUE_DEBUG_TOOLS__: VueDebugTools;
        __vue_app__: any;
    }
}