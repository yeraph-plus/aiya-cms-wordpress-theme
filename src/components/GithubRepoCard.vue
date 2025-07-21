<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";
//Heroicons
import { ClipboardIcon } from "@heroicons/vue/24/outline";
import { ArrowTopRightOnSquareIcon } from "@heroicons/vue/20/solid";
import { StarIcon, EyeIcon } from "@heroicons/vue/24/solid";

//Props
const props = defineProps({
    repo: {
        type: String,
        required: false,
        default: "",
    },
});

// 国际化
const { t } = useI18n();

// 响应式数据
const repoData = ref(null);
const loading = ref(false);
const error = ref(null);

// 计算属性
const repoUrl = computed(() => `https://github.com/${props.repo}`);
const cloneUrl = computed(() => `https://github.com/${props.repo}.git`);

// 获取仓库数据
const fetchRepoData = async () => {
    if (!props.repo) return;

    loading.value = true;
    error.value = null;

    try {
        const response = await fetch(`https://api.github.com/repos/${props.repo}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        repoData.value = await response.json();
    } catch (err) {
        error.value = err.message;
        console.error("Failed to fetch repo data:", err);
    } finally {
        loading.value = false;
    }
};

// 格式化数字
const formatNumber = (num) => {
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + "k";
    }
    return num.toString();
};

// 生命周期
onMounted(() => {
    fetchRepoData();
});
</script>

<template>
    <div class="github-repo-card bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
        <!-- 加载状态 -->
        <div
            v-if="loading"
            class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400">{{ t("loading") || "Loading..." }}</span>
        </div>

        <!-- 错误状态 -->
        <div
            v-else-if="error"
            class="text-center py-8">
            <div class="text-red-500 mb-2">{{ t("error") || "Error" }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ error }}</div>
        </div>

        <!-- 仓库数据 -->
        <div
            v-else-if="repoData"
            class="space-y-4">
            <!-- 头部信息 -->
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                        {{ repoData.name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2"> by {{ repoData.owner.login }} </p>
                    <p
                        v-if="repoData.description"
                        class="text-gray-700 dark:text-gray-300 text-sm">
                        {{ repoData.description }}
                    </p>
                </div>

                <!-- 外部链接 -->
                <a
                    :href="repoUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex-shrink-0 p-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    :title="t('viewOnGithub') || 'View on GitHub'">
                    <ArrowTopRightOnSquareIcon class="w-5 h-5" />
                </a>
            </div>

            <!-- 统计信息 -->
            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center space-x-1">
                    <StarIcon class="w-4 h-4 text-yellow-500" />
                    <span>{{ formatNumber(repoData.stargazers_count) }}</span>
                </div>
                <div class="flex items-center space-x-1">
                    <EyeIcon class="w-4 h-4 text-blue-500" />
                    <span>{{ formatNumber(repoData.watchers_count) }}</span>
                </div>
                <div
                    v-if="repoData.language"
                    class="flex items-center space-x-1">
                    <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                    <span>{{ repoData.language }}</span>
                </div>
            </div>

            <!-- 底部信息 -->
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-600">
                <span v-if="repoData.updated_at"> {{ t("lastUpdated") || "Updated" }}: {{ new Date(repoData.updated_at).toLocaleDateString() }} </span>
                <div class="flex items-center space-x-2">
                    <span v-if="repoData.license">{{ repoData.license.spdx_id }}</span>
                    <span v-if="repoData.forks_count">{{ formatNumber(repoData.forks_count) }} forks</span>
                </div>
            </div>
        </div>

        <!-- 空状态 -->
        <div
            v-else
            class="text-center py-8 text-gray-500 dark:text-gray-400">
            {{ t("noRepoSpecified") || "No repository specified" }}
        </div>
    </div>
</template>
