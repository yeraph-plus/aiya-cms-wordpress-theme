# 主题 PHP 侧（`inc/`）与设置体系

## `inc/` 的分层与职责

### `inc/core/`

用于把 WordPress 的原生对象/查询结果整理成更适合前端消费的数据结构，避免模板层重复组装数据。

包含的核心封装（示例）：

- `WP_Query.php`：查询封装与数据提取
- `WP_Post.php`：文章对象结构化
- `WP_Term.php`：分类/标签等术语对象结构化
- `WP_Menu.php`：菜单结构提取
- `WP_Paged.php`：分页数据结构
- `WP_Breadcrumb.php`：面包屑数据结构

### `inc/settings/`

主题设置表单的“定义层”，统一通过 `AYF::new_opt([...])` 创建设置页与字段。

当前已加载的设置文件由 `functions.php` 明确 require：

- `opt-basic.php`：基础设置（站点信息、默认资源等）
- `opt-land.php`：页面组件/正文过滤等
- `opt-notify.php`：通知相关
- `opt-access.php`：订阅接入/第三方接口相关
- `opt-oplist.php`：OpenList 客户端模块

### `inc/widgets/`

主题小工具实现。小工具的注册与卸载通过框架模块进行集中管理（见 `functions.php` 中的 `AYF::module('Widget_Load', ...)` 等）。

### `inc/func-*.php`

主题功能函数与集成点，按领域拆分：

- `func-public.php`：通用方法（版本、debug 判断、设置读取等）
- `func-wp-emends.php`：WP 行为修正/初始化钩子
- `func-wp-scripts.php`：向前端传递配置（`wp_localize_script` 等）
- `func-vite-helpers.php`：Vite 资源加载、React 群岛节点输出等
- `func-template.php`：模板路由、模板加载、页面类型判断/边栏选择等
- `func-api-router.php`：接口路由封装
- `func-user.php` / `func-payment.php` / `func-openlist.php` / `func-notify.php`：业务功能模块
- `func-shotcodes.php`：短代码集合

## 设置读取：`aya_opt()`

主题侧统一通过 `aya_opt()` 读取设置项：

- 定义：`inc/func-public.php`
- 行为：内部调用 `AYF::get_opt()` 或 `AYF::get_checked()`（布尔开关建议使用第三参 `$opt_bool = true`）

约定：

- 设置项的 `$opt_slug` 对应 `AYF::new_opt([... 'slug' => 'xxx' ...])` 的 slug
- 字段 `id` 建议带类型后缀（示例：`_bool` / `_type` / `_text` / `_upload` / `_list`），减少前台调用时的类型歧义

## 增加新功能时的放置规则（最常用）

- 新增“主题功能模块”：优先在 `inc/` 下创建 `func-xxx.php`，并在 `functions.php` 中按领域顺序 `aya_require('func-xxx')`
- 新增“核心数据结构/查询封装”：放在 `inc/core/` 并在 `functions.php` 中 `aya_require('ClassName', 'core')`
- 新增“主题设置页/字段”：在 `inc/settings/` 新建 `opt-xxx.php`，在 `functions.php` 中 `aya_require('opt-xxx', 'settings')`
- 新增“小工具”：在 `inc/widgets/` 创建 `widget-xxx.php`，并把类名加入 `AYF::module('Widget_Load', [...])`

## 模板路由（CMS 页面类型）

主题提供一个“独立页面路由表”：

- 定义位置：`functions.php` 的 `$GLOBALS['aya_land_page']`
- 执行位置：`inc/func-template.php` 的 `aya_route_core()`

该机制用于：

- 通过 URL path 映射到 `templates/pages/*.php`
- 可选是否加载 `header/footer`（`orginal/original` 标记）
- 支持路由回调（例如为面包屑追加节点）

