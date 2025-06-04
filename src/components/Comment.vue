<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useI18n } from "vue-i18n";
import CommentForm from "./units/CommentForm.vue";
import CommentList from "./units/CommentList.vue";
import CommentPagination from "./units/CommentPagination.vue";

const { t } = useI18n();

//Data
const props = defineProps({
    ajaxUrl: {
        type: String,
        required: true,
    },
    postId: {
        type: [String, Number],
        required: true,
    },
    commentsOpen: {
        type: Boolean,
        default: true,
    },
    commentsNum: {
        type: Number,
        default: 0,
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
});

//初始化数据状态
interface CommentType {
    id: number;
    parent_id: number;
    [key: string]: any;
}

const comments = ref<CommentType[]>([]);
const pagination = ref({
    current_page: 1,
    max_pages: 1,
    total_comments: 0,
    top_level_comments: 0,
    per_page: 20,
});

//处理状态
const loading = ref(false);
const error = ref("");
const replyToId = ref(0);
const replyToAuthor = ref("");

//加载评论
async function loadComments(page = 1) {
    loading.value = true;
    error.value = "";

    try {
        const params = new URLSearchParams({
            action: "get_comments",
            post_id: props.postId.toString(),
            page: page.toString(),
            nonce: props.nonce,
        });

        const response = await fetch(`${props.ajaxUrl}?${params.toString()}`);
        const data = await response.json();

        if (data.success) {
            comments.value = data.data.comments;
            pagination.value = data.data.pagination;
        } else {
            error.value = data.data.message || t("comment_load_failed");
        }
    } catch (err) {
        error.value = t("network_error");
        console.error("Failed to load comments:", err);
    } finally {
        loading.value = false;
    }
}

//设置回复目标
function setReplyTo(commentId, authorName) {
    //如果不能评论，则不允许回复
    if (!props.commentsOpen) return;

    replyToId.value = commentId;
    replyToAuthor.value = authorName;

    //滚动到评论表单
    setTimeout(() => {
        const formElement = document.querySelector(".comment-form");
        if (formElement) {
            formElement.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    }, 100);
}

//取消回复
function cancelReply() {
    replyToId.value = 0;
    replyToAuthor.value = "";
}

//处理新评论提交成功
function handleCommentSubmitted(newComment) {
    //重置回复状态
    replyToId.value = 0;
    replyToAuthor.value = "";

    //如果是新评论（不是回复），更新评论列表
    if (props.settings.comment_order === "desc") {
        //按最新评论排序时，将新评论添加到顶部
        comments.value.unshift(newComment);
    } else if (newComment.parent_id === 0) {
        //按旧评论排序时，将新的顶级评论添加到底部
        comments.value.push(newComment);
    } else {
        //如果是回复评论，重新加载整个评论列表以确保嵌套正确
        loadComments(pagination.value.current_page);
    }

    //更新评论计数
    pagination.value.total_comments += 1;

    if (newComment.parent_id === 0) {
        pagination.value.top_level_comments += 1;
    }
}

//页面变更处理
function handlePageChange(page) {
    loadComments(page);
}

//初始加载评论
onMounted(() => {
    loadComments();
});
</script>

<template>
    <div class="flex flex-col">
        <CommentForm
            v-if="commentsOpen"
            :ajax-url="ajaxUrl"
            :post-id="postId"
            :current-user="currentUser"
            :nonce="nonce"
            :settings="settings"
            :reply-to-id="replyToId"
            :reply-to-author="replyToAuthor"
            @comment-submitted="handleCommentSubmitted"
            @cancel-reply="cancelReply" />
        <CommentList
            :comments="comments"
            :comments-num="pagination.total_comments"
            :loading="loading"
            :error="error"
            :settings="settings"
            :pagination="pagination"
            @reply="setReplyTo"
            @page-change="handlePageChange" />
    </div>
</template>
