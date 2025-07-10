<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    logoUrl: {
        type: String,
        required: true,
    },
    department: {
        type: Object,
        required: true,
    },
    relatedDepartments: {
        type: Array,
        default: () => [],
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
    services: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
        }),
    },
});

const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Get the current URL for sharing
const currentUrl = ref(window.location.href);

// Format phone number for display
const formatPhone = (phone) => {
    if (!phone) return "";
    // Format as (XXX) XXX-XXXX if it's a 10-digit number
    const cleaned = ("" + phone).replace(/\D/g, "");
    const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
    return match ? `(${match[1]}) ${match[2]}-${match[3]}` : phone;
};

// Team section functionality
const activeTeamTab = ref("all");
const showAllTeamMembers = ref(false);

// Get initials from name for avatar
const getInitials = (name) => {
    if (!name) return "??";
    return name
        .split(" ")
        .map((part) => part[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

// Filter team members based on active tab
const filteredTeam = computed(() => {
    if (!props.department.staff) return [];

    let filtered = [...props.department.staff];

    // Filter by tab
    if (activeTeamTab.value === "leadership") {
        filtered = filtered.filter((member) => member.is_leadership);
    } else if (activeTeamTab.value === "staff") {
        filtered = filtered.filter((member) => !member.is_leadership);
    }

    // Limit number of displayed members if needed
    if (!showAllTeamMembers.value && filtered.length > 8) {
        return filtered.slice(0, 8);
    }

    return filtered;
});

// Service filtering
const isServiceFilterOpen = ref(false);
const filters = reactive({
    freeOnly: false,
    processingTime: "",
});

// Filter services based on active filters
const filteredServices = computed(() => {
    if (!props.department.services) return [];

    return props.department.services.filter((service) => {
        // Filter by free only
        if (filters.freeOnly && !service.is_free) return false;

        // Filter by processing time (simplified example)
        if (filters.processingTime) {
            if (
                filters.processingTime === "same-day" &&
                service.processing_time !== "Same Day"
            )
                return false;
            if (
                filters.processingTime === "1-3" &&
                !service.processing_time?.includes("1-3")
            )
                return false;
            // Add more processing time filters as needed
        }

        return true;
    });
});

// Reset all service filters
const resetFilters = () => {
    filters.freeOnly = false;
    filters.processingTime = "";
    isServiceFilterOpen.value = false;
};

// Get the first paragraph from HTML content
const getFirstParagraph = (html) => {
    if (!html) return "";
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
};

// Check if department has contact info
const hasContactInfo = computed(() => {
    return (
        props.department.contact_email ||
        props.department.contact_phone ||
        props.department.office_location ||
        props.department.working_hours
    );
});

// Check if department has team members
const hasTeam = computed(() => {
    return props.department.staff && props.department.staff.length > 0;
});

// Check if department has services
const hasServices = computed(() => {
    return props.department.services && props.department.services.length > 0;
});
</script>

<template>
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
        <Head :title="title">
            <meta
                name="description"
                :content="getFirstParagraph(department.description)"
            />
            <meta
                property="og:title"
                :content="`${department.name} | ${title}`"
            />
            <meta
                property="og:description"
                :content="getFirstParagraph(department.description)"
            />
            <meta property="og:url" :content="currentUrl" />
            <meta name="twitter:card" content="summary_large_image" />
        </Head>

        <!-- Breadcrumb -->
        <nav
            class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
        >
            <div class="container mx-auto px-4 py-3">
                <ol class="flex items-center space-x-1 md:space-x-3">
                    <li
                        v-for="(item, index) in breadcrumbs"
                        :key="index"
                        class="inline-flex items-center"
                    >
                        <Link
                            v-if="item.url"
                            :href="item.url"
                            class="text-sm font-medium text-gray-700 hover:text-green-600 dark:text-gray-400 dark:hover:text-white"
                        >
                            {{ item.label }}
                        </Link>
                        <span
                            v-else
                            class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400"
                        >
                            {{ item.label }}
                        </span>
                        <svg
                            v-if="index < breadcrumbs.length - 1"
                            class="w-4 h-4 mx-2 text-gray-400"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 12 10"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m7 9 4-4-4-4M1 9l4-4-4-4"
                            />
                        </svg>
                    </li>
                </ol>
            </div>
        </nav>

        <!-- Department Header -->
        <div class="relative bg-gradient-to-r from-blue-600 to-green-600">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="container mx-auto px-4 py-16 md:py-24 relative">
                <div class="max-w-3xl mx-auto text-center">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/10 backdrop-blur-sm mb-6"
                    >
                        <i
                            :class="
                                department.icon_path ||
                                'fas fa-building text-3xl text-white'
                            "
                        ></i>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">
                        {{ department.name }}
                    </h1>
                    <p
                        v-if="department.tagline"
                        class="text-xl text-blue-100 mb-6"
                    >
                        {{ department.tagline }}
                    </p>
                    <div class="flex flex-wrap justify-center gap-3 mt-6">
                        <a
                            v-if="department.website_url"
                            :href="department.website_url"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-full text-sm font-medium backdrop-blur-sm transition-colors"
                        >
                            <i class="fas fa-external-link-alt mr-2"></i> Visit
                            Website
                        </a>
                        <a
                            v-if="department.contact_email"
                            :href="'mailto:' + department.contact_email"
                            class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-full text-sm font-medium backdrop-blur-sm transition-colors"
                        >
                            <i class="fas fa-envelope mr-2"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Stats -->
        <div
            class="bg-white dark:bg-gray-800 shadow-sm -mt-8 relative z-10 mx-4 md:mx-6 rounded-lg"
        >
            <div
                class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-200 dark:divide-gray-700"
            >
                <div class="p-4 text-center">
                    <div
                        class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                    >
                        {{ department.services?.length || 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Services
                    </div>
                </div>
                <div class="p-4 text-center">
                    <div
                        class="text-2xl font-bold text-green-600 dark:text-green-400"
                    >
                        {{ department.staff?.length || 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Team Members
                    </div>
                </div>
                <div v-if="department.established_date" class="p-4 text-center">
                    <div
                        class="text-2xl font-bold text-purple-600 dark:text-purple-400"
                    >
                        {{
                            new Date(department.established_date).getFullYear()
                        }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Established
                    </div>
                </div>
                <div class="p-4 text-center">
                    <div
                        class="text-2xl font-bold text-amber-600 dark:text-amber-400"
                    >
                        {{ department.average_rating || "4.8" }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-star text-amber-400"></i> Average
                        Rating
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- About Section -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6"
                    >
                        <h2
                            class="text-2xl font-bold text-gray-900 dark:text-white mb-4"
                        >
                            About {{ department.name }}
                        </h2>
                        <div
                            class="prose max-w-none dark:prose-invert"
                            v-html="
                                department.description ||
                                'No description available.'
                            "
                        ></div>

                        <!-- Mission & Vision -->
                        <div
                            v-if="department.mission || department.vision"
                            class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6"
                        >
                            <div
                                v-if="department.mission"
                                class="bg-blue-50 dark:bg-blue-900/20 p-5 rounded-lg"
                            >
                                <h3
                                    class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2"
                                >
                                    Our Mission
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    {{ department.mission }}
                                </p>
                            </div>
                            <div
                                v-if="department.vision"
                                class="bg-green-50 dark:bg-green-900/20 p-5 rounded-lg"
                            >
                                <h3
                                    class="text-lg font-semibold text-green-800 dark:text-green-300 mb-2"
                                >
                                    Our Vision
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    {{ department.vision }}
                                </p>
                            </div>
                        </div>

                        <!-- Core Values -->
                        <div
                            v-if="
                                department.core_values &&
                                department.core_values.length > 0
                            "
                            class="mt-8"
                        >
                            <h3
                                class="text-xl font-semibold text-gray-900 dark:text-white mb-4"
                            >
                                Core Values
                            </h3>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                            >
                                <div
                                    v-for="(
                                        value, index
                                    ) in department.core_values"
                                    :key="index"
                                    class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow"
                                >
                                    <div class="flex items-center">
                                        <div
                                            class="p-2 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 mr-3"
                                        >
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <h4
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ value.title }}
                                        </h4>
                                    </div>
                                    <p
                                        class="mt-2 text-sm text-gray-600 dark:text-gray-300 ml-11"
                                    >
                                        {{ value.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services Section -->
                    <div
                        v-if="
                            department.services &&
                            department.services.length > 0
                        "
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8"
                    >
                        <div class="flex justify-between items-center mb-6">
                            <h2
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                Our Services
                            </h2>
                            <div class="relative">
                                <button
                                    @click="
                                        isServiceFilterOpen =
                                            !isServiceFilterOpen
                                    "
                                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                                >
                                    <i class="fas fa-filter mr-2"></i>
                                    Filter Services
                                    <i
                                        class="fas fa-chevron-down ml-2 text-xs"
                                    ></i>
                                </button>
                                <div
                                    v-show="isServiceFilterOpen"
                                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-600 z-10"
                                >
                                    <div class="p-2">
                                        <label
                                            class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
                                        >
                                            <input
                                                type="checkbox"
                                                v-model="filters.freeOnly"
                                                class="rounded text-blue-600 focus:ring-blue-500"
                                            />
                                            <span
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                >Free Services Only</span
                                            >
                                        </label>
                                        <div
                                            class="border-t border-gray-200 dark:border-gray-600 my-2"
                                        ></div>
                                        <div class="px-2 py-1">
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"
                                                >Processing Time</label
                                            >
                                            <select
                                                v-model="filters.processingTime"
                                                class="w-full text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                                            >
                                                <option value="">Any</option>
                                                <option value="same-day">
                                                    Same Day
                                                </option>
                                                <option value="1-3">
                                                    1-3 Days
                                                </option>
                                                <option value="1-week">
                                                    1 Week
                                                </option>
                                                <option value="2-weeks">
                                                    2 Weeks
                                                </option>
                                                <option value="1-month">
                                                    1 Month+
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="filteredServices.length === 0"
                            class="text-center py-8"
                        >
                            <i
                                class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"
                            ></i>
                            <p class="text-gray-500 dark:text-gray-400">
                                No services match your filter criteria.
                            </p>
                            <button
                                @click="resetFilters"
                                class="mt-2 text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium"
                            >
                                Clear all filters
                            </button>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="(service, index) in filteredServices"
                                :key="index"
                                class="group border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-200 dark:hover:border-blue-800"
                            >
                                <div class="flex items-start">
                                    <div
                                        class="flex-shrink-0 h-12 w-12 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mr-4 mt-1"
                                    >
                                        <i
                                            :class="
                                                service.icon ||
                                                'fas fa-cog text-xl'
                                            "
                                        ></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'services.show',
                                                        service.slug
                                                    )
                                                "
                                                class="hover:underline"
                                            >
                                                {{ service.name }}
                                            </Link>
                                        </h3>
                                        <p
                                            class="mt-1 text-gray-600 dark:text-gray-300 line-clamp-2"
                                        >
                                            {{
                                                service.short_description ||
                                                "No description available."
                                            }}
                                        </p>
                                        <div
                                            class="mt-3 flex flex-wrap items-center gap-3 text-sm"
                                        >
                                            <span
                                                v-if="service.is_free"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400"
                                            >
                                                <i
                                                    class="fas fa-check-circle mr-1.5"
                                                ></i>
                                                Free
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                                            >
                                                <i
                                                    class="fas fa-tag mr-1.5"
                                                ></i>
                                                {{
                                                    formatCurrency(
                                                        service.fee_amount
                                                    )
                                                }}
                                            </span>
                                            <span
                                                class="inline-flex items-center text-gray-500 dark:text-gray-400"
                                            >
                                                <i
                                                    class="far fa-clock mr-1.5"
                                                ></i>
                                                {{
                                                    service.processing_time ||
                                                    "Varies"
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    service.requirements &&
                                                    service.requirements
                                                        .length > 0
                                                "
                                                class="inline-flex items-center text-amber-600 dark:text-amber-400"
                                            >
                                                <i
                                                    class="fas fa-clipboard-list mr-1.5"
                                                ></i>
                                                {{
                                                    service.requirements.length
                                                }}
                                                Requirements
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <Link
                                            :href="
                                                route(
                                                    'services.show',
                                                    service.slug
                                                )
                                            "
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                        >
                                            View Details
                                            <i
                                                class="fas fa-arrow-right ml-1.5 text-xs"
                                            ></i>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Contact Information -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Contact Information
                        </h3>
                        <div class="space-y-4">
                            <div v-if="department.contact_email">
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Email
                                </h4>
                                <a
                                    :href="'mailto:' + department.contact_email"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ department.contact_email }}
                                </a>
                            </div>
                            <div v-if="department.contact_phone">
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Phone
                                </h4>
                                <a
                                    :href="'tel:' + department.contact_phone"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ department.contact_phone }}
                                </a>
                            </div>
                            <div v-if="department.office_location">
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Location
                                </h4>
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ department.office_location }}
                                </p>
                            </div>
                            <div v-if="department.working_hours">
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Working Hours
                                </h4>
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ department.working_hours }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Team Section -->
                    <div
                        v-if="department.staff && department.staff.length > 0"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8"
                    >
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6"
                        >
                            <div>
                                <h3
                                    class="text-xl font-bold text-gray-900 dark:text-white"
                                >
                                    Meet Our Team
                                </h3>
                                <p
                                    class="text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    Dedicated professionals serving you
                                </p>
                            </div>
                            <div class="mt-4 sm:mt-0 flex space-x-2">
                                <button
                                    @click="activeTeamTab = 'all'"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-full transition-colors',
                                        activeTeamTab === 'all'
                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                            : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700',
                                    ]"
                                >
                                    All Team
                                </button>
                                <button
                                    @click="activeTeamTab = 'leadership'"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-full transition-colors',
                                        activeTeamTab === 'leadership'
                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                            : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700',
                                    ]"
                                >
                                    Leadership
                                </button>
                                <button
                                    @click="activeTeamTab = 'staff'"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-full transition-colors',
                                        activeTeamTab === 'staff'
                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                            : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700',
                                    ]"
                                >
                                    Staff
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="filteredTeam.length === 0"
                            class="text-center py-12"
                        >
                            <i
                                class="fas fa-users text-4xl text-gray-300 dark:text-gray-600 mb-3"
                            ></i>
                            <p class="text-gray-500 dark:text-gray-400">
                                No team members found matching your criteria.
                            </p>
                        </div>

                        <div
                            v-else
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
                        >
                            <div
                                v-for="member in filteredTeam"
                                :key="member.id"
                                class="group bg-white dark:bg-gray-700 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800"
                            >
                                <!-- Profile Image -->
                                <div
                                    class="relative h-56 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-600 dark:to-gray-700 overflow-hidden"
                                >
                                    <img
                                        v-if="member.photo_path"
                                        :src="member.photo_path"
                                        :alt="member.name"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        loading="lazy"
                                    />
                                    <div
                                        v-else
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white text-5xl font-bold"
                                    >
                                        {{ getInitials(member.name) }}
                                    </div>

                                    <!-- Position Badge -->
                                    <div class="absolute top-4 right-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                            :class="
                                                member.is_leadership
                                                    ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400'
                                                    : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                            "
                                        >
                                            <i
                                                :class="
                                                    member.is_leadership
                                                        ? 'fas fa-star'
                                                        : 'fas fa-user'
                                                "
                                                class="mr-1.5"
                                            ></i>
                                            {{
                                                member.is_leadership
                                                    ? "Leadership"
                                                    : "Staff"
                                            }}
                                        </span>
                                    </div>

                                    <!-- Social Links -->
                                    <div
                                        class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent"
                                    >
                                        <div
                                            class="flex justify-between items-end"
                                        >
                                            <div>
                                                <h4
                                                    class="text-white font-bold text-lg leading-tight"
                                                >
                                                    {{ member.name }}
                                                </h4>
                                                <p
                                                    class="text-blue-200 text-sm"
                                                >
                                                    {{ member.position }}
                                                </p>
                                            </div>
                                            <div
                                                class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                            >
                                                <a
                                                    v-if="member.email"
                                                    :href="
                                                        'mailto:' + member.email
                                                    "
                                                    class="w-8 h-8 flex items-center justify-center bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors"
                                                    aria-label="Email"
                                                >
                                                    <i
                                                        class="fas fa-envelope text-sm"
                                                    ></i>
                                                </a>
                                                <a
                                                    v-if="member.phone"
                                                    :href="
                                                        'tel:' + member.phone
                                                    "
                                                    class="w-8 h-8 flex items-center justify-center bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors"
                                                    aria-label="Phone"
                                                >
                                                    <i
                                                        class="fas fa-phone-alt text-xs"
                                                    ></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member Details -->
                                <div class="p-5">
                                    <div
                                        v-if="member.department"
                                        class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3"
                                    >
                                        <i
                                            class="fas fa-building mr-2 opacity-70"
                                        ></i>
                                        <span>{{ member.department }}</span>
                                    </div>

                                    <p
                                        v-if="member.bio"
                                        class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3"
                                    >
                                        {{ member.bio }}
                                    </p>

                                    <!-- Skills -->
                                    <div
                                        v-if="
                                            member.skills &&
                                            member.skills.length > 0
                                        "
                                        class="mb-4"
                                    >
                                        <h5
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2"
                                        >
                                            EXPERTISE
                                        </h5>
                                        <div class="flex flex-wrap gap-1.5">
                                            <span
                                                v-for="(
                                                    skill, index
                                                ) in member.skills.slice(0, 3)"
                                                :key="index"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                            >
                                                {{ skill }}
                                            </span>
                                            <span
                                                v-if="member.skills.length > 3"
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-600 dark:text-gray-300"
                                            >
                                                +{{
                                                    member.skills.length - 3
                                                }}
                                                more
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Social Links -->
                                    <div
                                        class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-600"
                                    >
                                        <div class="flex space-x-3">
                                            <a
                                                v-if="member.linkedin_url"
                                                :href="member.linkedin_url"
                                                target="_blank"
                                                class="text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                                aria-label="LinkedIn"
                                            >
                                                <i
                                                    class="fab fa-linkedin text-lg"
                                                ></i>
                                            </a>
                                            <a
                                                v-if="member.twitter_handle"
                                                :href="
                                                    'https://twitter.com/' +
                                                    member.twitter_handle
                                                "
                                                target="_blank"
                                                class="text-gray-400 hover:text-blue-400 dark:text-gray-400 dark:hover:text-blue-300 transition-colors"
                                                aria-label="Twitter"
                                            >
                                                <i
                                                    class="fab fa-twitter text-lg"
                                                ></i>
                                            </a>
                                            <a
                                                v-if="member.website_url"
                                                :href="member.website_url"
                                                target="_blank"
                                                class="text-gray-400 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                                aria-label="Website"
                                            >
                                                <i
                                                    class="fas fa-globe text-lg"
                                                ></i>
                                            </a>
                                        </div>

                                        <Link
                                            v-if="member.slug"
                                            :href="
                                                route('team.show', member.slug)
                                            "
                                            class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors group"
                                        >
                                            View Profile
                                            <i
                                                class="fas fa-arrow-right ml-1.5 text-xs transform transition-transform group-hover:translate-x-1"
                                            ></i>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- View All Button -->
                        <div
                            v-if="filteredTeam.length > 8"
                            class="mt-8 text-center"
                        >
                            <button
                                @click="
                                    showAllTeamMembers = !showAllTeamMembers
                                "
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            >
                                {{
                                    showAllTeamMembers
                                        ? "Show Less"
                                        : "View All Team Members"
                                }}
                                <i
                                    class="fas"
                                    :class="
                                        showAllTeamMembers
                                            ? 'fa-chevron-up ml-2'
                                            : 'fa-chevron-down ml-2'
                                    "
                                ></i>
                            </button>
                        </div>
                    </div>

                    <!-- Related Departments -->
                    <div
                        v-if="relatedDepartments.length > 0"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Related Departments
                        </h3>
                        <ul class="space-y-3">
                            <li
                                v-for="related in relatedDepartments"
                                :key="related.id"
                            >
                                <Link
                                    :href="
                                        route('frontend.show.department', {
                                            id: related.id,
                                        })
                                    "
                                    class="flex items-center text-blue-600 hover:underline"
                                >
                                    <i
                                        :class="
                                            related.icon_path ||
                                            'fas fa-arrow-right mr-2 text-sm'
                                        "
                                    ></i>
                                    {{ related.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
