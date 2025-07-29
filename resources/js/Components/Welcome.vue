<script setup>
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const { props } = usePage();

// Tab state management
const activeTab = ref('personal');

// Access the authenticated user data
const user = computed(() => props.auth?.user || null);

// Access the profile data with all relationships
const profile = computed(() => user.value?.profile || {});

// Format date to a readable string
const formatDate = (dateString) => {
    if (!dateString) return "Not specified";
    try {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString(undefined, options);
    } catch (e) {
        return "Invalid date";
    }
};

// Calculate age from date of birth
const calculateAge = (dateString) => {
    if (!dateString) return "N/A";
    try {
        const birthDate = new Date(dateString);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        return age;
    } catch (e) {
        return "N/A";
    }
};

// Format user's address
const getAddress = computed(() => {
    if (!user.value) return "Not provided";

    const address = [
        profile.value?.address_line_1,
        profile.value?.address_line_2,
        profile.value?.city,
        profile.value?.state,
        profile.value?.citizen?.uuid,
        profile.value?.citizen?.county?.name,
        profile.value?.citizen?.sub_county?.name,
        profile.value?.citizen?.constituency?.name,
        profile.value?.citizen?.ward?.name,
    ].filter(Boolean);

    return address.length > 0 ? address.join(", ") : "Not provided";
});

// Get religion from profile
const religion = computed(() => {
    return profile.value?.religion?.name || "Not specified";
});

// Get ethnicity from profile
const ethnicity = computed(() => {
    return profile.value?.ethnicity?.name || "Not specified";
});

// Format disability status
const disabilityStatus = computed(() => {
    const hasDisability = profile.value?.disability_status === true || 
                         profile.value?.disability_status === 'true' || 
                         profile.value?.disability_status === '1' ||
                         profile.value?.disability_status === 'yes';
    
    return hasDisability ? 'Yes' : 'No';
});

// Toast notification state
const showToast = ref(false);
const toastMessage = ref('');

// Copy to clipboard function with fallback
const copyToClipboard = (text) => {
    // Fallback method for older browsers
    const fallbackCopy = () => {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            const successful = document.execCommand('copy');
            const message = successful ? 'Copied to clipboard!' : 'Failed to copy';
            showNotification(message, successful);
        } catch (err) {
            showNotification('Failed to copy', false);
        }
        
        document.body.removeChild(textarea);
    };

    // Modern Clipboard API method
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(
            () => showNotification('Copied to clipboard!', true),
            () => fallbackCopy() // Fallback if modern API fails
        );
    } else {
        fallbackCopy();
    }
};

// Show notification with success/error styling
const showNotification = (message, isSuccess = true) => {
    toastMessage.value = message;
    showToast.value = true;
    
    // Add success/error class to toast
    const toastElement = document.querySelector('.toast-notification');
    if (toastElement) {
        toastElement.classList.remove('bg-green-50', 'dark:bg-green-900', 'bg-red-50', 'dark:bg-red-900');
        if (isSuccess) {
            toastElement.classList.add('bg-green-50', 'dark:bg-green-900');
        } else {
            toastElement.classList.add('bg-red-50', 'dark:bg-red-900');
        }
    }
    
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

// Library section state
const isLibraryExpanded = ref(false);

// Toggle library section
const toggleLibrary = () => {
    isLibraryExpanded.value = !isLibraryExpanded.value;
};

// Import route helper
// Inside the <script setup> section
const {
    stats,
    featuredServices,
    featuredDepartments,
    latestActivities
} = props.data || {
    stats: null,
    featuredServices: null,
    featuredDepartments: null,
    latestActivities: null
};
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <section class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="stats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Total Users Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.total_users || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Members Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                                    <i class="fas fa-user-friends text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Members</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.active_members || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Branches Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                    <i class="fas fa-building text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Branches</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.total_branches || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Partnerships Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                    <i class="fas fa-handshake text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Partnerships</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.total_partnerships || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Departments Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                    <i class="fas fa-code-branch text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Departments</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.total_departments || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Services</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.total_services || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Projects Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400">
                                    <i class="fas fa-history text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Projects</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.featured_projects || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400">
                                    <i class="fas fa-calendar-alt text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming Events</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stats.upcoming_events || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="props.data.role === 'administrator'">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 mb-10">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-history text-primary-600 dark:text-primary-400 mr-2"></i>
                                Latest Activites
                            </h3>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4">
                                These activity logs are pulled from all the models in the database using the Laravel Spatie Audit Trail package.
                            </p>
                            <div class="space-y-4">
                                <div v-for="(activity, index) in latestActivities" :key="index" 
                                    class="flex items-start pb-4 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
                                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg mr-3">
                                        <i class="fas text-blue-500 dark:text-blue-400" :class="getActivityIcon(activity.log_name)"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ activity.description }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ formatRelativeTime(activity.created_at) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Library Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 mb-10">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-book-open text-primary-600 dark:text-primary-400 mr-2"></i>
                                Library
                            </h3>
                            <button 
                                @click="toggleLibrary"
                                class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                :title="isLibraryExpanded ? 'Hide Documents' : 'Show Documents'"
                            >
                                <i class="fas" :class="isLibraryExpanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Download important party documents and resources
                        </p>
                        <div v-show="isLibraryExpanded" class="space-y-3">
                            <!-- FKP Ideology -->
                            <div class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="w-10 h-10 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 rounded-lg mr-3">
                                    <i class="fas fa-book-open text-blue-500 dark:text-blue-400 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Party Ideology
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1">
                                        Core principles and beliefs that guide the Forward Kenya Party
                                    </p>
                                    <div class="flex justify-between items-center w-full text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex-shrink-0">
                                            <a :href="route('profile.view', 1)" class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4">FKP Admin</a> • <span class="text-gray-400 dark:text-gray-500">15 June 2025</span>
                                        </span>
                                        <span class="ml-2 whitespace-nowrap"><i class="far fa-file-pdf mr-1"></i> PDF • 2.4 MB</span>
                                    </div>
                                </div>
                                <a href="/assets/FKP_Documents/FKP IDEOLOGY.pdf" download target="_blank" class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200" title="Download party ideology document">
                                    <i class="fas fa-arrow-down text-green-600"></i>
                                </a>
                            </div>

                            <!-- Party Manifesto -->
                            <div class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="w-10 h-10 flex items-center justify-center bg-green-50 dark:bg-green-900/20 rounded-lg mr-3">
                                    <i class="fas fa-file-alt text-green-500 dark:text-green-400 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Party Manifesto
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1">
                                        Our comprehensive plan and commitments for national development
                                    </p>
                                    <div class="flex justify-between items-center w-full text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex-shrink-0">
                                            <a :href="route('profile.view', 1)" class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4">FKP Admin</a> • <span class="text-gray-400 dark:text-gray-500">15 June 2025</span>
                                        </span>
                                        <span class="ml-2 whitespace-nowrap"><i class="far fa-file-pdf mr-1"></i> PDF • 1.8 MB</span>
                                    </div>
                                </div>
                                <a href="/assets/FKP_Documents/FKP MANIFESTO (1).pdf" download target="_blank" class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200" title="Download party manifesto document">
                                    <i class="fas fa-arrow-down text-green-600"></i>
                                </a>
                            </div>

                            <!-- Party Constitution -->
                            <div class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="w-10 h-10 flex items-center justify-center bg-purple-50 dark:bg-purple-900/20 rounded-lg mr-3">
                                    <i class="fas fa-scroll text-purple-500 dark:text-purple-400 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Party Constitution
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1">
                                        Legal framework and structure of the Forward Kenya Party
                                    </p>
                                    <div class="flex justify-between items-center w-full text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex-shrink-0">
                                            <a :href="route('profile.view', 1)" class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4">FKP Admin</a> • <span class="text-gray-400 dark:text-gray-500">15 June 2025</span>
                                        </span>
                                        <span class="ml-2 whitespace-nowrap"><i class="far fa-file-pdf mr-1"></i> PDF • 3.1 MB</span>
                                    </div>
                                </div>
                                <a href="/assets/FKP_Documents/FORWARD KENYA PARTY CONSTITUTION.pdf" download target="_blank" class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200" title="Download party constitution document">
                                    <i class="fas fa-arrow-down text-green-600"></i>
                                </a>
                            </div>

                            <!-- Party Nomination Rules -->
                            <div class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="w-10 h-10 flex items-center justify-center bg-yellow-50 dark:bg-yellow-900/20 rounded-lg mr-3">
                                    <i class="fas fa-file-contract text-yellow-500 dark:text-yellow-400 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Party Nomination Rules
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1">
                                        Guidelines and procedures for party nominations and elections
                                    </p>
                                    <div class="w-full">
                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full text-xs text-gray-500 dark:text-gray-400 gap-1">
                                            <span class="flex-shrink-0">
                                                <a :href="route('profile.view', 1)" class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4">FKP Admin</a> • <span class="text-gray-400 dark:text-gray-500">15 June 2025</span>
                                            </span>
                                            <div class="flex items-center gap-1">
                                                <i class="far fa-file-pdf"></i>
                                                <span>PDF</span>
                                                <span>•</span>
                                                <span>2.4 MB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="/assets/FKP_Documents/NOMINATION_RULES_AMMENDED 12.5.25.pdf" download target="_blank" class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200" title="Download party nomination rules document">
                                    <i class="fas fa-arrow-down text-green-600"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
</template>
