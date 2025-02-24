<!-- info box -->
<div class="fixed bottom-0 p-4 w-[260px] z-50" :class="[$store.app.navbarMenu === 'vertical' ? '' : 'hidden', $store.app.menuBar ? 'hidden' : '']">
    <div class="border border-gray-500/20 bg-white dark:bg-[#0e1726] rounded-md p-6 pt-12 mt-8 relative">
        <div class="text-primary mb-5">
            <i data-feather="award" width="24" height="24" stroke-width="2"></i>
        </div>
        <h5 class="text-dark font-semibold mb-2 dark:text-white-light">AIYA-CMS PRO</h5>
        <p class="text-white-dark mb-2">一种很新的旧 WordPress 主题</p>
        <a href="#" class="text-primary font-semibold hover:underline group">link</a>
    </div>
</div>