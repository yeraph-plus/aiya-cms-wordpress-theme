<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";
//Heroicons
import { MagnifyingGlassIcon, XMarkIcon } from "@heroicons/vue/24/outline";

const { t } = useI18n();

//存储搜索关键词
const searchQuery = ref("");
const searchInput = ref<HTMLInputElement | null>(null);

//判断是否有搜索内容
const hasSearchQuery = computed(() => searchQuery.value.trim().length > 0);

//清空搜索内容
function clearSearch() {
    searchQuery.value = "";
    // 聚焦输入框
    if (searchInput.value) {
        searchInput.value.focus();
    }
}

//处理快捷键
function handleKeyDown(event: KeyboardEvent) {
    //检测 Ctrl+K 或 Command+K 组合键
    if ((event.ctrlKey || event.metaKey) && event.key === "k") {
        event.preventDefault();
        if (searchInput.value) {
            searchInput.value.focus();
        }
    }
    //按ESC键清空搜索框
    if (event.key === "Escape" && hasSearchQuery.value) {
        clearSearch();
    }
}

//添加和移除全局键盘事件监听
onMounted(() => {
    document.addEventListener("keydown", handleKeyDown);
});
onUnmounted(() => {
    document.removeEventListener("keydown", handleKeyDown);
});
</script>

<template>
    <div class="form-control w-full max-w-md mx-auto">
        <form
            action="/"
            method="get"
            class="relative group">
            <input
                ref="searchInput"
                type="search"
                name="s"
                v-model.trim="searchQuery"
                :placeholder="t('search_placeholder')"
                class="input input-bordered w-full bg-base-200 rounded focus:ring-2 focus:ring-primary/30 transition-all" />
            <button
                type="submit"
                class="btn btn-primary btn-circle btn-sm absolute right-1 top-1/2 -translate-y-1/2 opacity-0 scale-90 transition-all duration-200 group-focus-within:opacity-100 group-focus-within:scale-100"
                :class="{ 'opacity-100 scale-100': hasSearchQuery }"
                :disabled="!hasSearchQuery">
                <MagnifyingGlassIcon
                    class="size-4"
                    aria-hidden="true" />
            </button>
        </form>
    </div>
</template>
