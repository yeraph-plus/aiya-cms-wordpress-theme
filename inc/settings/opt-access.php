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
    'fields' => aya_opt_access_add_exists([
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
    ]),
]);

function aya_opt_access_add_exists($fields)
{
    $func_list = [
        'aya_add_opt_epay_core_settings',
    ];

    //循环追加设置来源
    foreach ($func_list as $func) {
        if (function_exists($func)) {
            $func_field = call_user_func($func);
            if (is_array($func_field)) {
                $fields = array_merge($fields, $func_field);
            }
        }
    }

    return $fields;
}