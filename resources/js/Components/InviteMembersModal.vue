<!-- resources/js/Components/InviteMembersModal.vue -->
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'success']);

// Form data
const phoneNumbers = ref('');
const isSubmitting = ref(false);
const results = ref([]);
const errors = ref([]);

// Computed properties
const phoneArray = computed(() => {
    if (!phoneNumbers.value.trim()) return [];
    
    // Split by comma, semicolon, or newline
    const phones = phoneNumbers.value.split(/[,;\n]/)
        .map(phone => phone.trim())
        .filter(phone => phone.length > 0);
    
    return phones;
});

const isValidPhoneCount = computed(() => {
    const validPhones = phoneArray.value.filter(phone => {
        // Remove all non-digit characters
        const cleanPhone = phone.replace(/\D/g, '');
        return cleanPhone.length === 10 && cleanPhone.startsWith('07');
    });
    
    return validPhones.length;
});

const invalidPhones = computed(() => {
    return phoneArray.value.filter(phone => {
        const cleanPhone = phone.replace(/\D/g, '');
        return cleanPhone.length !== 10 || !cleanPhone.startsWith('07');
    });
});

// Validation
const validatePhones = () => {
    errors.value = [];
    
    if (phoneArray.value.length === 0) {
        errors.value.push('Please enter at least one phone number');
        return false;
    }
    
    if (phoneArray.value.length > 100) {
        errors.value.push('Maximum 100 phone numbers allowed per invitation');
        return false;
    }
    
    if (invalidPhones.value.length > 0) {
        errors.value.push(`${invalidPhones.value.length} invalid phone number(s): ${invalidPhones.value.slice(0, 5).join(', ')}${invalidPhones.value.length > 5 ? '...' : ''}`);
        return false;
    }
    
    return true;
};

// Format phone numbers for WhatsApp
const formatPhoneForWhatsApp = (phone) => {
    // Remove all non-digit characters
    const cleanPhone = phone.replace(/\D/g, '');
    
    // Convert to international format
    if (cleanPhone.startsWith('07')) {
        return '254' + cleanPhone.substring(2) + '@c.us';
    }
    
    return cleanPhone + '@c.us';
};

// Send invitations
const sendInvitations = async () => {
    if (!validatePhones()) return;
    
    isSubmitting.value = true;
    errors.value = [];
    results.value = [];
    
    try {
        const response = await axios.post(route('whatsapp.invite'), {
            phone_numbers: phoneArray.value
        });
        
        if (response.data.success) {
            results.value = response.data.results;
            
            // Show success message
            if (typeof window !== 'undefined' && window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: 'Invitations Sent',
                    text: response.data.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', window.Toast.stopTimer);
                        toast.addEventListener('mouseleave', window.Toast.resumeTimer);
                    },
                });
            }
            
            // Reset form
            phoneNumbers.value = '';
            
            // Emit success event
            emit('success', response.data.results);
            
            // Close modal after a delay
            setTimeout(() => {
                emit('close');
            }, 2000);
        } else {
            throw new Error(response.data.message || 'Failed to send invitations');
        }
    } catch (error) {
        console.error('Error sending WhatsApp invitations:', error);
        
        if (error.response?.data?.errors) {
            errors.value = Object.values(error.response.data.errors).flat();
        } else {
            errors.value = [error.response?.data?.message || error.message || 'Failed to send invitations'];
        }
        
        // Show error message
        if (typeof window !== 'undefined' && window.Toast) {
            window.Toast.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to send some invitations',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', window.Toast.stopTimer);
                    toast.addEventListener('mouseleave', window.Toast.resumeTimer);
                },
            });
        }
    } finally {
        isSubmitting.value = false;
    }
};

// Close modal
const close = () => {
    if (!isSubmitting.value) {
        emit('close');
        phoneNumbers.value = '';
        errors.value = [];
        results.value = [];
    }
};

// Handle escape key
const handleKeydown = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

// Add event listener
onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

// Clean up
onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay with blur effect -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" @click="close"></div>
            
            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <!-- Modal header -->
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                            <i class="fas fa-paper-plane text-green-600 dark:text-green-400 mr-2"></i>
                            Invite Members via WhatsApp
                        </h3>
                        <button 
                            type="button" 
                            @click="close"
                            class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            :disabled="isSubmitting"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal body -->
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Instructions -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            How to use this feature:
                        </h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
                            <li>Enter phone numbers separated by commas, semicolons, or new lines</li>
                            <li>Format: 07xxxxxxxx (10 digits starting with 07)</li>
                            <li>Maximum 100 phone numbers per invitation</li>
                            <li>System will check if numbers are already registered</li>
                            <li>Only unregistered users will receive invitation messages</li>
                        </ul>
                    </div>

                    <!-- Phone numbers input -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Numbers <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="phoneNumbers"
                            :disabled="isSubmitting"
                            rows="8"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Enter phone numbers here...&#10;Example:&#10;0712345678&#10;0723456789&#10;0734567890"
                        ></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Enter up to 100 phone numbers, separated by commas, semicolons, or new lines
                        </p>
                    </div>

                    <!-- Validation feedback -->
                    <div v-if="errors.length > 0" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-4">
                        <h4 class="font-semibold text-red-900 dark:text-red-100 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Please fix the following errors:
                        </h4>
                        <ul class="text-sm text-red-800 dark:text-red-200 space-y-1 list-disc list-inside">
                            <li v-for="error in errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>

                    <!-- Phone count summary -->
                    <div v-if="phoneArray.length > 0" class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-3 mb-4">
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ phoneArray.length }}</p>
                                <p class="text-gray-600 dark:text-gray-400">Total Numbers</p>
                            </div>
                            <div class="text-center">
                                <p class="font-semibold text-green-600 dark:text-green-400">{{ isValidPhoneCount }}</p>
                                <p class="text-gray-600 dark:text-gray-400">Valid Numbers</p>
                            </div>
                            <div class="text-center">
                                <p class="font-semibold text-red-600 dark:text-red-400">{{ invalidPhones.length }}</p>
                                <p class="text-gray-600 dark:text-gray-400">Invalid Numbers</p>
                            </div>
                        </div>
                    </div>

                    <!-- Results display -->
                    <div v-if="results.length > 0" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-green-900 dark:text-green-100 mb-2">
                            <i class="fas fa-check-circle mr-2"></i>
                            Invitation Results:
                        </h4>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            <div v-for="result in results" :key="result.phone" class="flex items-center justify-between text-sm py-2 border-b border-green-200 dark:border-green-700 last:border-0">
                                <div class="flex items-center">
                                    <i 
                                        :class="[
                                            'fas mr-2',
                                            result.status === 'sent' ? 'fa-check-circle text-green-600' : 
                                            result.status === 'skipped' ? 'fa-exclamation-circle text-yellow-600' : 
                                            'fa-times-circle text-red-600'
                                        ]"
                                    ></i>
                                    <span class="font-medium">{{ result.phone }}</span>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ result.message }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        @click="sendInvitations"
                        :disabled="isSubmitting || isValidPhoneCount === 0"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        <span v-if="!isSubmitting">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Invitations ({{ isValidPhoneCount }})
                        </span>
                        <span v-else>
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Sending...
                        </span>
                    </button>
                    <button
                        type="button"
                        @click="close"
                        :disabled="isSubmitting"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
