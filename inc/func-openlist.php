<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ------------------------------------------------------------------------------
 *  OpenList 接口方法
 * ------------------------------------------------------------------------------
 */

//服务器地址
function aya_oplist_server_url()
{
    return trim(aya_opt('site_oplist_server_url', 'oplist'), '/');
}

//Ping测试
function aya_oplist_ping_server()
{
    $server = aya_oplist_server_url();

    $oplist_cli = new OpenList_API($server, false);

    return $oplist_cli->ping();
}

//获取API登录Token
function aya_oplist_request_token()
{
    $server = aya_oplist_server_url();

    $oplist_api = new OpenList_API($server, false);

    $user = aya_opt('site_oplist_server_user', 'oplist');
    $pswd = aya_opt('site_oplist_server_pswd', 'oplist');

    return $oplist_api->get_token($user, $pswd);
}

//Token缓存逻辑
function aya_oplist_transient_token()
{
    //缓存名
    $get_token = get_transient('oplist_client_jwt_token');

    if ($get_token) {
        return $get_token;
    }

    //请求新的令牌
    $get_token = aya_oplist_request_token();
    $expire_hours = aya_opt('site_oplist_server_token_hours', 'oplist');
    $expire_hours = intval($expire_hours);

    //网络失败或接口未返回有效令牌时，直接终止
    if (!is_string($get_token) || $get_token === '') {
        return false;
    }

    //不缓存时直接返回
    if ($expire_hours == 0) {
        return $get_token;
    }

    //检查令牌报错
    if (strpos($get_token, 'ERROR:') === false) {
        //设置缓存
        set_transient('oplist_client_jwt_token', $get_token, $expire_hours * 3600);

        return $get_token;
    }

    return false;
}

//Token缓存刷新
function aya_oplist_transient_refresh_token()
{
    //获取缓存
    $get_token = get_transient('oplist_client_jwt_token');

    if ($get_token) {
        //删除缓存
        delete_transient('oplist_client_jwt_token');
    } else {
        return false;
    }
    return true;
}

//获取 OpenList 实例
function aya_oplist_cli_init()
{
    static $oplist_cli = null;

    if ($oplist_cli !== null) {
        return $oplist_cli;
    }

    $server = aya_oplist_server_url();
    $token = aya_oplist_transient_token();

    $oplist_cli = new OpenList_API($server, $token);

    return $oplist_cli;
}

//OpenList 服务器连接测试
function aya_oplist_server_option_test_request()
{
    //未初始化
    if (empty(aya_oplist_server_url()) || aya_oplist_server_url() == 'https://your.openlist.server') {
        return [
            'desc' => '首次启动，请先设置 OpenList 服务器地址',
            'type' => 'message',
        ];
    }

    $ping_server = aya_oplist_ping_server();

    //连接失败
    if (!$ping_server) {
        return [
            'desc' => '[错误] OpenList 服务器连接失败，请检查服务器地址',
            'type' => 'warning',
        ];
    }

    //刷新令牌
    aya_oplist_transient_refresh_token();

    //用户名或密码错误
    if (!aya_oplist_transient_token()) {
        return [
            'desc' => '[错误] OpenList 用户名或密码错误，请检查设置',
            'type' => 'dismiss',
        ];
    }

    //连接正常
    return [
        'desc' => '连接成功！ OpenList 存储连接正常',
        'type' => 'success',
    ];
}

//OpenList 文件直链提取
function aya_oplist_file_raw_url($full_path, $password)
{
    $oplist_cli = aya_oplist_cli_init();

    $fs_data = $oplist_cli->fs_get($full_path, $password);

    if (!is_array($fs_data) || $fs_data['is_dir']) {
        return false;
    }

    return $fs_data['raw_url'];
}

//生成文件图标的键值表映射
function aya_oplist_icon_array_map()
{
    //静态变量缓存
    static $icon_map = null;

    //定义文件类型与图标的键值表映射
    if ($icon_map === null) {
        $icons_of_all = [
            //压缩文件
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'],
            //图片
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'heic', 'tiff', 'svg', 'raw', 'ico', 'swf'],
            //音频
            'audio' => ['mp3', 'flac', 'opus', 'ogg', 'aac', 'wav', 'wma', 'm4a'],
            //视频
            'video' => ['mp4', 'mkv', 'flv', 'ts', 'mov', 'mpg', 'mpeg', 'webm', 'm3u8'],
            //文本
            'text' => ['txt', 'json', 'conf', 'yml', 'log', 'ini', 'css', 'vtt', 'ass', 'srt', 'lrc'],
            //富文本
            'document' => ['rtf', 'html', 'htm', 'xhtml', 'mht', 'mhtml', 'chm', 'md', 'xml', 'epub', 'mobi'],
            //加密格式
            'encryption' => ['enc', 'pgp', 'gpg', 'bin', 'dll', 'pem', 'key'],
            //镜像格式
            'mirrorfile' => ['iso', 'vhdx', 'vhd', 'dmg', 'img', 'crypt', 'crypted'],
            //电子表单
            'spreadsheet' => ['csv', 'tsv'],
            //SQL
            'db' => ['sql', 'db'],
            //字体
            'font' => ['ttf', 'otf', 'ttc', 'oft', 'ps', 'woff', 'woff2'],
            //文档
            'docx' => ['doc', 'docx', 'odt'],
            'pptx' => ['ppt', 'pptx', 'odp'],
            'xlsx' => ['xls', 'xlsx', 'ods'],
            'pdf' => ['pdf'],
            //代码
            'code' => ['php', 'js', 'tsx', 'py', 'java', 'c', 'cpp', 'h', 'hpp', 'go', 'swift', 'vue', 'rs', 'lua', 'sh', 'bat', 'cmd'],
            //可执行文件
            'binary' => ['exe', 'msi', 'apk', 'ipa', 'deb', 'iso', 'pkg', 'appimage', 'snap'],
        ];

        //递归到键值表中
        foreach ($icons_of_all as $category => $extensions) {
            foreach ($extensions as $icon_ext) {
                $icon_map[$icon_ext] = $category;
            }
        }
    }

    return $icon_map;
}

//重新循环文件列表
function aya_oplist_rebuild_content($content, $raw_atts)
{
    //提取必要设置
    $fs_root_url = aya_oplist_server_url();
    $fs_method = $raw_atts['fs_method'];
    $fs_path = $raw_atts['path'];
    $fs_password = $raw_atts['password'];
    $fs_ignore_dir = filter_var($raw_atts['ignore_dir'], FILTER_VALIDATE_BOOLEAN);
    $use_raw_url = false;

    //链接模式切换
    switch (aya_opt('site_oplist_fs_link_type', 'oplist')) {
        case 'd':
            $ext_path = '/d';
            break;
        case 'p':
            $ext_path = '/p';
            break;
        case 'r':
            $use_raw_url = true;
            $ext_path = '';
            break;
        case 'f':
        default:
            $ext_path = '';
            break;
    }

    //匿名方法
    /*
    //格式化文件大小
    $func_format_size = function ($file_size = '', $precision = 2) {
        if (empty($file_size)) {
            return '--';
        }
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($file_size, 0);
        //幂等计算法
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        //$bytes /= (1 << (10 *$pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    };
    //格式化日期
    $func_format_date = function ($file_date = '') {
        if (empty($file_date)) {
            return '--';
        }
        //8601格式
        $date = new DateTime($file_date);
        return $date->format('Y-m-d');
    };
    */
    //匹配文件图标
    if (aya_opt('site_oplist_fs_icon_bool', 'oplist')) {
        $func_format_type = function ($file_name = '', $file_is_dir = false) {
            if ($file_is_dir) {
                return 'folder';
            } else {
                //分析文件名
                $icon_map = aya_oplist_icon_array_map();
                $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                return isset($icon_map[$extension]) ? $icon_map[$extension] : 'unknown';
            }
        };
    } else {
        $func_format_type = function ($file_name = '', $file_is_dir = false) {
            return $file_is_dir ? 'folder' : 'document';
        };
    }
    //转义URL字符
    $func_path_segments = function ($path = '') {
        $segments = explode('/', $path);
        $encoded = array_map('rawurlencode', $segments);
        return implode('/', $encoded);
    };
    //拼接 OpenList 跳转路径
    $func_format_url = function ($file_path = '', $file_name = '', $file_sign = '') use ($fs_root_url, $ext_path, $func_path_segments) {
        $rel_path = (empty($file_name)) ? $file_path : $file_path . '/' . $file_name;
        $file_path = $func_path_segments($rel_path);
        $file_sign = ($file_sign == '') ? '' : '?sign=' . $file_sign;
        return $fs_root_url . $ext_path . $file_path . $file_sign;
    };
    //重新请求 OpenList 获取直链
    $func_raw_url = function ($full_path = '') use ($fs_password) {
        return aya_oplist_file_raw_url($full_path, $fs_password);
    };

    //根据请求应用不同的数据处理方法
    $fs_content = [];
    //获取文件
    if ($fs_method == 'get') {
        $fs_content[] = [
            'name' => $content['name'],
            'size' => $content['size'],
            'modified' => $content['modified'],
            'created' => $content['created'],
            'type' => $func_format_type($content['name'], $content['is_dir']),
            'url' => ($use_raw_url) ? $content['raw_url'] : $func_format_url($fs_path, '', $content['sign']),
        ];
    }
    //文件列表
    else if ($fs_method == 'list') {
        $content = $content['content'];
        foreach ($content as $data) {
            //忽略文件夹
            if ($fs_ignore_dir && $data['is_dir']) {
                continue;
            }
            $fs_content[] = [
                'name' => $data['name'],
                'size' => $data['size'],
                'modified' => $data['modified'],
                'created' => $data['created'],
                'type' => $func_format_type($data['name'], $data['is_dir']),
                'url' => ($use_raw_url) ? $func_raw_url($fs_path . '/' . $data['name']) : $func_format_url($fs_path, $data['name'], $data['sign']),
            ];
        }
    }
    //搜索结果
    else if ($fs_method == 'search') {
        $content = $content['content'];
        //重新请求
        $oplist_cli = aya_oplist_cli_init();
        foreach ($content as $data) {
            //忽略文件夹
            if ($data['is_dir']) {
                continue;
            }
            $sub_path = $data['parent'] . '/' . $data['name'];
            $fs_data = $oplist_cli->fs_get($sub_path, $fs_password);
            //忽略报错
            if (!is_array($fs_data)) {
                continue;
            }
            $fs_content[] = [
                'name' => $fs_data['name'],
                'size' => $fs_data['size'],
                'modified' => $fs_data['modified'],
                'created' => $fs_data['created'],
                'type' => $func_format_type($fs_data['name'], $fs_data['is_dir']),
                'url' => ($use_raw_url) ? $fs_data['raw_url'] : $func_format_url($sub_path, '', $fs_data['sign']),
            ];
        }
    }

    return $fs_content;
}

/*
 * ------------------------------------------------------------------------------
 *  OpenList 组件方法
 * ------------------------------------------------------------------------------
 */

AYF::new_box([
    'title' => 'OpenList 客户端',
    'id' => 'oplist_client',
    'context' => 'normal',
    'priority' => 'low',
    'add_box_in' => ['post'],
    'desc' => 'OpenList 客户端加载模块',
    'fields' => [
        [
            'id' => 'sponsor_can',
            'title' => '支援者限定',
            'desc' => '仅对赞助者用户开放',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'id' => 'fs_method',
            'title' => '请求方法',
            'desc' => '选择请求方法',
            'type' => 'select',
            'sub' => [
                'off' => '关闭',
                'list' => '列出文件目录',
                'get' => '获取某个文件/目录',
                'dirs' => '获取目录',
                'search' => '搜索文件',
            ],
            'default' => 'off',
        ],
        [
            'id' => 'path',
            'title' => '目录',
            'desc' => '请求的目录或文件路径',
            'type' => 'text',
            'default' => '',
        ],
        [
            'id' => 'desc',
            'title' => '描述',
            'desc' => '请求的目录描述信息，留空时使用默认描述',
            'type' => 'textarea',
            'default' => '',
        ],
        [
            'id' => 'parent',
            'title' => '搜索目录',
            'desc' => '请求的搜索根目录路径 *仅搜索模式下',
            'type' => 'text',
            'default' => '',
        ],
        [
            'id' => 'keywords',
            'title' => '搜索关键词',
            'desc' => '搜索的关键词 *仅搜索模式下',
            'type' => 'text',
            'default' => '',
        ],
        [
            'id' => 'per_page',
            'title' => '分页显示参数',
            'desc' => '每页显示的文件数量，留空时显示全部',
            'type' => 'text',
            'default' => '0',
        ],
        [
            'id' => 'password',
            'title' => '访问密码',
            'desc' => '请求的目录或文件的访问密码',
            'type' => 'text',
            'default' => '',
        ],
        [
            'id' => 'refresh',
            'title' => '强制刷新',
            'desc' => '是否强制刷新（跳过缓存）',
            'type' => 'switch',
            'default' => false,
        ],
    ],
]);

// 检查文章是否配置了文件列表
function aya_is_oplist_cli_ready($post_id = 0)
{
    $post_id = absint($post_id);

    if ($post_id <= 0) {
        return false;
    }
    // 检查文章是否配置了请求方法
    $fs_method = aya_post_opt('fs_method', 'oplist_client', $post_id);
    if (!in_array($fs_method, ['list', 'get', 'dirs', 'search'], true)) {
        return false;
    }
    // 检查文章是否配置了搜索关键词
    if ($fs_method === 'search') {
        $keywords = (string) aya_post_opt('keywords', 'oplist_client', $post_id);
        return $keywords !== '';
    }
    // 检查文章是否配置了目录路径
    $path = (string) aya_post_opt('path', 'oplist_client', $post_id);
    return $path !== '';
}

/*
 * ------------------------------------------------------------------------------
 *  OpenList 旧短代码迁移层
 * ------------------------------------------------------------------------------
 */

/*
if (is_admin()) {
    AYA_Shortcode::shortcode_register('oplist-client', [
        'id' => 'sc-oplist-client',
        'title' => 'OpenList 客户端',
        'note' => 'OpenList 客户端的请求体调用',
        'template' => '[oplist_cli {{attributes}}]{{content}}[/oplist_cli]',
        'field_build' => [
            [
                'id' => 'fs_method',
                'type' => 'select',
                'label' => '请求方法',
                'desc' => '选择请求方法',
                'sub' => [
                    'list' => '列出文件目录',
                    'get' => '获取某个文件/目录',
                    'dirs' => '获取目录',
                ],
                'default' => 'list',
            ],
            [
                'id' => 'path',
                'type' => 'text',
                'label' => '路径',
                'desc' => '请求的目录或文件路径',
                'default' => '/',
            ],
            [
                'id' => 'password',
                'type' => 'text',
                'label' => '密码',
                'desc' => '请求的目录或文件的访问密码',
                'default' => '',
            ],
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '描述',
                'desc' => '在页面中显示的描述文本，留空时使用设置的默认描述',
                'default' => '',
            ],
            [
                'id' => 'per_page',
                'type' => 'text',
                'label' => '每页显示',
                'desc' => '每页显示的文件数量，留空时显示全部',
                'default' => '',
            ],
            [
                'id' => 'refresh',
                'type' => 'checkbox',
                'label' => '强制刷新',
                'desc' => '是否强制刷新（跳过缓存）',
                'default' => false,
            ],
        ]
    ]);

    AYA_Shortcode::shortcode_register('oplist-search', [
        'id' => 'sc-oplist-search',
        'title' => 'OpenList 搜索文件',
        'note' => 'OpenList 客户端的请求体调用',
        'template' => '[oplist_cli {{attributes}}]{{content}}[/oplist_cli]',
        'field_build' => [
            [
                'id' => 'fs_method',
                'type' => 'select',
                'label' => '请求方法',
                'desc' => '选择请求方法',
                'sub' => [
                    'search' => '搜索文件或文件夹',
                ],
                'default' => 'search',
            ],
            [
                'id' => 'parent',
                'type' => 'text',
                'label' => '搜索目录',
                'desc' => '搜索的目录路径',
                'default' => '/',
            ],
            [
                'id' => 'keywords',
                'type' => 'text',
                'label' => '关键词',
                'desc' => '搜索的关键词',
                'default' => '',
            ],
            [
                'id' => 'scope',
                'type' => 'select',
                'label' => '搜索类型',
                'desc' => '仅搜索文件或文件夹',
                'sub' => [
                    '0' => '全部',
                    '1' => '文件夹',
                    '2' => '文件',
                ],
                'default' => '2',
            ],
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '描述',
                'desc' => '在页面中显示的描述文本',
                'default' => '',
            ],
            [
                'id' => 'per_page',
                'type' => 'text',
                'label' => '每页显示',
                'desc' => '每页显示的文件数量，默认每页显示 10 个文件',
                'default' => '',
            ],
        ]
    ]);

    AYA_Shortcode::shortcode_register('plyr-client', [
        'id' => 'sc-plyr-client',
        'title' => 'Plyr 播放列表',
        'note' => '音频 / 视频模板时 Plyr 播放器的播放列表',
        'template' => '[plyr_cli {{attributes}} /]',
        'field_build' => [
            [
                'id' => 'title',
                'type' => 'text',
                'label' => '标题',
                'desc' => '视频标题',
                'default' => '',
            ],
            [
                'id' => 'src',
                'type' => 'text',
                'label' => '视频源',
                'desc' => '视频文件 URL',
                'default' => '',
            ],
            [
                'id' => 'poster',
                'type' => 'text',
                'label' => '封面',
                'desc' => '视频封面图片 URL',
                'default' => '',
            ],
            [
                'id' => 'type',
                'type' => 'select',
                'label' => '视频类型',
                'desc' => '选择视频类型',
                'sub' => [
                    'auto' => '自动',
                    'hls' => 'HLS 流',
                ],
                'default' => 'auto',
            ],
        ]
    ]);
}
*/

//短代码功能
add_shortcode('oplist_cli', 'aya_oplist_cli_shortcode_fs_methods');

function aya_oplist_cli_get_current_post_id()
{
    global $post;

    if ($post instanceof WP_Post) {
        return (int) $post->ID;
    }

    $post_id = get_the_ID();
    if (!empty($post_id)) {
        return (int) $post_id;
    }

    $post_id = get_queried_object_id();

    return !empty($post_id) ? (int) $post_id : 0;
}

function aya_oplist_cli_prepare_metabox_field($value)
{
    if (is_array($value)) {
        $value = array_filter($value);
        return $value === [] ? [] : $value;
    }

    $value = wp_unslash((string) $value);
    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

    return $value;
}

function aya_oplist_cli_has_saved_meta($post_id)
{
    if ($post_id <= 0) {
        return false;
    }

    return metadata_exists('post', $post_id, 'aya_box_oplist_client');
}

function aya_oplist_cli_persist_post_meta($post_id, $atts)
{
    if ($post_id <= 0 || !is_array($atts) || aya_oplist_cli_has_saved_meta($post_id)) {
        return false;
    }

    $fs_method = (string) ($atts['fs_method'] ?? 'list');
    if ($fs_method !== 'list') {
        return false;
    }

    $meta_values = [
        'sponsor_can' => '1',
        'fs_method' => aya_oplist_cli_prepare_metabox_field('list'),
        'path' => aya_oplist_cli_prepare_metabox_field('/' . trim((string) ($atts['path'] ?? '/'), '/')),
    ];

    update_post_meta($post_id, 'aya_box_oplist_client', $meta_values);

    return true;
}

//AIYA-CMS 短代码组件：OpenList功能卡片
function aya_oplist_cli_shortcode_fs_methods($atts = array(), $content = null)
{
    $route_page = aya_is_where();

    if ($route_page != 'single' && $route_page != 'page') {
        return '';
    }

    $atts = shortcode_atts(
        array(
            'fs_method' => 'list',
            'path' => '',
        ),
        $atts,
    );

    $post_id = aya_oplist_cli_get_current_post_id();

    if ($post_id > 0) {
        aya_oplist_cli_persist_post_meta($post_id, $atts);
    }

    return '';
}

/*
 * ------------------------------------------------------------------------------
 *  Plyr 播放器 短代码组件
 * ------------------------------------------------------------------------------
 */
/*
//短代码功能
add_shortcode('plyr_cli', 'aya_plyr_shortcodes_playlist_methods');

$GLOBALS['aya_plyr_playlist'] = [];

function aya_plyr_playlist_set_props($item)
{
    if (!is_array($item))
        return;

    $GLOBALS['aya_plyr_playlist'][] = $item;
}

function aya_plyr_playlist_get_props()
{
    return $GLOBALS['aya_plyr_playlist'] ?? [];
}

// AIYA-CMS 短代码组件：Plyr Player
function aya_plyr_shortcodes_playlist_methods($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'src' => '',
            'poster' => '',
            'title' => __('无标题', 'aiya-cms'),
            'type' => 'auto',
        ),
        $atts,
    );

    if (empty($atts['src']))
        return '';

    $is_hls = (strpos($atts['src'], '.m3u8') !== false) || $atts['type'] === 'hls';

    $source = [
        'type' => 'video',
        'title' => $atts['title'],
        'sources' => [
            [
                'src' => esc_url_raw($atts['src']),
                'provider' => 'html5',
                'type' => $is_hls ? 'application/x-mpegURL' : 'video/mp4'
            ]
        ],
        'poster' => esc_url_raw($atts['poster'])
    ];

    aya_plyr_playlist_set_props($source);

    return '';
}
*/
