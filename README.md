## AIYA-CMS Theme for WordPress

> **🎉** AIYA-CMS 船新V2.1绝赞每日build中，重一直构，打一直磨。

AIYA-CMS 主题，响应式多功能 WordPress 主题，使用 Vite + Vue + Tailwindcss 构建。

支持白天与暗黑模式、无刷新加载、支持多种页面布局。

内置WP优化插件，查询修改、后台安全、加速、SEO一套搞定。

### 安装主题

**提示：** 安装主题前请进行数据备份，防止主题不兼容或主题预定义字段重复导致站点数据丢失。

环境要求：WordPress 版本 `>=6.0` ，PHP 版本 `>=8.2` 

安装方式：

请前往 [Release页面](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/releases) 下载主题包（`aiya-cms-theme-release.zip`）和插件包（`aiya-cms-plugin-optimize-release.zip`）。

1. 登录到 WordPress 管理后台。
2. 在 [插件]->[安装插件] 页面，点击右上角 [上传插件] ,上传安装并启用插件。
3. 在 [外观]->[主题] 页面，点击右上角 [安装新主题] ,上传安装并启用主题即可。

PS：直接克隆或直接下载仓库的方式使用主题需要通过 npm 编译前端文件后才能使用，如果一定要用，那么请参考 ↓ 下文构建说明。

安装方式（Pro）：

也可以选择 [购买 Pro 版本](https://afdian.com/item/17528c7015b211eeb5515254001e7c00) ，提供更多定制功能以及在线更新。


### 建议/BUG发现/使用问题

提建议建议提 [issues](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/issues) ，或者在我的博客留言（或者任何你能找的到我的地方）。

BUG反馈时请附上报错内容、截图、或尽你所能的详细描述出现的问题，这有助于我更快的定位问题。

并不建议提PR，此主题的实现方式比较非主流，不了解代码细节的情况下容易导致灾难性重构。

### 主题文件修改指南

#### 后台选项框架：

主题使用了自己的选项框架，并为了重用封装了一些 WordPress 本身的模板方法，这部分代码存放在 [aiya-cms-theme-framework](https://github.com/yeraph-plus/aiya-cms-theme-framework) 仓库，下载这个仓库，作为 WordPress 插件直接安装。

#### 主要文件结构说明：

主题运行时引入的 composer 依赖打包位置在 `./inc/composer.json` ，根目录下的 `./composer.json` 是另外的一些开发工具，引入的 vendor 不会被打包到主题。

主题并非前后端分离设计，依然使用 PHP 进行页面输出，以确保不影响SEO。页面模板文件均在 `/templates` 目录下，页面中的前端组件均在 `./src` 目录下，使用 Vite 进行打包。

主题的函数部分均在 `./inc` 下，统一在 `./functions.php` 中处理引用顺序，在你准备开始魔改此主题之前，以下仅简要说明本主题模板结构的核心方法如下：

- `aya_core_route_entry()`：首页入口，主题使用了自定义的页面结构，与 WordPress 默认的模板路由方法并不兼容，为主题增加页面时请直接修改此函数，直接在目录中增加模板文件会导致错误。

- `aya_template_load($path)`: 使用相对路径从 `./templates` 目录下加载模板文件。

- `aya_vue_load($slug, $attrs)`: 挂载 Vue 组件的方法，$attrs直接接受数组为组件绑定数据（使用 Props 接收）。

#### 前端构建说明：

首先安装node环境，需要nodejs版本 `>=18.0.0` ，需要npm版本 `>=8.0.0`。

直接在你本地的 WordPress 程序 `./wp-content/themes` 目录运行 git 克隆此主题仓库，然后在 WordPress 中启用主题。 

此时主题会由于缺少构建文件报错，以下为构建步骤：

1. 进入到主题仓库目录，运行 `npm install` 安装依赖。

2. 运行命令 `npm run dev` 启动开发环境，此时主题会以HMR模式运行，由 Vite 监听文件修改。

此时刷新站点页面，可以看到页面已经正常显示，修改完主题后， Ctrl+C 退出开发环境，开始编译生产文件：

3. 运行命令 `npm run build` 启动构建，编译完成后，即使没有node环境主题可正常工作。

4. 执行命令 `npm run archive` ，打包主题，上传到你的 WordPress 站点即可。