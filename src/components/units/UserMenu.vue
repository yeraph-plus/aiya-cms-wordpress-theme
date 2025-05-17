<script setup lang="ts">
import { ref } from "vue";
//Toast
import { toast } from "../tools/ToastPlugin";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
//Headless UI
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
//Heroicons
import { UserIcon, LinkIcon, BoltIcon, InboxIcon, Squares2X2Icon, ArrowLeftStartOnRectangleIcon } from "@heroicons/vue/24/outline";

import { ChevronDownIcon } from "@heroicons/vue/20/solid";

//IconMap
const typeIconMap = {
    profile: UserIcon,
    sponsor: BoltIcon,
    inbox: InboxIcon,
    dashboard: Squares2X2Icon,
    link: LinkIcon,
};
//Data
const props = defineProps({
    data: {
        type: Object,
        default: () => [],
    },
    menus: {
        type: Object,
        default: () => [],
    },
    rest_nonce: {
        type: String,
        default: "",
    },
});

//用户徽章
function badgeColorClass(role) {
    switch (role) {
        case "administrator":
            return "border-green-200 bg-green-100 text-green-700 dark:border-green-700 dark:bg-green-700 dark:text-green-50";
        case "author":
            return "border-red-200 bg-red-100 text-red-700 dark:border-red-700 dark:bg-red-700 dark:text-red-50";
        case "sponsor":
            return "border-blue-200 bg-blue-100 text-blue-700 dark:border-blue-700 dark:bg-blue-700 dark:text-blue-50";
        case "subscriber":
            return "border-gray-200 bg-gray-100 text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200";
        case "guest":
        default:
            return "border-gray-200 bg-gray-100 text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200";
    }
}

async function handleLogout() {
    try {
        const res = await fetch("/api/aiya/v1/logout", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.rest_nonce,
            },
            credentials: "include",
        });
        const data = await res.json();

        if (data.success) {
            toast(t("logged_out_success"));
            //等待
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            toast(data.data?.message || t("logged_out_error"));
        }
    } catch (err) {
        console.error(err);
    }
}
</script>

<template>
    <Menu
        as="div"
        class="relative inline-block">
        <MenuButton class="inline-flex items-center justify-center gap-2 sm:px-3 my-2 text-sm rounded-lg leading-5 font-semibold text-gray-800 hover:border-gray-300 hover:text-gray-900 hover:shadow-xs focus:ring-3 focus:ring-gray-300/25 active:border-gray-200 active:shadow-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:text-gray-200 dark:focus:ring-gray-600/40 dark:active:border-gray-700">
            <img
                :src="props.data.avatar"
                alt="avatar"
                class="hi-mini hi-user-circle inline-block bg-white flex-shrink-0 size-8.5 rounded-full border border-gray-200 object-cover" />
            <ChevronDownIcon
                class="hi-mini hi-chevron-down size-5 opacity-40"
                aria-hidden="true" />
        </MenuButton>

        <!-- Dropdown -->
        <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90">
            <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-lg shadow-xl focus:outline-hidden dark:shadow-gray-900">
                <div class="divide-y divide-gray-100 rounded-lg bg-white ring-1 ring-black/5 dark:divide-gray-700 dark:bg-gray-800 dark:ring-gray-700">
                    <div class="space-y-1">
                        <!-- User Info -->
                        <div class="flex items-center gap-3 p-4 pb-4">
                            <img
                                :src="props.data.avatar"
                                alt="avatar"
                                class="size-12 rounded-full border border-gray-200 object-cover bg-white dark:border-gray-700" />
                            <div class="flex-1 min-w-0">
                                <div class="text-base font-semibold pb-1 text-gray-900 dark:text-gray-100">
                                    {{ props.data.name }}
                                    <span
                                        class="inline-flex rounded-sm border px-1.5 py-0.5 text-xs leading-4 font-sans"
                                        :class="badgeColorClass(props.data.role)">
                                        {{ $t(props.data.role) }}
                                    </span>
                                </div>
                                <div class="text-xs text-nowrap text-gray-500 dark:text-gray-400 break-all">{{ props.data.email }}</div>
                            </div>
                        </div>
                    </div>
                    <!-- User Menu -->
                    <div class="space-y-1 p-2.5">
                        <MenuItem
                            v-for="(item, idx) in props.menus"
                            :key="idx"
                            v-slot="{ active }">
                            <a
                                :href="item.url"
                                class="group flex items-center justify-between gap-2 rounded-lg border border-transparent px-2.5 py-2 text-sm font-medium"
                                :class="{
                                    'bg-blue-50 text-blue-800 dark:border-transparent dark:bg-gray-700/75 dark:text-white': active,
                                    'text-gray-700 hover:bg-blue-50 hover:text-blue-800 active:border-blue-100 dark:text-gray-200 dark:hover:bg-gray-700/75 dark:hover:text-white dark:active:border-gray-600': !active,
                                }">
                                <component
                                    :is="typeIconMap[item.icon] || typeIconMap['link']"
                                    class="inline-block size-5 flex-none opacity-50"
                                    aria-hidden="true" />
                                <span class="grow">{{ item.label }}</span>
                            </a>
                        </MenuItem>
                    </div>
                    <div class="space-y-1 p-2.5">
                        <MenuItem v-slot="{ active }">
                            <button
                                type="button"
                                @click="handleLogout"
                                class="group flex items-center justify-between gap-2 rounded-lg border border-transparent px-2.5 py-2 text-sm font-medium w-full text-left"
                                :class="{
                                    'bg-red-50 text-red-400 dark:border-transparent dark:bg-gray-700/75 dark:text-white': active,
                                    'text-gray-700 hover:bg-blue-50 hover:text-red-800 active:border-red-100 dark:text-gray-200 dark:hover:bg-gray-700/75 dark:hover:text-white dark:active:border-gray-600': !active,
                                }">
                                <ArrowLeftStartOnRectangleIcon
                                    class="hi-mini hi-lock-closed inline-block size-5 flex-none opacity-25 group-hover:opacity-50"
                                    aria-hidden="true" />
                                <span class="grow">
                                    {{ $t("log_out") }}
                                </span>
                            </button>
                        </MenuItem>
                    </div>
                </div>
            </MenuItems>
        </Transition>
    </Menu>
</template>
