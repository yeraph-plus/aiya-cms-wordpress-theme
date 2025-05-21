<script setup lang="ts">
import { ref, computed } from "vue";
//Heroicons
import { BellIcon, BellAlertIcon, BellSlashIcon } from "@heroicons/vue/24/outline";
import { CheckCircleIcon, InformationCircleIcon, ExclamationTriangleIcon, XCircleIcon, ChatBubbleLeftRightIcon } from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";

//IconMap
const typeIconMap = {
    success: CheckCircleIcon,
    info: InformationCircleIcon,
    warning: ExclamationTriangleIcon,
    error: XCircleIcon,
    message: ChatBubbleLeftRightIcon,
};

//Data
const props = defineProps({
    data: {
        type: Object,
        default: () => {},
    },
});

//将对象循环为数组
const notifyList = computed(() => {
    if (!props.data) {
        return [];
    } else {
        return Object.keys(props.data).map((key) => ({
            id: key,
            ...props.data[key],
        }));
    }
});

const hasNewNotify = computed(() => notifyList.value.length > 0);
</script>

<template>
    <div class="dropdown dropdown-end">
        <!-- 通知按钮 -->
        <div
            tabindex="0"
            role="button"
            class="btn btn-ghost hidden lg:flex">
            <!-- 减小内边距和按钮大小 -->
            <component
                :is="hasNewNotify ? BellAlertIcon : BellIcon"
                class="size-5"
                :class="hasNewNotify ? 'animate-shake' : ''"
                aria-hidden="true" />
            <span class="hidden sm:inline-flex items-center">
                <ChevronDownIcon
                    class="size-5 opacity-40"
                    aria-hidden="true" />
            </span>
        </div>

        <!-- 下拉内容 -->
        <div
            tabindex="0"
            class="dropdown-content z-[1] menu p-0 shadow bg-base-100 rounded-box w-72 max-h-96 overflow-auto">
            <div class="bg-base-100 rounded-box">
                <!-- 通知列表 -->
                <template v-if="notifyList.length">
                    <div class="divide-y divide-base-200">
                        <div
                            v-for="(note, idx) in notifyList"
                            :key="idx"
                            class="flex items-start gap-3 p-4 hover:bg-base-200 transition-colors">
                            <component
                                :is="typeIconMap[note.icon] || InformationCircleIcon"
                                class="self-center h-6 w-6 flex-shrink-0"
                                :class="{
                                    'text-success': note.icon === 'success',
                                    'text-info': note.icon === 'info',
                                    'text-warning': note.icon === 'warning',
                                    'text-error': note.icon === 'error',
                                    'text-base-content opacity-60': note.icon === 'message' || !note.icon,
                                }"
                                aria-hidden="true" />
                            <div class="flex-1 min-w-0">
                                <div
                                    class="font-medium text-base-content py-2"
                                    v-html="note.title"></div>
                                <div
                                    class="text-sm text-base-content opacity-70"
                                    v-html="note.content"></div>
                                <div class="text-xs text-base-content opacity-50 mt-1">{{ note.time }}</div>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- 空状态 -->
                <div
                    v-else
                    class="p-4 text-center text-base-content opacity-50">
                    <span>{{ $t("empty_note") }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
