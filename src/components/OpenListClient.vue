<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
//i18n
import { useI18n } from "vue-i18n";
//Heroicons
import { FolderIcon, DocumentIcon, DocumentTextIcon, DocumentPlusIcon, DocumentMagnifyingGlassIcon, DocumentChartBarIcon, PhotoIcon, MusicalNoteIcon, FilmIcon, CodeBracketSquareIcon, ArchiveBoxIcon, TableCellsIcon, PresentationChartLineIcon, CommandLineIcon, CircleStackIcon, QuestionMarkCircleIcon } from "@heroicons/vue/20/solid";
import { InformationCircleIcon, ArrowDownTrayIcon, ArrowTopRightOnSquareIcon, ArrowTurnDownRightIcon } from "@heroicons/vue/24/outline";
//Toast
import { toast } from "../scripts/toast-plugin";
//RPC Client
import OpenListClientSettings from "./units/OpenListClientSettings.vue";
//Data
const props = defineProps({
    fs: {
        type: Object,
        default: () => ({
            fs_method: "get",
            path: "/",
            password: "",
            page: 1,
            per_page: 0,
            refresh: false,
            ignore_dir: true,
            parent: "/",
            keywords: "",
            scope: 0,
        }),
        required: true,
    },
    rest_nonce: {
        type: String,
        default: "",
        required: true,
    },
});

const { t } = useI18n();

const isLoading = ref(false);
const setFileLog = ref<Set<string>>(new Set());
const fsError = ref("");
const fsContent = ref<any[]>([]);
const fsPagination = ref<{ total: number; per_page: number; page: number } | null>(null);
const fsTotalInfo = ref("...");

async function openlistRequest(page = 1) {
    isLoading.value = true;
    try {
        const send = { ...props.fs, page };
        const response = await fetch("/api/aiya-oplist/v4/fs", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.rest_nonce,
            },
            body: JSON.stringify(send),
        });

        const data = await response.json();

        if (data.success) {
            //检查是否有文件
            if (data.data.content.length === 0) {
                fsError.value = t("oplist_no_file_in_dir");
                fsContent.value = [];
                fsPagination.value = null;
                return;
            }

            fsContent.value = data.data.content;
            fsPagination.value = {
                total: data.data.total || 0,
                per_page: data.data.per_page || 0,
                page: data.data.page || page,
            };
            fsTotalInfo.value = t("oplist_total_info", { total: data.data.total });
        } else {
            fsError.value = data.data.detail;
            fsContent.value = [];
            fsPagination.value = null;
        }
    } catch (err) {
        fsError.value = t("network_error");
        console.error(err);
    } finally {
        isLoading.value = false;
    }
}

//列表翻页
function handlePageChange(page) {
    if (!fsPagination.value) return;
    if (fsPagination.value.page === page) return;

    const { total, per_page } = fsPagination.value;
    if (per_page <= 0) return;

    let currentPage = page;
    const maxPage = Math.ceil(total / per_page);

    if (currentPage > maxPage) {
        currentPage = maxPage;
    } else if (currentPage < 1) {
        currentPage = 1;
    }

    openlistRequest(currentPage);
}

//计算分页
function loadPageNumbers(): (number | string)[] {
    if (!fsPagination.value || fsPagination.value.per_page <= 0) return [];

    const current = fsPagination.value.page;
    const totalPages = Math.ceil(fsPagination.value.total / fsPagination.value.per_page);

    //直接返回
    if (totalPages <= 1) return [1];

    const pages: (number | string)[] = [];

    //兼容大页码处理
    if (totalPages <= 7) {
        for (let i = 1; i <= totalPages; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push("...");
            pages.push(totalPages);
        } else if (current >= totalPages - 3) {
            pages.push(1);
            pages.push("...");
            for (let i = totalPages - 4; i <= totalPages; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push("...");
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push("...");
            pages.push(totalPages);
        }
    }

    return pages;
}

//分配文件图标
function formatIcon(type) {
    const iconMap = {
        folder: FolderIcon,
        archive: ArchiveBoxIcon,
        image: PhotoIcon,
        audio: MusicalNoteIcon,
        video: FilmIcon,
        text: DocumentTextIcon,
        document: DocumentTextIcon,
        encryption: DocumentMagnifyingGlassIcon,
        mirrorfile: DocumentPlusIcon,
        spreadsheet: DocumentChartBarIcon,
        font: DocumentIcon,
        db: CircleStackIcon,
        docx: DocumentTextIcon,
        pptx: PresentationChartLineIcon,
        xlsx: TableCellsIcon,
        pdf: DocumentIcon,
        code: CommandLineIcon,
        binary: CodeBracketSquareIcon,
        unknown: QuestionMarkCircleIcon,
    };

    return iconMap[type] || iconMap["unknown"];
}

//格式化文件大小
function formatFileSize(bytes) {
    if (!bytes) return "-";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
}

//格式化日期
function formatDate(date) {
    if (!date) return "-";
    return new Date(date).toLocaleDateString();
}

//下载文件
function downloadLink(url) {
    if (!url) return;
    //记录点击事件
    setFileLog.value.add(url);
    //跳转
    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    iframe.src = url;

    document.body.appendChild(iframe);

    setTimeout(() => {
        document.body.removeChild(iframe);
    }, 5000);
}

//跳转文件
function openLink(url) {
    if (!url) return;

    window.open(url, "_blank");
}

//RPC配置状态
const rpcStingsRef = ref();

//获取RPC配置更新
const getRpcConfig = () => {
    if (rpcStingsRef.value) {
        return rpcStingsRef.value.getAria2Config();
    }
    console.log(rpcStingsRef.value);
    return { rpcEnable: true, rpcUrl: "", rpcSecret: "" };
};

const isClientEnabled = computed(() => {
    return getRpcConfig().rpcEnable;
});

//发送到客户端下载
const sendToAria2 = async (url, file_name) => {
    const config = getRpcConfig();
    if (!config.rpcUrl) {
        toast.error(t("oplist_rpc_not_configured"));
        return;
    }

    try {
        const baseParams = [[url], { out: file_name || undefined }];

        //检查secret参数
        const params = config.rpcSecret ? [`token:${config.rpcSecret}`, ...baseParams] : baseParams;

        const payload = {
            jsonrpc: "2.0",
            method: "aria2.addUri",
            id: Date.now().toString(),
            params: params,
        };

        const response = await fetch(config.rpcUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        });

        if (response.ok) {
            const data = await response.json();
            if (data.result) {
                toast.success(t("oplist_rpc_download_started", { file: file_name }));
                setFileLog.value.add(url);
            } else {
                toast.error(t("oplist_rpc_download_failed", { file: file_name }));
            }
        } else {
            toast.error(t("oplist_rpc_connection_failed"));
        }
    } catch (error) {
        console.log("RPC download failed:", error);
        toast.error(t("oplist_rpc_error"));
    }
};

//初始加载
onMounted(() => {
    openlistRequest();
});
</script>

<template>
    <div class="card bg-base-100 shadow-md">
        <table class="table table-zebra !my-0">
            <thead class="bg-base-200">
                <tr>
                    <th class="w-3/5 lg:w-2/5">
                        {{ t("oplist_table_name") }}
                    </th>
                    <th class="hidden lg:table-cell lg:w-1/6">
                        {{ t("oplist_table_size") }}
                    </th>
                    <th class="hidden lg:table-cell lg:w-1/6">
                        {{ t("oplist_table_modified") }}
                    </th>
                    <th class="w-1/4">
                        {{ t("oplist_table_actions") }}
                    </th>
                </tr>
            </thead>
            <!-- IF LOADING -->
            <tbody v-if="isLoading">
                <tr>
                    <td colspan="4">
                        <div class="flex justify-center items-center py-4 font-semibold text-base-content">
                            <span class="loading loading-spinner loading-lg text-primary"></span>
                            <span class="ml-3 text-base-content">
                                {{ t("oplist_loading") }}
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
            <!-- IF ERROR -->
            <tbody v-else-if="fsError != ''">
                <tr>
                    <td colspan="4">
                        <div class="flex justify-center items-center py-4 font-semibold text-base-content">
                            <InformationCircleIcon class="size-6" />
                            <span class="ml-3 text-base-content">
                                {{ fsError }}
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
            <!-- ELSE -->
            <tbody v-else>
                <tr
                    v-for="item in fsContent"
                    :key="item.name">
                    <td>
                        <span class="flex items-center">
                            <component
                                :is="formatIcon(item.type)"
                                class="size-5 mr-2"
                                aria-hidden="true" />
                            <span class="font-medium">
                                {{ item.name }}
                            </span>
                        </span>
                    </td>
                    <td class="hidden lg:table-cell">
                        <span class="text-sm text-base-content/70">
                            {{ formatFileSize(item.size) }}
                        </span>
                    </td>
                    <td class="hidden lg:table-cell">
                        <span class="text-sm text-base-content/70">
                            {{ formatDate(item.modified) }}
                        </span>
                    </td>
                    <td>
                        <div
                            v-if="item.url != ''"
                            class="flex space-x-2">
                            <button
                                v-if="item.type == 'folder'"
                                class="btn btn-soft btn-sm btn-primary"
                                @click="openLink(item.url)"
                                :title="t('oplist_button_open_title')">
                                <ArrowTopRightOnSquareIcon class="size-4 mr-1" />
                                {{ t("oplist_button_open") }}
                            </button>
                            <button
                                v-if="item.type != 'folder'"
                                class="btn btn-soft btn-sm"
                                :class="setFileLog.has(item.url) ? '' : 'btn-primary'"
                                @click="downloadLink(item.url)"
                                :title="t('oplist_button_down_title')">
                                <ArrowDownTrayIcon class="size-4 mr-1" />
                                {{ t("oplist_button_down") }}
                            </button>
                            <button
                                v-if="isClientEnabled && item.type != 'folder'"
                                class="btn btn-soft btn-sm btn-secondary"
                                :class="setFileLog.has(item.url) ? '' : 'btn-primary'"
                                @click="sendToAria2(item.url, item.name)"
                                :title="t('oplist_button_client_title')">
                                <ArrowTurnDownRightIcon class="size-4 mr-1" />
                                {{ t("oplist_button_client") }}
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Toolbar -->
        <div class="flex justify-between items-center p-4">
            <!-- Pagination -->
            <div class="flex items-center">
                <div class="join">
                    <template
                        v-if="fsPagination && fsPagination.total > fsPagination.per_page"
                        v-for="page in loadPageNumbers()"
                        :key="page">
                        <button
                            v-if="page !== '...'"
                            class="join-item btn btn-sm"
                            :class="{ 'btn-active': page === fsPagination.page }"
                            @click="handlePageChange(page)">
                            {{ page }}
                        </button>
                        <span
                            v-else
                            class="join-item btn btn-sm btn-disabled">
                            ...
                        </span>
                    </template>
                </div>
                <span
                    v-if="fsTotalInfo != ''"
                    class="ml-3 text-sm font-medium text-base-content/50">
                    {{ fsTotalInfo }}
                </span>
            </div>
            <OpenListClientSettings ref="rpcStingsRef" />
        </div>
    </div>
</template>
