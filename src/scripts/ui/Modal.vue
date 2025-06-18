<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    message: {
        type: String,
        required: true,
    },
    Refresh: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "refresh"]);

const visible = ref(true);

function close() {
    visible.value = false;
    emit("close");
}

function refresh() {
    emit("refresh");
    close();
    // 添加这一行来实际刷新页面
    window.location.reload();
}

//处理点击
function handleXMarkClick() {
    if (props.Refresh) {
        refresh();
    } else {
        close();
    }
}

function handleBackdropClick() {
    if (props.Refresh) {
        refresh();
    } else {
        close();
    }
}

onMounted(() => {
    document.body.classList.add("overflow-hidden");
});

onBeforeUnmount(() => {
    document.body.classList.remove("overflow-hidden");
});
</script>

<template>
    <Transition
        name="modal-fade"
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform scale-95"
        enter-to-class="opacity-100 transform scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform scale-100"
        leave-to-class="opacity-0 transform scale-95">
        <div
            v-if="visible"
            class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                @click="handleBackdropClick"></div>
            <!-- Modal -->
            <div class="bg-base-100 rounded-lg max-w-md w-full mx-4 z-10 shadow-xl transform transition-all">
                <div class="flex justify-between items-center p-4 border-b border-base-300">
                    <h3 class="font-bold text-lg">{{ title }}</h3>
                    <button
                        @click="handleXMarkClick"
                        class="btn btn-ghost btn-sm btn-circle">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <div class="p-6">
                    <p class="whitespace-pre-line">{{ message }}</p>
                </div>
            </div>
        </div>
    </Transition>
</template>
