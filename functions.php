<?php

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

/**
 * Twenty Sixteen functions and definitions
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

define('AYA_PATH', get_template_directory());
define('AYA_URI', get_template_directory_uri());
//define('AYA_RELEASE', true);
//define('AYA_CACHE_SECOND', HOUR_IN_SECONDS); // or MINUTE_IN_SECONDS

/*
 * ------------------------------------------------------------------------------
 * 载入初始化方法和页面组件
 * ------------------------------------------------------------------------------
 */

//转为输出
function aya_echo($data, $special = false)
{
    //htmlspecialchars
    if ($special) {
        $data = strip_tags($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    echo ($data);

    return;
}

//打印输出
function aya_print($data, $return = false)
{
    print_r('<pre>');

    print_r($data, $return);

    print_r('</pre>');

    return;
}

//JSON输出
function aya_json_print($data)
{
    //保持换行
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        $error = [
            'code' => json_last_error(),
            'message' => json_last_error_msg()
        ];

        return json_encode($error);
    }

    print_r('<pre>' . $json . '</pre>');
    
    return;
}

//获取主题版本
function aya_theme_version()
{
    return wp_get_theme()->get('Version');
}

//判断开发模式
function aya_is_dev_mode()
{
    return !(defined('AYA_RELEASE') && AYA_RELEASE === true);
}

//获取设置项
function aya_opt($opt_name, $opt_slug, $opt_bool = false)
{
    return ($opt_bool) ? AYF::get_checked($opt_name, $opt_slug) : AYF::get_opt($opt_name, $opt_slug);
}

//require方法
function aya_require($name = '', $path = '', $special = false)
{
    if (!$special) {
        $path = ($path == '') ? 'inc' : 'inc/' . $path;
    }

    $req_file = AYA_PATH . '/' . $path . '/' . $name . '.php';

    if (is_file($req_file)) {
        require_once $req_file;
    }
}

//加载预置插件
//aya_require('functions', 'plugins', 1);

//检查主题框架并拦截WP加载，防止严重报错
if (!class_exists('AYF') || !class_exists('AYP')) {

    if (!is_admin()) {
        $error_title = __('AIYA-CMS 启动错误', 'AIYA');
        $error_msg = __('AIYA-CMS 未找到主题框架依赖，请安装并激活 AIYA-Optimize 插件，或订阅 Pro 版本。', 'AIYA');

        wp_die($error_msg, $error_title, array('response' => 500));

        exit;
    }

    return;
}

//主题方法
aya_require('func-auto-loader');
aya_require('func-public');
aya_require('func-vite-helpers');
aya_require('func-wp-emends');
//aya_require('func-wp-scripts');
aya_require('func-api-router');
aya_require('func-user');
//aya_require('func-cache');
aya_require('func-notify');
aya_require('func-template');
//接口方法
//aya_require('func-api-router');
//设置页面
aya_require('opt-basic', 'settings');
aya_require('opt-notify', 'settings');
//aya_require('opt-homepage', 'settings');
//aya_require('opt-postpage', 'settings');
//aya_require('opt-ads', 'settings');
aya_require('opt-extra-plugin', 'plugins', 1);
//小工具
aya_require('widget-text-html', 'widgets');
aya_require('widget-add-menu', 'widgets');
aya_require('widget-comments', 'widgets');
aya_require('widget-post-comments', 'widgets');
aya_require('widget-post-views', 'widgets');
aya_require('widget-post-custom', 'widgets');
aya_require('widget-post-newest', 'widgets');
aya_require('widget-post-random', 'widgets');
aya_require('widget-author', 'widgets');
aya_require('widget-search', 'widgets');
aya_require('widget-tag-cloud', 'widgets');
aya_require('widget-welcome-panel', 'widgets');
//短代码工具
aya_require('code-basic', 'shotcodes');
//aya_require('code-hilight', 'shotcodes');
//aya_require('code-clipboard', 'shotcodes');
//aya_require('code-collapse', 'shotcodes');
//aya_require('code-aplayer', 'hotcodes');
//aya_require('code-dplayer', 'shotcodes');

/*
 * ------------------------------------------------------------------------------
 * 载入主题
 * 
 * Tips：以下是一些简化方法，内部定义了部分设置、路由和HTML结构，有需要时请自行修改
 * ------------------------------------------------------------------------------
 */

//运行环境检查
AYP::action('EnvCheck', array(
    //PHP最低版本
    'php_last' => '8.2',
    //PHP扩展
    'php_ext' => array('session', 'curl', 'mbstring', 'exif', 'gd', 'fileinfo'),
    //WP最低版本
    'wp_last' => '6.5',
));
//此钩子用于执行add_theme_support()
AYP::action('After_Setup_Theme', array(
    //将默认的帖子和评论RSS提要链接添加到<head>
    'automatic-feed-links' => '',
    //支持标签
    'title-tag' => '',
    //支持菜单
    'menus' => '',
    //支持文章类型
    'post-formats' => array(
        'gallery',
        'image',
        'audio',
        'video',
        //'status',
    ),
    //支持缩略图
    'post-thumbnails' => array('post', 'page'),
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
//后台自定义
AYP::action('Admin_Custom', array(
    //禁用前台顶部工具栏
    'remove_admin_bar' => true,
    //替换后台标题格式
    'admin_title_format' => true,
    //移除后台导航栏右上角WordPress标志
    'remove_admin_bar_wp_logo' => true,
    //隐藏后台仪表盘欢迎模块和WordPress新闻
    'remove_admin_dashboard_wp_news' => true,
    //替换后台页脚信息
    'admin_footer_replace' => __('感谢使用 <b>AIYA-CMS</b> 主题，欢迎访问 <a href="https://www.yeraph.com" target="_blank">Yeraph Studio</a> 了解更多。', 'AIYA'),
));
//注册小工具 Tips：请确保此时要注册的小工具的文件已被require
AYP::action('Widget_Load', array(
    //'小工具Class名',
    'AYA_Widget_Text_Html',
    'AYA_Widget_Menu',
    'AYA_Widget_Search',
    'AYA_Widget_Tag_Cloud',
    'AYA_Widget_Post_Comments',
    'AYA_Widget_Post_Views',
    'AYA_Widget_Post_Newest',
    'AYA_Widget_Post_Random',
    'AYA_Widget_Author_Box',
    'AYA_Widget_Comments',
    //'AYA_Widget_User_Welcome',
));
//解除 WP 自带的小工具
AYP::action('Widget_Unload', array(
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
//注册导航菜单
AYP::action('Register_Menu', array(
    //'菜单ID' => '菜单名',
    'primary-menu' => __('主要菜单', 'AIYA'),
    'secondary-menu' => __('次要菜单', 'AIYA'),
    'widget-menu' => __('小工具菜单', 'AIYA'),
));
//注册小工具栏位
AYP::action('Register_Sidebar', array(
    //'边栏ID' => '边栏名',
    'archive-sitebar' => __('首页/归档页面', 'AIYA'),
    'single-sitebar' => __('正文页面', 'AIYA'),
    'main-sitebar' => __('工具板', 'AIYA'),
));
//注册自定义模板页面
AYP::action('Template_New_Page', array(
    //'模板名' => '模板文件路径',
    'go' => 'pages/external-auto',
    'link' => 'pages/external-link',
));
//启用小工具缓存插件
AYP::action('Widget_Cache', true);
//文章浏览量计数器插件
AYP::action('Record_Visitors', true);
//文章点赞量计数器插件
AYP::action('Record_ClickLikes', true);


/*
 * ------------------------------------------------------------------------------
 * 首次启动
 * ------------------------------------------------------------------------------
 */

add_action('after_switch_theme', 'aya_theme_after_init');

function aya_theme_after_init()
{
    //刷新站点重写规则
    flush_rewrite_rules();

    //跳转主题设置页面
    /*
    global $pagenow;

    if ('themes.php' == $pagenow && isset($_GET['activated'])) {
        // options-general.php 改成你的主题设置页面网址
        wp_redirect(admin_url('?page=aya-options-basic'));
        exit;
    }
    */
}

/*
 * ------------------------------------------------------------------------------
 * 翻译模板
 * ------------------------------------------------------------------------------
 */

wp_localize_script('aiyacms-frontend', 'AIYA_I18N', [
    'logout' => __('退出登录', 'AIYA'),
    'confirm' => __('确定要删除吗？', 'AIYA'),
]);
/*
import { createI18n } from 'vue-i18n';
const i18n = createI18n({
  locale: AIYA_I18N.locale,
  messages: { [AIYA_I18N.locale]: AIYA_I18N.messages }
});
*/