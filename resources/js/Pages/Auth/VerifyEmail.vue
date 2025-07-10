<!-- resources/js/Pages/Auth/VerifyEmail.vue -->
<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <Head title="Email Verification" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="p-2 space-y-4 md:space-y-6 sm:p-8">
            <div class="mb-4 text-sm text-gray-600">
                Before continuing, could you verify your email address by
                clicking on the link we just emailed to you? If you didn't
                receive the email, we will gladly send you another.
            </div>

            <div
                v-if="verificationLinkSent"
                class="mb-4 font-medium text-sm text-green-600"
            >
                A new verification link has been sent to the email address you
                provided in your profile settings.
            </div>

            <form @submit.prevent="submit" class="space-y-4 md:space-y-6">
                <!-- Resend Verification Email Button -->
                <div>
                    <PrimaryButton
                        class="w-full"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Resend Verification Email
                    </PrimaryButton>
                </div>

                <!-- Edit Profile and Log Out Links -->
                <div class="flex items-center justify-between mt-4">
                    <Link
                        :href="route('profile.show')"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Edit Profile
                    </Link>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Log Out
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticationCard>
</template>
