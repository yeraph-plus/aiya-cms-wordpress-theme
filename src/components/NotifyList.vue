<script setup lang="ts">
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
//Heroicons
import { BellIcon, BellAlertIcon, BellSlashIcon } from "@heroicons/vue/24/outline";
import { CheckCircleIcon, InformationCircleIcon, ExclamationTriangleIcon, XCircleIcon, ChatBubbleLeftRightIcon } from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";

const { t } = useI18n();

//Data
const props = defineProps({
    data: {
        type: Object,
        default: {},
    },
});

//将对象循环为数组
const notifyList = computed(() => Object.values(props.data));

// 消息等级映射配置
const levelConfig = {
    success: {
        icon: CheckCircleIcon,
        bgColor: "bg-success/10",
        textColor: "text-success",
        borderColor: "border-success/20",
    },
    info: {
        icon: InformationCircleIcon,
        bgColor: "bg-info/10",
        textColor: "text-info",
        borderColor: "border-info/20",
    },
    warning: {
        icon: ExclamationTriangleIcon,
        bgColor: "bg-warning/10",
        textColor: "text-warning",
        borderColor: "border-warning/20",
    },
    error: {
        icon: XCircleIcon,
        bgColor: "bg-error/10",
        textColor: "text-error",
        borderColor: "border-error/20",
    },
    message: {
        icon: ChatBubbleLeftRightIcon,
        bgColor: "bg-base-100",
        textColor: "text-base-content",
        borderColor: "border-base-content/10",
    },
};

//计算消息内容的配色
const getNotifyConfig = (level) => {
    return levelConfig[level] || levelConfig.message;
};

const hasNewNotify = computed(() => notifyList.value.length > 0);
</script>

<template>
    <div class="dropdown dropdown-end">
        <button
            tabindex="0"
            class="btn btn-square btn-ghost md:h-auto md:w-auto md:aspect-auto md:min-h-[2.5rem] md:px-4">
            <component
                :is="hasNewNotify ? BellAlertIcon : BellIcon"
                class="size-5"
                :class="hasNewNotify ? 'animate-shake' : ''"
                aria-hidden="true" />
            <span class="hidden md:inline-flex items-center">
                <ChevronDownIcon
                    class="size-5 opacity-40"
                    aria-hidden="true" />
            </span>
        </button>
        <!-- Dropdown -->
        <div
            tabindex="0"
            class="dropdown-content z-[1] mt-4">
            <div class="w-80 bg-base-100 rounded-box overflow-hidden shadow-md">
                <div class="flex items-center justify-between p-4 border-b border-base-200">
                    <span class="text-base font-bold flex items-center gap-2">
                        {{ t("all_notifications") }}
                        <span class="badge badge-primary badge-sm">
                            {{ notifyList.length }}
                        </span>
                    </span>
                </div>
                <div class="overflow-y-auto max-h-80 custom-scrollbar">
                    <template v-if="hasNewNotify">
                        <div class="divide-y divide-base-100">
                            <div
                                v-for="(note, idx) in notifyList"
                                :key="idx"
                                class="p-3 transition-colors"
                                :class="[getNotifyConfig(note.level).bgColor]">
                                <div class="flex items-start gap-3">
                                    <component
                                        :is="getNotifyConfig(note.level).icon"
                                        class="self-center h-6 w-6 flex-shrink-0"
                                        :class="[getNotifyConfig(note.level).textColor]"
                                        aria-hidden="true" />
                                    <!-- Msg -->
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="font-medium py-1"
                                            :class="[getNotifyConfig(note.level).textColor]"
                                            v-html="note.title"></div>
                                        <div
                                            class="text-sm text-base-content opacity-80"
                                            v-html="note.content"></div>
                                        <div class="text-xs text-base-content opacity-50 mt-2 flex items-center justify-between">
                                            <span>{{ note.time }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!-- No Msg -->
                    <template v-else>
                        <div class="p-8 text-center text-base-content opacity-50 flex flex-col items-center gap-2">
                            <BellSlashIcon class="size-8 mb-2 opacity-30" />
                            <span>{{ t("empty_note") }}</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
