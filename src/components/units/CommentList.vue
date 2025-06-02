<script setup lang="ts">
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import CommentItem from "./CommentItem.vue";

const { t } = useI18n();

interface Comment {
    id: number | string;
    [key: string]: any;
}

const props = defineProps({
    comments: {
        type: Array,
        default: () => [],
    },
    commentsNum: {
        type: Number,
        default: 0,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: "",
    },
    settings: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["reply"]);

// 回复评论
function handleReply(commentId, authorName) {
    emit("reply", commentId, authorName);
}
</script>

<template>
    <div class="comments-list">
        <!-- 标题带徽章 -->
        <div class="flex items-center font-bold mb-4">
            <h3 class="text-lg">
                {{ t("all_comments") }}
            </h3>
            <div class="badge badge-primary mx-2">
                {{ props.commentsNum }}
            </div>
        </div>
        <div
            v-if="props.loading"
            class="flex justify-center p-8">
            <div class="loading loading-spinner loading-lg"></div>
        </div>
        <div
            v-else-if="props.error"
            class="alert alert-error">
            {{ error }}
        </div>
        <div v-else-if="props.comments.length > 0">
            <div class="space-y-6">
                <CommentItem
                    v-for="comment in props.comments"
                    :key="comment.id"
                    :comment="comment"
                    :max-depth="settings.thread_comments_depth || 5"
                    @reply="handleReply" />
            </div>
        </div>
        <div
            v-else
            class="text-center py-8 text-gray-500">
            {{ t("no_comments") }}
        </div>
    </div>
</template>
