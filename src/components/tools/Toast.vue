<script setup lang="ts">
import { ref, onMounted } from "vue";

const props = defineProps({
    message: { type: String, default: "" },
    duration: { type: Number, default: 3000 },
});

const visible = ref(true);
const gradientStyle = ref("");

//随机背景色
function randomGradient() {
    const hues = [200, 220, 260, 140, 30, 340];
    const angle = Math.floor(Math.random() * 360);
    const color1 = `hsl(${hues[Math.floor(Math.random() * hues.length)]}, 70%, 60%)`;
    const color2 = `hsl(${hues[Math.floor(Math.random() * hues.length)]}, 80%, 50%)`;

    return `linear-gradient(${angle}deg, ${color1}, ${color2})`;
}
//自动关闭
onMounted(() => {
    gradientStyle.value = randomGradient();

    if (props.duration > 0) {
        setTimeout(() => (visible.value = false), props.duration);
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
        :duration="{ enter: 300, leave: 200 }">
        <div
            v-if="visible"
            :style="{ background: gradientStyle }"
            class="fixed bottom-6 right-6 z-50 min-w-48 max-w-xs rounded text-base font-semibold text-white px-4 py-3 shadow-lg flex items-center gap-2">
            {{ message }}
            <button
                class="ml-auto text-white/60 hover:text-white"
                @click="visible = false">
                &times;
            </button>
        </div>
    </Transition>
</template>
