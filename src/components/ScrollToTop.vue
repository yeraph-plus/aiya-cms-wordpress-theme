<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
//Heroicons
import { ArrowUpIcon } from "@heroicons/vue/24/outline";

const showBackToTop = ref(false);

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
});
onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});

//滚动事件处理
function handleScroll() {
    showBackToTop.value = window.scrollY > 100;
}

//返回顶部
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
}
</script>

<template>
    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-75 translate-y-10"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-75 translate-y-10">
        <div
            v-show="showBackToTop"
            class="fixed bottom-4 right-4 p-2 z-50">
            <button
                @click="scrollToTop"
                class="btn w-12 h-12 btn-circle btn-primary shadow-lg relative transition-transform duration-300 hover:-translate-y-1 active:translate-y-0">
                <ArrowUpIcon class="w-6 h-6" />
            </button>
        </div>
    </transition>
</template>
