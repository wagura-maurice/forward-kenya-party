<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

// Custom components
const { title } = defineProps({
    title: {
        type: String,
        required: true,
    },
});

// Access page props
const { props } = usePage();

// Tab state management
const activeTab = ref("personal");

// Toast notification state
const showToast = ref(false);
const toastMessage = ref("");

// Access authenticated user data
const user = computed(() => props.auth?.user || null);
const profile = computed(() => user.value?.profile || {});

// Computed properties
const fullName = computed(() => {
    return (
        `${profile.value?.first_name} ${profile.value?.middle_name} ${profile.value?.last_name}`
            .trim()
            .replace(/\s+/g, " ") || "Member"
    );
});

const memberSince = computed(() => {
    return user.value?.created_at
        ? formatDate(user.value.created_at)
        : "Recent member";
});

// Format gender for display
const genderDisplayName = computed(() => {
    if (profile.value?.gender === "XY") return "Male";
    if (profile.value?.gender === "XX") return "Female";
    return profile.value?.gender || "Not provided";
});

const stats = computed(() => ({
    posts: props.auth?.user?.posts_count || 0,
    following: props.auth?.user?.following_count || 0,
    followers: props.auth?.user?.followers_count || 0,
}));

// Format date to a readable string
const formatDate = (dateString) => {
    if (!dateString) return "Not specified";
    try {
        return new Date(dateString).toLocaleDateString(undefined, {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
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

// Get initials for avatar
const getInitials = (name) => {
    if (!name) return "U";
    const splitName = name.split(" ");
    return `${splitName[0][0]}${splitName[splitName.length - 1][0]}`.toUpperCase();
};

// Copy text to clipboard
const copyToClipboard = (text) => {
    if (!text) return;

    const showFeedback = (message, success = true) => {
        toastMessage.value = message;
        showToast.value = true;
        const toastElement = document.querySelector(".toast-notification");
        if (toastElement) {
            toastElement.classList.remove(
                "bg-green-50",
                "dark:bg-green-900",
                "bg-red-50",
                "dark:bg-red-900"
            );
            toastElement.classList.add(
                success ? "bg-green-50" : "bg-red-50",
                success ? "dark:bg-green-900" : "dark:bg-red-900"
            );
        }
        setTimeout(() => {
            showToast.value = false;
        }, 3000);
    };

    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(
            () => showFeedback("Copied to clipboard!"),
            () => showFeedback("Failed to copy", false)
        );
    } else {
        const textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand("copy");
            showFeedback("Copied to clipboard!");
        } catch (err) {
            showFeedback("Failed to copy", false);
        }
        document.body.removeChild(textarea);
    }
};
</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    {{ title }}
                </h2>
                <nav aria-label="Breadcrumb" class="flex">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a
                                :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400"
                            >
                                <i class="fas fa-home mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i
                                    class="fas fa-chevron-right text-gray-400 mx-2"
                                ></i>
                                <span
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >{{ title }}</span
                                >
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </template>

        <div class="py-0 md:py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Profile Header - Mobile Optimized -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-2 lg:mb-6">
                    <div class="h-32 bg-gradient-to-r from-green-600 to-green-400 relative"></div>
                    <div class="px-4 pb-4 relative">
                        <div class="flex justify-between items-start -mt-12 mb-3">
                            <!-- Avatar Section -->
                            <div class="relative">
                                <div
                                    v-if="
                                        $page.props.jetstream.managesProfilePhotos
                                    "
                                    class="h-20 w-20 rounded-full border-4 border-white bg-white shadow-lg overflow-hidden"
                                    >
                                        <img
                                            class="h-full w-full rounded-full object-cover"
                                            :src="
                                                $page.props.auth.user.profile_photo_url
                                            "
                                            :alt="$page.props.auth.user.name"
                                        />
                                    </div>
                                <span class="absolute bottom-0 right-0 bg-green-500 rounded-full p-1 border-2 border-white">
                                    <i class="fas fa-check text-white text-xs" aria-hidden="true"></i>
                                </span>
                            </div>
                            
                            <!-- Edit Button - Top Right -->
                            <a :href="route('profile.show')" class="mt-2">
                                <button class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 rounded-md text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </button>
                            </a>
                        </div>

                        <!-- Name and Membership Info -->
                        <div class="mb-3">
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ fullName }}
                            </h1>
                            
                            <!-- Member Since and ID -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-1">
                                <div class="flex items-center px-2 py-1 bg-green-50 dark:bg-green-900/30 rounded-full text-xs font-medium text-green-700 dark:text-green-300 w-fit mb-2 sm:mb-0">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <span>Member since <span class="font-semibold">{{ memberSince }}</span></span>
                                </div>
                                <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 px-2 py-1 rounded-full text-md">
                                    <span class="text-gray-500 dark:text-gray-400 mr-1">ID:</span>
                                    <span class="font-medium">{{ profile?.citizen?.uuid || "N/A" }}</span>
                                    <button
                                        @click="copyToClipboard(profile?.citizen?.uuid)"
                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition-colors ml-1"
                                        :disabled="!profile?.citizen?.uuid"
                                        title="Copy membership number"
                                    >
                                        <i class="fas fa-copy text-md"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Stats - Horizontal Scrolling for Mobile -->
                        <div class="overflow-x-auto">
                            <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3 min-w-max">
                                <div class="text-center px-3">
                                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                        {{ stats.posts }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Posts
                                    </div>
                                </div>
                                <div class="text-center px-3">
                                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                        {{ stats.following }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Following
                                    </div>
                                </div>
                                <div class="text-center px-3">
                                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                        {{ stats.followers }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Followers
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content with Tabs -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex overflow-x-auto -mb-px scrollbar-hide">
                            <button 
                                @click="activeTab = 'personal'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400 dark:border-green-400': activeTab === 'personal',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'personal'
                                }"
                                class="whitespace-nowrap py-4 px-4 sm:px-6 border-b-2 font-medium text-sm flex-shrink-0"
                                aria-label="Personal Information"
                            >
                                <i class="fas fa-user-circle mr-2"></i>Personal
                            </button>
                            <button
                                @click="activeTab = 'contact'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400 dark:border-green-400': activeTab === 'contact',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'contact'
                                }"
                                class="whitespace-nowrap py-4 px-4 sm:px-6 border-b-2 font-medium text-sm flex-shrink-0"
                                aria-label="Contact Information"
                            >
                                <i class="fas fa-address-book mr-2"></i>Contact
                            </button>
                            <button
                                @click="activeTab = 'location'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400 dark:border-green-400': activeTab === 'location',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'location'
                                }"
                                class="whitespace-nowrap py-4 px-4 sm:px-6 border-b-2 font-medium text-sm flex-shrink-0"
                                aria-label="Location Information"
                            >
                                <i class="fas fa-map-marker-alt mr-2"></i>Location
                            </button>
                        </nav>
                    </div>
                    <div v-if="activeTab === 'personal'" class="p-6">
                        <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-6 rounded">
                            <div class="flex">
                                <p class="text-sm text-green-700 dark:text-green-300 ml-3">
                                    Personal helps us maintain accurate membership records and ensure proper identification. This data is securely stored and only used for official party communications and verification purposes as per our data protection policy.
                                </p>
                            </div>
                        </div>
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Surname
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-user text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.last_name || user.name }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    OtherName
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-user text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.first_name }} {{ profile?.middle_name}}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Date of Birth
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-calendar-alt text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ formatDate(profile?.date_of_birth) }}
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400 ml-1"
                                            >(Age:
                                            {{
                                                calculateAge(
                                                    profile?.date_of_birth
                                                )
                                            }})</span
                                        >
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Gender
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-venus-mars text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ genderDisplayName }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    National Idetification Number
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-id-card text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{
                                            profile?.citizen
                                                ?.national_identification_number ||
                                            "Not provided"
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Passport Number
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-id-card text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{
                                            profile?.citizen
                                                ?.passport_number ||
                                            "Not provided"
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Disability Status
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-wheelchair text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.disability_status ? "Yes" : "No" }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="profile?.disability_status">
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    NCPWD Number
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-id-card text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.ncpwd_number || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Ethnicity
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-users text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.ethnicity?.name }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Religion
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-pray text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.religion?.name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="activeTab === 'contact'" class="p-6">
                        <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-6 rounded">
                            <div class="flex">
                                <p class="text-sm text-green-700 dark:text-green-300 ml-3">
                                    Keeping your contact information updated ensures you receive important party updates, event invitations, and emergency communications. We respect your privacy and will never share your contact details with third parties without your explicit consent.
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Email
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-envelope text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ user.email || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Telephone Number
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-phone-alt text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.telephone || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Address Line 1
                                </p>
                                <div class="flex items-start">
                                    <i
                                        class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.address_line_1 || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Address Line 2
                                </p>
                                <div class="flex items-start">
                                    <i
                                        class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.address_line_2 || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    City
                                </p>
                                <div class="flex items-start">
                                    <i
                                        class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.city || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    State
                                </p>
                                <div class="flex items-start">
                                    <i
                                        class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.state || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Country
                                </p>
                                <div class="flex items-start">
                                    <i
                                        class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.country || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="activeTab === 'location'" class="p-6">
                        <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-6 rounded">
                            <div class="flex">
                                <p class="text-sm text-green-700 dark:text-green-300 ml-3">
                                    Your location information helps us organize local events, coordinate activities in your area, and ensure proper representation. This data is used to connect you with nearby party activities and keep you informed about developments in your region.
                                </p>
                            </div>
                        </div>
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    County
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-city text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.county?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Sub-County
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-map-pin text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.sub_county?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Constituency
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-map-signs text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.constituency?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Ward
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-map-marker text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.ward?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Location
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-map-marker text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.location?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Village
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-map-marker text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.village?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Polling Center
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-vote-yea text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.polling_center?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Polling Station
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-vote-yea text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.polling_station?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                                >
                                    Polling Stream
                                </p>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-vote-yea text-gray-400 mr-2 text-sm"
                                    ></i>
                                    <p
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        {{ profile?.citizen?.polling_stream?.name || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showToast"
                class="toast-notification fixed bottom-4 right-4 max-w-sm w-full bg-green-50 dark:bg-green-900 rounded-lg shadow-lg p-4 z-50"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i
                            class="fas fa-check-circle text-green-500 dark:text-green-400 text-xl"
                        ></i>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p
                            class="text-sm font-medium text-green-800 dark:text-green-100"
                        >
                            {{ toastMessage }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            @click="showToast = false"
                            class="inline-flex text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none"
                            aria-label="Close toast notification"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AppLayout>
</template>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
/* Ensure tab buttons don't shrink smaller than their content */
.tab-button {
    flex: 0 0 auto;
}
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
