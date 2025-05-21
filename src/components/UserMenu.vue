<script setup lang="ts">
import { ref } from "vue";

//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();

//Toast
import { toast } from "./tools/ToastPlugin";
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
            return "badge-success";
        case "author":
            return "badge-error";
        case "sponsor":
            return "badge-info";
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
        <!-- 头像按钮 -->
        <div
            tabindex="0"
            role="button"
            class="btn btn-ghost">
            <img
                :src="props.data.avatar"
                alt="avatar"
                class="size-8 rounded-full border border-base-300 object-cover bg-base-100" />
            <ChevronDownIcon
                class="size-5 opacity-40"
                aria-hidden="true" />
        </div>

        <!-- 下拉菜单 -->
        <div
            tabindex="0"
            class="dropdown-content z-[1] mt-2 w-56 rounded-box shadow-lg bg-base-100">
            <!-- 用户信息 -->
            <div class="p-4 border-b border-base-200">
                <div class="flex items-center gap-3">
                    <img
                        :src="props.data.avatar"
                        alt="avatar"
                        class="size-12 rounded-full border border-base-300 object-cover bg-base-100" />
                    <div class="flex-1 min-w-0">
                        <div class="text-base font-semibold pb-1">
                            {{ props.data.name }}
                            <span
                                class="badge badge-sm"
                                :class="badgeColorClass(props.data.role)">
                                {{ $t(props.data.role) }}
                            </span>
                        </div>
                        <div class="text-xs text-nowrap text-base-content opacity-60 break-all">{{ props.data.email }}</div>
                    </div>
                </div>
            </div>
            <!-- 菜单项 -->
            <ul class="menu w-full p-2">
                <li
                    v-for="(item, idx) in props.menus"
                    :key="idx">
                    <a
                        :href="item.url"
                        class="flex items-center gap-2 p-2 hover:bg-base-200 rounded-lg transition-colors">
                        <component
                            :is="typeIconMap[item.icon] || typeIconMap['link']"
                            class="size-5 opacity-60"
                            aria-hidden="true" />
                        <span>{{ item.label }}</span>
                    </a>
                </li>
            </ul>

            <!-- 登出按钮 -->
            <div class="p-2 border-t border-base-200">
                <a
                    href="#"
                    @click.prevent="handleLogout"
                    class="btn btn-ghost text-error justify-start w-full">
                    <ArrowLeftStartOnRectangleIcon
                        class="size-5 opacity-60"
                        aria-hidden="true" />
                    <span>{{ $t("log_out") }}</span>
                </a>
            </div>
        </div>
    </div>
</template>
