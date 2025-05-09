<?php
if (!defined('ABSPATH')) exit;

/**
 * NOTE:
 * 
 * 多选项标记为_type
 * 单选项标记为_bool
 * 文本框标记为_text
 * 上传文件标记为_upload
 * 自增列表标记为_list
 * 
 * <del>省得前台调用时候忘判断类型</del>
 */

//创建主题设置
AYF::new_opt([
    'title' => 'AIYA-CMS',
    'page_title' => '基本设置',
    'slug' => 'basic',
    'desc' => 'AIYA-CMS 主题，首选项设置',
    'fields' => [
        [
            'desc' => '静态资源设置（CDN）',
            'type' => 'title_1',
        ],
        [
            'title' => '静态文件加载位置',
            'desc' => '设置全站静态文件加载方式，使用CDN或从本地加载',
            'id' => 'site_scripts_load_type',
            'type' => 'radio',
            'sub'  => [
                'local' => '本地加载',
                'cdnjs' => 'CloudFlare',
                'zstatic' => 'ZstaticCDN',
                'bootcdn' => 'BootCDN',
            ],
            'default' => 'cdnjs',
        ],
        [
            'desc' => '页面布局设置（Layout）',
            'type' => 'title_1',
        ],
        [
            'title' => '导航栏布局',
            'desc' => '设置站点导航栏样式',
            'id' => 'site_nav_position_type',
            'type' => 'radio',
            'sub'  => [
                'horizontal' => '顶部导航',
                'vertical' => '侧边栏',
                'collapsible-vertical' => '侧边栏（抽屉）',
            ],
            'default' => 'vertical',
        ],
        [
            'title' => '导航栏浮动',
            'desc' => '设置站点导航栏是否跟随滚动',
            'id' => 'site_nav_sticky_type',
            'type' => 'radio',
            'sub'  => [
                'navbar-sticky' => '固定',
                'navbar-floating' => '浮动',
                'navbar-static' => '关闭',

            ],
            'default' => 'navbar-static',
        ],
        [
            'title' => '首页启用小工具栏',
            'desc' => '设置站点是否显示页面右 1/4 小工具栏',
            'id' => 'site_home_widget_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '文章列表循环结构',
            'desc' => '设置文章列表输出的布局模板，文章列表的输出数量请在站点 [url=options-reading.php]阅读设置[/url] 中调整',
            'id' => 'site_loop_mode_type',
            'type' => 'radio',
            'sub'  => [
                'list' => 'CMS列表模式',
                'grid' => 'CMS卡片模式',
                //'waterfall' => '瀑布流（Masonry模式）',
                //'blog' => '正文列表（博客模式）',
            ],
            'default' => 'grid',
        ],
        [
            'title' => '文章列表卡片宽度',
            'desc' => '设置列表宽度（仅影响卡片和瀑布流模式布局）',
            'id' => 'site_loop_width_type',
            'type' => 'radio',
            'sub'  => [
                'col-2' => '2列',
                'col-3' => '3列',
                'col-4' => '4列',
                'col-5' => '5列',
            ],
            'default' => 'col-3',
        ],
        [
            'title' => '文章列表分页设置',
            'desc' => '设置文章列表的分页选择器',
            'id' => 'site_loop_paged_type',
            'type' => 'radio',
            'sub'  => [
                'page' => '分页按钮',
                'next' => '上下页按钮',
                'more' => '加载更多',
            ],
            'default' => 'page',
        ],
        [
            'desc' => '样式兼容设置',
            'type' => 'title_1',
        ],
        [
            'title' => '外观切换器',
            'desc' => '在页面右侧显示外观切换功能抽屉',
            'id' => 'site_theme_customizer_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '页面动画',
            'desc' => '设置站点主要内容区域切换时的动画',
            'id' => 'site_router_trans_type',
            'type' => 'radio',
            'sub'  => [
                'none' => '无动画',
                'fadeIn' => '淡入淡出',
                'fadeInDown' => '下淡入',
                'fadeInUp' => '上淡入',
                'fadeInLeft' => '左淡入',
                'fadeInRight' => '右淡入',
                'slideInDown' => '下滑',
                'slideInLeft' => '左滑',
                'slideInRight' => '右滑',
                'zoomIn' => '缩放',
            ],
            'default' => 'none',
        ],
        [
            'title' => '暗色主题',
            'desc' => '设置站点首次加载时的配色模式',
            'id' => 'site_dark_scheme_type',
            'type' => 'radio',
            'sub'  => [
                'light' => '浅色',
                'dark' => '暗色',
                'system' => '跟随系统',
            ],
            'default' => 'system',
        ],
        [
            'title' => '盒模式布局',
            'desc' => '设置站点强制使用CSS盒子进行布局，忽略页面宽度自适应',
            'id' => 'site_boxed_layout_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => 'RTL 模式',
            'desc' => '设置站点文字书写和显示方向为书写方向右到左（兼容亚非语系）',
            'id' => 'site_rtl_direction_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'desc' => '图片文件设置',
            'type' => 'title_1',
        ],
        [
            'title' => ' LOGO 图片',
            'desc' => ' LOGO 使用的图片地址Url',
            'id' => 'site_logo_image_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/logo.png',
        ],
        [
            'title' => '显示站点名称',
            'desc' => ' LOGO 位置显示站点名称，如果使用图标作为 LOGO 时，可以切换LOGO部分为文字显示',
            'id' => 'site_logo_text_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '默认文章缩略图',
            'desc' => '上传文章默认缩略图',
            'id' => 'site_default_thumb_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/default-thumb.png',
        ],
        [
            'title' => '内容为空占位图',
            'desc' => '上传分类、搜索、作者等文章列表为空时提示占位图',
            'id' => 'site_none_content_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/default-404-error.png',
        ],
        [
            'title' => '外跳页面占位图',
            'desc' => '上传外部链接跳转页加载时提示占位图',
            'id' => 'site_ext_page_img_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/default-paper-plane.png',
        ],
        [
            'title' => ' 404 页面占位图',
            'desc' => '上传 404 错误页面时提示占位图',
            'id' => 'site_404_page_img_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/default-404-error.png',
        ],
        [
            'title' => ' 501 页面占位图',
            'desc' => '上传 501 错误页面时提示占位图',
            'id' => 'site_501_page_img_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default'  => AYA_URI . '/assets/image/default-404-error.png',
        ],
        [
            'desc' => '合规类功能设置',
            'type' => 'title_1',
        ],
        [
            'title' => '用户授权Cookie权限弹窗',
            'desc' => '开启后，用户首次访问网站时，会弹窗提示用户授权Cookie权限',
            'id' => 'site_cookie_consent_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '启用评论功能',
            'desc' => '开启后，网站将禁用评论组件和相关功能',
            'id' => 'site_comment_disable_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'desc' => '站点信息设置',
            'type' => 'title_1',
        ],
        [
            'title' => 'ICP备案',
            'desc' => '根据《互联网信息服务管理办法》要求，设置网站 ICP 备案信息',
            'id' => 'site_icp_beian_text',
            'type' => 'text',
            'default' => '', // 没ICP备11451419号-19（雾）
        ],
        [
            'title' => '公安网备',
            'desc' => '根据《计算机信息网络国际联网安全保护管理办法》要求，设置网站公网安备信息',
            'id' => 'site_mps_beian_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => '站长邮箱',
            'desc' => '在页脚显示站点管理员联系邮箱',
            'id' => 'site_master_email_text',
            'type' => 'text',
            'default' => '',
        ],
    ]
]);
