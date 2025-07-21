<script setup lang="ts">
import { ref, onMounted, defineEmits, defineExpose } from "vue";
//i18n
import { useI18n } from "vue-i18n";
//Heroicons
import { WrenchScrewdriverIcon } from "@heroicons/vue/24/outline";
//Toast
import { toast } from "../../scripts/toast-plugin";

const { t } = useI18n();

const isDropdownOpen = ref(false);
//Dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};
const saveAndClosDropdown = () => {
    saveAria2Config();
    isDropdownOpen.value = false;
};

//预设RPC配置
const presetConfigs = [
    { name: "Motrix", url: "http://localhost:16800/jsonrpc" },
    { name: "Aria2", url: "http://localhost:6800/jsonrpc" },
];
const selectedPresetIndex = ref("");
const aria2Config = ref({
    rpcEnable: false,
    rpcUrl: "http://localhost:6800/jsonrpc",
    rpcSecret: "",
});

const applyPresetConfig = () => {
    const index = parseInt(selectedPresetIndex.value);
    if (!isNaN(index) && presetConfigs[index] && presetConfigs[index].url) {
        aria2Config.value.rpcUrl = presetConfigs[index].url;
    }
};

//保存配置到localStorage
const saveAria2Config = () => {
    try {
        localStorage.setItem("aria2_config", JSON.stringify(aria2Config.value));
        toast.success(t("oplist_rpc_config_saved"));
    } catch (error) {
        console.log("Failed to save aria2 config:", error);
        toast.error(t("oplist_rpc_config_save_failed"));
    }
};

//从localStorage加载配置
const loadAria2Config = () => {
    const stored = localStorage.getItem("aria2_config");
    if (stored) {
        try {
            aria2Config.value = { ...aria2Config.value, ...JSON.parse(stored) };
        } catch (error) {
            console.log("Failed to parse aria2 config:", error);
        }
    }
};

//测试Aria2端口连接
const testLoading = ref(false);
const testAria2Port = async () => {
    testLoading.value = true;
    try {
        const payload = {
            jsonrpc: "2.0",
            method: "aria2.getVersion",
            id: "test",
            params: aria2Config.value.rpcSecret ? [`token:${aria2Config.value.rpcSecret}`] : [],
        };

        const rpc_client = aria2Config.value.rpcUrl;
        if (!rpc_client) {
            toast.error(t("oplist_rpc_test_none", { client: rpc_client }));
            return;
        }
        const response = await fetch(rpc_client, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        });

        if (response.ok) {
            const data = await response.json();
            if (data.result) {
                toast.success(t("oplist_rpc_test_success", { client: rpc_client }));
            } else {
                toast.warning(t("oplist_rpc_test_failed", { client: rpc_client }));
            }
        } else {
            toast.warning(t("oplist_rpc_test_failed", { client: rpc_client }));
        }
    } catch (error) {
        console.log("Aria2 connection test failed:", error);
        toast.error(t("oplist_rpc_test_error"));
    } finally {
        testLoading.value = false;
    }
};

//加载Aria2配置
defineExpose({
    getAria2Config: () => aria2Config.value,
});

onMounted(() => {
    loadAria2Config();
});
</script>

<template>
    <div class="flex items-center space-x-2">
        <div
            class="dropdown dropdown-end lg:dropdown-top"
            :class="{ 'dropdown-open': isDropdownOpen }">
            <div
                tabindex="0"
                role="button"
                class="btn btn-sm btn-outline"
                @click="toggleDropdown()">
                <WrenchScrewdriverIcon class="size-4 mr-1" />
                {{ t("oplist_button_rpc_settings") }}
            </div>
            <div class="dropdown-content mt-2 z-[1] card card-compact w-80 p-4 shadow bg-base-100 border border-base-300 m-2">
                <!-- Settings -->
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label cursor-pointer py-1">
                            <input
                                type="checkbox"
                                class="toggle toggle-primary toggle-sm"
                                v-model="aria2Config.rpcEnable" />
                            <span class="label-text text-sm">
                                {{ t("oplist_rpc_enable") }}
                            </span>
                        </label>
                    </div>
                    <template v-if="aria2Config.rpcEnable">
                        <div class="divider"></div>
                        <!-- Test -->
                        <div class="form-control">
                            <button
                                class="btn btn-primary btn-outline btn-sm w-full"
                                :class="{ 'btn-disabled': testLoading }"
                                @click="testAria2Port">
                                {{ t("oplist_rpc_send_test") }}
                            </button>
                        </div>
                        <!-- Preset -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-sm">
                                    {{ t("oplist_rpc_preset_config") }}
                                </span>
                            </label>
                            <select
                                v-model="selectedPresetIndex"
                                class="select select-bordered select-sm py-0 w-full"
                                @change="applyPresetConfig()">
                                <option
                                    value=""
                                    disabled>
                                    {{ t("oplist_rpc_preset_select") }}
                                </option>
                                <option
                                    v-for="(preset, index) in presetConfigs"
                                    :key="index"
                                    :value="index.toString()">
                                    {{ preset.name }}
                                </option>
                            </select>
                        </div>
                        <!-- URL -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-sm">
                                    {{ t("oplist_rpc_url") }}
                                </span>
                            </label>
                            <input
                                v-model="aria2Config.rpcUrl"
                                type="url"
                                class="input input-bordered input-sm w-full" />
                        </div>
                        <!-- Secret -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-sm">
                                    {{ t("oplist_rpc_secret") }}
                                </span>
                            </label>
                            <input
                                v-model="aria2Config.rpcSecret"
                                type="password"
                                class="input input-bordered input-sm w-full" />
                        </div>
                    </template>
                    <!-- Save -->
                    <div class="flex justify-end space-x-2 mt-4 pt-2 border-t border-base-300">
                        <button
                            class="btn btn-ghost btn-sm"
                            @click="toggleDropdown()">
                            {{ t("oplist_button_rpc_cancel") }}
                        </button>
                        <button
                            class="btn btn-primary btn-sm"
                            @click="saveAndClosDropdown()">
                            {{ t("oplist_button_rpc_save") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
