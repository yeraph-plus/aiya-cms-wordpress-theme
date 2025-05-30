<?php
/**
 * Template Name: 全宽页面
 *  
 **/

aya_template_load('part/header');
?>
<main class="flex-1 flex flex-col">
    <!-- Full Width Post Page -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <?php aya_while_have_content(); ?>
    </div>
</main>
<?php aya_template_load('part/footer'); ?>