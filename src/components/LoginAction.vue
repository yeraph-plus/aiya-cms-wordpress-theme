<script setup lang="ts">
import { ref, shallowRef } from "vue";
//Heroicons
import { XMarkIcon, UserPlusIcon, UserIcon } from "@heroicons/vue/24/outline";
//import
import LoginForm from "./units/LoginForm.vue";
import RegisterForm from "./units/RegisterForm.vue";
import SocialLogin from "./units/SocialLogin.vue";

//Data
const props = defineProps({
    enable_register: {
        type: Boolean,
        default: false,
    },
    enable_sso_register: {
        type: Boolean,
        default: false,
    },
    lost_password_url: {
        type: String,
        default: "",
    },
    rest_nonce: {
        type: String,
        default: "",
    },
});

const isSignIn = ref(true);
const loading = ref(false); // 内部集中管理loading状态
// 组件引用
const loginFormRef = ref(null);
const registerFormRef = ref(null);
const modalRef = ref(null);

// 简化emit，移除update:loading
const emit = defineEmits(["login-success"]);

// 打开登录模态框
function openLoginModal() {
    isSignIn.value = true;
    if (modalRef.value) modalRef.value.showModal();
}

// 打开注册模态框
function openRegisterModal() {
    if (!props.enable_register) return;
    isSignIn.value = false;
    if (modalRef.value) modalRef.value.showModal();
}

function closeModal() {
    if (modalRef.value) modalRef.value.close();

    // 重置表单
    setTimeout(() => {
        if (loginFormRef.value) loginFormRef.value.resetForm();
        if (registerFormRef.value) registerFormRef.value.resetForm();
    }, 300);
}

// 切换表单函数
function switchToLogin() {
    isSignIn.value = true;
}

function switchToRegister() {
    if (!props.enable_register) return;
    isSignIn.value = false;
}

// 处理成功登录或注册
function handleAuthSuccess(data) {
    // 3秒后重载页面
    setTimeout(() => {
        window.location.reload();
    }, 3000);
}

// 处理点击模态框外部关闭
function handleModalClick(event) {
    if (event.target === modalRef.value) {
        closeModal();
    }
}
</script>

<template>
    <div class="flex items-center">
        <button
            type="button"
            class="btn btn-primary mr-2"
            @click="openLoginModal">
            <UserIcon class="size-5" />
            {{ $t("sign_in") }}
        </button>
        <button
            v-if="props.enable_register"
            type="button"
            class="btn btn-outline btn-primary hidden lg:flex"
            @click="openRegisterModal">
            <UserPlusIcon class="size-5" />
            {{ $t("sign_up") }}
        </button>
    </div>
    <!-- Dialog Modal -->
    <dialog
        ref="modalRef"
        class="modal modal-bottom sm:modal-middle"
        @click="handleModalClick">
        <div class="modal-box relative">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-bold text-lg flex items-center">
                    <UserPlusIcon
                        v-if="!isSignIn"
                        class="size-6 mr-2" />
                    <UserIcon
                        v-else
                        class="size-6 mr-2" />
                    {{ isSignIn ? $t("sign_in") : $t("sign_up") }}
                </h5>
                <!-- Close -->
                <button
                    class="btn btn-circle btn-ghost"
                    @click="closeModal">
                    <XMarkIcon class="size-5" />
                </button>
            </div>
            <!-- Social Login -->
            <SocialLogin
                v-if="props.enable_sso_register"
                :rest_nonce="props.rest_nonce"
                v-model:loading="loading"
                @login-success="handleAuthSuccess" />
            <!-- Login Form -->
            <LoginForm
                v-if="isSignIn"
                ref="loginFormRef"
                :lost_password_url="props.lost_password_url"
                :rest_nonce="props.rest_nonce"
                v-model:loading="loading"
                @login-success="handleAuthSuccess"
                @switch-to-register="switchToRegister" />
            <!-- Register Form -->
            <RegisterForm
                v-else
                ref="registerFormRef"
                :rest_nonce="props.rest_nonce"
                v-model:loading="loading"
                @register-success="handleAuthSuccess"
                @switch-to-login="switchToLogin" />
        </div>
        <form
            method="dialog"
            class="modal-backdrop">
            <button>#</button>
        </form>
    </dialog>
</template>
