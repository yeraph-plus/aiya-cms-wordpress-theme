<!-- CommentPagination.vue -->
<script setup lang="ts">
import { computed } from "vue";

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["page-change"]);

// 计算分页显示的页码
const pageNumbers = computed(() => {
    const { current_page, max_pages } = props.pagination;
    const pages = [];

    // 显示当前页附近的页码
    const delta = 2; // 当前页前后显示的页码数量

    let minPage = Math.max(1, current_page - delta);
    let maxPage = Math.min(max_pages, current_page + delta);

    // 确保显示足够的页码
    const pagesShown = maxPage - minPage + 1;
    if (pagesShown < delta * 2 + 1) {
        if (current_page < max_pages / 2) {
            maxPage = Math.min(max_pages, minPage + delta * 2);
        } else {
            minPage = Math.max(1, maxPage - delta * 2);
        }
    }

    // 生成页码数组
    for (let i = minPage; i <= maxPage; i++) {
        pages.push(i);
    }

    return pages;
});

// 页面变更处理
function goToPage(page) {
    if (page === props.pagination.current_page) return;
    emit("page-change", page);
}
</script>

<template>
    <div class="pagination flex justify-center items-center gap-2 my-4">
        <!-- 首页按钮 -->
        <button
            v-if="pagination.current_page > 1"
            @click="goToPage(1)"
            class="btn btn-sm btn-outline">
            首页
        </button>

        <!-- 上一页按钮 -->
        <button
            v-if="pagination.current_page > 1"
            @click="goToPage(pagination.current_page - 1)"
            class="btn btn-sm btn-outline">
            上一页
        </button>

        <!-- 页码按钮 -->
        <button
            v-for="page in pageNumbers"
            :key="page"
            @click="goToPage(page)"
            class="btn btn-sm"
            :class="page === pagination.current_page ? 'btn-primary' : 'btn-outline'">
            {{ page }}
        </button>

        <!-- 下一页按钮 -->
        <button
            v-if="pagination.current_page < pagination.max_pages"
            @click="goToPage(pagination.current_page + 1)"
            class="btn btn-sm btn-outline">
            下一页
        </button>

        <!-- 尾页按钮 -->
        <button
            v-if="pagination.current_page < pagination.max_pages"
            @click="goToPage(pagination.max_pages)"
            class="btn btn-sm btn-outline">
            尾页
        </button>

        <!-- 评论计数信息 -->
        <span class="text-sm text-gray-500 ml-2"> {{ pagination.total_comments }} 条评论 </span>
    </div>
</template>
