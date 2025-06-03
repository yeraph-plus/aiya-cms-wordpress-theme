<?php
if (!defined('ABSPATH'))
    exit;

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
            'desc' => '站点设置',
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
            'title' => '默认使用暗色主题',
            'desc' => '设置站点初始化加载的配色为暗色主题，关闭此项时使用白色主题',
            'id' => 'site_default_dark_mode_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '默认折叠左侧边栏',
            'desc' => '设置站点默认状态下是否打开左侧边栏，关闭此项时左侧边栏默认展开',
            'id' => 'site_default_sitebar_close_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '默认文章缩略图',
            'desc' => '上传文章默认缩略图',
            'id' => 'site_default_thumb_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/default-thumb.png',
        ],
        [
            'title' => '页面 404 占位图',
            'desc' => '无内容或 404 错误页面时，提示区块占位图',
            'id' => 'site_404_page_img_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/default-404-error.png',
        ],
        [
            'title' => '内容为空占位图',
            'desc' => '分类、搜索、作者等文章列表为空时，提示区块占位图',
            'id' => 'site_none_content_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/default-404-error.png',
        ],
        [
            'title' => '外链跳转页面占位图',
            'desc' => '上传外部链接跳转页加载时，提示区块占位图',
            'id' => 'site_ext_page_img_upload',
            'type' => 'upload',
            'button_text' => '上传',
            'default' => AYA_URI . '/assets/image/default-paper-plane.png',
        ],
        [
            'desc' => '页面布局设置（Layout）',
            'type' => 'title_1',
        ],
        [
            'title' => '文章列表循环结构',
            'desc' => '设置文章列表输出的布局模板，文章列表的输出数量请在站点 [url=options-reading.php]阅读设置[/url] 中调整',
            'id' => 'site_loop_layout_type',
            'type' => 'radio',
            'sub' => [
                'list' => '列表',
                'grid' => '网格',
                'waterfall' => '瀑布流（ Masonry 模式）',
            ],
            'default' => 'grid',
        ],
        [
            'title' => '文章列表卡片宽度',
            'desc' => '设置列表宽度（仅影响卡片和瀑布流模式布局）',
            'id' => 'site_loop_column_type',
            'type' => 'radio',
            'sub' => [
                '2' => '2列',
                '3' => '3列',
                '4' => '4列',
                '5' => '5列',
            ],
            'default' => '3',
        ],
        [
            'title' => '文章列表分页设置',
            'desc' => '设置文章列表的分页选择器',
            'id' => 'site_loop_paged_type',
            'type' => 'radio',
            'sub' => [
                'full' => '完整分页',
                'simple' => '简单分页',
            ],
            'default' => 'full',
        ],
        [
            'title' => '文章时效性提示',
            'desc' => '在文章发布或更新时间超过天数后显示内容过期提示，留空时禁用',
            'id' => 'site_single_outdate_text',
            'type' => 'text',
            'default' => '0',
        ],
        [
            'title' => '文末声明',
            'desc' => '在文章末尾输出版权声明等额外文本',
            'id' => 'site_single_statement_text',
            'type' => 'textarea',
            'default' => '本站部分内容转载自网络，作品版权归原作者及来源网站所有，任何内容转载、商业用途等均须联系原作者并注明来源。',
        ],
        [
            'title' => '相关文章',
            'desc' => '在文章页面显示相关文章导航卡片（使用分类和标签进行相关度检索）',
            'id' => 'site_single_related_posts_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'desc' => '合规类功能设置',
            'type' => 'title_1',
        ],
        [
            'title' => '用户授权Cookie权限弹窗',
            'desc' => '用户首次访问网站时，会弹窗提示用户授权Cookie权限',
            'id' => 'site_cookie_consent_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => 'ICP备案',
            'desc' => '根据《互联网信息服务管理办法》要求的网站 ICP 备案信息',
            'id' => 'site_icp_beian_text',
            'type' => 'text',
            'default' => '', // 没ICP备11451419号-19（雾）
        ],
        [
            'title' => '公安备案',
            'desc' => '根据《计算机信息网络国际联网安全保护管理办法》要求的网站公网安备信息',
            'id' => 'site_mps_beian_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => '公安备案记录代码',
            'desc' => '由于地域备案号展示要求不同，手动填写您的公安备案号纯数字部分',
            'id' => 'site_mps_code_text',
            'type' => 'text',
            'default' => '',
        ],
        /*
        [
            'title' => '首页启用小工具栏',
            'desc' => '设置站点是否显示页面右 1/4 小工具栏',
            'id' => 'site_home_widget_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'desc' => '样式兼容设置',
            'type' => 'title_1',
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
        */
    ]
]);
