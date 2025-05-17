<script setup lang="ts">
import { ref } from "vue";
//Toast
import { toast } from "../tools/ToastPlugin";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();
// Headless UI
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from "@headlessui/vue";
// Heroicons
import { XMarkIcon, UserPlusIcon, UserIcon, ArrowRightEndOnRectangleIcon } from "@heroicons/vue/24/outline";
import { EnvelopeIcon, LockClosedIcon, ArrowUturnRightIcon, ArrowUturnLeftIcon } from "@heroicons/vue/20/solid";

//Data
const props = defineProps({
    enable_register: {
        type: Boolean,
        default: false,
    },
    lost_password_url: {
        type: String,
        default: "",
    },
    allow_anonymous_register: {
        type: Boolean,
        default: false,
    },
    rest_nonce: {
        type: String,
        default: "",
    },
});

const panelOpen = ref(false);
const isSignIn = ref(true);

//普通注册
const loginEmail = ref("");
const loginPassword = ref("");
const loginRemember = ref(false);
const regEmail = ref("");
const regName = ref("");
const regPassword = ref("");
const regPasswordConfirm = ref("");
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
            //等待
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    } catch (err) {
        errorMsg.value = t("network_error");
        console.error(err);
    }

    loading.value = false;
}

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
            //等待
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    } catch (err) {
        errorMsg.value = t("network_error");
        console.error(err);
    }

    loading.value = false;
}
</script>

<template>
    <button
        type="button"
        class="inline-flex items-center justify-center gap-2 rounded-lg border bg-blue-500 text-white border-gray-200 px-3 py-2 text-sm leading-5 font-semibold hover:border-gray-300 hover:text-white-900 hover:shadow-xs focus:ring-3 focus:ring-gray-300/25 active:border-gray-200 active:shadow-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:text-gray-200 dark:focus:ring-gray-600/40 dark:active:border-gray-700"
        @click="panelOpen = true">
        <UserIcon class="size-5" />
        {{ $t("sign_panel_btn") }}
    </button>

    <TransitionRoot
        appear
        :show="panelOpen"
        as="template">
        <Dialog
            as="div"
            class="relative z-50"
            @close="panelOpen = false">
            <!-- Box Shade -->
            <div
                class="fixed inset-0 bg-black/30"
                aria-hidden="true" />
            <!-- Panel -->
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <DialogPanel class="w-full max-w-md rounded bg-white dark:bg-gray-800 shadow-xl">
                    <DialogTitle class="flex items-center justify-between text-lg font-bold px-6 pt-6 pb-2">
                        <span class="flex items-center gap-2 text-gray-900 dark:text-gray-100">
                            <UserPlusIcon
                                v-if="!isSignIn"
                                class="size-7 mr-1" />
                            <UserIcon
                                v-else
                                class="size-7 mr-1" />
                            {{ isSignIn ? $t("sign_in") : $t("sign_up") }}
                        </span>
                        <button
                            class="px-2 py-1 rounded text-gray-700 dark:text-gray-200"
                            @click="panelOpen = false">
                            <XMarkIcon
                                class="size-7 opacity-40"
                                aria-hidden="true" />
                        </button>
                    </DialogTitle>
                    <div class="flex flex-col overflow-hidden rounded-lg bg-white shadow-xs dark:bg-gray-800 dark:text-gray-100">
                        <div class="grow p-5 md:px-12 md:py-6">
                            <form
                                @submit.prevent="isSignIn ? handleLogin() : handleRegister()"
                                class="space-y-6">
                                <div
                                    v-if="errorMsg"
                                    class="text-red-500 text-sm mb-2">
                                    {{ errorMsg }}
                                </div>
                                <!-- Sign In -->
                                <template v-if="isSignIn">
                                    <div class="space-y-1">
                                        <label
                                            for="email"
                                            class="inline-block ml-2 mb-1 text-sm font-medium">
                                            {{ $t("auth_email") }}
                                        </label>
                                        <div class="relative">
                                            <EnvelopeIcon class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400" />
                                            <input
                                                v-model="loginEmail"
                                                type="email"
                                                id="email"
                                                name="email"
                                                :placeholder="$t('auth_email_placeholder')"
                                                class="block w-full rounded-lg text-base font-medium border border-gray-200 px-10 py-3 leading-6 placeholder-gray-500 focus:border-blue-500 focus:ring-3 focus:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:placeholder-gray-400 dark:focus:border-blue-500" />
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label
                                            for="password"
                                            class="inline-block ml-2 mb-1 text-sm font-medium">
                                            {{ $t("auth_password") }}
                                        </label>
                                        <div class="relative">
                                            <LockClosedIcon class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400" />
                                            <input
                                                v-model="loginPassword"
                                                type="password"
                                                id="password"
                                                name="password"
                                                :placeholder="$t('auth_password_placeholder')"
                                                class="block w-full rounded-lg text-base font-medium border border-gray-200 px-10 py-3 leading-6 placeholder-gray-500 focus:border-blue-500 focus:ring-3 focus:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:placeholder-gray-400 dark:focus:border-blue-500" />
                                        </div>
                                    </div>
                                    <div class="mb-5 flex items-center justify-between gap-2">
                                        <label class="flex items-center">
                                            <input
                                                v-model="loginRemember"
                                                type="checkbox"
                                                id="remember"
                                                name="remember"
                                                class="size-4 rounded-sm border border-gray-200 text-blue-500 checked:border-blue-500 focus:border-blue-500 focus:ring-3 focus:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:ring-offset-gray-900 dark:checked:border-transparent dark:checked:bg-blue-500 dark:focus:border-blue-500" />
                                            <span class="ml-2 text-sm">
                                                {{ $t("remember") }}
                                            </span>
                                        </label>
                                        <a
                                            :href="props.lost_password_url"
                                            class="inline-block text-sm font-medium text-blue-600 hover:text-blue-400 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $t("forgot_password") }}
                                        </a>
                                    </div>
                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-700 bg-blue-700 px-6 py-3 leading-6 font-semibold text-white hover:border-blue-600 hover:bg-blue-600 hover:text-white focus:ring-3 focus:ring-blue-400/50 active:border-blue-700 active:bg-blue-700 dark:focus:ring-blue-400/90">
                                        <ArrowRightEndOnRectangleIcon class="size-5" />
                                        <span>{{ $t("sign_in") }}</span>
                                    </button>
                                </template>
                                <!-- Sign Up -->
                                <template v-else>
                                    <div class="space-y-1">
                                        <label
                                            for="reg_name"
                                            class="inline-block ml-2 mb-1 text-sm font-medium">
                                            {{ $t("auth_username") }}
                                        </label>
                                        <div class="relative">
                                            <UserIcon class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400" />
                                            <input
                                                v-model="regName"
                                                type="text"
                                                id="reg_name"
                                                name="reg_name"
                                                :placeholder="$t('auth_username_placeholder')"
                                                class="block w-full rounded-lg text-base font-medium border border-gray-200 px-10 py-3 leading-6 placeholder-gray-500 focus:border-blue-500 focus:ring-3 focus:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:placeholder-gray-400 dark:focus:border-blue-500" />
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label
                                            for="reg_email"
                                            class="inline-block ml-2 mb-1 text-sm font-medium">
                                            {{ $t("auth_email") }}
                                        </label>
                                        <div class="relative">
                                            <EnvelopeIcon class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400" />
                                            <input
                                                v-model="regEmail"
                                                type="email"
                                                id="reg_email"
                                                name="reg_email"
                                                :placeholder="$t('auth_email_placeholder')"
                                                class="block w-full rounded-lg text-base font-medium border border-gray-200 px-10 py-3 leading-6 placeholder-gray-500 focus:border-blue-500 focus:ring-3 focus:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:placeholder-gray-400 dark:focus:border-blue-500" />
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label
                                            for="reg_password"
                                            class="inline-block ml-2 mb-1 text-sm font-medium">
                                            {{ $t("auth_password") }}
                                        </label>
                                        <div class="relative">
                                            <LockClosedIcon class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400" />
                                            <input
                                                v-model="regPassword"
                                                type="password"
                                                id="reg_password"
                                                name="reg_password"
                                                :placeholder="$t('auth_password_placeholder')"
                                                class="block w-full rounded-lg border border-gray-200 px-10 py-3 leading-6 placeholder-gray-500 focus:border-blue-500 focus:ring-3 focus:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:placeholder-gray-400 dark:focus:border-blue-500" />
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label
                                            for="reg_password_confirm"
                                            class="inline-block ml-2 mb-1 text-sm font-medium">
                                            {{ $t("auth_password_confirm") }}
                                        </label>
                                        <div class="relative">
                                            <LockClosedIcon class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400" />
                                            <input
                                                v-model="regPasswordConfirm"
                                                type="password"
                                                id="reg_password_confirm"
                                                name="reg_password_confirm"
                                                :placeholder="$t('auth_password_confirm_placeholder')"
                                                class="block w-full rounded-lg border border-gray-200 px-10 py-3 leading-6 placeholder-gray-500 focus:border-blue-500 focus:ring-3 focus:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:placeholder-gray-400 dark:focus:border-blue-500" />
                                        </div>
                                    </div>
                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-700 bg-blue-700 px-6 py-3 leading-6 font-semibold text-white hover:border-blue-600 hover:bg-blue-600 hover:text-white focus:ring-3 focus:ring-blue-400/50 active:border-blue-700 active:bg-blue-700 dark:focus:ring-blue-400/90">
                                        <ArrowRightEndOnRectangleIcon class="size-5" />
                                        <span>{{ $t("sign_up") }}</span>
                                    </button>
                                </template>
                            </form>
                        </div>
                        <div class="grow p-5 bg-gray-50 text-center gap-2 text-sm font-medium md:px-16 dark:bg-gray-700/50">
                            <div v-if="props.enable_register">
                                <template v-if="isSignIn">
                                    <span class="mr-3">{{ $t("already_have_account") }}</span>
                                    <a
                                        href="#"
                                        class="text-blue-600 hover:text-blue-400 dark:text-blue-400 dark:hover:text-blue-300"
                                        @click.prevent="isSignIn = false">
                                        <ArrowUturnRightIcon class="size-3 align-middle mr-1 inline-block" />
                                        {{ $t("sign_up") }}
                                    </a>
                                </template>
                                <template v-else>
                                    <a
                                        href="#"
                                        class="text-blue-600 hover:text-blue-400 dark:text-blue-400 dark:hover:text-blue-300"
                                        @click.prevent="isSignIn = true">
                                        <ArrowUturnLeftIcon class="size-3 align-middle mr-1 inline-block" />
                                        {{ $t("return_sign_in") }}
                                    </a>
                                </template>
                            </div>
                            <div v-else>
                                {{ $t("register_closed_tip") }}
                            </div>
                        </div>
                    </div>
                </DialogPanel>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
