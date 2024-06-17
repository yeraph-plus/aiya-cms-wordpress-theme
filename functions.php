<?php

/**
 * AIYA-CMS functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @link https://developer.wordpress.org/themes/advanced-topics/child-themes/
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://developer.wordpress.org/plugins/}
 *
 */
if (!defined('ABSPATH')) die('Invalid request.');

/*
 * ------------------------------------------------------------------------------
 * 定义常量
 * ------------------------------------------------------------------------------
 */

define('AYA_PATH', get_template_directory());
define('AYA_URI', get_template_directory_uri());

//define('AYA_RELEASE', true);

/*
 * ------------------------------------------------------------------------------
 * 验证主题依赖组件
 * ------------------------------------------------------------------------------
 */

//从主题内加载
if (defined('AYA_RELEASE')) {
    //引入设置框架

    define('AYF_PATH', get_template_directory() . '/core');
    define('AYF_URL', get_template_directory_uri() . '/core');

    //引入设置框架
    require_once AYF_PATH . '/framework-required/setup.php';
    //组件模板
    //require_once AYF_PATH . '/framework-required/sample-config.php';

    //引入插件组
    require_once AYF_PATH . '/framework-unit/setup.php';
    //加载插件组
    AYP::include_plugins('plugin');

    require_once AYF_PATH . '/framework-unit/plugin-config-parent.php';
    require_once AYF_PATH . '/framework-unit/plugin-config.php';
}

//检查框架插件加载
if (!class_exists('AYF') || !class_exists('AYP')) {
    if (!is_admin()) {
        wp_die('AIYA-CMS 主题缺少必要依赖，请使用 Relase 版本，或安装并激活 AIYA-Optimize 插件。');
        exit;
    }
    return;
}

/*
 * ------------------------------------------------------------------------------
 * 载入主题方法和页面组件
 * ------------------------------------------------------------------------------
 */

//加载Composer
require_once AYA_PATH . '/composer/vendor/autoload.php';
//主题方法
require_once AYA_PATH . '/inc/function-public.php';
require_once AYA_PATH . '/inc/function-query.php';
require_once AYA_PATH . '/inc/function-enqueue.php';
require_once AYA_PATH . '/inc/function-fix.php';
require_once AYA_PATH . '/inc/function-single.php';
require_once AYA_PATH . '/inc/function-template.php';
//require_once AYA_PATH . '/inc/function-ajax.php';

//require_once AYA_INC . '/inc/function-format.php';

//加载组件
aya_require('module', 'menu-nav');
aya_require('module', 'menu-bread');
aya_require('module', 'menu-page');
aya_require('module', 'template-header');
aya_require('module', 'template-footer');
aya_require('module', 'template-single');
//aya_require('module', 'template-carousel');
//aya_require('module', 'template-archive');
//小工具
aya_require('widget', 'widget-add-menu');
aya_require('widget', 'widget-search');
aya_require('widget', 'widget-text-html');
aya_require('widget', 'widget-comments');
aya_require('widget', 'widget-welcome');
aya_require('widget', 'widget-tag-cloud');
aya_require('widget', 'widget-author');
aya_require('widget', 'widget-post-comments');
aya_require('widget', 'widget-post-custom');
aya_require('widget', 'widget-post-random');
aya_require('widget', 'widget-post-views');
aya_require('widget', 'widget-post-newest');
aya_require('widget', 'widget-tweet');
//短代码
aya_require('shotcode', 'code-email');
aya_require('shotcode', 'code-hide');
aya_require('shotcode', 'code-list');
aya_require('shotcode', 'code-aplayer');
aya_require('shotcode', 'code-dplayer');
aya_require('shotcode', 'code-meting');
aya_require('shotcode', 'code-download');
//主题设置项
aya_require('settings', 'theme-parent');
aya_require('settings', 'theme-color');
aya_require('settings', 'theme-layout');
//aya_require('settings', 'theme-home');

/*
 * ------------------------------------------------------------------------------
 * 载入主题
 * 
 * Tips：以下是一些简化方法，内部定义了部分设置、路由和HTML结构，有需要时请自行修改
 * ------------------------------------------------------------------------------
 */

AYP::include_plugins('inc');

//运行环境检查
AYP::action('EnvCheck', array(
    //PHP最低版本
    'php_last' => '8.1',
    //PHP扩展
    'php_ext' => array('session', 'curl'),
    //WP最低版本
    'wp_last' => '6.1',
    //经典编辑器插件
    'check_classic_editor' => false,
    //经典小工具插件
    'check_classic_widgets' => false,
));

//定义了一些全局变量
global $aya_post_type, $aya_tax_type;

//此钩子用于执行add_theme_support()
AYP::action_register('After_Setup_Theme', array(
    //将默认的帖子和评论RSS提要链接添加到<head>
    'automatic-feed-links' => '',
    //支持标签
    'title-tag' => '',
    //支持菜单
    'menus' => '',
    //支持文章类型
    'post-formats' => array(
        //'gallery',
        //'image',
        //'audio',
        //'video',
        //'status',
    ),
    //支持缩略图
    //'post-thumbnails' => array('post', 'page'),
    //搜索表单、注释表单和注释的默认核心标记
    'html5' => array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
        'navigation-widgets',
    ),
    //支持自定义图标
    'custom-logo' => array(
        'height' => 240,
        'width' => 240,
        'flex-height' => true,
    ),
    //支持自定义背景图
    'custom-background' => array(
        'default-repeat' => 'repeat',
        'default-position-x' => 'left',
        'default-position-y' => 'top',
        'default-size' => 'auto',
        'default-attachment' => 'fixed'
    )
));
//注册导航菜单
AYP::action_register('Register_Menu', array(
    //'菜单ID' => '菜单名',
    'main-menu' => __('全局菜单', 'AIYA'),
    'spare-menu' => __('备用菜单', 'AIYA'),
    'header-menu' => __('顶部导航', 'AIYA'),
    'footer-menu' => __('底部导航', 'AIYA'),
    'widget-menu' => __('小工具菜单', 'AIYA'),
));
//注册小工具栏位
AYP::action_register('Register_Sidebar', array(
    //'边栏ID' => '边栏名',
    'index-sitebar' => __('首页/归档页', 'AIYA'),
    'page-sitebar' => __('页面/文章页', 'AIYA'),
));
//注册自定义文章类型
AYP::action_register('Register_Post_Type', array(
    //'文章类型' => array('name' => '文章类型名','slug' => '别名','icon' => '图标'),
    'tweet' => array(
        'name' => __('推文', 'AIYA'),
        'slug' => 'tweet',
        'icon' => 'dashicons-format-quote',
    ),
));
//注册自定义分类法
/*
AYP::action_register('Register_Tax_Type', array(
    //'分类法' => array('name' => '分类法名','slug' => '别名','post_type' => array('此分类法适用的文章类型',)),
    'collect' => array(
        'name' => __('专题', 'AIYA'),
        'slug' => 'collect',
        'post_type' => array('post'),
    ),
));
*/
//重新定义模板位置
AYP::action_register('Template_Redefine', false);
//注册自定义模板页面
AYP::action_register('Template_New_Page', array(
    //'模板名' => '是否静态化',
    'go' => false,
    'link' => false,
));
//注册小工具 Tips：请确保此时要注册的小工具的文件已被require_once()
AYP::action_register('Widget_Load', array(
    //'小工具Class名',
    'AYA_Widget_Menu',
    'AYA_Widget_Serach',
    'AYA_Widget_Text_Html',
    'AYA_Widget_Tag_Cloud',
    //'AYA_Widget_Comments',
    'AYA_Widget_Post_Comments',
    'AYA_Widget_Post_Views',
    'AYA_Widget_Post_Newest',
    'AYA_Widget_Post_Random',
    'AYA_Widget_Post_Custom',
    //'AYA_Widget_Author_Box',
    //'AYA_Widget_User_Welcome',
    //'AYA_Widget_Tweet_Posts',
));
//解除 WP 自带的小工具
AYP::action_register('Widget_Unload', array(
    //'需要注销的小工具Class名',
    'WP_Widget_Archives',        //年份文章归档
    'WP_Widget_Calendar',        //日历
    'WP_Widget_Categories',      //分类列表
    'WP_Widget_Links',           //链接
    'WP_Widget_Media_Audio',     //音乐
    'WP_Widget_Media_Video',     //视频
    'WP_Widget_Media_Gallery',   //相册
    'WP_Widget_Custom_HTML',     //html
    'WP_Widget_Media_Image',     //图片
    'WP_Widget_Text',            //文本
    'WP_Widget_Meta',            //默认工具链接
    'WP_Widget_Pages',           //页面
    'WP_Widget_Recent_Comments', //评论
    'WP_Widget_Recent_Posts',    //文章列表
    'WP_Widget_RSS',             //RSS订阅
    'WP_Widget_Search',          //搜索
    'WP_Widget_Tag_Cloud',       //标签云
    'WP_Nav_Menu_Widget',        //菜单
    'WP_Widget_Block',           //区块
));
//后台自定义
AYP::action_register('Admin_Custom', array(
    //禁用前台顶部工具栏
    'remove_admin_bar' => true,
    //替换后台标题格式
    'admin_title_format' => true,
    //移除后台导航栏右上角WordPress标志
    'remove_admin_bar_wp_logo' => true,
    //隐藏后台仪表盘欢迎模块和WordPress新闻
    'remove_admin_dashboard_wp_news' => true,
    //替换后台页脚信息
    'admin_footer_replace' => '感谢使用 <b>AIYA-CMS</b> 主题，欢迎访问 <a href="https://www.yeraph.com" target="_blank">Yeraph Studio</a> 了解更多。',
    //自定义后台导航栏菜单
    'add_admin_bar_menu' => array(),
    'admin_add_system_dashboard_widgets' => true,
));
//启用小工具缓存插件
AYP::action_register('Widget_Cache', true);
//文章浏览量计数器插件
AYP::action_register('Record_Visitors', true);
//本地化头像插件
AYP::action_register('Local_Avatars', true);

//启动
AYP::action_all();
