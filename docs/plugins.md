# 插件体系

本仓库的 `plugins/` 目录包含“随主题分发”的插件集合。如 `internal-pic-bed/` 等，它们以插件方式组织，但由主题的加载器统一引入，其中包含私有功能，设计时考虑了与主题的解耦。如果在主题中调用了目录下的任何方法，都应该先进行 `class_exists()` 或 `function_exists()` 检查，确保方法存在，并配置降级处理。

## `plugins/basic-optimize/`（独立 WP 优化插件）

定位：一套独立的 WordPress 优化插件与修改 WP 行为的模块集合，使用独立的配置页面，不和主题设置耦合。

### 启动方式

- 入口：`plugins/basic-optimize/setup.php`
- 通过 `AYP`（继承自 `AYA_Plugin_Setup`）启动，并自动 include 插件模块与配置：
  - `plugin-filter-hub.php`
  - `plugin-config-parent.php`
  - `plugin-config.php`

### 开发约定

- 任何“与主题无关”的 WP 行为修改、后台增强、安全策略、性能优化等，优先落在该插件中
- 主题侧（`inc/`、`templates/`、`src/`）不要直接依赖 `basic-optimize` 内的实现细节
- 如需对外提供可被主题读取的能力，优先以明确的 action/filter 或 REST/AJAX 接口形式暴露

## `plugins/framework-required/`（主题选项框架与构建方法封装）

定位：主题依赖的选项框架与“简化主题构建”的模块封装；主题通过 `aya_opt()` 读取设置项；设置表单数据全部定义在 `inc/settings/`。

### 框架核心

- 入口：`plugins/framework-required/setup.php`
- `AYF` 继承自 `AYA_Framework_Setup`，负责：
  - 引入框架核心文件与字段类型（`inc/fields/*`）
  - 自动加载 `plugin/` 目录下的模块文件
  - 提供 `new_opt()`、`get_opt()`、`get_checked()` 等选项构造/读取能力

### module 机制（主题构建的关键抽象）

- 调用方式：`AYF::module('ModuleName', $args)`
- 类名约定：模块实现类名必须为 `AYA_Plugin_{ModuleName}`
- 参数约定：
  - `$args` 为 `false`：不启用该模块
  - `$args` 为 `true`：无参实例化该模块
  - `$args` 为数组：以数组作为构造参数实例化该模块

`plugins/framework-required/plugin/` 中既包含可被 `AYF::module()` 启用的模块，也包含“父类/构造器”一类的封装工具（用于简化 REST/AJAX/Widget/Shortcode 的开发）。

#### 常用封装（父类/构造器）

- `handle-ajax-hook.php`：提供 `AYA_WP_AJAX` 抽象父类，用于以“声明式”方式创建 `admin-ajax.php` 的 AJAX 调用
  - 子类需要实现 `ajax_action()`，返回数组：`name`、`callback_func`、`public`、`create_nonce`
  - 回调内可用 `parent::check_ajax_referer()` 做 nonce 校验；可用 `parent::get_ajax_req_body()` 读取 JSON body
- `handle-rest-api.php`：提供 `AYA_WP_REST_API`，用于简化自定义 REST API 端点构建
  - 通过 `$api = new AYA_WP_REST_API($namespace)` 创建实例
  - 通过 `$api->register_route($endpoint, $params)` 注册端点（内部挂载到 `rest_api_init`）
  - 提供统一响应封装：`$api->response($data, $status)` 与 `$api->error_response($key, $additional)`（返回 `WP_Error`）
- `handle-widget-bulider.php`：提供 `AYA_Widget` 抽象父类，用于简化小工具（Widget）构建
  - 子类需要实现 `widget_args()`（定义 id/title/classname/desc/field_build）与 `widget_func()`（输出前台内容）
  - `field_build` 支持 `input/textarea/checkbox/select`，并内置“移动端隐藏”开关（`mobile_hide`）
- `module-shortcode-manager.php`：经典编辑器（非区块编辑器）中的短代码插入 UI 组件
  - 通过 `AYA_Shortcode::instance()` 初始化
  - 通过 `AYA_Shortcode::shortcode_register($name, $args)` 注册短代码模板与字段，后台会在媒体按钮区域添加“简码编辑器”入口并弹窗生成短代码
  - 相关脚本：`assets/js/framework-shortcode-editor.js`

### 与主题的关系

- 主题在 `inc/settings/*.php` 中调用 `AYF::new_opt([...])` 定义设置页面
- 主题在运行时通过 `aya_opt($fieldId, $optSlug, $isBool)` 读取值（见 `inc/func-public.php`）

## `plugins/` 子仓库提交流程备忘（Submodule）

当前项目将 `plugins/` 作为子模块，指向仓库 `https://github.com/yeraph-plus/aiya-cms-theme-framework.git`，并跟踪 `main` 分支。日常开发时，`plugins/` 内的提交与主仓库提交是两套独立流程。

### 1) 在子仓库提交并推送

```bash
cd plugins
git checkout main
git pull --ff-only origin main

# 修改代码后
git add .
git commit -m "feat: your change"
git push origin main
```

### 2) 在主仓库记录子模块指针（推荐）

```bash
cd ..
git add plugins
git commit -m "chore: bump plugins submodule pointer"
git push
```

说明：
- 如果只做第 1 步而不做第 2 步，其他人仅拉主仓库时不会自动得到你子仓库的新提交。
- 已在 `plugins/.gitignore` 中忽略的本地测试文件不会进入子仓库提交。

### 3) 拉取子仓库最新提交（本地或 CI）

```bash
git submodule sync --recursive
git submodule update --init --recursive --remote
git -C plugins pull --ff-only origin main
```

说明：
- 上述命令用于将 `plugins/` 更新到远程最新 `main`。
- Submodule 模式下，主仓库始终会记录一个子模块提交指针（gitlink），这是 Git 机制本身。
