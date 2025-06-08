<script setup lang="ts">
import Clipboard from "clipboard";
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";
//Heroicons
import { ClipboardIcon } from "@heroicons/vue/24/outline";
import { ArrowTopRightOnSquareIcon } from "@heroicons/vue/20/solid";
//Toast
import { toast } from "../scripts/toast-plugin";
//Props
const props = defineProps({
    title: {
        type: String,
        required: false,
        default: "",
    },
    content: {
        type: String,
        required: true,
        default: "",
    },
});

const { t } = useI18n();

//引用自己，用于自动 textarea 调整高度
const textareaRef = ref(null);
const copyBtnRef = ref(null);

const componentId = ref(`clipboard-${Date.now()}-${Math.floor(Math.random() * 1000)}`);

//创建剪贴板实例
let clipboardInstance: any = null;

// 计算属性
const isValidUrl = computed(() => {
    if (!props.content) return false;

    try {
        new URL(props.content);
        return true;
    } catch (e) {
        return false;
    }
});

// 处理复制成功事件
const handleCopySuccess = () => {
    toast.success(t("copy_done"));
};

// 组件挂载后
onMounted(() => {
    // 初始化剪贴板
    if (copyBtnRef.value) {
        clipboardInstance = new Clipboard(copyBtnRef.value);

        // 设置剪贴板事件
        clipboardInstance.on("success", (e) => {
            handleCopySuccess();
            e.clearSelection();
        });

        clipboardInstance.on("error", (e) => {
            console.error("ERROR:", e);
            toast.error(t("copy_failed"));
        });
    }
});

// 组件卸载前清理
onUnmounted(() => {
    if (clipboardInstance) {
        clipboardInstance.destroy();
    }
});
</script>

<template>
    <div
        :id="componentId"
        class="bg-base-200 rounded-lg my-4">
        <div
            v-if="props.title"
            class="text-base-content text-md font-semibold px-4 py-2">
            <span>{{ props.title }}</span>
        </div>
        <div class="clipboard-content">
            <textarea
                ref="textareaRef"
                class="textarea w-full h-auto min-h-[40px] text-sm font-base bg-base-100 border-0 rounded-none resize-none focus:outline-none"
                :value="content"
                readonly>
            </textarea>
        </div>

        <div class="flex justify-end text-md px-4 py-2 gap-4">
            <a
                v-if="isValidUrl"
                :href="content"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-sm btn-link">
                <ArrowTopRightOnSquareIcon class="size-4 inline-block mr-1" />
                <span>{{ t("copy_direct_new_tab") }}</span>
            </a>
            <button
                ref="copyBtnRef"
                class="btn btn-sm"
                :data-clipboard-text="content">
                <ClipboardIcon class="size-4 inline-block mr-1" />
                <span>{{ t("copy_to_clipboard") }}</span>
            </button>
        </div>
    </div>
</template>
