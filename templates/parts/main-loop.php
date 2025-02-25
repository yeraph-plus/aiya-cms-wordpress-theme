<!-- Loop post grid -->
<div class="relative grid grid-cols-1 gap-4" :class="[$store.app.loopGridClass]">
    <?php aya_while_have_post(); ?>
</div>
<?php
//分页
aya_template_load('parts/pagination');
