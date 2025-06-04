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
    pagination: {
        // 添加分页信息属性
        type: Object,
        default: () => ({
            current_page: 1,
            max_pages: 1,
            total_comments: 0,
            top_level_comments: 0,
            per_page: 20,
            comment_order: "asc",
        }),
    },
});

const emit = defineEmits(["reply", "page-change"]);

//回复评论
function handleReply(commentId, authorName) {
    emit("reply", commentId, authorName);
}

//处理页面切换
function handlePageChange(page) {
    emit("page-change", page);
}
</script>

<template>
    <div class="comments-list">
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
                    :max-depth="Number(settings.thread_comments_depth) || 5"
                    @reply="handleReply" />
            </div>

            <!-- 添加评论分页组件 -->
            <div
                v-if="props.pagination.max_pages > 1"
                class="mt-8">
                <div class="flex justify-center">
                    <nav
                        class="pagination"
                        aria-label="Pagination">
                        <button
                            v-if="props.pagination.current_page > 1"
                            @click="handlePageChange(props.pagination.current_page - 1)"
                            class="btn btn-sm btn-outline">
                            {{ t("prev_page") }}
                        </button>

                        <span class="mx-4 flex items-center">
                            {{ t("page_x_of_y", { current: props.pagination.current_page, total: props.pagination.max_pages }) }}
                        </span>

                        <button
                            v-if="props.pagination.current_page < props.pagination.max_pages"
                            @click="handlePageChange(props.pagination.current_page + 1)"
                            class="btn btn-sm btn-outline">
                            {{ t("next_page") }}
                        </button>
                    </nav>
                </div>
            </div>
        </div>
        <div
            v-else
            class="text-center py-8 text-gray-500">
            {{ t("no_comments") }}
        </div>
    </div>
</template>
