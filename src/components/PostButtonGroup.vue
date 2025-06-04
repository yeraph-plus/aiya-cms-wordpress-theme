<script setup lang="ts">
import { ref, onMounted } from "vue";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
//Toast
import { toast } from "../scripts/toast-plugin";
//Heroicons
import { CheckIcon, HeartIcon, BookmarkIcon } from "@heroicons/vue/24/outline";

//Props
const props = defineProps({
    ajaxUrl: {
        type: String,
        default: "",
        required: true,
    },
    postId: {
        type: Number || String,
        default: "0",
        required: true,
    },
    likeCount: {
        type: Number,
        default: 0,
    },
    nonce: {
        type: String,
        default: "",
        required: true,
    },
});

//状态
const likes = ref(props.likeCount);
const hasLiked = ref(false);

const postId = String(props.postId);

const isFavorite = ref(false);
const isLikeLoading = ref(false);
const isFavoriteLoading = ref(false);

//创建存储的键名
const LIKED_POSTS_STORAGE = "aya_liked_posts";

//获取数组
const getLikedPosts = (): string[] => {
    const storedLikes = localStorage.getItem(LIKED_POSTS_STORAGE);
    if (storedLikes) {
        try {
            return JSON.parse(storedLikes);
        } catch (e) {
            return [];
        }
    }
    return [];
};

//保存数组
const saveLikeStatus = () => {
    try {
        const likedPosts = getLikedPosts();

        if (!likedPosts.includes(postId)) {
            likedPosts.push(postId);
            localStorage.setItem(LIKED_POSTS_STORAGE, JSON.stringify(likedPosts));
        }
    } catch (e) {
        console.error("Save localStorage Failed:", e);
    }
};

//点赞状态
const checkLocalLikeStatus = () => {
    try {
        // 获取已点赞文章ID数组
        const likedPosts = getLikedPosts();
        // 检查当前文章是否在已点赞列表中
        hasLiked.value = likedPosts.includes(postId);
    } catch (error) {
        console.error("Get localStorage Failed:", error);
        hasLiked.value = false;
    }
};

//点赞请求
const handleLike = async () => {
    if (isLikeLoading.value || hasLiked.value) return;

    isLikeLoading.value = true;

    try {
        const formData = new FormData();
        formData.append("action", "click_likes");
        formData.append("nonce", props.nonce);
        formData.append("post_id", postId);

        const response = await fetch(props.ajaxUrl, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        });

        const data = await response.json();

        if (data.status === "done") {
            likes.value = Number(likes.value) + 1;

            //保存文章ID到本地
            saveLikeStatus();

            hasLiked.value = true;
        } else {
            toast(data.message || t("click_operation_failed"), { type: "error" });
        }
    } catch (error) {
        toast(t("network_error"), { type: "error" });
        console.error("Action Likes Failed:", error);
    } finally {
        //重置加载
        isLikeLoading.value = false;
    }
};

//收藏请求
const handleFavorite = async () => {
    if (isFavoriteLoading.value) return;

    isFavoriteLoading.value = true;

    try {
        const formData = new FormData();
        formData.append("action", "click_favorites");
        formData.append("nonce", props.nonce);
        formData.append("post_id", postId);

        const response = await fetch(props.ajaxUrl, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        });

        const data = await response.json();

        if (data.status === "added") {
            isFavorite.value = true;
            toast(data.message, { type: "success" });
        } else if (data.status === "removed") {
            isFavorite.value = false;
            toast(data.message, { type: "info" });
        } else {
            toast(data.message || t("click_must_logged"), { type: "error" });
        }
    } catch (error) {
        toast(t("network_error"), { type: "error" });
        console.error("Action Favorites Failed:", error);
    } finally {
        //重置加载
        isFavoriteLoading.value = false;
    }
};

//检查已收藏
const checkUserFavorite = async () => {
    try {
        const formData = new FormData();
        formData.append("action", "query_favorites");
        formData.append("nonce", props.nonce);
        formData.append("post_id", postId);

        const response = await fetch(props.ajaxUrl, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        });

        const data = await response.json();

        if (data.status === "favorited") {
            isFavorite.value = true;
        } else {
            isFavorite.value = false;
        }
    } catch (error) {
        console.error("Query Favorites Failed:", error);
        //默认值
        isFavorite.value = false;
    }
};

//初始化
onMounted(() => {
    checkUserFavorite();
    checkLocalLikeStatus();
});
</script>

<template>
    <div class="flex items-center justify-center gap-2 p-6">
        <button
            class="btn lg:btn-wide btn-primary gap-2"
            :class="{
                'btn-disabled': isLikeLoading || hasLiked,
                'animate-pulse': isLikeLoading,
                'btn-success': hasLiked,
            }"
            @click="handleLike"
            :title="hasLiked ? t('click_liked') : t('click_like')">
            <span class="inline-flex items-center justify-center">
                <HeartIcon
                    v-if="!hasLiked"
                    class="size-5 mr-2"
                    aira-hidden="true" />
                <CheckIcon
                    v-else
                    class="size-5 mr-2"
                    aria-hidden="true" />
                {{ hasLiked ? t("click_liked") : t("click_like") }}
            </span>
            <span class="badge badge-sm">{{ likes }}</span>
        </button>
        <button
            class="btn lg:btn-wide btn-primary gap-2"
            :class="{
                'btn-disabled': isFavoriteLoading,
                'animate-pulse': isFavoriteLoading,
                'btn-warning': isFavorite,
                'btn-outline': !isFavorite,
            }"
            @click="handleFavorite"
            :title="t('click_collect')">
            <span class="inline-flex items-center justify-center">
                <BookmarkIcon
                    v-if="!isFavorite"
                    class="size-5 mr-2"
                    aria-hidden="true" />
                <CheckIcon
                    v-else
                    class="size-5 mr-2"
                    aria-hidden="true" />
                {{ isFavorite ? t("click_collected") : t("click_collect") }}
            </span>
            <span></span>
        </button>
    </div>
</template>
