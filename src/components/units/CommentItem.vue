<script setup lang="ts">
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    comment: {
        type: Object,
        required: true,
    },
    parentDepth: {
        type: Number,
        default: 0,
    },
    maxDepth: {
        type: Number,
        default: 5,
    },
});

const emit = defineEmits(["reply"]);

//计算当前评论的深度
const currentDepth = computed(() => props.parentDepth + 1);

//确定是否可以继续嵌套回复
const canReply = computed(() => currentDepth.value < props.maxDepth);

//回复此评论
function replyToComment() {
    emit("reply", props.comment.id, props.comment.author.name);
}

//检查是否有嵌套回复
const hasReplies = computed(() => {
    return props.comment.replies && props.comment.replies.length > 0;
});
</script>

<template>
    <div class="comment-item">
        <div class="comment-content bg-base-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-2">
                <img
                    :src="props.comment.author.avatar"
                    alt="Avatar"
                    class="w-8 h-8 rounded-full" />
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <a
                            v-if="props.comment.author.url"
                            :href="props.comment.author.url"
                            target="_blank"
                            rel="nofollow"
                            class="font-bold hover:text-primary">
                            {{ props.comment.author.name }}
                        </a>
                        <span
                            v-else
                            class="font-bold">
                            {{ props.comment.author.name }}
                        </span>
                        <span
                            class="badge badge-xs badge-neutral badge-outline"
                            v-if="props.comment.author.is_user">
                            {{ t("is_user") }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-500">
                        {{ props.comment.date.human }}
                    </span>
                </div>
            </div>
            <!-- Comment Content -->
            <div
                class="comment-text prose max-w-none mb-2"
                v-html="props.comment.content">
            </div>
            <!-- Reply -->
            <div class="mt-2 flex justify-end">
                <button
                    v-if="canReply"
                    @click="replyToComment"
                    class="btn btn-sm btn-outline">
                    {{ t("replay_comment") }}
                </button>
            </div>
        </div>
        <!-- Re Nesting -->
        <div
            v-if="hasReplies"
            class="comment-replies pl-6 mt-4 border-l-2 border-base-300">
            <div
                v-for="reply in comment.replies"
                :key="reply.id"
                class="mb-4">
                <CommentItem
                    :comment="reply"
                    :parent-depth="currentDepth"
                    :max-depth="maxDepth"
                    @reply="(id, name) => $emit('reply', id, name)" />
            </div>
        </div>
    </div>
</template>
