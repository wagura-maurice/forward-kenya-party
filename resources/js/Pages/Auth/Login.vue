<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    login: "", // Changed from email to login to support both email and phone
    password: "",
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Sign in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="p-2 space-y-4 md:space-y-6 sm:p-8">
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <h1
                class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white"
            >
                Sign in to your account
            </h1>

            <form @submit.prevent="submit" class="space-y-4 md:space-y-6">
                <!-- Login Input (Email or Telephone) -->
                <div>
                    <InputLabel for="login" value="Email Address / Telephone Number" />
                    <TextInput
                        id="login"
                        v-model="form.login"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your email address or telephone number"
                    />
                    <InputError class="mt-2" :message="form.errors.login || form.errors.email" />
                </div>

                <!-- Password Input and Forgot Password -->
                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <!-- Password Label on the Left -->
                        <InputLabel for="password" value="Password" />
                        <!-- Forgot Password Link on the Right -->
                        <Link :href="route('password.request')"
                            class="text-sm text-green-600 hover:text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium hover:underline hover:decoration-green-600 hover:dark:decoration-green-500 underline-offset-4 dark:text-green-500"
                        >
                            Forgot your password?
                        </Link>
                    </div>
                    <!-- Password Input -->
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        placeholder="********"
                    />
                    <!-- Password Error Message -->
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Remember Me -->
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>

                <!-- Sign In Button -->
                <div class="mt-4">
                    <PrimaryButton
                        class="w-full"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Sign In
                    </PrimaryButton>
                </div>

                <!-- Sign Up Link -->
                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Don’t have an account yet?
                    <Link
                        :href="route('register')"
                        class="font-medium text-green-600 hover:underline hover:decoration-green-600 hover:dark:decoration-green-500 underline-offset-4 dark:text-green-500"
                    >
                        Sign Up
                    </Link>
                </p>
            </form>
        </div>
    </AuthenticationCard>
</template>
