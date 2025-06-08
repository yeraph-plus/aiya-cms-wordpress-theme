import { App, ComponentInternalInstance } from 'vue';

/**
 * Vue 应用调试工具类
 */
export class VueDebugTools {
    private app: App;
    private originalSetAttribute: (qualifiedName: string, value: string) => void;
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

        console.log('[DEBUG] Vue debugging tool enabled.');
    }

    disable(): void {
        if (!this.enabled) return;

        //恢复原始的 setAttribute
        Element.prototype.setAttribute = this.originalSetAttribute;
        this.enabled = false;

        console.log('[DEBUG] Vue debugging tool disabled.');
    }

    private setupAttributeInterceptor(): void {
        const self = this;

        Element.prototype.setAttribute = function (name: string, value: string) {
            //默认检查属性名是否有效
            if (typeof name !== 'string' || /^\d/.test(name)) {

                const error = new Error('Attempt to set an invalid attribute name: "${name}", value: "${value}"');

                console.warn('Attempt to set an invalid attribute name: "${name}", value: "${value}"');
                console.warn('DOM Element:', this);
                console.warn('Call Stack:', error.stack);

                //受影响的DOM元素的上下文
                console.warn('Element HTML:', this.outerHTML);
                console.warn('Parent element:', this.parentElement);

                //父级Vue组件
                if (window.__VUE__) {
                    try {
                        const instance = window.__VUE__.findNearestComponentInstance(this);
                        if (instance) {
                            console.warn('Associated Vue Components:', instance.$options?.name || 'Unnamed component');
                        }
                    } catch (e) {
                        console.error('Error attempting to Vue component:', e);
                    }
                }

                return;
            }
            return self.originalSetAttribute.call(this, name, value);
        };
    }

    private setupVueGlobalHelpers(): void {
        //全局Vue引用
        if (typeof window !== 'undefined') {
            window.__VUE__ = {
                findNearestComponentInstance(el: Element): any {
                    let current = el;
                    while (current) {
                        const instance = (current as any).__vue_app__;
                        if (instance) return instance;
                        if (current.parentElement) {
                            current = current.parentElement;
                        } else {
                            break;
                        }
                    }
                    return null;
                }
            };
        }
    }

    private setupPerformanceMonitoring(): void {
        //启用性能监控
        if (typeof process !== 'undefined' && process.env && process.env.NODE_ENV !== 'production') {
            this.app.config.performance = true;
        }
    }

    private setupErrorHandler(): void {
        //捕获Vue错误并进行处理
        this.app.config.errorHandler = (err: unknown, instance: import('vue').ComponentPublicInstance | null, info: string) => {
            console.error('Vue Error:', err);
            console.error('Component:', (instance as any)?.$options?.name || 'Anonymous');
            console.error('Error Info:', info);

            //属性名错误
            if (typeof err === 'object' && err !== null && 'message' in err && typeof (err as any).message === 'string' && (err as any).message.includes('attribute name')) {
                console.error('Detected an incorrect attribute name, possibly due to a component attempting to set a number as the attribute name.');

                //检查组件的props和data
                if (instance) {
                    console.error('Props:', (instance as any).$props);
                    console.error('Data:', (instance as any).$data);
                    console.error('Instance:', instance);
                    console.error('Context:', (instance as any).$);
                }
            }
        };
    }
}

/**
 * Vue应用调试插件
 * 
 * @param app Vue应用实例
 */
export default {
    install: (app: App) => {
        const debugTools = new VueDebugTools(app);

        //将debug方法添加到app全局属性中
        app.config.globalProperties.$debug = debugTools;

        //将调试工具添加到app实例上
        app.debug = () => {
            debugTools.enable();
            return app;
        };

        //添加禁用调试的方法
        app.disableDebug = () => {
            debugTools.disable();
            return app;
        };

        //将调试工具添加到window对象，方便控制台访问
        if (typeof window !== 'undefined') {
            window.__VUE_DEBUG_TOOLS__ = debugTools;
        }
    }
}

//类型扩展声明
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