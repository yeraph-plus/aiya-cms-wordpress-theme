<main class="flex-1 flex flex-col">
    <?php aya_template_load('part/banner'); ?>
    <!-- Content Container -->
    <div class="container mx-auto p-4 transition-all duration-300 ease-in-out">
        <?php aya_carousel_component(); ?>
        <?php aya_while_the_post(); ?>
    </div>
</main>