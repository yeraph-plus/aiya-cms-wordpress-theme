# 系统架构与启动流程

AIYA-CMS 的整体设计分为两层：

- 插件层：提供“框架能力”和“独立优化能力”，可在主题之外单独启用/停用
- 主题层：聚合业务逻辑、模板与前端组件，对外呈现 CMS 站点

## 启动链路（高层）

1. 主题入口：`functions.php`
2. 加载主题内置插件组：`plugins/functions.php`
3. 插件组启动后，至少应提供：
   - `AYF`：主题选项框架（来自 `plugins/framework-required/`）
   - `AYP`：优化/行为修改插件框架（来自 `plugins/basic-optimize/`）
4. 主题继续加载 `inc/` 下的核心类与功能函数、模板方法、接口与设置定义

## 关键文件与职责

### `functions.php`

- 定义 `aya_require()`：用于加载 `inc/` 内的 PHP 文件（默认）或指定特殊路径
- 定义独立页面路由表 `$GLOBALS['aya_land_page']`
- 调用 `aya_require('functions', 'plugins', true)` 引入插件组加载器（`plugins/functions.php`）
- 在缺失 `AYF`/`AYP` 时执行防护逻辑，避免前台致命报错
- 依次加载：
  - `inc/core/*`：面向前端的数据结构封装
  - `inc/func-*.php`：功能函数与模板/接口
  - `inc/settings/*.php`：主题设置表单定义
  - `inc/widgets/*.php`：小工具
- 最后通过 `AYF::module(...)` 与 `AYP::action(...)` 启用若干模块能力（环境检查、注册菜单/边栏、缓存等）

### `plugins/functions.php`

- 在 `after_setup_theme` 时机注册一个“拓展功能”设置页（slug：`extra-plugin`）
- 提供：
  - `aya_add_plugin_opt(...)`：为“拓展功能”设置页追加字段
  - `aya_plugin_opt(...)`：读取 `extra-plugin` 下的设置
- 负责按需加载插件目录内各插件的 `setup.php`（例如 `framework-required`、`basic-optimize` 等）

## 边界约束（重要）

- `plugins/basic-optimize/` 以“独立插件”的方式修改 WP 行为：不要在主题 `inc/` 中反向依赖它的具体实现细节
- 主题读取设置统一走 `aya_opt()`（本质调用 `AYF` 的读取方法）：不要直接读 option key，避免绕过框架的缓存/约定
- `inc/core/` 的类用于“把 WP 原始对象/查询结果整理成前端可消费的数据结构”，业务展示逻辑优先放在 `templates/` 与 `src/`

