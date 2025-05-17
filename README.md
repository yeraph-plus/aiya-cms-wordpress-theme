## AIYA-CMS Theme for WordPress

> **🎉** AIYA-CMS 船新V2.1绝赞每日build中，重一直构，打一直磨。

AIYA-CMS 主题，响应式多功能 WordPress 主题，使用 Vite + Vue + Tailwindcss 构建。

支持白天与暗黑模式、无刷新加载、支持多种页面布局。

内置WP优化插件，查询修改、后台安全、加速、SEO一套搞定。

### 安装主题

**提示：** 安装主题前请进行数据备份，防止主题不兼容或主题预定义字段重复导致站点数据丢失。

环境要求：

WordPress 版本 >= `6.0`

PHP 版本 >= `8.2`

安装方式：

请前往 [Release页面](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/releases) 下载主题包（`aiya-cms-theme-release.zip`）和插件包（`aiya-cms-plugin-optimize-release.zip`）。

1. 登录到 WordPress 管理后台。
2. 在[插件]->[安装插件]页面，点击右上角[上传插件],上传安装并启用插件。
3. 在[外观]->[主题]页面，点击右上角[安装新主题],上传安装并启用主题即可。

PS：不建议直接克隆或直接下载仓库的方式使用主题（虽然这样是可用的），最新的提交中会包含一些尚未完成的功能，可能会导致问题。

### 文档

> **💊** to be continue...

### 建议/BUG发现/使用问题

提建议建议提 [issues](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/issues) ,或者在我的博客留言，或者任何你能找的到我的地方。

BUG反馈时请附上报错内容、截图、或尽你所能的详细描述出现的问题，这有助于我更快的定位问题。

提PR也行，但是不建议PR，此主题的实现方式比较非主流，不了解代码细节的情况下容易导致灾难性重构。

### 模板文件修改指南

#### 文件结构说明：

主题的 composer 依赖打包位置在 `/lib/composer.json` ，请勿手动修改  `/lib/vendor` 目录下的文件，根目录下的 `composer.json` 是一些开发工具，如无需要不必下载。

主题的前端组件在 `/src` 目录下，由 Vite 自动编译。

主题的模板文件在 `/templates` 目录下，由PHP输出基本页面结构，不影响SEO。


#### 依赖框架：

主题使用了自己的选项框架，并为了重用封装了一些 WordPress 本身的模板方法，这部分代码存放在 [aiya-cms-theme-framework](https://github.com/yeraph-plus/aiya-cms-theme-framework) 仓库（此仓库即为 Releases 页面中的 `plugin-optimize` 插件包）。

#### 样式表修改（tailwind.css）：

需要node环境，nodejs版本 `^18+` 。

1. git 克隆整个仓库。
2. 进入到项目根目录，执行 `npm install` 安装依赖。
3. 执行命令 `npm run dev` 启动开发环境，监听文件夹修改。
4. 执行命令 `npm run build` 编译发布的css文件。
5. 执行命令 `npm run archive` 打包主题压缩包，上传到 WordPress 即可使用。
