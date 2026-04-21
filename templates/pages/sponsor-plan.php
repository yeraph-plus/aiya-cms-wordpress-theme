<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    return wp_redirect(home_url());
}

//嵌入赞助计划的介绍页面
$page_id = aya_opt('site_sponsor_description_page', 'access');

//获取用户的赞助订单
$order_query = aya_sponsor_get_user_orders();
//计算查询为可读时间
if (!empty($order_query)) {
    //计算到期时间戳为可读时间
    $order_query['expiration'] = date_i18n('Y-m-d', $order_query['expiration']);
    //被强制取消状态
    $order_query['force_cancel'] = ($order_query['force_cancel'] === '1') ? true : false;
}

//获取订阅支付方案列表
$order_plan = aya_payment_sponsor_order_plan();
//系统可用的激活码来源
$code_from = aya_payment_sponsor_activation_code();

?>
<div class="my-8 flex flex-col gap-4">
    <?php aya_react_island('user-sponsor-dashboard', ['data' => $order_query]); ?>
    <?php aya_react_island('user-sponsor-subscribe', ['plans' => $order_plan]); ?>
    <?php aya_react_island('user-sponsor-activate', ['code_from' => $code_from]); ?>
</div>
<!-- The Page -->
<?php if (!empty($page_id)):
    $page_title = get_post($page_id)->post_title;
    $page_content = apply_filters('the_content', get_post($page_id)->post_content);
?>
    <div id="article-content" class=" prose prose-base lg:prose-lg max-w-none">
        <h2><?php aya_echo($page_title); ?></h2>
        <?php echo $page_content; ?>
    </div>
<?php endif; ?>