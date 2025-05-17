<script setup lang="ts">
// ...existing code...
const showAnonymous = ref(false);
const anonymousInfo = ref<{ email: string; password: string; display_name: string } | null>(null);
const anonymousLoading = ref(false);
const anonymousError = ref("");

// 获取匿名注册信息
async function fetchAnonymousInfo() {
    anonymousError.value = "";
    anonymousLoading.value = true;
    try {
        const res = await fetch("/api/aiya/v1/anonymous-register-info", {
            method: "GET",
            headers: {
                "X-WP-Nonce": props.rest_nonce,
            },
        });
        const data = await res.json();
        if (!data.success) {
            anonymousError.value = data.data?.detail || data.data || t("register_failed");
            anonymousInfo.value = null;
        } else {
            anonymousInfo.value = data.data;
        }
    } catch (err) {
        anonymousError.value = t("network_error");
        anonymousInfo.value = null;
    }
    anonymousLoading.value = false;
}

// 提交匿名注册
async function handleAnonymousRegister() {
    if (!anonymousInfo.value) return;
    anonymousError.value = "";
    anonymousLoading.value = true;
    try {
        const res = await fetch("/api/aiya/v1/anonymous-register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.rest_nonce,
            },
            body: JSON.stringify({
                email: anonymousInfo.value.email,
                password: anonymousInfo.value.password,
                display_name: anonymousInfo.value.display_name,
            }),
        });
        const data = await res.json();
        if (!data.success) {
            anonymousError.value = data.data?.detail || data.data || t("register_failed");
        } else {
            toast(data.data?.message);
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    } catch (err) {
        anonymousError.value = t("network_error");
    }
    anonymousLoading.value = false;
}
</script>

<template>
    <!-- ...existing code... -->
    <TransitionRoot
        appear
        :show="panelOpen"
        as="template">
        <Dialog
            as="div"
            class="relative z-50"
            @close="panelOpen = false">
            <!-- ...existing code... -->
            <div class="flex flex-col overflow-hidden rounded-lg bg-white shadow-xs dark:bg-gray-800 dark:text-gray-100">
                <div class="grow p-5 md:px-12 md:py-6">
                    <form
                        v-if="!showAnonymous"
                        @submit.prevent="isSignIn ? handleLogin() : handleRegister()"
                        class="space-y-6">
                        <!-- ...原有登录/注册表单... -->
                        <div
                            class="text-center mt-4"
                            v-if="!isSignIn && props.enable_register">
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-blue-600"
                                @click="
                                    showAnonymous = true;
                                    fetchAnonymousInfo();
                                ">
                                <UserIcon class="size-4" />
                                {{ $t("anonymous_register") }}
                            </button>
                        </div>
                    </form>
                    <!-- 匿名注册表单 -->
                    <div
                        v-else
                        class="space-y-6">
                        <div class="text-lg font-bold text-center mb-2">
                            {{ $t("anonymous_register_title") }}
                        </div>
                        <div
                            v-if="anonymousError"
                            class="text-red-500 text-sm mb-2 text-center"
                            >{{ anonymousError }}</div
                        >
                        <div
                            v-if="anonymousLoading"
                            class="text-center text-gray-400"
                            >{{ $t("loading") }}</div
                        >
                        <div
                            v-else-if="anonymousInfo"
                            class="space-y-3">
                            <div class="flex flex-col gap-2 items-center">
                                <div>
                                    <span class="font-medium">{{ $t("anonymous_email") }}：</span>
                                    <span class="select-all bg-gray-100 px-2 py-1 rounded text-blue-700">{{ anonymousInfo.email }}</span>
                                    <button
                                        class="ml-2 text-xs text-blue-500"
                                        @click="navigator.clipboard.writeText(anonymousInfo.email)">
                                        {{ $t("copy") }}
                                    </button>
                                </div>
                                <div>
                                    <span class="font-medium">{{ $t("anonymous_password") }}：</span>
                                    <span class="select-all bg-gray-100 px-2 py-1 rounded text-blue-700">{{ anonymousInfo.password }}</span>
                                    <button
                                        class="ml-2 text-xs text-blue-500"
                                        @click="navigator.clipboard.writeText(anonymousInfo.password)">
                                        {{ $t("copy") }}
                                    </button>
                                </div>
                                <div>
                                    <span class="font-medium">{{ $t("anonymous_display_name") }}：</span>
                                    <span class="select-all bg-gray-100 px-2 py-1 rounded text-blue-700">{{ anonymousInfo.display_name }}</span>
                                    <button
                                        class="ml-2 text-xs text-blue-500"
                                        @click="navigator.clipboard.writeText(anonymousInfo.display_name)">
                                        {{ $t("copy") }}
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-700 bg-blue-700 px-6 py-3 leading-6 font-semibold text-white hover:border-blue-600 hover:bg-blue-600 focus:ring-3 focus:ring-blue-400/50"
                                :disabled="anonymousLoading"
                                @click="handleAnonymousRegister">
                                <ArrowRightEndOnRectangleIcon class="size-5" />
                                <span>{{ $t("anonymous_register_confirm") }}</span>
                            </button>
                            <button
                                type="button"
                                class="w-full mt-2 text-xs text-gray-400 hover:text-blue-500"
                                @click="showAnonymous = false">
                                {{ $t("back_to_register") }}
                            </button>
                        </div>
                    </div>
                </div>
                <!-- ...底部切换按钮等... -->
            </div>
        </Dialog>
    </TransitionRoot>
</template>
