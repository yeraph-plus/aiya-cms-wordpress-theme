<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";

const props = defineProps({
    message: { type: String, default: "" },
    duration: { type: Number, default: 3000 },
    type: { type: String, default: "info" }, // 可选值: info, success, warning, error
});

const emit = defineEmits(['vanish']);

const visible = ref(true);

//消息颜色样式
const toastClass = computed(() => {
    switch (props.type) {
        case "success":
            return "alert-success";
        case "warning":
            return "alert-warning";
        case "error":
            return "alert-error";
        default:
            return "alert-info";
    }
});

//监听可见状态变化
watch(visible, (newValue) => {
    if (!newValue) {
        // 给动画一些时间完成
        setTimeout(() => emit('vanish'), 200);
    }
});

//自动关闭
onMounted(() => {
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
            class="toast toast-end toast-bottom z-50">
            <div :class="['alert', toastClass]">
                <span class="font-semibold">{{ message }}</span>
                <button
                    class="btn btn-sm btn-circle btn-ghost"
                    @click="visible = false">
                    ✕
                </button>
            </div>
        </div>
    </Transition>
</template>
