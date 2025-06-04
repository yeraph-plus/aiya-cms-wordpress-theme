<?php

if (!defined('ABSPATH')) {
    exit;
}

//获取分页数据
$page_turner = aya_opt('site_loop_paged_type', 'basic');

if ($page_turner == 'full') {
    $paged = aya_get_pagination();
} else {
    $paged = aya_get_simple_pagination();
}

if (empty($paged)) {
    return '';
}

?>
<nav role="navigation" aria-label="Pagination">
    <?php if ($page_turner == 'full'): ?>
        <div class="join flex justify-center items-center p-4">
            <?php foreach ($paged['links'] as $link): ?>
                <?php
                //按钮样式
                $btn_class = 'join-item btn';
                //当前页
                if ($link['active']) {
                    $btn_class .= ' btn-primary pointer-events-none';
                }
                //应用ARIA属性
                $aria_attributes = '';
                switch ($link['type']) {
                    case 'current':
                        $aria_attributes .= 'aria-current="page"';
                        break;
                    case 'prev':
                        $aria_attributes .= 'rel="prev" aria-label="Go previous page"';
                        break;
                    case 'next':
                        $aria_attributes .= 'rel="next" aria-label="Go next page"';
                        break;
                    case 'page':
                        $aria_attributes .= 'aria-label="Go ' . $link['label'] . ' page"';
                        break;
                    case 'dots':
                        $btn_class .= ' btn-disabled';
                        $aria_attributes .= 'aria-hidden="true"';
                    default:
                        break;
                }
                ?>
                <a href="<?php aya_echo($link['url']); ?>" class="<?php aya_echo($btn_class); ?>" <?php aya_echo($aria_attributes); ?>>
                    <?php aya_echo($link['text']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-center text-xs text-base-content/60 pb-2">
            <span aria-live="polite"><?php echo sprintf(__('第 %s 页，共 %s 页', 'AIYA'), $paged['current'], $paged['total']); ?></span>
        </div>
    <?php elseif ($page_turner == 'simple'): ?>
        <div class="join flex justify-center items-center p-6">
            <?php if ($paged['has_prev']): ?>
                <a href="<?php aya_echo($paged['prev_url']); ?>" class="join-item btn" rel="prev" aria-label="Go previous page">
                    <icon name="chevron-double-left" class="size-4"></icon>
                </a>
            <?php else: ?>
                <button class="join-item btn btn-disabled" aria-hidden="true" disabled>
                    <icon name="chevron-double-left" class="size-4"></icon>
                </button>
            <?php endif; ?>
            <span class="join-item btn no-animation pointer-events-none" aria-live="polite">
                <?php echo sprintf(__('第 %s 页，共 %s 页', 'AIYA'), $paged['current'], $paged['total']); ?>
            </span>
            <?php if ($paged['has_next']): ?>
                <a href="<?php echo esc_url($paged['next_url']); ?>" class="join-item btn" rel="next" aria-label="Go next page">
                    <icon name="chevron-double-right" class="size-4"></icon>
                </a>
            <?php else: ?>
                <button class="join-item btn btn-disabled" aria-hidden="true" disabled>
                    <icon name="chevron-double-right" class="size-4"></icon>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</nav>