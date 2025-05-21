<script setup lang="ts">
import { ref } from "vue";
//Toast
import { toast } from "../tools/ToastPlugin";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
// Heroicons
import { UserPlusIcon } from "@heroicons/vue/24/outline";
import { UserIcon, EnvelopeIcon, LockClosedIcon, ArrowUturnLeftIcon } from "@heroicons/vue/20/solid";

// 接收父组件传递的属性
const props = defineProps({
    allow_anonymous_register: {
        type: Boolean,
        default: false,
    },
    rest_nonce: {
        type: String,
        required: true,
    },
});

// 定义事件
const emit = defineEmits(["register-success", "switch-to-login", "update:loading"]);

// 普通注册表单数据
const regEmail = ref("");
const regName = ref("");
const regPassword = ref("");
const regPasswordConfirm = ref("");
const loading = ref(false);
const errorMsg = ref("");

//注册操作
async function handleRegister() {
    errorMsg.value = "";
    //Two passwords confirm
    if (regPassword.value !== regPasswordConfirm.value) {
        errorMsg.value = t("password_not_match");
        return;
    }
    //is empty
    if (!regName.value || !regEmail.value || !regPassword.value) {
        errorMsg.value = t("register_input_empty");
        return;
    }
    loading.value = true;
    emit("update:loading", true);

    try {
        const res = await fetch("/api/aiya/v1/register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.rest_nonce,
            },
            body: JSON.stringify({
                username: regName.value,
                email: regEmail.value,
                password: regPassword.value,
            }),
        });
        const data = await res.json();

        if (!data.success) {
            errorMsg.value = data.data?.detail || data.data || t("register_failed");
        } else {
            toast(data.data?.message);
            emit("register-success", data);
            // 成功注册后不需要在组件内部重载页面，交由父组件处理
        }
    } catch (err) {
        errorMsg.value = t("network_error");
        console.error(err);
    }

    loading.value = false;
    emit("update:loading", false);
}

// 重置表单
function resetForm() {
    regName.value = "";
    regEmail.value = "";
    regPassword.value = "";
    regPasswordConfirm.value = "";
    errorMsg.value = "";
}

// 向外暴露方法
defineExpose({
    resetForm,
});
</script>

<template>
    <div class="mx-12 my-4">
        <!-- errorMsg -->
        <div
            v-if="errorMsg"
            class="alert alert-outline alert-error mb-4">
            <span>{{ errorMsg }}</span>
        </div>

        <!-- RegisterFrom -->
        <form
            @submit.prevent="handleRegister"
            class="space-y-4">
            <!-- Default From -->
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ $t("auth_username") }}</span>
                </label>
                <div class="join w-full">
                    <div class="join-item flex items-center px-3 bg-base-200 border border-base-300">
                        <UserIcon class="size-5 text-base-content opacity-60" />
                    </div>
                    <input
                        v-model="regName"
                        type="text"
                        class="input input-bordered join-item flex-1 w-full"
                        :placeholder="$t('auth_username_placeholder')" />
                </div>
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ $t("auth_email") }}</span>
                </label>
                <div class="join w-full">
                    <div class="join-item flex items-center px-3 bg-base-200 border border-base-300">
                        <EnvelopeIcon class="size-5 text-base-content opacity-60" />
                    </div>
                    <input
                        v-model="regEmail"
                        type="email"
                        class="input input-bordered join-item flex-1 w-full"
                        :placeholder="$t('auth_email_placeholder')" />
                </div>
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ $t("auth_password") }}</span>
                </label>
                <div class="join w-full">
                    <div class="join-item flex items-center px-3 bg-base-200 border border-base-300">
                        <LockClosedIcon class="size-5 text-base-content opacity-60" />
                    </div>
                    <input
                        v-model="regPassword"
                        type="password"
                        class="input input-bordered join-item flex-1 w-full"
                        :placeholder="$t('auth_password_placeholder')" />
                </div>
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ $t("auth_password_confirm") }}</span>
                </label>
                <div class="join w-full">
                    <div class="join-item flex items-center px-3 bg-base-200 border border-base-300">
                        <LockClosedIcon class="size-5 text-base-content opacity-60" />
                    </div>
                    <input
                        v-model="regPasswordConfirm"
                        type="password"
                        class="input input-bordered join-item flex-1 w-full"
                        :placeholder="$t('auth_password_confirm_placeholder')" />
                </div>
            </div>

            <button
                type="submit"
                class="btn btn-primary mt-4 w-full"
                :disabled="loading">
                <span
                    v-if="loading"
                    class="loading loading-spinner"></span>
                <UserPlusIcon
                    v-else
                    class="size-5" />
                {{ $t("sign_up") }}
            </button>
        </form>
    </div>

    <!-- Switch -->
    <div class="mt-6 text-center">
        <span class="opacity-70 mr-2">{{ $t("already_have_account") }}</span>
        <button
            type="button"
            class="link link-primary"
            @click="$emit('switch-to-login')">
            <ArrowUturnLeftIcon class="size-3 inline-block mr-1" />
            {{ $t("sign_in") }}
        </button>
    </div>
</template>
