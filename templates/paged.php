<?php
/**
 * 生成分页HTML
 *
 */
function render($classes = [], $container = 'div')
{
    if (empty($this->paged_data['items'])) {
        return '';
    }

    $default_classes = ['pagination', 'aiya-pagination'];
    $class_attr = implode(' ', array_merge($default_classes, $classes));

    $output = '<' . $container . ' class="' . esc_attr($class_attr) . '">';

    foreach ($this->paged_data['items'] as $item) {
        if ($item['type'] === 'current') {
            $output .= '<span aria-current="' . esc_attr($item['aria-current']) . '" class="' . esc_attr($item['class']) . '">' . $item['text'] . '</span>';
        } elseif ($item['type'] === 'dots') {
            $output .= '<span class="' . esc_attr($item['class']) . '">' . $item['text'] . '</span>';
        } else {
            $output .= '<a href="' . esc_url($item['url']) . '" class="' . esc_attr($item['class']) . '">' . $item['text'] . '</a>';
        }
    }

    $output .= '</' . $container . '>';

    return $output;
}