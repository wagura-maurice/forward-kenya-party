<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, computed, watch, reactive } from 'vue';

const verificationResult = ref(null);
const isLoading = ref(false);
const form = reactive({
    national_id: '',
    errors: {}
});

// Reset verification result when national_id changes
watch(() => form.national_id, () => {
    if (verificationResult.value) {
        verificationResult.value = null;
    }
    // Clear errors when typing
    if (form.errors?.national_id) {
        form.errors = {};
    }
});

const verify = async () => {
    isLoading.value = true;
    verificationResult.value = null;
    form.errors = {};

    try {
        const response = await axios.post(route('frontend.verify.membership.request'), {
            national_id: form.national_id
        });

        verificationResult.value = response.data;
    } catch (error) {
        if (error.response?.status === 404) {
            verificationResult.value = {
                success: false,
                message: error.response.data.message || 'No member found with the provided National ID.'
            };
        } else if (error.response?.status === 422) {
            // Handle validation errors
            form.errors = error.response.data.errors || {};
            verificationResult.value = {
                success: false,
                message: 'Please correct the errors in the form.'
            };
        } else {
            verificationResult.value = {
                success: false,
                message: 'An error occurred while verifying membership. Please try again.'
            };
        }
    } finally {
        isLoading.value = false;
    }
};

// Computed property to determine if we should show the verification form
const showVerificationForm = computed(() => {
    return !verificationResult.value || !verificationResult.value.success;
});

// Function to reset and verify another ID
const verifyAnother = () => {
    verificationResult.value = null;
    form.national_id = "";
};
</script>

<template>
    <Head title="Verify Membership" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="p-2 space-y-4 md:space-y-6 sm:p-8">
            <!-- Header -->
            <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                Verify Party Membership
            </h1>

            <!-- Description -->
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Enter the National ID number to verify membership status.
            </p>

            <!-- Error Message -->
            <div v-if="verificationResult && !verificationResult.success" 
                 class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
                {{ verificationResult.message }}
            </div>

            <!-- Verification Form -->
            <div v-if="showVerificationForm" class="space-y-4">
                <form @submit.prevent="verify" class="space-y-4">
                    <!-- National ID Input -->
                    <div>
                        <InputLabel for="national_id" value="National Identification Number" />
                        <TextInput
                            id="national_id"
                            v-model="form.national_id"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            :autofocus="!verificationResult"
                            placeholder="Enter 8-digit National ID"
                            maxlength="8"
                            pattern="[0-9]{8}"
                            title="Please enter exactly 8 digits"
                        />
                        <InputError class="mt-2" :message="form.errors.national_id" />
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <PrimaryButton
                            class="w-full justify-center"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing || isLoading"
                        >
                            <span v-if="isLoading">Verifying...</span>
                            <span v-else>Verify Membership</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>

            <!-- Verification Result -->
            <div v-if="verificationResult?.success" class="mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Verification Result</h2>
                    <button
                        @click="verifyAnother"
                        class="text-sm text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                    >
                        Verify Another ID
                    </button>
                </div>
                
                <div :class="{
                    'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800': verificationResult.data.status_code === 'active',
                    'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800': ['pending', 'processing'].includes(verificationResult.data.status_code),
                    'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800': verificationResult.data.status_code === 'rejected'
                }" class="border rounded-lg p-4">
                    <h3 :class="{
                        'text-green-800 dark:text-green-200': verificationResult.data.status_code === 'active',
                        'text-yellow-800 dark:text-yellow-200': ['pending', 'processing'].includes(verificationResult.data.status_code),
                        'text-red-800 dark:text-red-200': verificationResult.data.status_code === 'rejected',
                    }" class="text-lg font-medium mb-3">
                        <template v-if="verificationResult.data.status_code === 'active'">
                            ✓ Membership Active
                        </template>
                        <template v-else-if="verificationResult.data.status_code === 'pending'">
                            ⏳ Membership Pending Review
                        </template>
                        <template v-else-if="verificationResult.data.status_code === 'processing'">
                            🔄 Membership Processing
                        </template>
                        <template v-else-if="verificationResult.data.status_code === 'rejected'">
                            ❌ Membership Rejected
                        </template>
                        <template v-else>
                            ℹ️ Membership Status: {{ verificationResult.data.status }}
                        </template>
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p>
                                <p class="font-medium">{{ verificationResult.data.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">National ID</p>
                                <p class="font-mono">{{ verificationResult.data.national_id }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Membership Status</p>
                                <span 
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': verificationResult.data.status_code === 'active',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': ['pending', 'processing'].includes(verificationResult.data.status_code),
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': verificationResult.data.status_code === 'rejected'
                                    }"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                >
                                    {{ verificationResult.data.status }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Registration Number</p>
                                <p class="font-mono">{{ verificationResult.data.registration_number || 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">County</p>
                                <p>{{ verificationResult.data.county }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Constituency</p>
                                <p>{{ verificationResult.data.constituency }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ward</p>
                                <p>{{ verificationResult.data.ward }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Registration Date</p>
                                <p>{{ verificationResult.data.registration_date }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Last Verified</p>
                                <p>{{ verificationResult.data.last_verified }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Print Button -->
                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        @click="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Verification
                    </button>
                </div>
            </div>
        </div>
    </AuthenticationCard>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
        color: black !important;
    }
    
    .bg-white {
        background-color: white !important;
    }
    
    .text-gray-900 {
        color: black !important;
    }
    
    .border-gray-200 {
        border-color: #e5e7eb !important;
    }
    
    .shadow {
        box-shadow: none !important;
    }
}
</style>