<!-- resources/js/Components/SyncMembersModal.vue -->
<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
// Using Font Awesome icons instead of Heroicons
// Make sure you have Font Awesome included in your project

defineProps({
    show: {
        type: Boolean,
        default: false
    },
    onClose: {
        type: Function,
        default: () => {}
    }
});

const emit = defineEmits(['close']);

// Sync status states
const syncStatus = ref('idle'); // 'idle', 'syncing', 'success', 'error'
const syncProgress = ref(0);
const syncMessage = ref('');
const syncDetails = ref({
    added: 0,
    updated: 0,
    deleted: 0,
    errors: []
});

// Sync options
const syncOptions = ref({
    syncType: 'both', // 'push', 'pull', 'both'
    forceUpdate: false
});

// Form for sync operation
const form = useForm({
    sync_type: 'both',
    force_update: false
});

// Close modal and reset state
const close = () => {
    syncStatus.value = 'idle';
    syncProgress.value = 0;
    syncMessage.value = '';
    syncDetails.value = { added: 0, updated: 0, deleted: 0, errors: [] };
    emit('close');
};

// Start synchronization process
const startSync = async () => {
    syncStatus.value = 'syncing';
    syncProgress.value = 10;
    syncMessage.value = 'Starting synchronization process...';
    
    try {
        // Simulate progress updates
        const progressInterval = setInterval(() => {
            if (syncProgress.value < 90) {
                syncProgress.value += 10;
            }
        }, 500);

        // Make API call to sync endpoint
        const response = await axios.post(route('members.sync'), form);
        
        clearInterval(progressInterval);
        syncProgress.value = 100;
        
        if (response.data.success) {
            syncStatus.value = 'success';
            syncMessage.value = 'Synchronization completed successfully!';
            syncDetails.value = response.data.data;
            
            // Emit event to refresh members list in parent
            emit('synced', response.data.data);
        } else {
            syncStatus.value = 'error';
            syncMessage.value = response.data.message || 'An error occurred during synchronization.';
            syncDetails.value.errors = response.data.errors || [];
        }
    } catch (error) {
        console.error('Sync error:', error);
        syncStatus.value = 'error';
        syncMessage.value = error.response?.data?.message || 'Failed to synchronize members. Please try again.';
        syncDetails.value.errors = error.response?.data?.errors || [error.message];
    }
};

// Format number with leading zero
const formatNumber = (num) => {
    return num < 10 ? `0${num}` : num;
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="close" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8 relative">
                <!-- Close button -->
                <button 
                    @click="close"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none"
                    :disabled="syncStatus === 'syncing'"
                >
                    <i class="fas fa-times text-xl"></i>
                    <span class="sr-only">Close</span>
                </button>

                <div class="w-full">
                    <!-- Modal header -->
                    <div class="text-center sm:text-left">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <span v-if="syncStatus === 'idle'">Synchronize Members</span>
                            <span v-else-if="syncStatus === 'syncing'" class="flex items-center justify-center sm:justify-start">
                                <i class="fas fa-sync-alt animate-spin h-5 w-5 mr-2"></i>
                                Synchronizing...
                            </span>
                            <span v-else-if="syncStatus === 'success'" class="text-green-600 dark:text-green-400">
                                Synchronization Complete
                            </span>
                            <span v-else class="text-red-600 dark:text-red-400">
                                Synchronization Failed
                            </span>
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ syncMessage }}
                        </p>
                    </div>

                    <!-- Progress bar -->
                    <div v-if="syncStatus === 'syncing'" class="mt-6">
                        <div class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <span>Progress</span>
                            <span>{{ syncProgress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div 
                                class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                :style="{ width: `${syncProgress}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Sync options (shown when idle) -->
                    <div v-if="syncStatus === 'idle'" class="mt-6 space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Synchronization Type</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <input 
                                        type="radio" 
                                        v-model="form.sync_type" 
                                        value="pull" 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                    >
                                    <div class="ml-3 flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Pull from Authority</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Download updates from the authority</span>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <input 
                                        type="radio" 
                                        v-model="form.sync_type" 
                                        value="push" 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                    >
                                    <div class="ml-3 flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Push to Authority</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Upload local changes to authority</span>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <input 
                                        type="radio" 
                                        v-model="form.sync_type" 
                                        value="both" 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                    >
                                    <div class="ml-3 flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Full Sync</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Two-way synchronization</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    id="force-update" 
                                    v-model="form.force_update" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="force-update" class="font-medium text-gray-700 dark:text-gray-300">Force update all records</label>
                                <p class="text-gray-500 dark:text-gray-400">Overwrite local/remote records even if they haven't changed</p>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                        This operation will synchronize member data with the authority. Deleted members on either side will be processed according to the synchronization type selected.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sync results (shown after completion) -->
                    <div v-if="syncStatus === 'success'" class="mt-6">
                        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700 dark:text-green-400">
                                        Synchronization completed successfully!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-md p-3">
                                            <i class="fas fa-upload text-gray-400"></i>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Added</dt>
                                                <dd class="flex items-baseline">
                                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ syncDetails.added }}</div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-md p-3">
                                            <i class="fas fa-sync-alt text-gray-400"></i>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Updated</dt>
                                                <dd class="flex items-baseline">
                                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ syncDetails.updated }}</div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-red-100 dark:bg-red-900/30 rounded-md p-3">
                                            <i class="fas fa-trash-alt text-gray-400"></i>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Deleted</dt>
                                                <dd class="flex items-baseline">
                                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ syncDetails.deleted }}</div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="syncDetails.errors && syncDetails.errors.length > 0" class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Errors</h4>
                            <div class="bg-red-50 dark:bg-red-900/10 border-l-4 border-red-400 p-4 overflow-auto max-h-40">
                                <ul class="list-disc pl-5 space-y-1 text-sm text-red-700 dark:text-red-400">
                                    <li v-for="(error, index) in syncDetails.errors" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Error state -->
                    <div v-if="syncStatus === 'error'" class="mt-6">
                        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 dark:text-red-400">
                                        {{ syncMessage }}
                                    </p>
                                    <div v-if="syncDetails.errors && syncDetails.errors.length > 0" class="mt-2">
                                        <ul class="list-disc pl-5 space-y-1 text-sm">
                                            <li v-for="(error, index) in syncDetails.errors" :key="index" class="text-red-700 dark:text-red-400">
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            v-if="syncStatus === 'idle'"
                            type="button"
                            @click="close"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button
                            v-if="syncStatus === 'idle'"
                            type="button"
                            @click="startSync"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <ArrowPathIcon class="-ml-1 mr-2 h-4 w-4" />
                            Start Synchronization
                        </button>
                        <button
                            v-if="syncStatus === 'success' || syncStatus === 'error'"
                            type="button"
                            @click="close"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>