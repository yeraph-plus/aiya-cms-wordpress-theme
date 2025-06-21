<script setup lang="ts">
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import { toast } from "../../scripts/toast-plugin";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";

const { t } = useI18n();

const props = defineProps({
    from_source: {
        type: Array as () => string[],
        default: () => [],
    },
    rest_nonce: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(["activation-success"]);

//订单操作状态
const orderInput = ref("");
const dropdownOpen = ref(false);

const activeSource = ref<string>(props.from_source[0] as string);
const activatingOrder = ref(false);
const activeDisplayText = ref(sourceDisplayInfo(activeSource.value, "hint"));

//用于更新输入框提示文本
function updateDisplayText(text: string) {
    activeDisplayText.value = (text);
}

//匹配激活源的显示文本
function sourceDisplayInfo(source: string, infoType: "name" | "placeholder" | "hint" = "name") {
    //Text
    const getSourceText = (source: string, type: string) => t(`bill_activation_from_info.${source}.${type}`);

    const sourceInfo = {
        afdian: {
            name: getSourceText("afdian", "name"),
            placeholder: getSourceText("afdian", "placeholder"),
            hint: getSourceText("afdian", "hint"),
        },
        mbd: {
            name: getSourceText("mbd", "name"),
            placeholder: getSourceText("mbd", "placeholder"),
            hint: getSourceText("mbd", "hint"),
        },
        kofi: {
            name: getSourceText("kofi", "name"),
            placeholder: getSourceText("kofi", "placeholder"),
            hint: getSourceText("kofi", "hint"),
        },
        gumroad: {
            name: getSourceText("gumroad", "name"),
            placeholder: getSourceText("gumroad", "placeholder"),
            hint: getSourceText("gumroad", "hint"),
        },
        patreon: {
            name: getSourceText("patreon", "name"),
            placeholder: getSourceText("patreon", "placeholder"),
            hint: getSourceText("patreon", "hint"),
        },
        code: {
            name: getSourceText("code", "name"),
            placeholder: getSourceText("code", "placeholder"),
            hint: getSourceText("code", "hint"),
        },
    };

    if (!sourceInfo[source]) {
        return sourceInfo["code"][infoType];
    }

    return sourceInfo[source][infoType];
}

//切换激活源
function switchSource(source: string) {
    activeSource.value = source;
    dropdownOpen.value = false;
    //清空输入
    orderInput.value = "";
}

//切换下拉菜单状态
function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value;
}

//关闭下拉菜单
function closeDropdown() {
    dropdownOpen.value = false;
}

//刷新页面
function refreshPage() {
    setTimeout(() => {
        window.location.reload();
    }, 3000);
}

//提交激活订单
async function activateOrder() {
    if (!orderInput.value || activatingOrder.value) return;

    activatingOrder.value = true;

    try {
        const response = await fetch("/api/aiya/v1/sponsor_activate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.rest_nonce,
            },
            body: JSON.stringify({
                order_by: activeSource.value,
                order: orderInput.value.trim(),
            }),
        });

        const result = await response.json();

        console.log(result);

        // 处理成功响应
        if (!result.code || result.code === "success") {
            if (result.data.description) {
                //更新提示信息
                updateDisplayText(`${result.data.description}`);
            }
            //查询成功
            toast.success(result.data.message);

            emit("activation-success");
        } else {
            //查询失败
            toast.error(t("bill_by_code_activation_failed"));
            //更新提示信息
            updateDisplayText(`
                <span class="text-error">
                    ${result.data.detail}
                </span>
            `);
        }
    } catch (err) {
        console.error(err);
        toast.error(t("network_error"));
    } finally {
        activatingOrder.value = false;
    }
}
</script>

<template>
    <div
        v-if="props.from_source.length > 0"
        class="relative w-full flex items-center justify-start">
        <div class="join w-4/5 mb-1">
            <!-- Dorpdown -->
            <div
                class="relative dropdown"
                v-if="props.from_source.length > 0">
                <button
                    @click="toggleDropdown"
                    type="button"
                    class="btn join-item dropdown-toggle border-r-0 min-w-[100px] flex justify-between items-center"
                    :disabled="activatingOrder">
                    <span>
                        {{ sourceDisplayInfo(activeSource) }}
                    </span>
                    <ChevronDownIcon class="h-4 w-4" />
                </button>
                <ul
                    class="dropdown-menu absolute z-10 bg-base-200 rounded-lg shadow-lg mt-1 py-2 w-full min-w-[120px]"
                    v-if="dropdownOpen"
                    @blur="closeDropdown">
                    <li
                        v-for="source in props.from_source"
                        :key="source"
                        @click="switchSource(source)"
                        class="px-4 py-2 hover:bg-base-300 cursor-pointer"
                        :class="{ 'bg-primary/10': activeSource === source }">
                        {{ sourceDisplayInfo(source) }}
                    </li>
                </ul>
            </div>
            <!-- Code Source -->
            <button
                v-else
                type="button"
                class="btn join-item border-r-0 min-w-sm"
                disabled>
                {{ sourceDisplayInfo(activeSource) }}
            </button>
            <!-- Input -->
            <input
                type="text"
                v-model="orderInput"
                :placeholder="sourceDisplayInfo(activeSource, 'placeholder')"
                class="input input-bordered join-item flex-1 rounded-l-none"
                :disabled="activatingOrder" />
            <!-- Button -->
            <button
                class="btn btn-primary join-item ml-0.5"
                @click="activateOrder"
                :disabled="!orderInput || activatingOrder">
                <span
                    v-if="activatingOrder"
                    class="loading loading-spinner"></span>
                {{ activatingOrder ? t("bill_by_code_querying") : t("bill_by_code_query") }}
            </button>
        </div>
    </div>
    <p
        v-html="activeDisplayText"
        class="text-sm text-base-content/70">
    </p>
</template>
