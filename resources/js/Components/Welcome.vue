<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const { props } = usePage();

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

// Format user's full name
const fullName = computed(() => {
    if (!user.value) return "Not available";

    const nameParts = [
        profile.value?.first_name,
        profile.value?.middle_name,
        profile.value?.last_name,
    ].filter(Boolean);

    return nameParts.length > 0
        ? nameParts.join(" ")
        : user.value.name || "Not provided";
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

// Format user's address
const getAddress = computed(() => {
    if (!user.value) return "Not provided";

    const address = [
        profile.value?.address_line_1,
        profile.value?.address_line_2,
        profile.value?.city,
        profile.value?.state,
        profile.value?.county?.name,
        profile.value?.sub_county?.name,
        profile.value?.constituency?.name,
        profile.value?.ward?.name,
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
                            <a href="#" class="inline-flex items-center font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                                <i class="fas fa-home w-4 h-4 mr-2"></i>
                                <span class="hidden sm:inline">Home</span>
                            </a>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-chevron-right w-3 h-3 mx-2"></i>
                            <a href="#" class="font-medium text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white transition-colors duration-200">
                                My Account
                            </a>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-chevron-right w-3 h-3 mx-2"></i>
                            <span class="font-medium text-primary-600 dark:text-primary-400">
                                Summary
                            </span>
                        </li>
                    </ol>
                </nav>

                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div class="space-y-1">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                            Account Summary
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Welcome back, {{ profile.first_name || 'Member' }}! Here's your account summary.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <Link
                            :href="route('profile.show')"
                            class="text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
                        >
                            <i class="fas fa-user-edit w-4 h-4 mr-2 transition-transform group-hover:scale-110"></i>
                            Edit Profile
                        </Link>
                    </div>
                </div>
                <!-- Content Section -->
                <div class="overflow-hidden transition-all duration-300">
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <!-- Personal Information -->
                            <div class="space-y-5">
                                <div class="relative pb-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-2">
                                        <i class="fas fa-user-circle text-primary-600 dark:text-primary-400 mr-2 text-xl"></i>
                                        Personal Information
                                    </h3>
                                    <div class="absolute -bottom-1 left-0 w-24 h-0.5 bg-gradient-to-r from-green-500 to-blue-500 rounded-full"></div>
                                </div>
                                <div class="space-y-4 p-1">
                                    <div class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-gray-800 dark:to-gray-800 p-3 rounded-lg border border-primary-100 dark:border-gray-700 shadow-sm">
                                        <p class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-1.5 flex items-center">
                                            <i class="fas fa-id-card mr-1.5 text-sm"></i>
                                            Member Number
                                        </p>
                                        <div class="flex items-center justify-between bg-white dark:bg-gray-900 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center">
                                                <i class="fas fa-hashtag text-primary-500 dark:text-primary-400 mr-2 text-base"></i>
                                                <span class="text-base font-bold text-gray-900 dark:text-white font-mono tracking-wide uppercase">
                                                    {{ profile.uuid }}
                                                </span>
                                            </div>
                                            <button 
                                                @click="copyToClipboard(profile.uuid)"
                                                class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-200"
                                                title="Copy to clipboard"
                                            >
                                                <i class="far fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5 flex items-center">
                                            <i class="fas fa-info-circle mr-1 text-[10px]"></i>
                                            Your unique Forward Kenya Party identifier
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                            Full Name
                                        </p>
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-gray-400 mr-2 text-sm"></i>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ fullName }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Gender and Date of Birth in one row -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                Gender
                                            </p>
                                            <div class="flex items-center">
                                                <i class="fas fa-venus-mars text-gray-400 mr-2 text-sm"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    {{ 
                                                        profile.gender === 'XX' ? 'Female' : 
                                                        profile.gender === 'XY' ? 'Male' : 
                                                        'Not specified' 
                                                    }}
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
                                                    {{
                                                        formatDate(
                                                            profile.date_of_birth
                                                        ) || 'Not provided'
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- National ID and Passport Number in one row -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                National ID
                                            </p>
                                            <div class="flex items-center">
                                                <i class="fas fa-id-card text-gray-400 mr-2 text-sm"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    {{
                                                        profile?.national_identification_number ||
                                                        "Not provided"
                                                    }}
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
                                                    {{ profile?.passport_number || "Not provided" }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Disability Status and NCPWD Number in one row -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                Disability Status
                                            </p>
                                            <div class="flex items-center">
                                                <i class="fas fa-wheelchair text-gray-400 mr-2 text-sm"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    {{ disabilityStatus }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="profile.ncpwd_number">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                NCPWD Number
                                            </p>
                                            <div class="flex items-center">
                                                <i class="fas fa-id-card text-gray-400 mr-2 text-sm"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    {{ profile.ncpwd_number }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Ethnicity and Religion in one row -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                Ethnicity
                                            </p>
                                            <div class="flex items-center">
                                                <i class="fas fa-globe text-gray-400 mr-2 text-sm"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    {{ ethnicity || 'Not provided' }}
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
                                                    {{ religion || 'Not provided' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Personal Information -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                Enlisting Date
                                            </p>
                                            <div class="flex items-center">
                                                <i class="far fa-calendar-check text-gray-400 mr-2 text-sm"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    2025-01-15
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                Recruiting Person
                                            </p>
                                            <div class="flex items-center">
                                                <i class="fas fa-user-tie text-gray-400 mr-2 text-sm"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    John Doe
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                Member Signature
                                            </p>
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800">
                                                <img 
                                                    src="https://cdn.imgbin.com/25/20/5/imgbin-digital-signature-digital-data-others-AZfAzpYQZFVuv6m16afW46QvA.jpg" 
                                                    alt="Member Signature"
                                                    class="h-16 w-auto object-contain mx-auto"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                Recruiter's Signature
                                            </p>
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800">
                                                <img 
                                                    src="https://cdn.imgbin.com/25/20/5/imgbin-digital-signature-digital-data-others-AZfAzpYQZFVuv6m16afW46QvA.jpg" 
                                                    alt="Recruiter's Signature"
                                                    class="h-16 w-auto object-contain mx-auto"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="space-y-5">
                                <div class="relative pb-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-2">
                                        <i class="fas fa-id-card text-primary-600 dark:text-primary-400 mr-2 text-xl"></i>
                                        Contact Information
                                    </h3>
                                    <div class="absolute -bottom-1 left-0 w-24 h-0.5 bg-gradient-to-r from-green-500 to-blue-500 rounded-full"></div>
                                </div>
                                <div class="space-y-4 p-1">
                                    <!-- Email and Phone in one row -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="w-full">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                Email
                                            </p>
                                            <div class="flex items-start">
                                                <i class="fas fa-envelope text-gray-400 mt-0.5 mr-2 flex-shrink-0"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    {{ user?.email || "Not available" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="w-full">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                Phone Number
                                            </p>
                                            <div class="flex items-center">
                                                <i class="fas fa-phone-alt text-gray-400 mr-2"></i>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                    {{
                                                        user?.profile?.telephone ||
                                                        user?.telephone ||
                                                        "Not provided"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Postal Address - full width -->
                                    <div class="w-full">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                            Postal Address
                                        </p>
                                        <div class="flex items-start">
                                            <i class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 flex-shrink-0"></i>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{
                                                    [
                                                        profile.address_line_1,
                                                        profile.address_line_2,
                                                        profile.city,
                                                        profile.state,
                                                    ]
                                                        .filter(Boolean)
                                                        .join(", ") || 'Not provided'
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Location Information -->
                                    <div class="space-y-5">
                                        <div class="relative pb-1">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-2">
                                                <i class="fas fa-map-marked-alt text-primary-600 dark:text-primary-400 mr-2 text-xl"></i>
                                                Location Information
                                            </h3>
                                            <div class="absolute -bottom-1 left-0 w-24 h-0.5 bg-gradient-to-r from-green-500 to-blue-500 rounded-full"></div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-1">
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                    County
                                                </p>
                                                <div class="flex items-center">
                                                    <i class="fas fa-city text-gray-400 mr-2 text-sm"></i>
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                        {{ profile.county?.name || "Not provided" }}
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
                                                        {{ profile.sub_county?.name || "Not provided" }}
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
                                                        {{ profile.constituency?.name || "Not provided" }}
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
                                                        {{ profile.ward?.name || "Not provided" }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Party information -->
                                    <div class="space-y-5">
                                        <div class="relative pb-1">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-2">
                                                <i class="fas fa-file-alt text-primary-600 dark:text-primary-400 mr-2 text-xl"></i>
                                                Party Information
                                            </h3>
                                            <div class="absolute -bottom-1 left-0 w-24 h-0.5 bg-gradient-to-r from-green-500 to-blue-500 rounded-full"></div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-2">
                                                Download important party documents and resources
                                            </p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-1">
                                            <!-- FKP Ideology -->
                                            <a
                                                href="/assets/FKP_Documents/FKP IDEOLOGY.pdf"
                                                download
                                                target="_blank"
                                                class="group flex items-center p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer dark:bg-gray-800 dark:border-gray-700 hover:border-primary-100 dark:hover:border-primary-800"
                                            >
                                                <div class="p-3 mr-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors duration-200">
                                                    <i class="fas fa-book-open text-blue-600 dark:text-blue-400"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 break-words">
                                                        FKP Ideology
                                                    </p>
                                                    <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="far fa-file-pdf mr-1"></i>
                                                        <span>PDF • 2.4 MB</span>
                                                    </div>
                                                </div>
                                                <div class="ml-auto">
                                                    <i class="fas fa-arrow-down text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                                                </div>
                                            </a>

                                            <!-- FKP Manifesto -->
                                            <a
                                                href="/assets/FKP_Documents/FKP MANIFESTO (1).pdf"
                                                download
                                                target="_blank"
                                                class="group flex items-center p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer dark:bg-gray-800 dark:border-gray-700 hover:border-primary-100 dark:hover:border-primary-800"
                                            >
                                                <div class="p-3 mr-3 rounded-xl bg-green-50 dark:bg-green-900/20 group-hover:bg-green-100 dark:group-hover:bg-green-900/30 transition-colors duration-200">
                                                    <i class="fas fa-file-alt text-green-600 dark:text-green-400"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 break-words">
                                                        FKP Manifesto
                                                    </p>
                                                    <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="far fa-file-pdf mr-1"></i>
                                                        <span>PDF • 2.4 MB</span>
                                                    </div>
                                                </div>
                                                <div class="ml-auto">
                                                    <i class="fas fa-arrow-down text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                                                </div>
                                            </a>

                                            <!-- Party Constitution -->
                                            <a
                                                href="/assets/FKP_Documents/FORWARD KENYA PARTY CONSTITUTION.pdf"
                                                download
                                                target="_blank"
                                                class="group flex items-center p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer dark:bg-gray-800 dark:border-gray-700 hover:border-primary-100 dark:hover:border-primary-800"
                                            >
                                                <div class="p-3 mr-3 rounded-xl bg-purple-50 dark:bg-purple-900/20 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/30 transition-colors duration-200">
                                                    <i class="fas fa-scroll text-purple-600 dark:text-purple-400"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 break-words">
                                                        Party Constitution
                                                    </p>
                                                    <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="far fa-file-pdf mr-1"></i>
                                                        <span>PDF • 2.4 MB</span>
                                                    </div>
                                                </div>
                                                <div class="ml-auto">
                                                    <i class="fas fa-arrow-down text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                                                </div>
                                            </a>

                                            <!-- Nomination Rules -->
                                            <a
                                                href="/assets/FKP_Documents/NOMINATION_RULES_AMMENDED 12.5.25.pdf"
                                                download
                                                target="_blank"
                                                class="group flex items-center p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer dark:bg-gray-800 dark:border-gray-700 hover:border-primary-100 dark:hover:border-primary-800"
                                            >
                                                <div class="p-3 mr-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 group-hover:bg-yellow-100 dark:group-hover:bg-yellow-900/30 transition-colors duration-200">
                                                    <i class="fas fa-file-contract text-yellow-600 dark:text-yellow-400"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 break-words">
                                                        Nomination Rules
                                                    </p>
                                                    <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="far fa-file-pdf mr-1"></i>
                                                        <span>PDF • 2.4 MB</span>
                                                    </div>
                                                </div>
                                                <div class="ml-auto">
                                                    <i class="fas fa-arrow-down text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6 text-center">
                                    <Link
                                        :href="route('profile.show')"
                                        class="group inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200"
                                    >
                                        <i class="fas fa-user-edit w-4 h-4 mr-2 transition-transform group-hover:scale-110"></i>
                                        Edit Profile
                                    </Link>
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
