## AIYA-CMS Theme for WordPress

AIYA-CMS 主题，响应式多功能 WordPress 主题，使用 Vite + React19 构建。

主题完全兼容 WP 原生方法和各类插件，提供有独立控件构造查询，比传统模板查询量更低，二次开发简单。

内置WP优化插件，查询修改、后台安全、CDN加速、SEO配置一套搞定。

- 支持多种页面布局，支持轮播、列表模式切换、消息弹窗、独立分类展示、独立文章模板等， CMS 站点功能组件应有尽有。

- 支持白天与暗黑模式、全局自适应布局，体验流畅。

- 支持等多种经典编辑器快捷功能，提供短代码快捷输入、并提供文章自动排版修正、自动别名生成。

- 支持 OpenList 列表程序的功能嵌入，直接在文章页面中为提供由 OpenList 托管的文件。

- 支持 爱发电 赞助平台的API联动，在站点中获取你的赞助用户并提供额外功能。

### 安装主题

**提示：** 安装主题前请进行数据备份，防止主题不兼容或主题预定义字段重复导致站点数据丢失。

环境要求：WordPress 版本 `>=6.9` ，PHP 版本 `>=8.4` 

安装方式：

1. 前往 [Release页面](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/releases) 下载主题安装文件。

2. 登录到 WordPress 管理后台

3. 在 [外观]->[主题] 页面，点击右上角 [安装新主题] ,上传安装并启用主题即可。

PS：直接克隆或直接下载仓库的方式使用主题需要通过 npm 编译前端文件后才能使用，如果一定要用，那么请参考最后部分的构建说明。

### 建议/BUG发现/使用问题

提建议建议提 [issues](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/issues) ，或者在我的博客留言（或者任何你能找的到我的地方）。

BUG反馈时请附上报错内容、截图、或尽你所能的详细描述出现的问题，这有助于我更快的定位问题。

并不建议提PR，此主题的实现方式比较非主流，不了解代码细节的情况下容易导致灾难性重构。

### 主题文件修改指南

仓库中不包含 Composer 依赖和前端构建，如果不使用 Release 中的版本自己修改主题代码，需要按照以下步骤操作。

#### step 1：克隆仓库

直接在你本地的 WordPress 程序 `./wp-content/themes` 目录运行 `git clone` 克隆此主题仓库。 

此时可以在 WordPress 中启用主题，主题会由于缺少构建文件停止加载，并抛出报错。

#### step 2：安装后台选项框架和 Composer 依赖

 `cd ./aiya-cms-wordpress-theme` 进入主题目录，执行 `git submodule update` 拉取子仓库。或者直接从 [aiya-cms-theme-framework](https://github.com/yeraph-plus/aiya-cms-theme-framework) 下载仓库，重命名仓库文件夹为 `plugins` 合并文件到主题目录下。

此仓库是是主题的选项框架，以及一些为了便于迁移而封装的模板方法和功能插件。

 `cd ./plugins` 继续进入子仓库目录，运行 `composer install` 安装依赖，使组件正常运行。*Tips：根目录下的 `./composer.json` 是 WP-CLI 和代码检查工具，运行时不需要。

#### step 3：构建前端文件：

先安装node环境，需要nodejs版本 `>=24.0.0` 。

1. 进入到主题仓库目录，运行 `npm install` 安装依赖。

2. 运行命令 `npm run dev` 启动开发环境，此时主题会以HMR模式运行，由 Vite 监听文件修改。

3. 此时刷新站点页面，可以看到页面已经正常显示，修改完主题后， Ctrl+C 退出开发环境，后面可以开始编译静态资源打包。

4. 运行命令 `npm run build` 启动构建，编译完成后，即使没有node环境主题可正常工作。

5. 执行命令 `npm run archive` ，打包主题为 release.zip，上传这个安装包到你的 WordPress 站点即可。

#### step 4：在运行环境重新安装 Composer 依赖

使用 `archive` 命令打包主题时，不会同时打包 vendor 目录，所以需要在运行环境中重新安装 Composer 依赖。

安装主题后，进入到主题 `./plugins` 目录，再次运行 `composer install` 安装依赖。（这是符合规范的，你不应该在不同的 PHP 环境中运行相同的 Composer 产物。）

#### step 999：主要文件结构说明：

主题并非前后端分离设计，依然使用 PHP 进行页面输出，服务器不需要 node 环境。

页面模板文件均在 `/templates` 目录下，页面中的前端组件均在 `./src` 目录下，使用 Vite 进行打包。

主题的函数部分均在 `./inc` 下，统一在 `./functions.php` 中处理引用顺序，在你准备开始魔改此主题之前，以下仅简要说明本主题模板结构的核心方法如下：

- `aya_route_core()`：首页入口，主题使用了自定义的页面结构，与 WordPress 默认的模板路由方法并不兼容，为主题增加页面时请直接修改此函数，直接在目录中增加模板文件会导致错误。

- `aya_template_load($path)`: 使用相对路径从 `./templates` 目录下加载模板文件。

- `aya_react_island($slug, $attrs)`: 这个方法用于实现以群岛方式挂载 React 组件，$attrs直接接受数组为组件绑定数据（使用 Props 接收）。