<?php
//获取分页数据
$paged = aya_get_pagination();

if (empty($paged)) {
    return;
}

?>
<nav role="navigation" aria-label="Pagination">
    <div class="join flex justify-center items-center py-4">
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
                    $aria_attributes .= 'aria-hidden="true';
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
</nav>