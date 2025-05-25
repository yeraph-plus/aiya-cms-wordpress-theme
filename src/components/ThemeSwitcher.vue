<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
//Heroicons
import { SunIcon, MoonIcon } from "@heroicons/vue/24/outline";

//当前主题
const currentTheme = ref("light");
//可用主题列表
const availableThemes = [
    "light",
    "dark"
];

//循环切换
const toggleTheme = () => {
    const currentIndex = availableThemes.findIndex((t) => t === currentTheme.value);

    // 计算下一个主题的索引（循环）
    const nextIndex = (currentIndex + 1) % availableThemes.length;

    // 设置为下一个主题
    setTheme(availableThemes[nextIndex]);
};

// 设置主题
const setTheme = (theme) => {
    // 保存到 localStorage
    localStorage.setItem("theme", theme);
    applyTheme(theme);
};

// 应用主题
const applyTheme = (theme) => {
    currentTheme.value = theme;

    // 根元素添加 data-theme 属性（用于 DaisyUI）
    document.documentElement.setAttribute("data-theme", theme);

    // 处理深色模式 class
    if (theme === "dark") {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
};

// 系统暗色模式状态
const isSystemDarkMode = ref(false);

// 监听系统颜色方案变化
const setupSystemModeListener = () => {
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    isSystemDarkMode.value = mediaQuery.matches;

    // 监听系统颜色方案变化
    mediaQuery.addEventListener("change", (e) => {
        isSystemDarkMode.value = e.matches;

        // 如果没有保存的主题，则使用系统默认
        if (!localStorage.getItem("theme")) {
            applyTheme(isSystemDarkMode.value ? "dark" : "light");
        }
    });
};

// 初始加载主题
onMounted(() => {
    setupSystemModeListener();

    // 从 localStorage 获取用户保存的主题
    const savedTheme = localStorage.getItem("theme");

    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        // 如果没有保存的主题，则使用系统默认
        applyTheme(isSystemDarkMode.value ? "dark" : "light");
    }
});
</script>
<template>
    <div
        class="theme-switcher tooltip tooltip-bottom"
        :data-tip="$t('theme_switch')">
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
