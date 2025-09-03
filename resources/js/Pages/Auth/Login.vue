<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import ReCaptcha from '@/Components/ReCaptcha.vue';
import { ref } from 'vue';
import Swal from 'sweetalert2';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const activeTab = ref('password'); // 'password' or 'otp'
const otpSent = ref(false);
const otpCountdown = ref(0);
const otpTimer = ref(null);

const form = useForm({
    login: "", // For both email and telephone
    password: "",
    otp: "", // For OTP login
    remember: false,
    'g-recaptcha-response': '',
});

const setCaptchaResponse = (response) => {
    form['g-recaptcha-response'] = response;
};

// Send OTP function
const sendOtp = async () => {
    if (!form.login) {
        form.errors.login = 'Telephone number is required';
        return;
    }

    try {
        const response = await axios.post('/api/auth/request-otp', {
            telephone: form.login
        }, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.data.status === 'success') {
            otpSent.value = true;
            startOtpCountdown();
            form.errors.login = ''; // Clear any previous errors
            
            // Show success toast
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: response.data.message || 'OTP sent successfully',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            throw new Error(response.data.message || 'Failed to send OTP');
        }
    } catch (error) {
        console.error('OTP Error:', error);
        const errorMessage = error.response?.data?.message || error.message || 'Failed to send OTP. Please try again.';
        form.errors.login = errorMessage;
        
        // Show error toast
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: errorMessage,
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
    }
};

// Start OTP countdown timer
const startOtpCountdown = () => {
    otpCountdown.value = 60; // 60 seconds countdown
    clearInterval(otpTimer.value);
    otpTimer.value = setInterval(() => {
        if (otpCountdown.value > 0) {
            otpCountdown.value--;
        } else {
            clearInterval(otpTimer.value);
        }
    }, 1000);
};

// Switch tabs
const switchTab = (tab) => {
    activeTab.value = tab;
    form.clearErrors();
    otpSent.value = false;
    clearInterval(otpTimer.value);
    otpCountdown.value = 0;
};

const submit = async () => {
    if (activeTab.value === 'password') {
        try {
            const response = await axios.post(route('login'), {
                login: form.login,
                password: form.password,
                remember: form.remember ? "on" : ""
            });

            // Show success message
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Login successful!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Redirect to auto-login after showing success message
                window.location.href = '/dashboard';
            });

        } catch (error) {
            // Handle login error
            let errorMessage = 'An error occurred during login.';
            
            if (error.response?.data?.errors) {
                form.errors = error.response.data.errors;
                errorMessage = Object.values(error.response.data.errors).flat().join(' ');
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            // Show error message
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: errorMessage,
                showConfirmButton: false,
                timer: 3000
            });
        }
    } else {
        // OTP login flow
        try {
            const response = await axios.post('/api/auth/verify-otp', {
                telephone: form.login,
                otp: form.otp,
                remember: form.remember
            }, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Show success message
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'OTP verified successfully!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Redirect to auto-login after showing success message
                window.location.href = `${route('auto.login')}?telephone=${encodeURIComponent(form.login)}`;
            });
        } catch (error) {
            console.error('OTP Verification Error:', error);
            form.errors.otp = error.response?.data?.message || error.message || 'An error occurred. Please try again.';
        }
    }
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

            <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                Sign in to your account
            </h1>

            <!-- Login Method Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="me-2">
                        <button
                            @click="switchTab('password')"
                            :class="{
                                'inline-block p-4 border-b-2 rounded-t-lg': true,
                                'border-green-600 text-green-600 dark:text-green-500 dark:border-green-500': activeTab === 'password',
                                'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300': activeTab !== 'password'
                            }"
                        >
                            Email & Password
                        </button>
                    </li>
                    <li class="me-2">
                        <button
                            @click="switchTab('otp')"
                            :class="{
                                'inline-block p-4 border-b-2 rounded-t-lg': true,
                                'border-green-600 text-green-600 dark:text-green-500 dark:border-green-500': activeTab === 'otp',
                                'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300': activeTab !== 'otp'
                            }"
                            aria-current="page"
                        >
                            Telephone & OTP
                        </button>
                    </li>
                </ul>
            </div>

            <form @submit.prevent="submit" class="space-y-4 md:space-y-6">
                <!-- Email/Telephone Input -->
                <div>
                    <InputLabel 
                        :for="activeTab === 'password' ? 'login' : 'telephone'" 
                        :value="activeTab === 'password' ? 'Email Address' : 'Telephone Number'" 
                    />
                    <TextInput
                        :id="activeTab === 'password' ? 'login' : 'telephone'"
                        v-model="form.login"
                        :type="activeTab === 'password' ? 'email' : 'tel'"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        :autocomplete="activeTab === 'password' ? 'username' : 'tel'"
                        :placeholder="activeTab === 'password' ? 'Enter your email address' : 'Enter your telephone number'"
                    />
                    <InputError class="mt-2" :message="form.errors.login || form.errors.email" />
                </div>

                <!-- Password Input (only for password tab) -->
                <div v-if="activeTab === 'password'">
                    <div class="flex items-center justify-between">
                        <InputLabel for="password" value="Password" />
                        <Link :href="route('password.request')"
                            class="text-sm text-green-600 hover:text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium hover:underline hover:decoration-green-600 hover:dark:decoration-green-500 underline-offset-4 dark:text-green-500"
                        >
                            Forgot your password?
                        </Link>
                    </div>
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        placeholder="********"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- OTP Input (only for OTP tab) -->
                <div v-if="activeTab === 'otp'">
                    <div class="flex items-center justify-between">
                        <InputLabel for="otp" value="Verification Code" />
                        <button
                            type="button"
                            @click="sendOtp"
                            :disabled="otpCountdown > 0"
                            :class="{
                                'text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium underline-offset-4': true,
                                'text-green-600 hover:text-green-900 hover:underline hover:decoration-green-600 dark:text-green-500': otpCountdown === 0,
                                'text-gray-400 cursor-not-allowed': otpCountdown > 0
                            }"
                        >
                            {{ otpCountdown > 0 ? `Resend in ${otpCountdown}s` : 'Send OTP' }}
                        </button>
                    </div>
                    <TextInput
                        id="otp"
                        v-model="form.otp"
                        type="text"
                        class="mt-1 block w-full"
                        :required="otpSent"
                        :disabled="!otpSent"
                        maxlength="6"
                        placeholder="Enter 6-digit code"
                    />
                    <InputError class="mt-2" :message="form.errors.otp" />
                    
                    <div v-if="otpSent" class="mt-2 text-sm text-green-600">
                        Verification code sent to your telephone
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember" class="flex items-center">
                        <Checkbox
                            id="remember"
                            name="remember"
                            v-model:checked="form.remember"
                        />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ $page.props.rememberMe ? $page.props.rememberMe : 'Remember me' }}
                        </span>
                    </label>
                </div>

                <!-- reCAPTCHA Component -->
                <div class="mt-4">
                    <ReCaptcha 
                        action="login" 
                        :site-key="$page.props.recaptchaSiteKey" 
                        @captcha-response="setCaptchaResponse" 
                    />
                </div>

                <!-- Sign In Button -->
                <div class="mt-4">
                    <PrimaryButton
                        class="w-full"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ activeTab === 'password' ? 'Sign In' : 'Verify & Sign In' }}
                    </PrimaryButton>
                </div>

                <!-- Sign Up Link -->
                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Don't have an account yet?
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