<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 生成Bootstrap分页和其他分页的方法
 * ------------------------------------------------------------------------------
 */

//分页函数（完整）
function aya_get_page_nav_item($range = 7)
{
    global $paged, $wp_query;
    $max_page = $wp_query->max_num_pages;

    //拼接分页列表
    $html = '';
    if ($max_page > 1) {
        $html .= '<div class="paged-nav-full"><ul class="pagination">';
        if (!$paged) {
            $paged = 1;
        }
        //上一页
        if ($paged != 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link(1) . '"><span aria-hidden="true">' . __('首页', 'AIYA') . '</span></a></li>';
            $html .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($paged - 1) . '"><span aria-hidden="true">' . __('上一页', 'AIYA') . '</span></a></li>';
        }
        //生成页码
        if ($max_page > $range) {
            $range_page = ceil($range / 2);
            $call_page = $max_page - $range_page;
            if ($paged < $range) {
                for ($i = 1; $i <= $range; $i++) {
                    $active = ($i == $paged) ? 'active' : '';
                    $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                }
            } elseif ($paged >= $call_page) {
                for ($i = ($max_page - $range); $i <= $max_page; $i++) {
                    $active = ($i == $paged) ? 'active' : '';
                    $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                }
            } elseif ($paged >= $range && $paged < $call_page) {
                for ($i = ($paged - $range_page); $i <= ($paged + $range_page); $i++) {
                    $active = ($i == $paged) ? 'active' : '';
                    $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                }
            }
        } else {
            for ($i = 1; $i <= $max_page; $i++) {
                $active = ($i == $paged) ? 'active' : '';
                $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
            }
        }
        //尾页
        if ($paged != $max_page) {
            $html .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($paged + 1) . '"><span aria-hidden="true">' . __('下一页', 'AIYA') . '</span></a></li>';
            $html .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($max_page) . '"><span aria-hidden="true">' . __('尾页', 'AIYA') . '</span></a></li>';
        }
        //总页数
        $html .= '<li class="page-item disabled"><a class="page-link">共 ' . $max_page . ' 页</a></li>';
        $html .= '</ul></div>';
    }

    echo $html;
}

//分页函数（简化）
function aya_get_page_nav($range = 3)
{
    global $paged, $wp_query;
    $max_page = $wp_query->max_num_pages;

    if ($max_page == 1) return;

    if (!$paged) {
        $paged = 1;
    }
    //拼接分页列表
    $html = '';
    $html .= '<div id="load-page" class="paged-nav"><ul class="paged text-center">';

    //上一页
    if ($paged != 1) {
        $html .= '<li class="page-button prev-page"><a class="page-link" href="' . get_pagenum_link($paged - 1) . '">&laquo;</a></li>';
    }
    if ($paged > $range + 1) {
        $html .= '<li class="page-button"><a class="page-link" href="' . get_pagenum_link(1) . '">1</a></li>';
    }
    if ($paged > $range + 2) {
        $html .= '<li class="page-button"><a class="page-link" href="javascript:void(0)">...</a></li>';
    }
    for ($i = $paged - $range; $i <= $paged + $range; $i++) {
        if ($i > 0 && $i <= $max_page) {
            if ($i == $paged) {
                $html .= '<li class="page-button"><a class="page-link cur">' . $i . '</a></li>';
            } else {
                $html .= '<li class="page-button"><a class="page-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
            }
        }
    }
    if ($paged < $max_page - $range - 1) {
        $html .= '<li class="page-button"><a class="page-link" href="javascript:void(0)">...</a></li>';
        $html .= '<li class="page-button"><a class="page-link" href="' . get_pagenum_link($max_page) . '">' . $max_page . '</a></li>';
    }
    if ($paged != $max_page) {
        $html .= '<li class="page-button next-page"><a class="page-link" href="' . get_pagenum_link($paged + 1) . '">&raquo;</a></li>';
    }
    $html .= '</ul></div>';

    echo $html;
}

//分页函数（AJAX更多）
function aya_get_page_load_more($auto_load = true)
{
    global $paged, $wp_query;
    $max_page = $wp_query->max_num_pages;

    if ($max_page > $paged) {
        $page_link = get_next_posts_page_link('?paged=' . ($paged + 1));
        $html = '<div id="load-more" class="load-more text-center trans-200"><a class="page-link" href="' . esc_url($page_link) . '">' . __('加载更多', 'AIYA') . '</a></div>';

        echo $html;
    }
}
