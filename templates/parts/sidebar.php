<div class="">
    <?php
    //小工具栏位
    aya_widget_bar();
    ?>
</div>
<script>
    document.addEventListener("alpine:init", () => {
        Alpine.data("dropdown", (initialOpenState = false) => ({
            open: initialOpenState,

            toggle() {
                this.open = !this.open;
            },
        }));
    });
</script>