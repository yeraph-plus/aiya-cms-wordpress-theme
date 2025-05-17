<script setup lang="ts">
import { ref, computed } from "vue";
//Headless UI
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
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
        type: Array,
        default: () => [],
    },
});

//兼容处理字符串
const notifyList = computed(() => {
    let raw = props.data;

    if (typeof raw === "string") {
        try {
            raw = JSON.parse(raw);
        } catch {
            return [];
        }
    }

    if (!Array.isArray(raw)) {
        return [];
    }

    return raw;
});

const hasNewNotify = computed(() => notifyList.value.length > 0);

</script>

<template>
    <Popover
        v-slot="{ open }"
        class="relative inline-block">
        <PopoverButton class="hidden sm:inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm leading-5 font-semibold text-gray-800 hover:border-gray-300 hover:text-gray-900 hover:shadow-xs focus:ring-3 focus:ring-gray-300/25 active:border-gray-200 active:shadow-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:text-gray-200 dark:focus:ring-gray-600/40 dark:active:border-gray-700">
            <component
                :is="hasNewNotify ? BellAlertIcon : BellIcon"
                class="size-5"
                :class="hasNewNotify ? 'animate-shake' : ''"
                aria-hidden="true" />
            <ChevronDownIcon
                class="hi-mini hi-chevron-down size-5 opacity-40"
                aria-hidden="true" />
        </PopoverButton>

        <!-- Dropdown -->
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90">
            <PopoverPanel class="absolute right-0 z-10 mt-2 lg:w-80 w-64 max-h-96 overflow-auto origin-top-right rounded-lg shadow-xl focus:outline-hidden dark:shadow-gray-900">
                <div class="divide-y divide-gray-100 rounded-lg bg-white ring-1 ring-black/5 dark:divide-gray-700 dark:bg-gray-800 dark:ring-gray-700">
                    <template v-if="notifyList.length">
                        <div
                            class="flex items-start gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                            v-for="(note, idx) in notifyList"
                            :key="idx">
                            <component
                                :is="typeIconMap[note.icon] || InformationCircleIcon"
                                class="self-center h-6 w-6 flex-shrink-0"
                                :class="{
                                    'text-green-500': note.icon === 'success',
                                    'text-blue-500': note.icon === 'info',
                                    'text-yellow-500': note.icon === 'warning',
                                    'text-red-500': note.icon === 'error',
                                    'text-gray-400': note.icon === 'message' || !note.icon,
                                }"
                                aria-hidden="true" />
                            <div class="flex-1 min-w-0">
                                <div
                                    class="text-sm font-medium text-gray-900 dark:text-gray-100 py-2"
                                    v-html="note.title"></div>
                                <div
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    v-html="note.content"></div>
                                <div class="text-xs text-gray-400 mt-1">{{ note.time }}</div>
                            </div>
                        </div>
                    </template>
                    <div
                        v-else
                        class="p-4 text-center text-gray-400 dark:text-gray-500">
                        <span>{{ $t('empty_note') }}</span>
                    </div>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>
