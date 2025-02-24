## AIYA-CMS 2.0

![截图](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/blob/main/screenshot.png)

---

响应式多功能 WordPress 主题，使用 Alpinejs + Tailwindcss 构建。

此主题依赖[aiya-cms-theme-framework](https://github.com/yeraph-plus/aiya-cms-theme-framework)项目，请作为插件安装并启用主题框架。

###如何安装

1. 在[Release页面](https://github.com/yeraph-plus/aiya-cms-wordpress-theme/releases)下载已发布的主题压缩包（如果有）。
2. 登录 WordPress 后台，进入插件页面，上传启用框架插件。
3. 进入外观->主题页面，上传启用主题。

###修改主题

1. 下载整个仓库。
2. 在node环境下安装依赖：`npm install`。
3. 使用命令：`npm run dev`启动开发环境，监听文件夹修改。
4. 使用命令：`npm run build`构建发布css文件。
5. 使用命令：`npm run archive`打包主题压缩包，上传到WordPress即可使用。

主题的静态资源文件均在`/assets`目录下，主题的模板文件均在`/templates`目录下。

主题的composer依赖打包在`/lib/vendor`目录下，请勿手动修改。
