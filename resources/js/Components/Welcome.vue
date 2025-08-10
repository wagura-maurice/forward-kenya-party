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
const { data: pageData } = usePage().props;

// Access the nested data property from the Inertia response
const data = pageData?.data || {};

// Destructure the data with default values
const {
    stats = {
        total_members: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        active_users: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        new_members_this_month: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        engagement_rate: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        donations: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        monthly_subscriptions: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        membership_fees: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        pending_approvals: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        candidates: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        nomination_papers: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        compliance_items: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        deadlines: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        departments: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        projects: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        services: { count: 0, change: 0, previous_period: 0, percentage_change: 0 },
        press_releases: { count: 0, change: 0 },
        social_media_reach: { count: 0, change: 0 },
        upcoming_events: { count: 0, change: 0 },
        meetings_this_week: { count: 0, change: 0 },
        active_volunteers: { count: 0, change: 0 },
        volunteer_hours: { count: 0, change: 0 },
        youth_members: { count: 0, change: 0 },
        women_members: { count: 0, change: 0 },
    },
    featuredServices = [],
    featuredDepartments = [],
    featuredProjects = [],
    activities = [],
    roles = [],
    user: userData = null
} = data;

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

// Format relative time (e.g., '2 hours ago')
const formatRelativeTime = (dateString) => {
    if (!dateString) return "";

    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60,
        second: 1,
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return interval === 1
                ? `${interval} ${unit} ago`
                : `${interval} ${unit}s ago`;
        }
    }

    return "just now";
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
                    <template
                        v-for="(stat, key) in [
                            {
                                key: 'total_users',
                                title: 'Total Users',
                                icon: 'users',
                                color: 'blue',
                                iconClass: 'fas fa-users',
                            },
                            {
                                key: 'active_users',
                                title: 'Active Members',
                                icon: 'user-check',
                                color: 'green',
                                iconClass: 'fas fa-user-check',
                            },
                            {
                                key: 'branches',
                                title: 'Branches',
                                icon: 'code-branch',
                                color: 'purple',
                                iconClass: 'fas fa-code-branch',
                            },
                            {
                                key: 'partnerships',
                                title: 'Partnerships',
                                icon: 'handshake',
                                color: 'yellow',
                                iconClass: 'fas fa-handshake',
                            },
                            {
                                key: 'departments',
                                title: 'Departments',
                                icon: 'sitemap',
                                color: 'indigo',
                                iconClass: 'fas fa-sitemap',
                            },
                            {
                                key: 'services',
                                title: 'Services',
                                icon: 'concierge-bell',
                                color: 'pink',
                                iconClass: 'fas fa-concierge-bell',
                            },
                            {
                                key: 'projects',
                                title: 'Projects',
                                icon: 'project-diagram',
                                color: 'red',
                                iconClass: 'fas fa-project-diagram',
                            },
                            {
                                key: 'upcoming_events',
                                title: 'Upcoming Events',
                                icon: 'calendar-alt',
                                color: 'pink',
                                iconClass: 'fas fa-calendar-alt',
                            },
                        ]"
                        :key="stat.key"
                    >
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200"
                        >
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <div
                                            :class="[
                                                'p-3 rounded-full',
                                                `bg-${stat.color}-100 dark:bg-${stat.color}-900/30 text-${stat.color}-600 dark:text-${stat.color}-400`,
                                            ]"
                                        >
                                            <i
                                                :class="[
                                                    stat.iconClass,
                                                    'text-xl',
                                                ]"
                                            ></i>
                                        </div>
                                        <div class="ml-4">
                                            <p
                                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                            >
                                                {{ stat.title }}
                                            </p>
                                            <p
                                                class="text-2xl font-semibold text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    stats[stat.key]?.count || 0
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        v-if="
                                            stats[stat.key]?.change !==
                                            undefined
                                        "
                                        class="ml-2 flex flex-col items-end"
                                    >
                                        <span
                                            :class="[
                                                'text-xs font-medium',
                                                formatChange(
                                                    stats[stat.key]?.change
                                                ).class,
                                            ]"
                                        >
                                            {{
                                                formatChange(
                                                    stats[stat.key]?.change
                                                ).text
                                            }}
                                        </span>
                                        <span
                                            class="text-xs text-gray-400 mt-0.5"
                                            >vs last month</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Latest Activities Section -->
                <div
                    class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10"
                    v-if="role === 'administrator'"
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
                            <div class="space-y-4">
                                <div
                                    v-if="activities.length === 0"
                                    class="text-center py-4 text-gray-500"
                                >
                                    No recent activities found.
                                </div>
                                <div
                                    v-for="activity in activities"
                                    :key="activity.id"
                                    class="flex items-start"
                                >
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center"
                                        >
                                            <i
                                                :class="[
                                                    'fas',
                                                    getActivityIcon(
                                                        activity.icon
                                                    ),
                                                    'text-lg',
                                                    getActivityColor(
                                                        activity.color
                                                    ),
                                                ]"
                                            ></i>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ activity.description }}
                                        </p>
                                        <div
                                            class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            <span>{{
                                                activity.user_name
                                            }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{
                                                activity.created_at
                                            }}</span>
                                            <span
                                                v-if="activity.subject_type"
                                                class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700"
                                            >
                                                {{ activity.subject_type }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="latestActivities.length > 0"
                                class="mt-4 text-right"
                            >
                                <a
                                    href="#"
                                    class="text-sm font-medium text-primary-600 hover:text-green-700 dark:text-primary-400 dark:hover:text-primary-300 hover:underline underline-offset-4"
                                >
                                    View all activities
                                    <i class="fas fa-arrow-right ml-1"></i>
                                </a>
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
                            <div class="space-y-2">
                                <a
                                    href="#"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-plus-circle text-green-500 w-5 mr-3"
                                    ></i>
                                    <span>Add New Member</span>
                                </a>
                                <a
                                    href="#"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-file-import text-blue-500 w-5 mr-3"
                                    ></i>
                                    <span>Import Data</span>
                                </a>
                                <a
                                    href="#"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-file-export text-purple-500 w-5 mr-3"
                                    ></i>
                                    <span>Export Reports</span>
                                </a>
                                <Link
                                    :href="route('settings')"
                                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fas fa-cog text-gray-500 w-5 mr-3"
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
