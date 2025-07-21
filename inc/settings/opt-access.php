<?php

if (!defined('ABSPATH')) {
    exit;
}

//创建主题设置
AYF::new_opt([
    'title' => '外部接入',
    'parent' => 'basic',
    'slug' => 'access',
    'desc' => 'AIYA-CMS 主题，第三方系统接口设置',
    'fields' => aya_opt_access_add_exists(),
]);

function aya_opt_access_add_exists()
{
    $fields = [];
    $fields = array_merge($fields, [
        [
            'desc' => '社交登录设置',
            'type' => 'title_1',
        ],
        [
            'desc' => 'To be continued ...',
            'type' => 'content',
        ],
        /*
        [
            'title' => 'OpenID 登录',
            'desc' => '启用 OpenID 登录接入',
            'id' => 'site_oauth_openid_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => 'Github 登录',
            'desc' => '启用 Github 登录接入',
            'id' => 'site_oauth_github_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => 'Github Client ID',
            'desc' => 'Github OAuth Client ID',
            'id' => 'site_oauth_github_client_id',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => 'Github Client Secret',
            'desc' => 'Github OAuth Client Secret',
            'id' => 'site_oauth_github_client_secret',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => 'Google 登录',
            'desc' => '启用 Google 登录接入',
            'id' => 'site_oauth_google_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => 'Google Client ID',
            'desc' => 'Google OAuth Client ID',
            'id' => 'site_oauth_google_client_id',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => 'Google Client Secret',
            'desc' => 'Google OAuth Client Secret',
            'id' => 'site_oauth_google_client_secret',
            'type' => 'text',
            'default' => '',
        ],
        */
        [
            'desc' => '赞助者订阅设置',
            'type' => 'title_1',
        ],
        [
            'title' => '启用赞助者权限模块',
            'desc' => '启用赞助者权限和相关功能，允许用户购买查看专属内容',
            'id' => 'site_sponsor_module_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => '订阅说明页面',
            'desc' => '指定一个页面，展示于赞助者组件模块下方',
            'id' => 'site_sponsor_description_page',
            'type' => 'select',
            'sub_mode' => 'page',
            'default' => '',
        ],
        [
            'desc' => '爱发电接口设置',
            'type' => 'title_1',
        ],
        [
            'desc' => '此接口用于完成爱发电平台订阅与站内赞助者系统联动，请根据 [url=https://afdian.com/dashboard/dev]爱发电开发者[/url] 页面完成设置（账号需要为爱发电认证用户）。',
            'type' => 'content',
        ],
        [
            'desc' => '自动回调或用户手动激活爱发电订单时，系统仅会验证用户在爱发电平台来源的赞助订单有效性[b]（即任意赞助金额均会为用户激活相同的权限）[/b]。如果要求用户赞助最小金额，请在爱发电的主页设置中隐藏“自选金额赞助”。',
            'type' => 'warning',
        ],
        [
            'title' => '爱发电主页',
            'desc' => '你的爱发电主页名，[b]仅需填写网址SLUG部分[/b]，即 [code]https://afdian.net/a/{SLUG}[/code]',
            'id' => 'stie_afdian_homepage_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => '开发者用户 ID',
            'desc' => '你的爱发电账号的 [code]user_id[/code]，可在开发者页面查询',
            'id' => 'stie_afdian_userid_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => '开发者专属 Token',
            'desc' => '由爱发电提供给你的 Token ，可在开发者页面查询，[b]切勿泄露，意外损失时请及时重置[/b]',
            'id' => 'stie_afdian_token_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'desc' => '使用自动回调时，请在爱发电开发者页面中设置此站点的回调地址：[code]' . rest_url('afdian/callbacks') . ' [/code]',
            'type' => 'message',
        ],
        [
            'title' => '使用爱发电激活订阅',
            'desc' => '当用户通过爱发电平台支付后获得网站内的赞助者权限',
            'id' => 'site_afdian_convert_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => '自动回调方式',
            'desc' => '设置支持自动回调激活的方案，显示于主题的 “获取订阅”页面',
            'id' => 'site_afdian_plan_type',
            'type' => 'radio',
            'sub' => [
                'none' => '不使用',
                'optional' => '自选金额方案',
                'preset' => '使用预设方案',
            ],
            'default' => 'optional',
        ],
        [
            'title' => '预设方案链接',
            'desc' => '需要填写你的爱发电主页->赞助方案详情（支付页面）的 URL 地址 [br/]*链接需要包含有效的 [code]plan_id[/code] 参数，否则无法正确回调 [br/]e.g. [code]https://afdian.net/order/create? plan_id=xxxxxxxxxxxxxxxxxxxxxxxx[/code]',
            'id' => 'site_afdian_preset_plan_url',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => '保存爱发电回调日志',
            'desc' => '启用后，保存爱发电平台 WebHook 发回的信息到 LOG ，用于回查激活记录，日志文件保存位置 [code]./wp-content/webhook_logs[/code] ',
            'id' => 'site_afdian_savelog_bool',
            'type' => 'switch',
            'default' => true,
        ],
    ]);

    //支付模块
    if (function_exists('aya_epay_core_init')) {
        $fields = array_merge($fields, [
            [
                'desc' => '易支付接口设置',
                'type' => 'title_1',
            ],
            [
                'desc' => '此接口为易支付（彩虹版）兼容接口，由于各支付平台提供的接口存在差异，上线业务前请测试',
                'type' => 'content',
            ],
            [
                'desc' => '易支付接口仅能够自动回调，掉单时需要在你的服务商后台手动补单。请在订阅说明中保留用户可以联系到的客服方式，及时补单防止被平台认定为欺诈商户。',
                'type' => 'warning',
            ],
            /*
            [
                'title' => '兼容方法',
                'desc' => '',
                'id' => 'site_epay_method_select',
                'type' => 'select',
                'sub' => array(
                    'epay' => '易支付（彩虹版）',
                ),
                'default' => 'epay',
            ],
            */
            [
                'title' => '接口地址',
                'desc' => '易支付接口地址，仅支持 https:// ',
                'id' => 'site_epay_url_text',
                'type' => 'text',
                'default' => '',
            ],
            [
                'title' => '商户ID',
                'desc' => '易支付商户 ID ，由服务商提供',
                'id' => 'site_epay_id_text',
                'type' => 'text',
                'default' => '',
            ],
            [
                'title' => '商户密钥',
                'desc' => '易支付商户密钥（key），由服务商提供',
                'id' => 'site_epay_key_text',
                'type' => 'text',
                'default' => '',
            ],
            [
                'title' => '使用易支付激活订阅',
                'desc' => '用户可以通过易支付平台支付后获得网站内的赞助者权限',
                'id' => 'site_epay_convert_bool',
                'type' => 'switch',
                'default' => true,
            ],
            [
                'title' => '赞助者权限商品设置',
                'desc' => '配置用于购买赞助者权限的商品',
                'id' => 'site_epay_purchas_plan',
                'type' => 'group',
                'sub_type' => [
                    [
                        'title' => '商品名称',
                        'id' => 'name',
                        'type' => 'text',
                        'default' => '获取订阅',
                    ],
                    [
                        'title' => '商品价格（￥）',
                        'id' => 'price',
                        'type' => 'text',
                        'default' => '10.00',
                    ],
                    [
                        'title' => '激活周期（天）',
                        'id' => 'days',
                        'type' => 'text',
                        'default' => '30',
                    ],
                ]
            ],
            [
                'title' => '支付方式',
                'desc' => '选择需要使用的支付方式，可用性请询问你的服务商',
                'id' => 'site_epay_method_type',
                'type' => 'checkbox',
                'sub' => [
                    'alipay' => '支付宝',
                    'wxpay' => '微信',
                    //'unionpay' => '云闪付',
                    //'paypal' => 'PayPal',
                    'usdt' => 'USDT',
                ],
                'default' => ['alipay', 'wxpay'],
            ],
            [
                'title' => '保存易支付回调日志',
                'desc' => '启用后，保存易支付接口 WebHook 发回的订阅信息到 LOG ，用于回查激活记录，日志文件保存位置 [code]./wp-content/webhook_logs[/code] ',
                'id' => 'site_epay_savelog_bool',
                'type' => 'switch',
                'default' => true,
            ],
            /*
            [
                'desc' => '激活码接口设置',
                'type' => 'title_2',
            ],
            [
                'desc' => '此接口为通用发卡兑换接口，启用后，会在后台添加独立的激活码管理页面',
                'type' => 'content',
            ],
            [
                'title' => '使用激活码兑换注册',
                'desc' => '会在用户注册时要求“激活码”选项',
                'id' => 'site_register_convert_bool',
                'type' => 'switch',
                'default' => true,
            ],
            [
                'title' => '使用激活码兑换订阅',
                'desc' => '会在订阅权限组件中显示“激活码”的兑换选项',
                'id' => 'site_sponsor_convert_bool',
                'type' => 'switch',
                'default' => true,
            ],
            */
        ]);
    }

    //OpenList组件
    if (function_exists('aya_oplist_cli_init')) {
        $fields = array_merge($fields, [
            [
                'desc' => ' OpenList 客户端模块设置',
                'type' => 'title_1',
            ],
            [
                'desc' => ' OpenList 是一个开源的网盘列表程序，此模块用于直接调用 OpenList 的文件列表显示在文章页面上',
                'type' => 'content',
            ],
            aya_oplist_server_option_test_request(),
            [
                'title' => '服务器地址',
                'desc' => ' OpenList 的服务器地址',
                'id' => 'site_oplist_server_url',
                'type' => 'text',
                'default' => 'https://your.openlist.server',
            ],
            [
                'title' => '用户名',
                'desc' => '用于请求 OpenList API 登录的用户',
                'id' => 'site_oplist_server_user',
                'type' => 'text',
                'default' => 'username',
            ],
            [
                'title' => '密码',
                'desc' => '用于请求 OpenList API 登录的用户',
                'id' => 'site_oplist_server_pswd',
                'type' => 'text',
                'default' => 'password',
            ],
            [
                'title' => '令牌缓存时间',
                'desc' => ' OpenList 的 JWt Token 缓存时间（小时），设置为 [code]0[/code] 则每次都重新请求令牌（*取决于 OpenList 站点配置，默认为 48 小时）',
                'id' => 'site_oplist_server_token_hours',
                'type' => 'text',
                'default' => '48',
            ],
            [
                'title' => '启用文件图标匹配',
                'desc' => '在 OpenList 返回文件列表时为文件匹配图标',
                'id' => 'site_oplist_fs_icon_bool',
                'type' => 'switch',
                'default' => true,
            ],
            [
                'title' => '文件跳转设置',
                'desc' => '设置从文件列表跳转到 OpenList 的方法
                [br/]文件页面：直接跳转到 OpenList 的文件/文件夹详情页面
                [br/]直接下载：下载文件（由 OpenList 完成 302 跳转）
                [br/]代理下载：下载文件（由 OpenList 本机代理下载）
                [br/]直链下载：循环请求尝试直接取出真实文件地址，会大幅增加加载时间
                ',
                'id' => 'site_oplist_fs_link_type',
                'type' => 'radio',
                'sub' => [
                    'f' => '详情页面',
                    'd' => '直接下载',
                    'p' => '代理下载',
                    'r' => '直链下载',
                ],
                'default' => 'd',
            ],
            [
                'title' => '默认列表描述',
                'desc' => '设置 OpenList 的文件列表底部默认的描述文本',
                'id' => 'site_oplist_file_desc',
                'type' => 'textarea',
                'default' => '文件下载由 your.openlist.server 提供支持',
            ],
        ]);
    }
    return $fields;
}