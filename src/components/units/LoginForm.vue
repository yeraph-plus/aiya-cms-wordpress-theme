<script setup lang="ts">
import { ref } from "vue";
//Toast
import { toast } from "../tools/ToastPlugin";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
// Heroicons
import { ArrowRightEndOnRectangleIcon } from "@heroicons/vue/24/outline";
import { EnvelopeIcon, LockClosedIcon, ArrowUturnRightIcon } from "@heroicons/vue/20/solid";

// 接收父组件传递的属性
const props = defineProps({
    lost_password_url: {
        type: String,
        default: "",
    },
    rest_nonce: {
        type: String,
        required: true,
    },
});

// 定义事件
const emit = defineEmits(["login-success", "switch-to-register", "update:loading"]);

// 表单数据
const loginEmail = ref("");
const loginPassword = ref("");
const loginRemember = ref(false);
const loading = ref(false);
const errorMsg = ref("");

//登录操作
async function handleLogin() {
    errorMsg.value = "";
    //Is empty
    if (!loginEmail.value || !loginPassword.value) {
        errorMsg.value = t("login_input_empty");
        return;
    }
    loading.value = true;
    emit("update:loading", true);

    try {
        const res = await fetch("/api/aiya/v1/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.rest_nonce,
            },
            body: JSON.stringify({
                email: loginEmail.value,
                password: loginPassword.value,
                remember: loginRemember.value,
            }),
        });
        const data = await res.json();

        if (!data.success) {
            errorMsg.value = data.data?.detail || data.data || t("login_failed");
        } else {
            toast(data.data?.message);
            emit("login-success", data);
            // 成功登录后不需要在组件内部重载页面，交由父组件处理
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
    loginEmail.value = "";
    loginPassword.value = "";
    loginRemember.value = false;
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

        <!-- LoginFrom -->
        <form
            @submit.prevent="handleLogin"
            class="space-y-4">
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ $t("auth_email") }}</span>
                </label>
                <div class="join w-full">
                    <div class="join-item flex items-center px-3 bg-base-200 border border-base-300">
                        <EnvelopeIcon class="size-5 text-base-content opacity-60" />
                    </div>
                    <input
                        v-model="loginEmail"
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
                        v-model="loginPassword"
                        type="password"
                        class="input input-bordered join-item flex-1 w-full"
                        :placeholder="$t('auth_password_placeholder')" />
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <label class="cursor-pointer label">
                    <input
                        v-model="loginRemember"
                        type="checkbox"
                        class="checkbox checkbox-sm checkbox-primary mr-2" />
                    <span class="label-text">{{ $t("remember") }}</span>
                </label>
                <a
                    :href="props.lost_password_url"
                    class="link link-primary text-sm">
                    {{ $t("forgot_password") }}
                </a>
            </div>

            <button
                type="submit"
                class="btn btn-primary w-full"
                :disabled="loading">
                <span
                    v-if="loading"
                    class="loading loading-spinner"></span>
                <ArrowRightEndOnRectangleIcon
                    v-else
                    class="size-5" />
                {{ $t("sign_in") }}
            </button>
        </form>
    </div>

    <!-- Switch -->
    <div class="mt-6 text-center">
        <span class="opacity-70 mr-2">{{ $t("need_account") }}</span>
        <button
            type="button"
            class="link link-primary"
            @click="$emit('switch-to-register')">
            <ArrowUturnRightIcon class="size-3 inline-block mr-1" />
            {{ $t("sign_up") }}
        </button>
    </div>
</template>
