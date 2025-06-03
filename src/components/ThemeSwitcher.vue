<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
//Heroicons
import { SunIcon, MoonIcon } from "@heroicons/vue/24/outline";

const { t } = useI18n();

declare global {
    interface Window {
        currentTheme?: string;
        toggleTheme: () => void;
    }
}

// 当前主题状态
const currentTheme = ref(window.currentTheme || "light");

// 监听全局主题变化
const updateThemeState = () => {
    currentTheme.value = window.currentTheme || "dark";
};

// 切换主题
const toggleTheme = () => {
    // 调用全局主题切换函数
    window.toggleTheme();
    // 更新组件状态
    updateThemeState();
};

// 组件挂载时，确保状态与全局同步
onMounted(() => {
    updateThemeState();

    // 监听主题变化事件（如果你想要支持多组件同步）
    window.addEventListener("theme-changed", updateThemeState);
});
</script>
<template>
    <div
        class="theme-switcher tooltip tooltip-bottom"
        :data-tip="t('theme_switch')">
        <!-- Theme Switcher -->
        <button
            @click="toggleTheme"
            class="btn btn-square btn-ghost"
            area-label="Toggle Theme">
            <span
                v-if="currentTheme === 'light'"
                class="inline-block">
                <SunIcon class="w-5 h-5" />
            </span>
            <span
                v-else
                class="inline-block">
                <MoonIcon class="w-5 h-5" />
            </span>
        </button>
    </div>
</template>
