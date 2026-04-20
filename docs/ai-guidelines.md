# 开发与贡献约定（给 AI/协作者）

这份约定的目标是：扩展功能时尽量“可预测、可回滚、边界清晰”，避免把逻辑散落到不该放的位置。

## 目录边界

- `plugins/basic-optimize/`
  - 只做：独立 WP 优化、后台增强、安全/性能/行为修改
  - 不做：主题业务（页面结构、前端组件 props 形状、主题特有的数据建模）
- `plugins/framework-required/`
  - 只做：选项框架与“通用主题构建模块”封装（注册菜单、边栏、hooks、REST/AJAX 辅助等）
  - 不做：具体业务逻辑与页面表现
- `inc/`
  - 做：主题业务、模板辅助、接口与数据结构封装、设置定义、小工具
- `templates/`
  - 做：页面结构与群岛挂载点
- `src/`
  - 做：交互组件与 UI 体系（shadcn/ui + 业务组件）

## 最常见改动的落点（强约定）

- 新增/调整主题设置字段：`inc/settings/opt-*.php`，读取统一用 `aya_opt()`
- 新增后端数据结构/查询封装：`inc/core/*.php`
- 新增主题业务函数：`inc/func-*.php`（按领域拆分）
- 新增模板布局：`templates/*.php` 或 `templates/contents/*.php` 或 `templates/pages/*.php`
- 新增群岛组件：`src/components/<slug>.tsx`，模板中用 `aya_react_island('<slug>', $props)`
- 新增群岛骨架屏：`src/components/<slug>.skeleton.tsx`

## 群岛组件命名与 props 约束

- slug 只能使用：`[a-zA-Z0-9_-]`
- slug 必须与组件文件名一致：`aya_react_island('nav-menu')` ⇔ `src/components/nav-menu.tsx`
- props 必须是可 JSON 序列化的“纯数据对象”，不要传递资源句柄/闭包/对象实例
- 能在 PHP 侧稳定生成 HTML 的组件才允许开启 hydrate（`aya_react_island(..., $server_html, true)`）

## 设置系统约定

- `inc/settings/` 中的字段 `id` 建议带类型后缀：
  - `_bool`：开关
  - `_type`：单选枚举
  - `_text`：文本
  - `_upload`：上传
  - `_list`：列表/组
- 读取时：
  - 布尔开关：`aya_opt('xxx_bool', 'basic', true)`
  - 其他：`aya_opt('xxx_text', 'basic')`

## 提交前自检清单

- PHP：
  - 不直接访问未定义的数组 key（先 `isset()`/默认值）
  - 输出到 HTML 前使用 WP 的 escape（`esc_html`/`esc_attr`/`esc_url`）
- 前端：
  - 群岛 slug 与文件名一致
  - 可选提供 skeleton，避免首屏闪烁
- 架构：
  - 不跨越目录边界引入依赖（例如主题业务不要写到 `basic-optimize`）

