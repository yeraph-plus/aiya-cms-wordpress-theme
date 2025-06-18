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
    'fields' => [
        [
            'desc' => '爱发电接口设置',
            'type' => 'title_2',
        ],
        [
            'desc' => '此接口用于完成爱发电平台订阅与站内赞助者系统联动，请根据 [url=https://afdian.com/dashboard/dev]爱发电开发者[/url] 页面完成设置（账号需要为爱发电认证用户）。[br/][br/]
            * 此系统仅验证用户在爱发电平台来源的赞助订单有效性，不会限制赞助金额，如果希望限制用户赞助最小金额，请在你的赞助方案设置中隐藏自选金额赞助。[br/][br/]
            ** 使用自动回调时，请在爱发电开发者页面中设置此站点的回调地址：[code]' . home_url('api/afdian/response') . ' [/code]',
            'type' => 'content',
        ],
        [
            'title' => '爱发电主页',
            'desc' => '你的爱发电主页名，仅需填写网址SLUG部分，即 [code]https://afdian.net/a/{SLUG}[/code] [br/]**此项留空时不启用爱发电接入',
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
            'title' => '支付后自动激活方式',
            'desc' => '显示于主题的 “获取订阅”页面，支持自动回调激活',
            'id' => 'site_afdian_plan_type',
            'type' => 'radio',
            'sub' => [
                'none' => '不使用',
                'optional' => '自选金额赞助方案',
                'preset' => '预设的赞助方案',
            ],
            'default' => 'optional',
        ],
        [
            'title' => '预设赞助方案链接',
            'desc' => '需要填写你的爱发电主页->赞助方案详情（支付页面）的 URL 地址 [br/]*链接需要包含有效的 [code]plan_id[/code] 参数，否则无法正确回调 [br/]e.g. [code]https://afdian.net/order/create? plan_id=xxxxxxxxxxxxxxxxxxxxxxxx[/code]',
            'id' => 'site_afdian_preset_plan_url',
            'type' => 'text',
            'default' => '',
        ],
        [
            'title' => '保存 WebHook 数据日志',
            'desc' => '启用后，保存爱发电的平台发回的订阅信息到 LOG ，用于回查激活记录，日志文件保存位置 [code]./wp-content/afdian_logs[/code] ',
            'id' => 'site_afdian_savelog_bool',
            'type' => 'switch',
            'default' => true,
        ],
        /*
        [
            'desc' => '社交登录',
            'type' => 'title_2',
        ],
        [
            'title' => 'Github 登录',
            'desc' => '启用 Github 登录接入',
            'id' => 'site_github_oauth_url',
            'type' => 'switch',
            'default' => false,
        ]
        */
    ]
]);
