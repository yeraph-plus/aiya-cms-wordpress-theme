<?php

if (!defined('ABSPATH')) {
    exit;
}

//预定义数据
$items = aya_get_breadcrumb();

//隐藏首页
if (count($items) <= 1) {
    return '';
}

?>
<div class="breadcrumbs text-sm pb-4 overflow-hidden" aria-label="Breadcrumb">
    <ul itemscope itemtype="https://schema.org/BreadcrumbList">
        <?php foreach ($items as $i => $item): ?>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item">
                <?php if ($item['url'] && $i < count($items) - 1): ?>
                    <a href="<?php aya_echo($item['url']); ?>" itemprop="item" class="text-base-content/70 hover:text-primary">
                        <?php if ($i === 0): ?>
                            <icon name="home" class="size-4 mr-1"></icon>
                        <?php endif; ?>
                        <span itemprop="name">
                            <?php aya_echo($item['label']); ?>
                        </span>
                    </a>
                <?php else: ?>
                    <span itemprop="name" class="text-base-content">
                        <?php aya_echo($item['label']); ?>
                    </span>
                <?php endif; ?>
                <meta itemprop="position" content="<?php aya_echo($i + 1); ?>" />
            </li>
        <?php endforeach; ?>
    </ul>
</div>