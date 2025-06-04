<script setup lang="ts">
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
//Toast
import { toast } from "../scripts/toast-plugin";
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
            return "badge-error";
        case "author":
            return "badge-info";
        case "sponsor":
            return "badge-primary";
        case "subscriber":
            return "badge-neutral";
        case "guest":
        default:
            return "badge-ghost";
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
    <div class="dropdown dropdown-end">
        <button
            tabindex="0"
            class="btn btn-ghost">
            <img
                :src="props.data.avatar"
                alt="avatar"
                class="size-8 rounded-full border border-base-300 object-cover bg-base-200" />
            <span class="max-w-0 lg:max-w-[8em] truncate font-medium">{{ props.data.name }}</span>
            <ChevronDownIcon
                class="size-5 opacity-40"
                aria-hidden="true" />
        </button>
        <div
            tabindex="0"
            class="dropdown-content z-[1] p-0 mt-4 shadow-md overflow-auto">
            <div class="w-56 bg-base-100 rounded-box">
                <!-- User Info -->
                <div class="p-4 border-b border-base-200">
                    <div class="flex items-center gap-3">
                        <img
                            :src="props.data.avatar"
                            alt="avatar"
                            class="size-12 rounded-full border border-base-300 object-cover bg-base-200" />
                        <div class="flex-1 min-w-0">
                            <div class="text-base font-semibold pb-1">
                                {{ props.data.name }}
                                <span
                                    class="badge badge-sm"
                                    :class="badgeColorClass(props.data.role)">
                                    {{ t(props.data.role) }}
                                </span>
                            </div>
                            <div class="text-xs text-nowrap text-base-content opacity-60 break-all">{{ props.data.email }}</div>
                        </div>
                    </div>
                </div>
                <!-- User Menu -->
                <ul class="menu w-full p-2">
                    <li
                        v-for="(item, idx) in props.menus"
                        :key="idx">
                        <a
                            :href="item.url"
                            class="flex items-center gap-2 p-2 transition-colors">
                            <component
                                :is="typeIconMap[item.icon] || typeIconMap['link']"
                                class="size-5 opacity-60"
                                aria-hidden="true" />
                            <span v-html="item.label"></span>
                        </a>
                    </li>
                    <!-- Logout -->
                    <li class="mt-2 border-t border-base-200">
                        <a
                            href="#"
                            @click.prevent="handleLogout"
                            class="flex items-center gap-2 p-2 transition-colors text-error hover:bg-error hover:text-white">
                            <ArrowLeftStartOnRectangleIcon
                                class="size-5 opacity-60"
                                aria-hidden="true" />
                            <span>{{ t("log_out") }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
