<?php if (aya_opt('site_single_related_type', 'postpage') == 'next-prev'):
    //生成上一篇&下一篇文章
    $prev_post = get_previous_post();
    $next_post = get_next_post();
?>
    <!-- Previous&Next Articles -->
    <div class="flex justify-between px-6 pt-4">
        <?php if (!empty($prev_post)) : ?>
            <a href="<?php aya_echo(get_permalink($prev_post->ID)); ?>" class="flex items-center space-x-2 inset-0 font-semibold hover:text-primary transition-colors duration-300">
                <i data-feather="arrow-left" width="20" height="20" class="m-4"></i>
                <span><?php aya_echo(__('上一篇：', 'AIYA') . $prev_post->post_title); ?></span>
            </a>
        <?php endif; ?>
        <?php if (!empty($next_post)) : ?>
            <a href="<?php aya_echo(get_permalink($next_post->ID)); ?>" class="flex items-center space-x-2 inset-0 font-semibold hover:text-primary transition-colors duration-300">
                <span><?php aya_echo(__('下一篇：', 'AIYA') . $next_post->post_title); ?></span>
                <i data-feather="arrow-right" width="20" height="20" class="m-4"></i>
            </a>
        <?php endif; ?>
    </div>
<?php elseif (aya_opt('site_single_related_type', 'postpage') == 'related'): ?>
    <!-- Related articles -->
    <div class="panel w-full mt-4 py-4 px-6">
        <h3 class="font-semibold text-lg dark:text-white-light mb-4"><?php aya_echo(__('相关文章', 'AIYA')); ?></h3>
        <div class="text-white-dark">
            <ul class="space-y-3 list-inside list-disc font-semibold">
                <?php
                $related_posts = aya_get_related_posts(0, 5);
                $li_format = '<li class="mb-1"><a href="%s" title="%s" class="hover:text-primary transition-colors duration-300">%s</a></li>';

                if ($related_posts === false) {
                    aya_echo('<li>' . __('暂无相关文章', 'AIYA') . '</li>');
                } else {
                    foreach ($related_posts as $post => $post_data) {
                        aya_echo(sprintf($li_format, $post_data['url'], $post_data['attr_title'], $post_data['title']));
                    }
                }
                ?>
            </ul>
        </div>
    </div>
<?php endif; ?>