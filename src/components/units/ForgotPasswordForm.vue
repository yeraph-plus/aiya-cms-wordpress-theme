<script setup lang="ts">
import { ref } from "vue";
//Heroicons
import { EnvelopeIcon } from "@heroicons/vue/20/solid";
import { ArrowPathIcon } from "@heroicons/vue/24/outline";
//Toast
import { toast } from "../../scripts/toast-plugin";
//i18n
import { useI18n } from "vue-i18n";
const { t } = useI18n();

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

const emit = defineEmits(["update:loading", "switch-to-login", "forgot-password-success"]);

const email = ref("");
const errorMsg = ref("");

//重置表单
function resetForm() {
    email.value = "";
    errorMsg.value = "";
}

//找回密码
async function handleForgotPassword() {
    errorMsg.value = "";
    if (!email.value) {
        errorMsg.value = t("email_required");
        return;
    }

    emit("update:loading", true);

    try {
        const res = await fetch("/api/aiya/v1/forgot_password", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": props.rest_nonce,
            },
            body: JSON.stringify({
                email: email.value,
            }),
        });

        const data = await res.json();

        if (!data.success) {
            errorMsg.value = data.data?.detail || t("forgot_password_failed");
        } else {
            emit("forgot-password-success", data);
            toast(data.data?.message || t("reset_link_sent"), { type: "success" });
        }
    } catch (err) {
        errorMsg.value = t("network_error");
        console.error(err);
    } finally {
        emit("update:loading", false);
    }
}

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

        <!-- ForgotPasswordForm -->
        <form
            @submit.prevent="handleForgotPassword"
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
                        v-model="email"
                        type="email"
                        class="input input-bordered join-item flex-1 w-full"
                        :placeholder="$t('auth_email_placeholder')" />
                </div>
                <label class="label mt-2">
                    <span class="label-text-alt">{{ $t("forgot_password_hint") }}</span>
                </label>
            </div>

            <button
                type="submit"
                class="btn btn-primary w-full"
                :disabled="props.loading">
                <span
                    v-if="props.loading"
                    class="loading loading-spinner"></span>
                <ArrowPathIcon
                    v-else
                    class="size-5" />
                {{ $t("reset_password") }}
            </button>
        </form>
    </div>
</template>
