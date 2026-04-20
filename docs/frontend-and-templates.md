# 前端与模板（MPA + 群岛 + 运行时解析器）

AIYA-CMS 当前采用 `PHP 模板输出主体 + MPA 页面入口 + React 群岛挂载 + 运行时解析器增强` 的结构。

页面的大部分 HTML 由 `templates/` 直接输出，前端只负责两类事情：

- 挂载真正需要 React 的交互组件
- 对 PHP 输出的占位标签做运行时增强，例如图标、徽章、列表布局切换

## 前端目录（`src/`）

- `src/app/startup.ts`：公共启动入口，负责页面模式标记、群岛挂载、运行时解析器启动、应用 ready 事件
- `src/entrypoints/*.ts`：Vite 的 MPA 页面入口，按页面类型注册附加增强逻辑
- `src/runtime/islands.tsx`：群岛扫描与挂载逻辑，按需动态导入业务组件并挂载到 `data-island` 容器
- `src/runtime/icon-slots.tsx`：扫描 `.icon-slot[data-icon]`，把 PHP 占位标签替换为 SVG 图标
- `src/runtime/badge-slots.ts`：扫描 `.badge-slot`，根据 `data-badge-variant` 或 `data-badge-alias` 注入徽章样式和内置图标
- `src/runtime/post-grid-layout.tsx`：增强 `loop-grid.php` 输出的文章列表，负责 `grid / list` 视图切换
- `src/runtime/providers.tsx`：群岛公共 Providers 容器
- `src/components/`：仍然由 React 管理的业务组件
- `src/components/ui/`：shadcn/ui 基础组件，仅供 React 组件内部使用，不参与群岛 slug 映射
- `src/components/**/*.skeleton.tsx`：群岛组件的可选骨架屏
- `src/lib/*.ts`：纯工具函数和非 DOM 运行时工具，例如 `utils`、`viewer-plugin`、`prism-plugin`

## 页面入口与 MPA 启动

页面入口通过 `runPageEntry()` 注册，例如：

- `src/entrypoints/common.ts`：只注册公共启动
- `src/entrypoints/home.ts`：启动公共逻辑后，额外执行 `bootPostGridLayout(document)`
- `src/entrypoints/archive.ts`：启动公共逻辑后，额外执行 `bootPostGridLayout(document)`
- `src/entrypoints/single.ts`：启动公共逻辑后，增强 `#article-content` 的 `Viewer + Prism`
- `src/entrypoints/user.ts`：只注册用户页入口，不再做文章内容增强

`src/app/startup.ts` 的启动链路如下：

- `runPageEntry(entry, onReady?)` 先写入 `document.documentElement.dataset.cmsPageEntry`
- 统一通过 `ensureApplicationBootstrapped()` 确保公共启动只执行一次
- 公共启动会写入 `document.documentElement.dataset.cmsAppMode = 'mpa'`
- 然后依次执行：
  - `hydrateAllIslands(document)`
  - `bootBadgeSlots(document)`
  - `bootIconSlots(document)`
- 最后抛出 `app:ready` 事件，供后续附加逻辑监听

这意味着当前前端是标准的 MPA 模式，不存在前端路由层，也不使用 SPA 页面切换。

## 模板目录（`templates/`）

- `templates/*.php`：页面模板，例如 `index`、`single`、`archive`、`search`
- `templates/contents/*.php`：正文主体区域拆分
- `templates/fragments/*.php`：可复用片段模板，例如 `breadcrumb`、`loop-grid`、`loop-section`、`content-detail`
- `templates/pages/*.php`：独立路由页面（由 `aya_land_page` 映射）

模板当前的主要职责：

- 输出页面主体结构与静态 HTML
- 从 `inc/core/` 或 `inc/func-*.php` 获取数据
- 使用 `aya_template_part_load()` 复用子模板
- 仅在确实需要 React 交互时调用 `aya_react_island()`
- 使用 `icon-slot` / `badge-slot` 这类占位语法，把展示增强交给运行时解析器

## Vite 资源注入

资源注入由 `inc/func-vite-helpers.php` 的 `aya_dist_scripts_loader()` 完成，并挂载在 `wp_head`。

Debug 模式：

- 注入 `@vite/client`
- 注入当前激活入口模块
- 支持 HMR 和 React refresh

生产模式：

- 读取 `build/.vite/manifest.json`
- 只输出当前页面需要的入口脚本
- 仅对静态依赖链中的 `vendor` 做 `modulepreload`
- 继续输出相关 CSS
- 不再把 `dynamicImports` 全量展开为硬 preload

这套策略的目标是减少首屏硬加载的资产数量，保留按需加载能力。

## 子模板输出：`aya_template_part_load()`

定义位置：`inc/func-template.php`

当前工作方式：

- 根据名称解析 `templates/fragments/<name>.php`
- 通过 `extract($data)` 把数组字段解构为子模板变量
- 直接 `include` 子模板并输出缓冲内容

约定：

- 子模板优先使用下划线命名的数据键
- 上游已经规范好的数据，不要在子模板内再次做一层驼峰转换

## 群岛输出：`aya_react_island()`

定义位置：`inc/func-vite-helpers.php`

当前调用签名：

- `aya_react_island(string $slug, array $props = [], string $server_html = ''): void`

输出行为：

- 生成 `<div data-id="..." data-island="slug" data-props="...">...</div>`
- 如果传入 `$server_html`，会先把这段 HTML 输出到容器内部，作为首屏占位内容

当前说明：

- 这段占位 HTML 不参与 hydration
- `src/runtime/islands.tsx` 在挂载前会清空容器内容，然后用 `createRoot()` 重新挂载组件
- 因此 `$server_html` 的意义是“首屏占位”而不是“服务端同构”

## 群岛组件命名约定

`src/runtime/islands.tsx` 会通过 `import.meta.glob()` 自动建立 slug 到组件文件的映射：

- 扫描：`src/components/**/*.{tsx,ts,jsx,js}`
- 排除：`src/components/ui/**/*`
- 排除：所有 `*.skeleton.*`
- 取文件 basename 去掉扩展名作为 slug

例如：

- `aya_react_island('nav-menu')` 对应 `src/components/nav-menu.tsx`
- `aya_react_island('content-button-group')` 对应 `src/components/content-button-group.tsx`
- 可选骨架屏可放在 `src/components/nav-menu.skeleton.tsx`

## 运行时解析器

除了 React 群岛，当前前端还维护一批“DOM 解析器”，它们直接增强 PHP 输出：

### `icon-slot`

由 `src/runtime/icon-slots.tsx` 负责。

模板写法：

```html
<span class="icon-slot" data-icon="navigation"></span>
```

功能：

- 扫描 `.icon-slot[data-icon]`
- 根据内置图标表渲染 SVG
- 支持 `data-icon-class`
- 支持 `MutationObserver` 处理后续新增节点

### `badge-slot`

由 `src/runtime/badge-slots.ts` 负责。

模板写法：

```html
<span class="badge-slot" data-badge-variant="secondary">标签</span>
```

或：

```html
<span class="badge-slot" data-badge-alias="sticky">置顶</span>
```

功能：

- 根据 `data-badge-variant` 应用预设徽章样式
- 或根据 `data-badge-alias` 走内置别名表
- 别名模式下会自动注入对应图标
- 同样支持 `MutationObserver`

当前内置别名主要用于文章状态，例如：

- `sticky`
- `newest`
- `password`
- `private`
- `pending`
- `future`
- `draft`
- `auto-draft`
- `trash`

### `post-grid-layout`

由 `src/runtime/post-grid-layout.tsx` 负责。

它不渲染列表本身，而是增强 `templates/fragments/loop-grid.php` 输出的 HTML：

- 读取 `data-post-grid-root`
- 绑定 `data-post-grid-controls`
- 根据 `useUiPreferencesStore()` 中的布局偏好切换 `grid / list`
- 通过 DOM class 操作切换卡片展示样式

这类模块是“运行时增强器”，而不是通用 `lib` 工具。

## 当前推荐实践

- SEO 主体、文章列表、面包屑、详情头部等内容优先用 PHP 模板输出
- 纯交互块继续用 `aya_react_island()`，例如按钮组、弹窗、评论、用户面板
- 纯展示增强优先使用运行时解析器，例如 `icon-slot`、`badge-slot`
- 不再描述或依赖 hydrate 流程；当前群岛统一按“清空容器后重新挂载”的方式工作

## 模板侧示例

典型用法：

- 导航菜单：`aya_react_island('nav-menu', ['menu' => $menu_items]);`
- 按钮组：`aya_react_island('content-button-group', $props);`
- 面包屑：`aya_template_part_load('breadcrumb', ['items' => $breadcrumb_items]);`
- 文章列表：`aya_template_part_load('loop-grid', [...]);`
- 图标占位：`<span class="icon-slot" data-icon="calendar"></span>`
- 徽章占位：`<span class="badge-slot" data-badge-alias="sticky">置顶</span>`
