<?php

if (!defined('ABSPATH')) {
    exit;
}

?>
<main class="flex-1 flex flex-col">
    <!-- General Archive -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_template_load('part/breadcrumb'); ?>
        <?php aya_while_the_post(); ?>
    </div>
</main>