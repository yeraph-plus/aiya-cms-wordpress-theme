<script setup lang="ts">
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";

// 初始化 i18n
const { t } = useI18n();

const props = defineProps({
    ajaxUrl: {
        type: String,
        required: true,
    },
    postId: {
        type: [String, Number],
        required: true,
    },
    currentUser: {
        type: Object,
        default: () => null,
    },
    nonce: {
        type: String,
        required: true,
    },
    settings: {
        type: Object,
        required: true,
    },
    replyToId: {
        type: Number,
        default: 0,
    },
    replyToAuthor: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["comment-submitted", "cancel-reply"]);

// 表单状态
const comment = ref("");
const author = ref("");
const email = ref("");
const submitting = ref(false);
const error = ref("");
const success = ref("");

// 重置表单
function resetForm() {
    comment.value = "";
    author.value = "";
    email.value = "";
    error.value = "";
    success.value = "";
}

// 当回复对象变化时，更新表单提示
watch(
    () => props.replyToId,
    (newValue) => {
        if (newValue === 0) {
            resetForm();
        }
    }
);

// 取消回复
function cancelReply() {
    emit("cancel-reply");
}

// 提交评论
async function submitComment() {
    // 验证
    if (!comment.value.trim()) {
        error.value = t("comment_content_required");
        return;
    }

    if (!props.currentUser && props.settings.require_name_email) {
        if (!author.value.trim()) {
            error.value = t("name_required");
            return;
        }

        if (!email.value.trim()) {
            error.value = t("email_required");
            return;
        }

        // 简单的邮箱格式验证
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            error.value = t("valid_email_required");
            return;
        }
    }

    // 重置消息
    error.value = "";
    success.value = "";
    submitting.value = true;

    try {
        // 准备提交数据
        const formData = new FormData();
        formData.append("action", "comments_submit");
        formData.append("post_id", props.postId.toString());
        formData.append("comment", comment.value);
        formData.append("nonce", props.nonce);

        if (props.replyToId > 0) {
            formData.append("comment_parent", props.replyToId.toString());
        }

        if (!props.currentUser) {
            formData.append("author", author.value);
            formData.append("email", email.value);
        }

        // 发送请求
        const response = await fetch(props.ajaxUrl, {
            method: "POST",
            body: formData,
        });

        const result = await response.json();

        if (result.success) {
            success.value = result.data.message || t("comment_submitted_success");

            // 如果评论需要审核
            if (result.data.status === "unapproved" || result.data.status === "hold") {
                // 仅重置表单，不更新评论列表
                resetForm();
            } else {
                // 重置表单并通知父组件添加新评论
                resetForm();
                emit("comment-submitted", result.data.comment_data);
            }
        } else {
            error.value = result.data.message || t("comment_submission_failed");
        }
    } catch (err) {
        console.error("Failed to submit comment:", err);
        error.value = t("network_error");
    } finally {
        submitting.value = false;
    }
}

// 定义外部访问的重置方法
defineExpose({
    resetForm,
});
</script>

<template>
    <div class="comment-form mb-8">
        <div
            v-if="error"
            class="alert alert-error mb-4">
            {{ error }}
        </div>

        <div
            v-if="success"
            class="alert alert-success mb-4">
            {{ success }}
        </div>

        <form @submit.prevent="submitComment">
            <!-- From -->
            <div class="form-control mb-4">
                <textarea
                    v-model="comment"
                    rows="3"
                    :placeholder="replyToId ? t('reply_placeholder', { name: replyToAuthor }) : t('comment_placeholder')"
                    class="textarea textarea-bordered w-full"
                    required>
                </textarea>
            </div>
            <!-- User -->
            <div
                v-if="!props.currentUser"
                class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="form-control">
                    <input
                        v-model="author"
                        type="text"
                        :placeholder="t('name_placeholder')"
                        class="input input-bordered w-full"
                        :required="props.settings.require_name_email" />
                </div>

                <div class="form-control">
                    <input
                        v-model="email"
                        type="email"
                        :placeholder="t('email_placeholder')"
                        class="input input-bordered w-full"
                        :required="props.settings.require_name_email" />
                </div>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-4">
                <!-- If User is logged -->
                <div class="flex items-center gap-2">
                    <div
                        v-if="props.currentUser"
                        class="flex items-center gap-2 mb-4">
                        <img
                            :src="props.currentUser.avatar"
                            alt="Avatar"
                            class="w-6 h-6 rounded-full" />
                        <span class="text-sm">
                            {{ t("commenting_as", { name: props.currentUser.name }) }}
                        </span>
                    </div>
                    <div
                        v-else-if="replyToId"
                        class="text-xs text-base-content/70">
                        {{ t("replying_to", { name: replyToAuthor }) }}
                    </div>
                </div>
                <!-- Submitting -->
                <div class="flex items-center gap-2">
                    <button
                        v-if="replyToId"
                        type="button"
                        @click="cancelReply"
                        class="btn btn-link">
                        {{ t("cancel_reply") }}
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="submitting">
                        <span
                            v-if="submitting"
                            class="loading loading-spinner loading-xs mr-1">
                        </span>
                        <span>
                            {{ submitting ? t("submitting") : t("submit_comment") }}
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
