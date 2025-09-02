<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

// Tab state management
const activeTab = ref('profile');

// Props
defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    title: {
        type: String,
        default: "Account Settings"
    }
});
</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ title }}
                </h2>
                <nav aria-label="Breadcrumb" class="flex">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a :href="route('dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400">
                                <i class="fas fa-home mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </template>

        <div class="py-0 md:py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tabs Navigation -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-t-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex -mb-px">
                            <button 
                                @click="activeTab = 'profile'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400 dark:border-green-400': activeTab === 'profile',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'profile'
                                }"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                                aria-label="Profile Information"
                            >
                                <i class="fas fa-user-circle mr-2"></i>Profile
                            </button>
                            <button 
                                @click="activeTab = 'security'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400 dark:border-green-400': activeTab === 'security',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'security'
                                }"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                                aria-label="Security Settings"
                            >
                                <i class="fas fa-shield-alt mr-2"></i>Security
                            </button>
                            <button 
                                v-if="$page.props.jetstream.hasAccountDeletionFeatures"
                                @click="activeTab = 'danger'"
                                :class="{
                                    'border-red-500 text-red-600 dark:text-red-400 dark:border-red-400': activeTab === 'danger',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'danger'
                                }"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                                aria-label="Party Membership"
                            >
                                <i class="fas fa-door-open mr-2"></i>Membership
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-b-lg">
                    <!-- Profile Information -->
                    <div v-if="activeTab === 'profile'" class="p-6">
                        <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                            <UpdateProfileInformationForm />
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div v-else-if="activeTab === 'security'" class="p-6 space-y-6">
                        <div v-if="$page.props.jetstream.canUpdatePassword">
                            <UpdatePasswordForm />
                            <SectionBorder class="my-6" />
                        </div>

                        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                            <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
                            <SectionBorder class="my-6" />
                        </div>

                        <div>
                            <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                        </div>
                    </div>

                    <!-- Membership Management -->
                    <div v-else-if="activeTab === 'danger'" class="p-6">
                        <div v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.tab-button {
    transition: all 0.2s ease;
}

.tab-button.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}

.tab-button:not(.active):hover {
    color: #4b5563;
    border-bottom-color: #d1d5db;
}

/* Dark mode styles */
.dark .tab-button.active {
    color: #60a5fa;
    border-bottom-color: #60a5fa;
}

.dark .tab-button:not(.active):hover {
    color: #9ca3af;
    border-bottom-color: #4b5563;
}
</style>
