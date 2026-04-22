# AIYA-CMS 主题 Repo 文档

这份文档用于描述 AIYA-CMS（WordPress CMS 类型主题）的系统结构与开发约定，目标是让开发者与自动化工具能够在不破坏既有架构边界的前提下扩展功能。

## 目录结构速览

- `plugins/`
  - `basic-optimize/`：独立的 WP 优化插件与行为修改模块，使用独立的配置页面，不与主题设置耦合
  - `framework-required/`：主题选项框架与主题构建工具集，主题通过 `aya_opt()` 读取设置项
- `inc/`：主题 PHP 侧逻辑（功能函数、接口、设置、widget、核心查询封装）
  - `inc/settings/`：主题设置表单定义（通过框架创建）
  - `inc/core/`：主题使用的查询/数据结构封装（面向前端消费）
  - `inc/lib/`：主题引用的 SDK 和外部库
- `src/`：前端构建（Vite + React），组件体系基于 shadcn/ui
- `templates/`：主题模板（PHP），HTML 结构主要在此处；通过 React 群岛模式挂载前端组件

## 快速导航

- 系统架构与启动流程：见 [architecture.md](./architecture.md)
- 插件体系（basic-optimize / framework-required）：见 [plugins.md](./plugins.md)
- 主题 PHP 侧（inc/core、inc/settings、aya_opt）：见 [theme.md](./theme.md)
- 前端与模板对接（React 群岛、props 传递、命名约定）：见 [frontend-and-templates.md](./frontend-and-templates.md)
- 翻译与文本域约定（aiya-cms / aiya-framework）：见 [i18n.md](./i18n.md)
- 开发与贡献约定（AI/协作者必读）：见 [ai-guidelines.md](./ai-guidelines.md)

