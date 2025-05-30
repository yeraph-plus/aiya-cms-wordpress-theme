<script setup lang="ts">
import { ref } from "vue";
//Heroicons
import { ArrowRightEndOnRectangleIcon } from "@heroicons/vue/24/outline";
import { EnvelopeIcon, LockClosedIcon } from "@heroicons/vue/20/solid";
//Toast
import { toast } from "../../scripts/toast-plugin";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
//Data
const props = defineProps({
    rest_nonce: {
        type: String,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
//向外暴露方法
defineExpose({
    resetForm,
});

//定义事件
const emit = defineEmits(["login-success", "update:loading"]);

//表单数据
const loginEmail = ref("");
const loginPassword = ref("");
const loginRemember = ref(false);
const errorMsg = ref("");

//重置表单
function resetForm() {
    loginEmail.value = "";
    loginPassword.value = "";
    loginRemember.value = false;
    errorMsg.value = "";
}

//登录操作
async function handleLogin() {
    errorMsg.value = "";
    //is empty
    if (!loginEmail.value || !loginPassword.value) {
        errorMsg.value = t("login_input_empty");
        return;
    }
    //loading
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
            //登录成功
            emit("login-success", data);
            //创建页面消息
            toast(data.data?.message, { type: "success" });
        }
    } catch (err) {
        errorMsg.value = t("network_error");
        console.error(err);
        emit("update:loading", false);
    }
}
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
            <div class="flex justify-between items-center my-4">
                <label class="cursor-pointer label">
                    <input
                        v-model="loginRemember"
                        type="checkbox"
                        class="checkbox checkbox-sm checkbox-primary mr-2" />
                    <span class="text-base-content">{{ $t("remember") }}</span>
                </label>
                <button
                    type="button"
                    class="btn btn-link p-0 link-primary text-base font-normal"
                    @click.prevent="$emit('switch-to-forgot-password')">
                    {{ $t("forgot_password") }}
                </button>
            </div>
            <button
                type="submit"
                class="btn btn-primary w-full"
                :disabled="props.loading">
                <span
                    v-if="props.loading"
                    class="loading loading-spinner"></span>
                <ArrowRightEndOnRectangleIcon
                    v-else
                    class="size-5" />
                {{ $t("sign_in") }}
            </button>
        </form>
    </div>
</template>
