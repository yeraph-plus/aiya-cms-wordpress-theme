<?php

if (!defined('ABSPATH')) {
    exit;
}

//创建主题设置
AYF::new_opt([
    'title' => __('订阅接入', 'aiya-cms'),
    'parent' => 'basic',
    'slug' => 'access',
    'desc' => __('AIYA-CMS 主题，第三方平台接口设置', 'aiya-cms'),
    'fields' => aya_opt_access_add_exists(),
]);

function aya_opt_access_add_exists()
{
    $fields = array(
        [
            'desc' => __('社交登录设置', 'aiya-cms'),
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
        */
    );

    $fields = array_merge($fields, array(
        [
            'desc' => __('赞助者订阅设置', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'title' => __('启用赞助者权限模块', 'aiya-cms'),
            'desc' => __('启用赞助者权限和相关功能，允许用户购买查看专属内容', 'aiya-cms'),
            'id' => 'site_sponsor_module_bool',
            'type' => 'switch',
            'default' => false,
        ],
        [
            'title' => __('订阅说明页面', 'aiya-cms'),
            'desc' => __('指定一个页面，展示于赞助者组件模块下方', 'aiya-cms'),
            'id' => 'site_sponsor_description_page',
            'type' => 'select',
            'sub_mode' => 'page',
            'default' => '',
        ],
        [
            'desc' => __('爱发电接口设置', 'aiya-cms'),
            'type' => 'title_1',
        ],
        [
            'desc' => __('此接口用于完成爱发电平台订阅与站内赞助者系统联动，请根据 [url=https://afdian.com/dashboard/dev]爱发电开发者[/url] 页面完成设置（账号需要为爱发电认证用户）。', 'aiya-cms'),
            'type' => 'content',
        ],
        [
            'desc' => __('自动回调或用户手动激活爱发电订单时，系统仅会验证用户在爱发电平台来源的赞助订单有效性[b]（即任意赞助金额均会为用户激活相同的权限）[/b]。如果要求用户赞助最小金额，请在爱发电的主页设置中隐藏“自选金额赞助”。', 'aiya-cms'),
            'type' => 'warning',
        ],
        [
            'title' => __('爱发电主页', 'aiya-cms'),
            'desc' => __('你的爱发电主页名，[b]仅需填写网址SLUG部分[/b]，即 [code]https://afdian.net/a/{SLUG}[/code]', 'aiya-cms'),
            'id' => 'stie_afdian_homepage_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => __('开发者用户 ID', 'aiya-cms'),
            'desc' => __('你的爱发电账号的 [code]user_id[/code]，可在开发者页面查询', 'aiya-cms'),
            'id' => 'stie_afdian_userid_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => __('开发者专属 Token', 'aiya-cms'),
            'desc' => __('由爱发电提供给你的 Token ，可在开发者页面查询，[b]切勿泄露，意外损失时请及时重置[/b]', 'aiya-cms'),
            'id' => 'stie_afdian_token_text',
            'type' => 'text',
            'default' => '',
        ],
        [
            'desc' => __('使用自动回调时，请在爱发电开发者页面中设置此站点的回调地址：[code]' . rest_url('afdian/callbacks') . ' [/code]', 'aiya-cms'),
            'type' => 'message',
        ],
        [
            'title' => __('使用爱发电激活订阅', 'aiya-cms'),
            'desc' => __('当用户通过爱发电平台支付后获得网站内的赞助者权限', 'aiya-cms'),
            'id' => 'site_afdian_convert_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'title' => __('自动回调方式', 'aiya-cms'),
            'desc' => __('设置支持自动回调激活的方案，显示于主题的 “获取订阅”页面', 'aiya-cms'),
            'id' => 'site_afdian_plan_type',
            'type' => 'radio',
            'sub' => [
                'none' => __('不使用', 'aiya-cms'),
                'optional' => __('自选金额方案', 'aiya-cms'),
                'preset' => __('使用预设方案', 'aiya-cms'),
            ],
            'default' => 'optional',
        ],
        [
            'title' => __('预设方案链接', 'aiya-cms'),
            'desc' => __('需要填写你的爱发电主页->赞助方案详情（支付页面）的 URL 地址 [br/]*链接需要包含有效的 [code]plan_id[/code] 参数，否则无法正确回调 [br/]e.g. [code]https://afdian.net/order/create? plan_id=xxxxxxxxxxxxxxxxxxxxxxxx[/code]', 'aiya-cms'),
            'id' => 'site_afdian_preset_plan_url',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => __('保存爱发电回调日志', 'aiya-cms'),
            'desc' => __('启用后，保存爱发电平台 WebHook 发回的信息到 LOG ，用于回查激活记录，日志文件保存位置 [code]./wp-content/webhook_logs[/code] ', 'aiya-cms'),
            'id' => 'site_afdian_savelog_bool',
            'type' => 'switch',
            'default' => true,
        ],
        [
            'desc' => __('激活码接口设置', 'aiya-cms'),
            'type' => 'title_2',
        ],
        [
            'desc' => __('此接口为通用发卡兑换接口，启用后，会在后台添加独立的激活码管理页面', 'aiya-cms'),
            'type' => 'content',
        ],
        [
            'title' => __('使用激活码兑换订阅', 'aiya-cms'),
            'desc' => __('会在订阅权限组件中显示“激活码”的兑换选项', 'aiya-cms'),
            'id' => 'site_sponsor_convert_bool',
            'type' => 'switch',
            'default' => true,
        ],
    ));

    //订阅支付模块
    if (function_exists('aya_epay_core_init')) {
        $fields = array_merge($fields, [
            [
                'desc' => __('易支付接口设置', 'aiya-cms'),
                'type' => 'title_1',
            ],
            [
                'desc' => __('此接口为易支付（彩虹版）兼容接口，由于各支付平台提供的接口存在差异，上线业务前请测试', 'aiya-cms'),
                'type' => 'content',
            ],
            [
                'desc' => __('易支付接口仅能够自动回调，掉单时需要在你的服务商后台手动补单。请在订阅说明中保留用户可以联系到的客服方式，及时补单防止被平台认定为欺诈商户。', 'aiya-cms'),
                'type' => 'warning',
            ],
            /*
            [
                'title' => __('兼容方法', 'aiya-cms'),
                'desc' => '',
                'id' => 'site_epay_method_select',
                'type' => 'select',
                'sub' => array(
                    'epay' => __('易支付（彩虹版）', 'aiya-cms'),
                ),
                'default' => 'epay',
            ],
            */
            [
                'title' => __('接口地址', 'aiya-cms'),
                'desc' => __('易支付接口地址，仅支持 https:// ', 'aiya-cms'),
                'id' => 'site_epay_url_text',
                'type' => 'text',
                'default' => '',
            ],
            [
                'title' => __('商户ID', 'aiya-cms'),
                'desc' => __('易支付商户 ID ，由服务商提供', 'aiya-cms'),
                'id' => 'site_epay_id_text',
                'type' => 'text',
                'default' => '',
            ],
            [
                'title' => __('商户密钥', 'aiya-cms'),
                'desc' => __('易支付商户密钥（key），由服务商提供', 'aiya-cms'),
                'id' => 'site_epay_key_text',
                'type' => 'text',
                'default' => '',
            ],
            [
                'title' => __('使用易支付激活订阅', 'aiya-cms'),
                'desc' => __('用户可以通过易支付平台支付后获得网站内的赞助者权限', 'aiya-cms'),
                'id' => 'site_epay_convert_bool',
                'type' => 'switch',
                'default' => false,
            ],
            [
                'title' => __('赞助者权限商品设置', 'aiya-cms'),
                'desc' => __('配置用于购买赞助者权限的商品', 'aiya-cms'),
                'id' => 'site_epay_purchas_plan',
                'type' => 'group_mult',
                'sub_type' => [
                    [
                        'title' => __('订阅名称', 'aiya-cms'),
                        'id' => 'name',
                        'type' => 'text',
                        'default' => __('月度订阅', 'aiya-cms'),
                    ],
                    [
                        'title' => __('商品价格（￥）', 'aiya-cms'),
                        'id' => 'price',
                        'type' => 'text',
                        'default' => '10.00',
                    ],
                    [
                        'title' => __('激活周期（天）', 'aiya-cms'),
                        'id' => 'days',
                        'type' => 'text',
                        'default' => '30',
                    ],
                ]
            ],
            [
                'title' => __('支付方式', 'aiya-cms'),
                'desc' => __('选择需要使用的支付方式，可用性请询问你的服务商', 'aiya-cms'),
                'id' => 'site_epay_method_type',
                'type' => 'checkbox',
                'sub' => [
                    'alipay' => __('支付宝', 'aiya-cms'),
                    'wxpay' => __('微信', 'aiya-cms'),
                    //'unionpay' => __('云闪付', 'aiya-cms'),
                    //'paypal' => __('PayPal', 'aiya-cms'),
                    'usdt' => __('USDT'),
                ],
                'default' => ['alipay', 'wxpay'],
            ],
            [
                'title' => __('保存易支付回调日志', 'aiya-cms'),
                'desc' => __('启用后，保存易支付接口 WebHook 发回的订阅信息到 LOG ，用于回查激活记录，日志文件保存位置 [code]./wp-content/webhook_logs[/code] ', 'aiya-cms'),
                'id' => 'site_epay_savelog_bool',
                'type' => 'switch',
                'default' => true,
            ],
        ]);
    }

    return $fields;
}
