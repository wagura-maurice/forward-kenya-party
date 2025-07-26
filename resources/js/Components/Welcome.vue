<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const { props } = usePage();

// Tab state management
const activeTab = ref('personal');

// Access the authenticated user data
const user = computed(() => props.auth?.user || null);

// Access the profile data with all relationships
const profile = computed(() => user.value?.profile || {});

// Debugging
onMounted(() => {
    console.log("Page props:", props);
    console.log("User data:", user.value);
    console.log("Profile data:", profile.value);
});

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
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <section class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="mb-6 sm:mb-8" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li class="inline-flex items-center">
                            <Link href="/" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                <i class="fas fa-home mr-1.5"></i>
                                Home
                            </Link>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right text-xs text-gray-400 mx-2"></i>
                            <span class="text-gray-600 dark:text-gray-300 font-medium">My Profile</span>
                        </li>
                    </ol>
                </nav>

                <!-- Page Header -->
                <div class="mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">Member Dashboard</h2>
                    <p class="text-gray-600 dark:text-gray-300">Welcome back, {{ profile?.surname || 'Member' }}! Here's your account summary.</p>
                </div>
                
                <!-- Profile Card with Tabs -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                            <button 
                                @click="activeTab = 'personal'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400': activeTab === 'personal',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'personal'}"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center"
                            >
                                <i class="fas fa-user-circle mr-2 text-lg"></i>
                                Personal Information
                            </button>
                            <button 
                                @click="activeTab = 'contact'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400': activeTab === 'contact',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'contact'}"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center"
                            >
                                <i class="fas fa-address-card mr-2 text-lg"></i>
                                Contact Information
                            </button>
                            <button 
                                @click="activeTab = 'location'"
                                :class="{
                                    'border-green-500 text-green-600 dark:text-green-400': activeTab === 'location',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'location'}"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center"
                            >
                                <i class="fas fa-map-marked-alt mr-2 text-lg"></i>
                                Location Information
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Personal Information Tab -->
                        <div v-show="activeTab === 'personal'" class="space-y-6">
                            <!-- Profile Header -->
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-100 dark:border-gray-700 pb-6">
                                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                                    <div class="relative">
                                        <div class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                            <i class="fas fa-user text-4xl text-gray-400"></i>
                                        </div>
                                        <span class="absolute -bottom-1 -right-1 bg-green-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                            <i class="fas fa-check text-xs"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ profile?.surname }} {{ profile?.other_names }}
                                        </h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Party Member</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg inline-block">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Membership Number
                                    </p>
                                    <div class="flex items-center">
                                        <div class="flex items-center">
                                            <i class="fas fa-hashtag text-primary-500 dark:text-primary-400 mr-2 text-base"></i>
                                            <span class="text-base font-bold text-gray-900 dark:text-white font-mono tracking-wide uppercase">
                                                {{ profile?.citizen?.uuid || 'N/A' }}
                                            </span>
                                        </div>
                                        <button 
                                            @click="copyToClipboard(profile?.citizen?.uuid || '')"
                                            class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-200 ml-2"
                                            title="Copy to clipboard"
                                        >
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Personal Information Fields -->
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Full Name
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-user text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.surname }} {{ profile?.other_names }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Gender
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-venus-mars text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.gender ? profile.gender.charAt(0).toUpperCase() + profile.gender.slice(1) : 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Date of Birth
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ formatDate(profile?.date_of_birth) }}
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                                (Age: {{ calculateAge(profile?.date_of_birth) }})
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        National ID
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-id-card text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.national_identification_number || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Passport Number
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-passport text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.passport_number || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Disability Status
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-wheelchair text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.disability_status === 'yes' ? 'Yes' : 'No' }}
                                            <span v-if="profile?.disability_status === 'yes' && profile?.ncpwd_number" class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                                (NCPWD: {{ profile.ncpwd_number }})
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Ethnicity
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.ethnicity?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Religion
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-pray text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.religion?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Enlisting Date
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-plus text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ formatDate(profile?.citizen?.created_at) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Tab -->
                        <div v-show="activeTab === 'contact'" class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Email Address
                                    </p>
                                    <div class="flex items-start">
                                        <i class="fas fa-envelope text-gray-400 mt-0.5 mr-2 flex-shrink-0"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ user?.email || "Not available" }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Phone Number
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-phone-alt text-gray-400 mr-2"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ user?.profile?.telephone || user?.telephone || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Physical Address
                                    </p>
                                    <div class="flex items-start">
                                        <i class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 flex-shrink-0"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ getAddress }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Tab -->
                        <div v-show="activeTab === 'location'" class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        County
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-city text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.county?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Sub-County
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-pin text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.sub_county?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Constituency
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-signs text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.constituency?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Ward
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.ward?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Location
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.location?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Village
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.village?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Polling Center
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-vote-yea text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.polling_center?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Polling Station
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-vote-yea text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.polling_station?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                        Polling Stream
                                    </p>
                                    <div class="flex items-center">
                                        <i class="fas fa-stream text-gray-400 mr-2 text-sm"></i>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ profile?.citizen?.polling_stream?.name || 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
        <div v-if="showToast" class="toast-notification fixed bottom-4 right-4 max-w-sm w-full bg-green-50 dark:bg-green-900 rounded-lg shadow-lg p-4 transform transition-all duration-300 ease-in-out z-50">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-green-800 dark:text-green-100">
                        {{ toastMessage }}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button 
                        @click="showToast = false"
                        class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>
