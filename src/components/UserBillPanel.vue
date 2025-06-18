<script setup lang="ts">
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import { BoltSlashIcon, BoltIcon, TicketIcon, EyeSlashIcon } from "@heroicons/vue/24/outline";
import SponsorActivationForm from "./units/SponsorActivationForm.vue";
import { showModal } from "../scripts/modal-plugin";

const { t } = useI18n();
//Data
const props = defineProps({
    from_source: {
        type: Array,
        default: [],
        required: true,
    },
    order_plan: {
        type: Object,
        default: () => ({}),
        required: true,
    },
    order_history: {
        type: Object,
        default: () => ({}),
        required: true,
    },
    rest_nonce: {
        type: String,
        default: "",
        required: false,
    },
});

//表格排序
const sortKey = ref("start_time");
const sortOrder = ref("desc");

//已登录
const isLoggedIn = computed(() => props.rest_nonce !== "");

//排序订单记录
const sortedOrders = computed(() => {
    if (!props.order_history?.orders?.length) return [];

    const orders = [...props.order_history.orders];
    return orders.sort((a, b) => {
        let valueA = a[sortKey.value];
        let valueB = b[sortKey.value];

        // 数字比较
        if (!isNaN(Number(valueA)) && !isNaN(Number(valueB))) {
            valueA = Number(valueA);
            valueB = Number(valueB);
        }

        if (valueA === valueB) return 0;
        const result = valueA > valueB ? 1 : -1;
        return sortOrder.value === "asc" ? result : -result;
    });
});

//订阅有效性状态消息
const sponsor_level = computed(() => {
    if (props.order_history.is_valid) {
        return {
            title: t("bill_sub_activate_status"),
            description: t("bill_sub_activate_desc"),
            badge: t("bill_sub_activate_badge"),
            class: "badge-primary",
        };
    } else if (props.order_history.force_cancel) {
        return {
            title: t("bill_sub_cancelled_status"),
            description: t("bill_sub_cancelled_desc"),
            badge: t("bill_sub_cancelled_badge"),
            class: "badge-error",
        };
    } else {
        if (!isLoggedIn) {
            return {
                title: t("bill_sub_inactive_status"),
                description: t("bill_sub_inactive_desc"),
                badge: t("bill_sub_inactive_badge"),
                class: "badge-neutral",
            };
        } else {
            return {
                title: t("bill_sub_inactive_status"),
                description: "",
                badge: "",
                class: "hidden",
            };
        }
    }
});

//订单状态
function formatStatus(status) {
    const statusMap: Record<string, string> = {
        paid: t("order_status_paid"),
        unpaid: t("order_status_unpaid"),
        pending: t("order_status_pending"),
        cancelled: t("order_status_cancelled"),
    };

    return statusMap[status as string] || status;
}

//订单时间
function formatDate(timestamp: string): string {
    // Convert string timestamp to milliseconds
    const numericTimestamp = parseInt(timestamp, 10) * 1000;

    const date = new Date(numericTimestamp);

    return date.toLocaleDateString("zh-CN", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
}

//触发列表排序
function sortTable(key) {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }
}

//显示赞助方案的弹窗消息并处理页面跳转
function handlePlanAction(plan) {
    // 首先显示模态框
    if (plan.triggered_msg) {
        showModal(plan.triggered_msg, { title: plan.title, Refresh: plan.refresh });
    }

    // 处理链接跳转
    if (plan.href) {
        // 使用 window.open 在新标签页打开链接
        window.open(plan.href, "_blank");
    }
}
</script>

<template>
    <div class="bg-base-100 border border-base-300 rounded-lg">
        <div class="relative p-4 flex items-center justify-between">
            <div class="flex items-center">
                <TicketIcon class="inline size-6 mr-2" />
                <h3 class="text-xl md:text-xl font-bold">
                    {{ sponsor_level.title }}
                </h3>
                <!-- Sponsor Status -->
                <span
                    class="ml-2 badge"
                    :class="[sponsor_level.class]">
                    {{ sponsor_level.badge }}
                </span>
            </div>
            <span class="text-sm text-base-content/70">
                {{ sponsor_level.description }}
            </span>
        </div>

        <div class="p-4 pt-0 space-y-4">
            <!-- is Logged -->
            <div
                v-if="!isLoggedIn"
                class="text-center py-10">
                <div class="flex flex-col items-center justify-center gap-4">
                    <EyeSlashIcon class="size-16 text-base-300" />
                    <h3 class="text-xl font-semibold text-base-content/70">{{ t("please_login_first") }}</h3>
                    <p class="text-base-content/60">{{ t("login_to_view_bill_plans") }}</p>
                </div>
            </div>
            <div v-else>
                <!-- Progress Total -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <div v-if="order_history.total_days > 0">
                        <div class="flex justify-between items-center">
                            <span class="text-md font-semibold text-base-content/70">
                                {{ t("bill_sub_period") }}
                            </span>
                            <span class="text-md font-medium text-base-content/50">
                                {{ t("bill_sub_days_remaining", { left: order_history.left_days, total: order_history.total_days }) }}
                            </span>
                        </div>
                        <progress
                            class="progress progress-primary w-full"
                            :value="order_history.left_days"
                            :max="order_history.total_days">
                        </progress>
                    </div>

                    <div class="flex items-center mt-4">
                        <span
                            v-if="order_history.force_cancel"
                            class="flex items-center font-semibold text-error">
                            {{ t("bill_sub_cancelled_message") }}
                        </span>
                        <span
                            v-else-if="order_history.total_days > 0 && order_history.left_days > 0"
                            class="flex items-center font-medium text-base-content/70">
                            <BoltIcon class="size-5 text-primary mr-2" />
                            {{ t("bill_sub_active_message_before") }}
                            <span class="text-primary font-bold mx-1">
                                {{ order_history.expiration }}
                            </span>
                            {{ t("bill_sub_active_message_after") }}
                        </span>
                        <span
                            v-else-if="order_history.total_days > 0 && order_history.left_days === 0"
                            class="flex items-center font-medium text-base-content/70">
                            <BoltSlashIcon class="size-5 text-neutral mr-2" />
                            {{ t("bill_sub_expired_message_before") }}
                            <span class="text-primary font-bold mx-1">
                                {{ order_history.expiration }}
                            </span>
                            {{ t("bill_sub_expired_message_after") }}
                        </span>
                        <span
                            v-else
                            class="flex items-center font-medium text-base-content/70">
                            <BoltSlashIcon class="size-5 text-neutral mr-1.5" />
                            {{ t("bill_sub_inactive_message") }}
                        </span>
                    </div>
                </div>

                <!-- Activation By Code -->
                <h5 class="font-bold mb-4 mt-8">
                    {{ t("bill_activation_by_code") }}
                </h5>
                <SponsorActivationForm
                    :rest_nonce="props.rest_nonce"
                    :from_source="props.from_source" />

                <!-- Payment List -->
                <h5 class="font-bold mb-4 mt-8">
                    {{ t("bill_sub_plans_list") }}
                </h5>
                <template
                    v-for="(plan, key) in order_plan"
                    :key="key">
                    <button
                        class="btn mr-2 text-white"
                        :class="plan.color ? '' : 'btn-neutral'"
                        :style="plan.color ? `--btn-color: ${plan.color};` : ''"
                        :alt="plan.alt"
                        @click="handlePlanAction(plan)">
                        {{ plan.name }}
                    </button>
                </template>

                <!-- Order History Table -->
                <h5 class="font-bold mb-4 mt-8">
                    {{ t("bill_sub_order_history") }}
                </h5>
                <div
                    class="overflow-x-auto p-2 bg-base-200 rounded-lg"
                    v-if="sortedOrders.length > 0">
                    <table class="table table-sm table-zebra w-full">
                        <thead>
                            <tr>
                                <th
                                    @click="sortTable('order_id')"
                                    class="cursor-pointer">
                                    {{ t("order_id") }}
                                    <span v-if="sortKey === 'order_id'">{{ sortOrder === "asc" ? "↑" : "↓" }}</span>
                                </th>
                                <th
                                    @click="sortTable('start_time')"
                                    class="cursor-pointer">
                                    {{ t("order_start_time") }}
                                    <span v-if="sortKey === 'start_time'">{{ sortOrder === "asc" ? "↑" : "↓" }}</span>
                                </th>
                                <th
                                    @click="sortTable('duration_days')"
                                    class="cursor-pointer">
                                    {{ t("order_duration_days") }}
                                    <span v-if="sortKey === 'duration_days'">{{ sortOrder === "asc" ? "↑" : "↓" }}</span>
                                </th>
                                <th
                                    @click="sortTable('source')"
                                    class="cursor-pointer">
                                    {{ t("order_source") }}
                                    <span v-if="sortKey === 'source'">{{ sortOrder === "asc" ? "↑" : "↓" }}</span>
                                </th>
                                <th
                                    @click="sortTable('status')"
                                    class="cursor-pointer">
                                    {{ t("order_status") }}
                                    <span v-if="sortKey === 'status'">{{ sortOrder === "asc" ? "↑" : "↓" }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="order in sortedOrders"
                                :key="order.id">
                                <td class="whitespace-nowrap">
                                    <span class="text-xs">
                                        {{ order.order_id }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap">
                                    {{ formatDate(order.start_time) }}
                                </td>
                                <td class="text-center">
                                    {{ order.duration_days }}
                                </td>
                                <td>
                                    {{ order.source }}
                                </td>
                                <td>
                                    <span
                                        class="badge badge-sm"
                                        :class="{
                                            'badge-primary': order.status === 'paid',
                                            'badge-info': order.status === 'pending',
                                            'badge-neutral': order.status === 'cancelled' || order.status === 'unpaid',
                                        }">
                                        {{ formatStatus(order.status) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Null Order -->
                <div
                    v-else
                    class="text-center p-6 bg-base-200 rounded-lg">
                    <p class="text-base-content/70">
                        {{ t("bill_sub_no_orders") }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
