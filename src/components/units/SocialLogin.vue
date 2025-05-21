<script setup lang="ts">
import { ref } from "vue";
//Toast
import { toast } from "../tools/ToastPlugin";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();

// 接收父组件传递的属性
const props = defineProps({
    rest_nonce: {
        type: String,
        required: true,
    },
});

// 定义事件
const emit = defineEmits(["login-success", "update:loading"]);

// 加载状态
const loadingProvider = ref("");

// 处理社交登录
async function handleSocialLogin(provider) {
    loadingProvider.value = provider;
    emit("update:loading", true);

    try {
        // 构建OAuth授权URL
        let authUrl;

        if (provider === "google") {
            // 获取Google的授权URL
            const response = await fetch("/api/aiya/v1/oauth/google/url", {
                method: "GET",
                headers: {
                    "X-WP-Nonce": props.rest_nonce,
                },
            });
            const data = await response.json();

            if (data.success && data.data?.url) {
                authUrl = data.data.url;
            } else {
                throw new Error(data.data?.detail || "Failed to get authorization URL");
            }
        } else if (provider === "github") {
            // 获取GitHub的授权URL
            const response = await fetch("/api/aiya/v1/oauth/github/url", {
                method: "GET",
                headers: {
                    "X-WP-Nonce": props.rest_nonce,
                },
            });
            const data = await response.json();

            if (data.success && data.data?.url) {
                authUrl = data.data.url;
            } else {
                throw new Error(data.data?.detail || "Failed to get authorization URL");
            }
        }

        // 打开OAuth授权窗口
        if (authUrl) {
            // 存储当前URL，以便登录后重定向回来
            sessionStorage.setItem("auth_redirect", window.location.href);

            // 打开新窗口进行授权
            const oauthWindow = window.open(authUrl, "oauth", "width=600,height=600");

            // 检查授权窗口是否被阻止
            if (!oauthWindow || oauthWindow.closed || typeof oauthWindow.closed === "undefined") {
                toast(t("oauth_popup_blocked"));
            }

            // 轮询检查登录状态
            const checkLoginInterval = setInterval(async () => {
                try {
                    const statusRes = await fetch("/api/aiya/v1/oauth/status", {
                        method: "GET",
                        headers: {
                            "X-WP-Nonce": props.rest_nonce,
                        },
                    });
                    const statusData = await statusRes.json();

                    if (statusData.success && statusData.data?.logged_in) {
                        clearInterval(checkLoginInterval);
                        emit("login-success", statusData.data);

                        if (oauthWindow && !oauthWindow.closed) {
                            oauthWindow.close();
                        }
                    }
                } catch (e) {
                    console.error("Error checking login status:", e);
                }
            }, 2000);

            // 60秒后停止检查
            setTimeout(() => {
                clearInterval(checkLoginInterval);
                loadingProvider.value = "";
                emit("update:loading", false);
            }, 60000);
        }
    } catch (err) {
        console.error("Social login error:", err);
        toast(t("oauth_error"));
    }

    loadingProvider.value = "";
    emit("update:loading", false);
}
</script>

<template>
    <div class="social-login-container">
        <!-- SSO Mode -->
        <div class="flex gap-2 justify-center">
            <!-- Google登录 -->
            <button
                type="button"
                class="btn btn-outline"
                :class="{ 'btn-disabled': loadingProvider !== '' }"
                @click="handleSocialLogin('google')">
                <span
                    v-if="loadingProvider === 'google'"
                    class="loading loading-spinner loading-sm"></span>
                <svg
                    v-else
                    class="size-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24">
                    <path
                        fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.3v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.08z" />
                    <path
                        fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path
                        fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                    <path
                        fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                <span class="ml-2">Google</span>
            </button>

            <!-- GitHub登录 -->
            <button
                type="button"
                class="btn btn-outline"
                :class="{ 'btn-disabled': loadingProvider !== '' }"
                @click="handleSocialLogin('github')">
                <span
                    v-if="loadingProvider === 'github'"
                    class="loading loading-spinner loading-sm"></span>
                <svg
                    v-else
                    class="size-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24">
                    <path
                        fill="currentColor"
                        d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z" />
                </svg>
                <span class="ml-2">GitHub</span>
            </button>
        </div>
    </div>
    <div class="mx-12 my-4">
        <div class="divider">
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800">{{ $t("or_register_in") }}</span>
        </div>
    </div>
</template>
