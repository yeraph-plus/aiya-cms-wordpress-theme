<!-- Left sidebar menu overlay -->
<div x-cloak class="fixed inset-0 bg-[black]/60 z-50 lg:hidden" :class="{'hidden' : !$store.app.menuBar}" @click="$store.app.toggleMenuBar()"></div>
<div x-data="Swup4Control"></div>
<style>
    .lozad {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .lozad.loaded {
        opacity: 1;
    }
</style>