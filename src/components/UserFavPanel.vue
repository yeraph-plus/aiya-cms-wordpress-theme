<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useI18n } from "vue-i18n";
import { toast } from "../scripts/toast-plugin";
import { HeartIcon, TrashIcon, InboxIcon, ArrowRightIcon } from "@heroicons/vue/24/outline";

const { t } = useI18n();
//Data
const props = defineProps({
    posts: {
        type: Object,
        default: () => ({}),
        required: true,
    },
    ajax_url: {
        type: String,
        default: "",
        required: true,
    },
    ajax_nonce: {
        type: String,
        default: "",
        required: false,
    },
});

//转换为数组渲染
const favoriteItems = ref<any[]>([]);
const isLoading = ref(false);

//计算属性
const isLoggedIn = computed(() => props.ajax_nonce !== "");
const isEmpty = computed(() => favoriteItems.value.length === 0);

//徽章状态映射
const statusClassMap = {
    sticky: "badge-primary",
    newest: "badge-secondary",
    password: "badge-neutral",
    private: "badge-neutral",
    pending: "badge-info",
    future: "badge-info",
    draft: "badge-info",
    trash: "badge-error",
};
const getStatusClass = (statusKey: string) => {
    return statusClassMap[statusKey] || "badge-neutral";
};

//列表数据
const initFavoriteList = () => {
    // 如果未登录，不初始化列表
    if (!isLoggedIn.value) return;

    const postsData = props.posts;
    if (!postsData || Object.keys(postsData).length === 0) {
        favoriteItems.value = [];
        return;
    }

    favoriteItems.value = Object.values(postsData);
};

//移除收藏
const removeFavorite = async (postId: string) => {
    if (isLoading.value || !isLoggedIn.value) return;

    isLoading.value = true;

    try {
        const formData = new FormData();
        formData.append("action", "click_favorites");
        formData.append("nonce", props.ajax_nonce);
        formData.append("post_id", postId);

        const response = await fetch(props.ajax_url, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        });

        const data = await response.json();

        if (data.status === "removed") {
            // 更新本地列表
            favoriteItems.value = favoriteItems.value.filter((item) => item.id !== postId);
            toast.success(data.message || t("favorite_removed_success"));
        } else {
            toast.error(data.message || t("operation_failed"));
        }
    } catch (error) {
        console.error("Remove favorite failed:", error);
        toast.error(t("network_error"));
    } finally {
        isLoading.value = false;
    }
};

//初始化
onMounted(() => {
    initFavoriteList();
});
</script>

<template>
    <div class="relative p-4 flex items-center">
        <h3 class="text-xl md:text-xl font-bold">
            <InboxIcon class="inline size-6 mr-2" />
            {{ t("my_favorites") }}
        </h3>
        <span
            v-if="isLoggedIn && !isEmpty"
            class="badge badge-primary ml-2">
            {{ favoriteItems.length }}
        </span>
    </div>

    <div class="p-4 pt-0 space-y-4">
        <!-- is Logged -->
        <div
            v-if="!isLoggedIn"
            class="text-center py-10">
            <div class="flex flex-col items-center justify-center gap-4">
                <HeartIcon class="size-16 text-base-300" />
                <h3 class="text-xl font-semibold text-base-content/70">{{ t("please_login_first") }}</h3>
                <p class="text-base-content/60">{{ t("login_to_view_favorites") }}</p>
            </div>
        </div>
        <!-- loading dot -->
        <div
            v-else-if="isLoading && isEmpty"
            class="flex justify-center my-6">
            <span class="loading loading-dots loading-lg"></span>
        </div>
        <!-- is Empty -->
        <div
            v-else-if="isEmpty"
            class="text-center py-10">
            <div class="flex flex-col items-center justify-center gap-4">
                <HeartIcon class="size-16 text-base-300" />
                <h3 class="text-xl font-semibold text-base-content/70">{{ t("no_favorites_yet") }}</h3>
                <p class="text-base-content/60">{{ t("browse_and_add_favorites") }}</p>
            </div>
        </div>
        <!-- Table -->
        <div
            v-else
            class="overflow-x-auto w-full">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>
                            {{ t("title") }}
                        </th>
                        <th class="hidden md:table-cell">
                            {{ t("publish_date") }}
                        </th>
                        <th class="hidden md:table-cell">
                            {{ t("last_updated") }}
                        </th>
                        <th class="text-center">
                            {{ t("actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in favoriteItems"
                        :key="item.id"
                        class="hover">
                        <td>
                            <div class="flex flex-col">
                                <div class="flex flex-wrap items-center gap-1">
                                    <a
                                        :href="item.url"
                                        class="link link-hover font-medium">
                                        {{ item.title }}
                                    </a>
                                    <div
                                        v-if="typeof item.status === 'object'"
                                        v-for="(label, key) in item.status"
                                        :key="key"
                                        class="badge badge-xs"
                                        :class="getStatusClass(String(key))">
                                        {{ label }}
                                    </div>
                                    <div
                                        v-else-if="item.status"
                                        class="badge badge-xs"
                                        :class="getStatusClass(item.status)">
                                        {{ item.status }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell text-base-content/70">
                            <div class="flex items-center gap-1">
                                {{ item.date }}
                            </div>
                        </td>
                        <td class="hidden md:table-cell text-base-content/70">
                            <div
                                v-if="item.modified"
                                class="flex items-center gap-1">
                                {{ item.modified }}
                            </div>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <a
                                    :href="item.url"
                                    class="btn btn-link btn-xs">
                                    <ArrowRightIcon class="size-4" />
                                    {{ t("view_post") }}
                                </a>
                                <button
                                    @click="removeFavorite(item.id)"
                                    class="btn btn-secondary btn-xs"
                                    :disabled="isLoading">
                                    <TrashIcon class="size-4" />
                                    {{ t("remove_post") }}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
