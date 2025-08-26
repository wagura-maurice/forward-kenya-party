<!-- resources/js/Components/Welcome.vue -->
<script setup>
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const { props } = usePage();

// Tab state management
const activeTab = ref("personal");

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

        if (
            monthDiff < 0 ||
            (monthDiff === 0 && today.getDate() < birthDate.getDate())
        ) {
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
    const hasDisability =
        profile.value?.disability_status === true ||
        profile.value?.disability_status === "true" ||
        profile.value?.disability_status === "1" ||
        profile.value?.disability_status === "yes";

    return hasDisability ? "Yes" : "No";
});

// Toast notification state
const showToast = ref(false);
const toastMessage = ref("");

// Copy to clipboard function with fallback
const copyToClipboard = (text) => {
    // Fallback method for older browsers
    const fallbackCopy = () => {
        const textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";
        document.body.appendChild(textarea);
        textarea.select();

        try {
            const successful = document.execCommand("copy");
            const message = successful
                ? "Copied to clipboard!"
                : "Failed to copy";
            showNotification(message, successful);
        } catch (err) {
            showNotification("Failed to copy", false);
        }

        document.body.removeChild(textarea);
    };

    // Modern Clipboard API method
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(
            () => showNotification("Copied to clipboard!", true),
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
    const toastElement = document.querySelector(".toast-notification");
    if (toastElement) {
        toastElement.classList.remove(
            "bg-green-50",
            "dark:bg-green-900",
            "bg-red-50",
            "dark:bg-red-900"
        );
        if (isSuccess) {
            toastElement.classList.add("bg-green-50", "dark:bg-green-900");
        } else {
            toastElement.classList.add("bg-red-50", "dark:bg-red-900");
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

// Get the page props
const pageProps = usePage().props;
const auth = pageProps.auth;

// Access the nested data property from the Inertia response
const data = pageProps.data || {};

// Check if user has administrator role
const hasAdminRole = computed(() => {
    return Array.isArray(auth?.user?.roles) && auth.user.roles.includes('administrator');
});

// Debug: Log the data structure
console.log('Page props:', pageProps);
console.log('Nested data:', data);

// Get the stats from the data object, handling both direct and nested structures
const stats = data?.stats || data?.data?.stats || {
    total_users: { count: 0, change: 0, title: 'Total Users', icon: 'fa-users', color: 'blue' },
    active_users: { count: 0, change: 0, title: 'Active Users', icon: 'fa-user-check', color: 'green' },
    new_members_this_month: { count: 0, change: 0, title: 'New Members This Month', icon: 'fa-user-plus', color: 'purple' },
    engagement_rate: { count: 0, change: 0, title: 'Engagement Rate', icon: 'fa-chart-line', color: 'yellow' },
    donations: { count: 0, change: 0, title: 'Donations', icon: 'fa-donate', color: 'indigo' },
    monthly_subscriptions: { count: 0, change: 0, title: 'Monthly Subscriptions', icon: 'fa-calendar-alt', color: 'pink' },
    membership_fees: { count: 0, change: 0, title: 'Membership Fees', icon: 'fa-id-card', color: 'red' },
    pending_approvals: { count: 0, change: 0, title: 'Pending Approvals', icon: 'fa-clock', color: 'orange' },
    candidates: { count: 0, change: 0, title: 'Candidates', icon: 'fa-user-tie', color: 'teal' },
    nomination_papers: { count: 0, change: 0, title: 'Nomination Papers', icon: 'fa-file-signature', color: 'blue' },
    compliance_items: { count: 0, change: 0, title: 'Compliance Items', icon: 'fa-clipboard-check', color: 'green' },
    deadlines: { count: 0, change: 0, title: 'Deadlines', icon: 'fa-clock', color: 'red' },
    departments: { count: 0, change: 0, title: 'Departments', icon: 'fa-building', color: 'indigo' },
    services: { count: 0, change: 0, title: 'Services', icon: 'fa-concierge-bell', color: 'purple' },
    projects: { count: 0, change: 0, title: 'Projects', icon: 'fa-project-diagram', color: 'yellow' },
    press_releases: { count: 0, change: 0, title: 'Press Releases', icon: 'fa-newspaper', color: 'blue' },
    social_media_reach: { count: 0, change: 0, title: 'Social Media Reach', icon: 'fa-share-alt', color: 'pink' },
    upcoming_events: { count: 0, change: 0, title: 'Upcoming Events', icon: 'fa-calendar-alt', color: 'green' },
    meetings_this_week: { count: 0, change: 0, title: 'Meetings This Week', icon: 'fa-users', color: 'indigo' },
    active_volunteers: { count: 0, change: 0, title: 'Active Volunteers', icon: 'fa-hands-helping', color: 'orange' },
    volunteer_hours: { count: 0, change: 0, title: 'Volunteer Hours', icon: 'fa-clock', color: 'teal' },
    youth_members: { count: 0, change: 0, title: 'Youth Members', icon: 'fa-user-graduate', color: 'blue' },
    women_members: { count: 0, change: 0, title: 'Women Members', icon: 'fa-female', color: 'pink' }
};

// Get other data with proper fallbacks, using optional chaining for safety
const activities = data?.activities || data?.data?.activities || [];
const roles = data?.roles || data?.data?.roles || [];
const userData = data?.user || data?.data?.user || null;

// Debug: Log the activities data
console.log('Activities:', activities);
console.log('Has admin role:', hasAdminRole.value);

// Get icon class based on activity type
const getActivityIcon = (iconType) => {
    const icons = {
        plus: "fa-plus-circle",
        "pencil-alt": "fa-pen",
        trash: "fa-trash",
        "sign-in-alt": "fa-sign-in-alt",
        "sign-out-alt": "fa-sign-out-alt",
        "user-plus": "fa-user-plus",
        bell: "fa-bell",
    };
    return icons[iconType] || "fa-circle";
};

// Get color class based on activity type
const getActivityColor = (color) => {
    const colors = {
        green: "text-green-500",
        blue: "text-blue-500",
        red: "text-red-500",
        indigo: "text-indigo-500",
        gray: "text-gray-500",
    };
    return colors[color] || "text-gray-500";
};

// Format percentage change with appropriate styling
const formatChange = (change) => {
    if (change === undefined || change === null)
        return { text: "N/A", class: "text-gray-500" };
    const isPositive = change > 0;
    const isNeutral = change === 0;

    return {
        text: `${isPositive ? "+" : ""}${change}%`,
        class: isNeutral
            ? "text-gray-500"
            : isPositive
            ? "text-green-500 dark:text-green-400"
            : "text-red-500 dark:text-red-400",
    };
};
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <section class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10"
                >
                    <!-- Stat Card Component -->
                    <template v-for="(stat, key) in Object.entries(stats)" :key="key">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200"
                        >
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div
                                        class="p-3 rounded-lg mr-4"
                                        :class="`bg-${stat[1].color}-50 dark:bg-${stat[1].color}-900/20`"
                                    >
                                        <i
                                            :class="`${stat[1].icon} text-${stat[1].color}-500 dark:text-${stat[1].color}-400 text-xl`"
                                        ></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ stat[1].title }}
                                        </p>
                                        <div class="flex items-center">
                                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                                {{ stat[1].count?.toLocaleString() }}
                                            </p>
                                            <span
                                                v-if="stat[1].change !== undefined"
                                                class="ml-2 text-sm font-medium"
                                                :class="{
                                                    'text-green-500 dark:text-green-400': stat[1].change > 0,
                                                    'text-red-500 dark:text-red-400': stat[1].change < 0,
                                                    'text-gray-500': stat[1].change === 0
                                                }"
                                            >
                                                {{ stat[1].change > 0 ? '+' : '' }}{{ stat[1].change }}{{ stat[1].change !== 0 ? '%' : '' }}
                                            </span>
                                        </div>
                                        <p v-if="stat[1].percentage_change !== undefined" class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ stat[1].percentage_change > 0 ? '+' : '' }}{{ stat[1].percentage_change }}% from last period
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Latest Activities Section - Only visible to administrators -->
                <div
                    class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10"
                    v-if="hasAdminRole"
                >
                    <div
                        class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4"
                            >
                                <i
                                    class="fas fa-history text-primary-600 dark:text-primary-400 mr-2"
                                ></i>
                                Latest Activities
                            </h3>
                            <p
                                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4"
                            >
                                These activity logs are pulled from all the
                                models in the database using the Laravel Spatie
                                Audit Trail package.
                            </p>
                            <div class="space-y-2">
                                <div
                                    v-if="!activities || activities.length === 0"
                                    class="text-center py-8 text-gray-500 dark:text-gray-400"
                                >
                                    <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                                    <p>No recent activities found</p>
                                </div>
                                <template v-else>
                                <div
                                    v-for="activity in activities"
                                    :key="activity.id"
                                    class="flex items-start group hover:bg-gray-50 dark:hover:bg-gray-700/50 p-2 rounded-lg transition-colors duration-200"
                                >
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-10 w-10 rounded-full flex items-center justify-center"
                                            :class="[
                                                'bg-opacity-10',
                                                activity.color === 'blue' ? 'bg-blue-500' :
                                                activity.color === 'green' ? 'bg-green-500' :
                                                activity.color === 'red' ? 'bg-red-500' :
                                                activity.color === 'yellow' ? 'bg-yellow-500' :
                                                activity.color === 'purple' ? 'bg-purple-500' : 'bg-gray-500'
                                            ]"
                                        >
                                            <i
                                                :class="[
                                                    'fas',
                                                    activity.icon || 'fa-info-circle',
                                                    'text-lg',
                                                    activity.color === 'blue' ? 'text-blue-500' :
                                                    activity.color === 'green' ? 'text-green-500' :
                                                    activity.color === 'red' ? 'text-red-500' :
                                                    activity.color === 'yellow' ? 'text-yellow-500' :
                                                    activity.color === 'purple' ? 'text-purple-500' : 'text-gray-500'
                                                ]"
                                                :title="activity.action || 'Activity'"
                                            ></i>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ activity.title || 'System Activity' }}
                                            </p>
                                            <span 
                                                class="text-xs px-2 py-0.5 rounded-full"
                                                :class="[
                                                    activity.status_class === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                                    activity.status_class === 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                                                    activity.status_class === 'danger' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
                                                ]"
                                            >
                                                {{ activity.status || 'Completed' }}
                                            </span>
                                        </div>
                                        <!-- <p class="text-sm text-gray-600 dark:text-gray-300 mt-1"> -->
                                            <!-- {{ activity.description || 'No description available' }} -->
                                            <!-- <span v-if="activity.details" class="text-xs text-gray-500 block mt-1">
                                                {{ activity.details }}
                                            </span> -->
                                        <!-- </p> -->
                                        <div class="flex flex-wrap items-center mt-1 text-xs text-gray-500 dark:text-gray-400 gap-1">
                                            <div class="flex items-center capitalize">
                                                <i class="far fa-user mr-1"></i>
                                                <span>
                                                    <Link v-if="activity.user_id" :href="route('profile.view', { user_id: activity.user_id })" class="text-green-600 hover:underline underline-offset-4">
                                                        {{ activity.user_name }}
                                                    </Link>
                                                    <span v-else>System</span>
                                                </span>
                                            </div>
                                            <span>•</span>
                                            <div class="flex items-center" :title="'Created ' + activity.created_at">
                                                <i class="far fa-clock mr-1"></i>
                                                <span>{{ activity.created_at }}</span>
                                            </div>
                                            <template v-if="activity.service_name">
                                                <span>•</span>
                                                <div class="flex items-center">
                                                    <i class="fas fa-cog mr-1"></i>
                                                    <span>{{ activity.service_name }}</span>
                                                </div>
                                            </template>
                                            <template v-if="activity.department_name">
                                                <span>•</span>
                                                <div class="flex items-center">
                                                    <i class="fas fa-building mr-1"></i>
                                                    <span>{{ activity.department_name }}</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                </template>
                            </div>
                            <div
                                v-if="activities && activities.length > 0"
                                class="mt-4 text-right"
                            >
                                <Link
                                    :href="route('activity')"
                                    class="text-sm font-medium text-primary-600 hover:text-green-700 dark:text-primary-400 dark:hover:text-primary-300 hover:underline underline-offset-4"
                                >
                                    View all activities
                                    <i class="fas fa-arrow-right ml-1"></i>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4"
                            >
                                <i
                                    class="fas fa-bolt text-primary-600 dark:text-primary-400 mr-2"
                                ></i>
                                Quick Actions
                            </h3>
                            <p
                                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4"
                            >
                                These are some quick actions to help you get started.
                            </p>
                            <div class="space-y-2">
                                <a
                                    href="#"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-plus-circle text-green-500 w-5 h-5 mr-3"
                                    ></i>
                                    <span>Add New Member</span>
                                </a>
                                <a
                                    href="#"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-file-import text-blue-500 w-5 h-5 mr-3"
                                    ></i>
                                    <span>Import Members</span>
                                </a>
                                <a
                                    href="#"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-file-export text-purple-500 w-5 h-5 mr-3"
                                    ></i>
                                    <span>Export Members</span>
                                </a>
                                <a
                                    href="#"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-pen-nib text-yellow-500 w-5 h-5 mr-3"
                                    ></i>
                                    <span>Update Member</span>
                                </a>
                                <Link
                                    :href="route('settings')"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-cog text-gray-500 w-5 h-5 mr-3"
                                    ></i>
                                    <span>Settings</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Library Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 mb-10"
                >
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                            >
                                <i
                                    class="fas fa-book-open text-primary-600 dark:text-primary-400 mr-2"
                                ></i>
                                Library
                            </h3>
                            <button
                                @click="toggleLibrary"
                                class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                :title="
                                    isLibraryExpanded
                                        ? 'Hide Documents'
                                        : 'Show Documents'
                                "
                            >
                                <i
                                    class="fas"
                                    :class="
                                        isLibraryExpanded
                                            ? 'fa-chevron-up'
                                            : 'fa-chevron-down'
                                    "
                                ></i>
                            </button>
                        </div>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 mb-4"
                        >
                            Download important party documents and resources
                        </p>
                        <div v-show="isLibraryExpanded" class="space-y-3">
                            <!-- FKP Ideology -->
                            <div
                                class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                            >
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 rounded-lg mr-3"
                                >
                                    <i
                                        class="fas fa-book-open text-blue-500 dark:text-blue-400 text-lg"
                                    ></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Party Ideology
                                    </p>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1"
                                    >
                                        Core principles and beliefs that guide
                                        the Forward Kenya Party
                                    </p>
                                    <div
                                        class="flex justify-between items-center w-full text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <span class="flex-shrink-0">
                                            <a
                                                :href="route('profile.view', 1)"
                                                class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4"
                                                >FKP Admin</a
                                            >
                                            •
                                            <span
                                                class="text-gray-400 dark:text-gray-500"
                                                >15 June 2025</span
                                            >
                                        </span>
                                        <span class="ml-2 whitespace-nowrap"
                                            ><i
                                                class="far fa-file-pdf mr-1"
                                            ></i>
                                            PDF • 2.4 MB</span
                                        >
                                    </div>
                                </div>
                                <a
                                    href="/assets/FKP_Documents/FKP IDEOLOGY.pdf"
                                    download
                                    target="_blank"
                                    class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
                                    title="Download party ideology document"
                                >
                                    <i
                                        class="fas fa-arrow-down text-green-600"
                                    ></i>
                                </a>
                            </div>

                            <!-- Party Manifesto -->
                            <div
                                class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                            >
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-green-50 dark:bg-green-900/20 rounded-lg mr-3"
                                >
                                    <i
                                        class="fas fa-file-alt text-green-500 dark:text-green-400 text-lg"
                                    ></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Party Manifesto
                                    </p>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1"
                                    >
                                        Our comprehensive plan and commitments
                                        for national development
                                    </p>
                                    <div
                                        class="flex justify-between items-center w-full text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <span class="flex-shrink-0">
                                            <a
                                                :href="route('profile.view', 1)"
                                                class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4"
                                                >FKP Admin</a
                                            >
                                            •
                                            <span
                                                class="text-gray-400 dark:text-gray-500"
                                                >15 June 2025</span
                                            >
                                        </span>
                                        <span class="ml-2 whitespace-nowrap"
                                            ><i
                                                class="far fa-file-pdf mr-1"
                                            ></i>
                                            PDF • 1.8 MB</span
                                        >
                                    </div>
                                </div>
                                <a
                                    href="/assets/FKP_Documents/FKP MANIFESTO (1).pdf"
                                    download
                                    target="_blank"
                                    class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
                                    title="Download party manifesto document"
                                >
                                    <i
                                        class="fas fa-arrow-down text-green-600"
                                    ></i>
                                </a>
                            </div>

                            <!-- Party Constitution -->
                            <div
                                class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                            >
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-purple-50 dark:bg-purple-900/20 rounded-lg mr-3"
                                >
                                    <i
                                        class="fas fa-scroll text-purple-500 dark:text-purple-400 text-lg"
                                    ></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Party Constitution
                                    </p>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1"
                                    >
                                        Legal framework and structure of the
                                        Forward Kenya Party
                                    </p>
                                    <div
                                        class="flex justify-between items-center w-full text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <span class="flex-shrink-0">
                                            <a
                                                :href="route('profile.view', 1)"
                                                class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4"
                                                >FKP Admin</a
                                            >
                                            •
                                            <span
                                                class="text-gray-400 dark:text-gray-500"
                                                >15 June 2025</span
                                            >
                                        </span>
                                        <span class="ml-2 whitespace-nowrap"
                                            ><i
                                                class="far fa-file-pdf mr-1"
                                            ></i>
                                            PDF • 3.1 MB</span
                                        >
                                    </div>
                                </div>
                                <a
                                    href="/assets/FKP_Documents/FORWARD KENYA PARTY CONSTITUTION.pdf"
                                    download
                                    target="_blank"
                                    class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
                                    title="Download party constitution document"
                                >
                                    <i
                                        class="fas fa-arrow-down text-green-600"
                                    ></i>
                                </a>
                            </div>

                            <!-- Party Nomination Rules -->
                            <div
                                class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                            >
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-yellow-50 dark:bg-yellow-900/20 rounded-lg mr-3"
                                >
                                    <i
                                        class="fas fa-file-contract text-yellow-500 dark:text-yellow-400 text-lg"
                                    ></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Party Nomination Rules
                                    </p>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-300 mt-1 mb-1"
                                    >
                                        Guidelines and procedures for party
                                        nominations and elections
                                    </p>
                                    <div class="w-full">
                                        <div
                                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full text-xs text-gray-500 dark:text-gray-400 gap-1"
                                        >
                                            <span class="flex-shrink-0">
                                                <a
                                                    :href="
                                                        route('profile.view', 1)
                                                    "
                                                    class="text-gray-500 dark:text-gray-400 hover:text-green-600 hover:underline hover:decoration-green-600 underline-offset-4"
                                                    >FKP Admin</a
                                                >
                                                •
                                                <span
                                                    class="text-gray-400 dark:text-gray-500"
                                                    >15 June 2025</span
                                                >
                                            </span>
                                            <div
                                                class="flex items-center gap-1"
                                            >
                                                <i class="far fa-file-pdf"></i>
                                                <span>PDF</span>
                                                <span>•</span>
                                                <span>2.4 MB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a
                                    href="/assets/FKP_Documents/NOMINATION_RULES_AMMENDED 12.5.25.pdf"
                                    download
                                    target="_blank"
                                    class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
                                    title="Download party nomination rules document"
                                >
                                    <i
                                        class="fas fa-arrow-down text-green-600"
                                    ></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
