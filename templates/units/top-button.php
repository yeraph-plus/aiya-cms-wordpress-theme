<!-- scroll button -->
<div class="fixed bottom-8 ltr:right-6 rtl:left-6 z-50" x-data="scrollToTop">
    <template x-if="showTopButton">
        <button type="button" class="btn btn-outline-primary rounded-full p-3 animate-pulse bg-[#fafafa] dark:bg-[#060818] dark:hover:bg-primary" @click="goToTop">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
                <line x1="12" y1="19" x2="12" y2="5"></line>
                <polyline points="5 12 12 5 19 12"></polyline>
            </svg>
        </button>
    </template>
</div>
<script>
    document.addEventListener("alpine:init", () => {
        Alpine.data("scrollToTop", () => ({
            showTopButton: false,
            init() {
                window.onscroll = () => {
                    this.scrollFunction();
                };
            },

            scrollFunction() {
                if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                    this.showTopButton = true;
                } else {
                    this.showTopButton = false;
                }
            },

            goToTop() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            },
        }));
    });
</script>