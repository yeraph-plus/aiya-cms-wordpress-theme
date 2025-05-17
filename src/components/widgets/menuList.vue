<script setup lang="ts">
import { computed, ref } from "vue";
//Hendless UI
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
//Heroicons
//import { MenuIcon, XIcon } from "@heroicons/vue/outline";

const props = defineProps({

    data: {
        type: Object,
        required: true,
        default: () => ({
            menu_items: '{}', // 默认值为空 JSON 字符串
        }),
    },
});
//解析菜单数据
const menuItems = computed(() => {
    if (!props.data || !props.data.menu_items) {
        console.error('Invalid data or menu_items is missing');
        return {};
    }

    try {
        return JSON.parse(props.data.menu_items); // 将 JSON 字符串解析为对象
    } catch (error) {
        console.error('Invalid JSON format in menu_items:', error);
        return {};
    }
});

const mobileNavOpen = ref(false);

//@apply
</script>

<style scoped>
@reference "tailwindcss";
</style>
<template>
    <!-- Navigation -->
    <nav class="hidden items-center gap-2 lg:flex">
        <a v-for="(item, key) in menuItems" :key="key" :href="item.url" :class="[
            'group flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-medium',
            item.is_active
                ? 'border-blue-100 bg-blue-50 text-blue-600 dark:border-transparent dark:bg-gray-700 dark:text-white'
                : 'border-transparent text-gray-800 hover:bg-blue-50 hover:text-blue-600 active:border-blue-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white dark:active:border-gray-600',
        ]">
            <span>{{ item.label }}</span>
        </a>
    </nav>
</template>