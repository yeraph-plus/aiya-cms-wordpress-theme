<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
//Toast
import { toast } from "../scripts/toast-plugin";
//Masonry
import MasonryLayout from "../scripts/ui/MasonryLayout.vue";
//Heroicons
import { PencilIcon, MagnifyingGlassIcon, HandThumbUpIcon, HandThumbDownIcon, ExclamationCircleIcon, EllipsisHorizontalCircleIcon } from "@heroicons/vue/24/outline";

//Props
const props = defineProps({
    api: {
        type: String,
        required: true,
        default: "",
    },
    nonce: {
        type: String,
        required: true,
        default: "",
    },
    publish: {
        type: Boolean,
        default: true,
    },
    can_view: {
        type: Boolean,
        default: true,
    },
});

//类型定义
interface Post {
    id: string | number;
    content: string;
    created_at: string;
    updated_at: string;
    like_count: number | string;
    diss_count: number | string;
    user_id: string | number;
    comment_off: string | boolean;
    is_sticky: string | boolean;
    comments_num: number;
    status: string;
    tags: string[];
    user_name?: string;
}

//发帖状态管理
const postContent = ref("");
//const commentOff = ref(false);
const isSubmitting = ref(false);

//帖子列表状态
const posts = ref<Post[]>([]);
const isLoading = ref(false);
const totalPosts = ref(0);
const currentPage = ref(1);
const totalPages = ref(1);
const searchKeyword = ref("");
const orderBy = ref("created_at");
const order = ref("DESC");
const isLoadingMore = ref(false);
const hasMorePosts = ref(true); // 是否还有更多帖子可加载
const initialLoad = ref(true); // 是否是首次加载
//投票请求加载状态
const votingPostId = ref(null);

//处理帖子数据格式
const processPosts = (posts) => {
    return posts.map((post) => ({
        ...post,
        id: post.id, // 保持原始格式
        like_count: typeof post.like_count === "string" ? parseInt(post.like_count) || 0 : post.like_count || 0,
        diss_count: typeof post.diss_count === "string" ? parseInt(post.diss_count) || 0 : post.diss_count || 0,
        comments_num: typeof post.comments_num === "string" ? parseInt(post.comments_num) || 0 : post.comments_num || 0,
        is_sticky: post.is_sticky === "1" || post.is_sticky === true,
        comment_off: post.comment_off === "1" || post.comment_off === true,
        tags: Array.isArray(post.tags) ? post.tags : [],
    }));
};

//获取帖子列表
const fetchPosts = async (loadMore = false) => {
    //验证允许查看
    if (!props.can_view) {
        return;
    }
    //如果是加载更多且没有更多数据，则直接返回
    if (loadMore && !hasMorePosts.value) {
        return;
    }

    //根据是首次加载还是加载更多来设置加载状态
    if (loadMore) {
        isLoadingMore.value = true;
    } else {
        isLoading.value = true;
        //搜索或排序变化时清空列表
        posts.value = [];
    }

    try {
        //查询参数
        const queryParams = new URLSearchParams({
            s: searchKeyword.value,
            orderby: orderBy.value,
            order: order.value,
            paged: currentPage.value.toString(),
        });

        const response = await fetch(`${props.api}/posts?${queryParams.toString()}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.nonce,
            },
        });

        const data = await response.json();

        if (response.ok && data.success) {
            //console.log("Posts:", data);
            //分页数据
            totalPosts.value = parseInt(data.data.total) || 0;
            currentPage.value = parseInt(data.data.paged) || 1;
            totalPages.value = parseInt(data.data.pages) || 1;

            //判断是否还有更多数据
            hasMorePosts.value = currentPage.value < totalPages.value;

            // 加载更多时追加数据，否则替换数据
            if (loadMore) {
                posts.value = [...posts.value, ...processPosts(data.data.posts || [])];
            } else {
                posts.value = processPosts(data.data.posts || []);
            }
        } else {
            console.error("ERROR:", data);
            toast.error(t("hub_load_posts_failed"));
        }
    } catch (error) {
        console.error("ERROR:", error);
        toast.error(t("hub_load_posts_failed"));
    } finally {
        isLoading.value = false;
        isLoadingMore.value = false;
        initialLoad.value = false;
    }
};

//处理点赞、点踩
const handleVote = async (postId, voteType) => {
    //避免重复点击
    if (votingPostId.value === postId) {
        return;
    }

    votingPostId.value = postId;

    try {
        const response = await fetch(`${props.api}/post/${postId}/vote`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.nonce,
            },
            body: JSON.stringify({
                type: voteType, //'like' 'diss'
            }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            //找到对应的帖子并更新数据
            const postIndex = posts.value.findIndex((p) => p.id === postId);
            if (postIndex !== -1) {
                const field = voteType === "like" ? "like_count" : "diss_count";
                posts.value[postIndex][field]++;
            }
        } else {
            console.error("ERROR: click", data);
        }
    } catch (error) {
        console.error("ERROR:", error);
        toast.error(t("hub_load_votes_failed"));
    } finally {
        votingPostId.value = null;
    }
};

//处理搜索
const handleSearch = () => {
    currentPage.value = 1;
    hasMorePosts.value = true;
    fetchPosts(false);
};

//处理排序变化
const handleSortChange = () => {
    currentPage.value = 1;
    hasMorePosts.value = true;
    fetchPosts(false);
};

//格式化日期
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString("zh-CN", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// 加载更多帖子
const loadMorePosts = () => {
    if (!isLoadingMore.value && hasMorePosts.value) {
        currentPage.value++;
        fetchPosts(true);
    }
};

// 添加滚动监听
const observeIntersection = () => {
    const observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && !isLoading.value && !isLoadingMore.value) {
                loadMorePosts();
            }
        },
        {
            //当加载更多按钮距离视窗底部200px时触发
            rootMargin: "0px 0px 200px 0px",
        }
    );

    const loadMoreEl = document.querySelector("#load-more-trigger");

    if (loadMoreEl) {
        observer.observe(loadMoreEl);
    }

    return observer;
};

//表单提交
const submitPost = async () => {
    //验证允许发布
    if (!props.publish) {
        return;
    }
    //内容验证
    if (!postContent.value.trim()) {
        toast.info(t("hub_post_content_required"));
        return;
    }

    //设置提交状态
    isSubmitting.value = true;

    try {
        const response = await fetch(`${props.api}/post`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.nonce,
            },
            body: JSON.stringify({
                content: postContent.value,
            }),
        });

        const data = await response.json();

        if (response.ok && data.data && data.data.id) {
            //成功发布
            postContent.value = "";
            toast.info(t("hub_post_published", { id: data.data.id }));
            //刷新帖子列表以显示新发布的内容
            fetchPosts();
        } else {
            console.error("ERROR:", data);
            //返回数据异常提示
            toast.warning(t("hub_publish_failed"));
        }
    } catch (error) {
        console.error("ERROR:", error);
        toast.error(t("hub_retry_publish"));
    } finally {
        isSubmitting.value = false;
    }
};

//标签点击
const handleTagClick = (tag) => {
    //设置搜索关键词为当前标签
    searchKeyword.value = `#${tag}#`;
};

//瀑布流配置
const masonryOptions = {
    itemSelector: ".masonry-item",
    columnWidth: ".masonry-sizer",
    percentPosition: true,
    gutter: 16, // 间距
    transitionDuration: "0.5s", // 过渡动画
};

import type { Ref } from "vue";
const observerRef: Ref<IntersectionObserver | null> = ref(null);

//页面加载时获取帖子列表
onMounted(() => {
    fetchPosts();

    // 延迟添加滚动监听
    setTimeout(() => {
        observerRef.value = observeIntersection();
    }, 1000);
});

//在组件卸载时清理观察器 - 正确位置
onUnmounted(() => {
    if (observerRef.value) {
        observerRef.value.disconnect();
    }
});
</script>
<template>
    <template v-if="props.publish">
        <!-- New Post -->
        <h2 class="flex items-center my-2">
            <PencilIcon class="size-4 mr-1" />
            <span class="text-lg font-bold">{{ t("hub_new_post") }}</span>
        </h2>
        <div class="bg-base-100 border border-base-300 rounded-lg mb-8 p-4">
            <form @submit.prevent="submitPost">
                <div class="form-control w-full">
                    <textarea
                        class="textarea textarea-bordered w-full h-32"
                        :placeholder="t('hub_post_placeholder')"
                        v-model="postContent"
                        required>
                    </textarea>
                </div>
                <div class="card-actions flex items-center justify-end mt-4">
                    <span class="text-gray-500 mr-4">
                        {{ t("hub_post_tag_hint") }}
                    </span>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        :class="{ 'btn-disabled': isSubmitting }"
                        :disabled="isSubmitting">
                        {{ isSubmitting ? t("hub_publishing") : t("hub_publish_post") }}
                    </button>
                </div>
            </form>
        </div>
    </template>
    <!-- Post List -->
    <template v-if="props.can_view">
        <!-- Select From -->
        <div class="flex flex-row justify-between bg-base-100 border border-base-300 rounded-lg p-4 mb-8 gap-4">
            <div class="join">
                <input
                    type="text"
                    class="input input-bordered join-item"
                    v-model="searchKeyword"
                    :placeholder="t('search_placeholder')"
                    @keyup.enter="handleSearch" />
                <button
                    class="btn join-item"
                    @click="handleSearch">
                    <MagnifyingGlassIcon class="size-5" />
                </button>
            </div>
            <div class="hidden md:flex w-56 gap-2">
                <select
                    class="select select-bordered w-32"
                    v-model="orderBy"
                    @change="handleSortChange">
                    <option value="created_at">
                        {{ t("hub_sort_by_created") }}
                    </option>
                    <option value="like_count">
                        {{ t("hub_sort_by_likes") }}
                    </option>
                    <option value="diss_count">
                        {{ t("hub_sort_by_disslikes") }}
                    </option>
                </select>
                <select
                    class="select select-bordered w-24"
                    v-model="order"
                    @change="handleSortChange">
                    <option value="DESC">
                        {{ t("hub_sort_order_desc") }}
                    </option>
                    <option value="ASC">
                        {{ t("hub_sort_order_asc") }}
                    </option>
                </select>
            </div>
        </div>

        <div
            v-if="isLoading"
            class="flex justify-center items-center py-12">
            <span class="loading loading-spinner loading-lg text-primary"></span>
            <span class="ml-2"> LOADING... </span>
        </div>

        <!-- List -->
        <div v-else-if="posts.length > 0">
            <MasonryLayout
                :items="posts"
                :options="masonryOptions"
                :addMarginB="16">
                <template #default="{ item: post }">
                    <div class="bg-base-100 hover:shadow-md transition-shadow border border-base-300 rounded-lg">
                        <div class="flex justify-start items-center text-sm text-gray-500 px-4 py-2 border-b border-base-300">
                            <span> PID-{{ post.id }}#{{ formatDate(post.created_at) }} </span>
                        </div>

                        <div
                            class="prose prose-base p-4 max-w-none"
                            v-html="post.content">
                        </div>

                        <div
                            v-if="post.tags && post.tags.length > 0"
                            class="flex flex-wrap gap-1 p-4">
                            <span
                                v-for="tag in post.tags"
                                :key="tag"
                                class="text-sm text-primary cursor-pointer transition-colors"
                                @click="handleTagClick(tag)">
                                #{{ tag }}#
                            </span>
                        </div>

                        <div class="flex justify-start gap-2 px-4 py-2">
                            <button
                                class="btn btn-sm btn-outline gap-1"
                                :class="{ 'btn-primary': parseInt(post.like_count) > 0 }"
                                @click="handleVote(post.id, 'like')"
                                :disabled="votingPostId === post.id">
                                <HandThumbUpIcon class="size-5" />
                                {{ post.like_count || 0 }}
                            </button>

                            <button
                                class="btn btn-sm btn-outline gap-1"
                                :class="{ 'btn-secondary': parseInt(post.diss_count) > 0 }"
                                @click="handleVote(post.id, 'diss')"
                                :disabled="votingPostId === post.id">
                                <HandThumbDownIcon class="size-5" />
                                {{ post.diss_count || 0 }}
                            </button>
                        </div>
                    </div>
                </template>
            </MasonryLayout>

            <!-- Load More -->
            <div class="mt-8 text-center">
                <div
                    v-if="hasMorePosts"
                    id="load-more-trigger"
                    class="py-4">
                    <button
                        v-if="!isLoadingMore"
                        @click="loadMorePosts"
                        class="btn btn-outline">
                        {{ t("hub_load_more") }}
                    </button>
                    <div
                        v-else
                        class="flex justify-center items-center">
                        <span class="loading loading-spinner loading-md text-primary"></span>
                        <span class="ml-2">
                            {{ t("hub_loading_more") }}
                        </span>
                    </div>
                </div>
                <div
                    v-else
                    class="text-sm text-gray-500 py-2">
                    {{ t("hub_all_loaded", { count: totalPosts }) }}
                </div>
            </div>
        </div>

        <!-- NULL -->
        <div
            v-else
            class="bg-base-100 border border-base-300 rounded-lg">
            <div class="flex flex-col items-center justify-center py-12">
                <EllipsisHorizontalCircleIcon class="size-12 text-gray-400 mb-4" />
                <p class="text-gray-500">
                    {{ t("hub_no_posts") }}
                </p>
            </div>
        </div>
    </template>
    <template v-else>
        <div class="bg-base-100 border border-base-300 rounded-lg">
            <div class="flex flex-col items-center justify-center py-12">
                <ExclamationCircleIcon class="size-12 text-warning mb-4" />
                <p class="text-gray-500">
                    {{ t("hub_login_required") }}
                </p>
            </div>
        </div>
    </template>
</template>
