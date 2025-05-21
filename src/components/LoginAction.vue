<script setup lang="ts">
import { ref, shallowRef, onMounted, onUnmounted } from "vue";
// Heroicons
import { XMarkIcon, UserPlusIcon, UserIcon } from "@heroicons/vue/24/outline";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();

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

const modalOpen = ref(false);
const isSignIn = ref(true);
const loading = ref(false);

// 组件引用
const loginFormRef = ref(null);
const registerFormRef = ref(null);

// 打开登录模态框
function openLoginModal() {
    isSignIn.value = true;
    modalOpen.value = true;
    // 防止背景滚动
    document.body.classList.add("overflow-hidden");
}

// 打开注册模态框
function openRegisterModal() {
    if (!props.enable_register) return;
    isSignIn.value = false;
    modalOpen.value = true;
    // 防止背景滚动
    document.body.classList.add("overflow-hidden");
}

function closeModal() {
    modalOpen.value = false;
    // 恢复滚动
    document.body.classList.remove("overflow-hidden");

    // 重置表单
    setTimeout(() => {
        if (loginFormRef.value) loginFormRef.value.resetForm();
        if (registerFormRef.value) registerFormRef.value.resetForm();
    }, 300);
}

// 切换到登录表单
function switchToLogin() {
    isSignIn.value = true;
}

// 切换到注册表单
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

// 确保组件卸载时移除类
onUnmounted(() => {
    if (modalOpen.value) {
        document.body.classList.remove("overflow-hidden");
    }
});
</script>

<template>
    <div class="flex items-center">
        <!-- Login -->
        <button
            type="button"
            class="btn btn-primary mr-2"
            @click="openLoginModal">
            <UserIcon class="size-5" />
            {{ $t("sign_in") }}
        </button>
        <!-- Register -->
        <button
            v-if="props.enable_register"
            type="button"
            class="btn btn-outline btn-primary hidden lg:flex"
            @click="openRegisterModal">
            <UserPlusIcon class="size-5" />
            {{ $t("sign_up") }}
        </button>
    </div>

    <div
        v-if="modalOpen"
        class="modal-container z-50">
        <!-- Backdrop -->
        <div
            class="backdrop-overlay"
            @click="closeModal">
        </div>

        <div class="modal-content">
            <!-- 模态框标题和关闭按钮 -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg flex items-center">
                    <UserPlusIcon
                        v-if="!isSignIn"
                        class="size-6 mr-2" />
                    <UserIcon
                        v-else
                        class="size-6 mr-2" />
                    {{ isSignIn ? $t("sign_in") : $t("sign_up") }}
                </h3>
                <button
                    class="btn btn-sm btn-circle btn-ghost"
                    @click="closeModal">
                    <XMarkIcon class="size-5" />
                </button>
            </div>

            <SocialLogin
                v-if="props.enable_sso_register"
                :rest_nonce="props.rest_nonce"
                v-model:loading="loading"
                @login-success="handleAuthSuccess" />

            <LoginForm
                v-if="isSignIn"
                ref="loginFormRef"
                :lost_password_url="props.lost_password_url"
                :rest_nonce="props.rest_nonce"
                v-model:loading="loading"
                @login-success="handleAuthSuccess"
                @switch-to-register="switchToRegister" />

            <RegisterForm
                v-else
                ref="registerFormRef"
                :rest_nonce="props.rest_nonce"
                v-model:loading="loading"
                @register-success="handleAuthSuccess"
                @switch-to-login="switchToLogin" />
        </div>
    </div>
</template>

<style scoped>
.modal-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}

.backdrop-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(2px);
    z-index: -1;
}

.modal-content {
    background-color: var(--b1, hsl(0, 0%, 100%));
    color: var(--bc, hsl(0, 0%, 12%));
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    width: 95%;
    max-width: 28rem;
    max-height: 90vh;
    overflow-y: auto;
    padding: 1.5rem;
    margin: 1rem;
    animation: modal-pop 0.2s ease-out;
}

@media (min-width: 640px) {
    .modal-content {
        width: 90%;
    }
}

/* 用于防止页面滚动时的位移 */
:global(body.overflow-hidden) {
    overflow: hidden;
}

@keyframes modal-pop {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
