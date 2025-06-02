<script setup lang="ts">
import { ref, shallowRef, onMounted, onUnmounted } from "vue";
//Heroicons
import { XMarkIcon, UserPlusIcon, UserIcon } from "@heroicons/vue/24/outline";
import { ArrowUturnRightIcon, ArrowUturnLeftIcon } from "@heroicons/vue/20/solid";
//Components
import LoginForm from "./units/LoginForm.vue";
import RegisterForm from "./units/RegisterForm.vue";
import SocialLogin from "./units/SocialLogin.vue";
import ForgotPasswordForm from "./units/ForgotPasswordForm.vue";
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
    rest_nonce: {
        type: String,
        default: "",
    },
});

const isSignIn = ref(true);
const loading = ref(false); //loading状态
// 组件引用
const loginFormRef = ref(null);
const registerFormRef = ref(null);
const forgotPasswordFormRef = ref(null);
const modalRef = ref(null);
const isForgotPassword = ref(false);

//定义事件
const emit = defineEmits(["login-success"]);

//打开登录模态框
function openLoginModal() {
    isSignIn.value = true;
    if (modalRef.value) modalRef.value.showModal();
}

//切换到忘记密码表单
function switchToForgotPassword() {
    isForgotPassword.value = true;
    isSignIn.value = false;
}

//忘记密码填写后返回到登录表单
function handleForgotPasswordSuccess(data) {
    setTimeout(() => {
        switchToLogin();
    }, 3000);
}

//成功登录刷新页面
function handleAuthSuccess(data) {
    // 3秒后重载页面
    setTimeout(() => {
        window.location.reload();
    }, 3000);
}

//开关模态框
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
        if (forgotPasswordFormRef.value) forgotPasswordFormRef.value.resetForm();
        // 重置状态
        isSignIn.value = true;
        isForgotPassword.value = false;
    }, 300);
}

//切换表单函数
function switchToLogin() {
    isSignIn.value = true;
    isForgotPassword.value = false;
}

function switchToRegister() {
    if (!props.enable_register) return;
    isSignIn.value = false;
}

//处理点击模态框外部关闭
function handleModalClick(event) {
    if (event.target === modalRef.value) {
        closeModal();
    }
}

//类型声明
declare global {
    interface Window {
        LoginAction?: {
            showLogin: (() => void) | null;
        };
    }
}

//注册一个全局方法
onMounted(() => {
    // 确保全局对象存在
    if (!window.LoginAction) {
        window.LoginAction = { showLogin: null };
    }
    // 将 openLoginModal 绑定到全局对象
    window.LoginAction.showLogin = openLoginModal;
});
//清理全局方法
onUnmounted(() => {
    if (window.LoginAction) {
        window.LoginAction.showLogin = null;
    }
});
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
            <template v-if="isSignIn && !isForgotPassword">
                <!-- Login Form -->
                <LoginForm
                    ref="loginFormRef"
                    :rest_nonce="props.rest_nonce"
                    v-model:loading="loading"
                    @login-success="handleAuthSuccess"
                    @switch-to-forgot-password="switchToForgotPassword" />
            </template>
            <template v-else-if="isForgotPassword">
                <!-- Forgot Password Form -->
                <ForgotPasswordForm
                    ref="forgotPasswordFormRef"
                    :rest_nonce="props.rest_nonce"
                    v-model:loading="loading"
                    @forgot-password-success="handleForgotPasswordSuccess"
                    @switch-to-login="switchToLogin" />
            </template>
            <template v-else>
                <!-- Register Form -->
                <RegisterForm
                    ref="registerFormRef"
                    :rest_nonce="props.rest_nonce"
                    v-model:loading="loading"
                    @register-success="handleAuthSuccess"
                    @switch-to-login="switchToLogin" />
            </template>
            <!-- Switch -->
            <template v-if="isSignIn && !isForgotPassword && props.enable_register">
                <div class="mt-6 text-center">
                    <span class="opacity-70 mr-2">{{ $t("need_account") }}</span>
                    <button
                        type="button"
                        class="link link-primary"
                        @click="switchToRegister">
                        <ArrowUturnRightIcon class="size-3 inline-block mr-1" />
                        {{ $t("sign_up") }}
                    </button>
                </div>
            </template>
            <template v-else-if="!isSignIn && !isForgotPassword">
                <div class="mt-6 text-center">
                    <span class="opacity-70 mr-2">{{ $t("already_have_account") }}</span>
                    <button
                        type="button"
                        class="link link-primary"
                        @click="switchToLogin">
                        <ArrowUturnLeftIcon class="size-3 inline-block mr-1" />
                        {{ $t("sign_in") }}
                    </button>
                </div>
            </template>
            <template v-else-if="isForgotPassword">
                <div class="mt-6 text-center">
                    <span class="opacity-70 mr-2">{{ $t("already_have_account") }}</span>
                    <button
                        type="button"
                        class="link link-primary"
                        @click="switchToLogin">
                        <ArrowUturnLeftIcon class="size-3 inline-block mr-1" />
                        {{ $t("sign_in") }}
                    </button>
                </div>
            </template>
        </div>
        <form
            method="dialog"
            class="modal-backdrop">
            <button>#</button>
        </form>
    </dialog>
</template>
