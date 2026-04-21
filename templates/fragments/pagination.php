<?php

if (empty($paged['links'])) {
    return;
}

$pagination_html = static function ($paged = array()) {
    if (empty($paged['links']) || !is_array($paged['links'])) {
        return '';
    }

    $prev_link = null;
    $next_link = null;

    foreach ($paged['links'] as $link) {
        if (!is_array($link) || empty($link['type'])) {
            continue;
        }

        if ($link['type'] === 'prev') {
            $prev_link = $link;
        }

        if ($link['type'] === 'next') {
            $next_link = $link;
        }
    }

    $html = '<nav role="navigation" aria-label="Pagination" class="my-4mx-auto flex w-full justify-center">';
    if (!empty($prev_link['url'])) {
        $html .= '<a href="' . esc_url($prev_link['url']) . '" rel="prev" aria-label="Go previous page" class="inline-flex h-9 items-center rounded-md px-3 text-sm hover:bg-accent">' . esc_html(($prev_link['text'] ?? __('上一页', 'AIYA'))) . '</a>';
    } else {
        $html .= '<span class="inline-flex h-9 items-center rounded-md px-3 text-sm opacity-50">' . esc_html(__('上一页', 'AIYA')) . '</span>';
    }
    if (!empty($next_link['url'])) {
        $html .= '<a href="' . esc_url($next_link['url']) . '" rel="next" aria-label="Go next page" class="inline-flex h-9 items-center rounded-md px-3 text-sm hover:bg-accent">' . esc_html(($next_link['text'] ?? __('下一页', 'AIYA'))) . '</a>';
    } else {
        $html .= '<span class="inline-flex h-9 items-center rounded-md px-3 text-sm opacity-50">' . esc_html(__('下一页', 'AIYA')) . '</span>';
    }
    $html .= '</nav>';

    return $html;
};

// 渲染分页组件
aya_react_island('content-pagination', $paged,  $pagination_html($paged));
