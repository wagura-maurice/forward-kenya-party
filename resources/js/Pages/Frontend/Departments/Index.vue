<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";

const props = defineProps({
    title: {
        type: String,
        default: "Party Departments",
    },
    logoUrl: {
        type: String,
        default: "/storage/logos/logo.png",
    },
    departments: {
        type: Object,
        required: true,
    },
    sortOptions: {
        type: Array,
        default: () => [],
    },
    currentSort: {
        type: String,
        default: "name_asc",
    },
    currentSortLabel: {
        type: String,
        default: "A to Z",
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            sort: "name_asc",
        }),
    },
});

// Initialize filters from props (which come from URL params via backend)
const search = ref(props.filters.search || "");
const sortBy = ref(props.currentSort || "name_asc");

// Track if we're currently navigating to prevent double updates
const isNavigating = ref(false);

// Debounce utility
const debounce = (fn, delay) => {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
    };
};

// Single navigation function that handles all URL updates
const navigateToServices = (updates = {}) => {
    if (isNavigating.value) return;

    isNavigating.value = true;

    // Build parameters object with current values and updates
    const params = {
        page: updates.page || 1,
        search: updates.search !== undefined ? updates.search : search.value,
        sort: updates.sort !== undefined ? updates.sort : sortBy.value,
    };

    // Clean up empty parameters
    Object.keys(params).forEach((key) => {
        if (
            params[key] === "" ||
            params[key] === null ||
            params[key] === undefined
        ) {
            delete params[key];
        }
    });

    // Ensure page is always present and at least 1
    if (!params.page || params.page < 1) {
        params.page = 1;
    }

    router.get(route("frontend.departments"), params, {
        preserveState: true,
        replace: false,
        preserveScroll: true,
        only: ["departments", "filters", "currentSort", "currentSortLabel"],
        onFinish: () => {
            isNavigating.value = false;
        },
    });
};

// Debounced navigation for search
const debouncedNavigate = debounce((updates) => {
    navigateToServices(updates);
}, 300);

// Watch for local filter changes
watch([search], ([newSearch], [oldSearch]) => {
    if (isNavigating.value) return;

    // Reset to page 1 when filters change
    debouncedNavigate({
        search: newSearch,
        page: 1,
    });
});

// Watch for sort changes (no debounce needed)
watch(sortBy, (newSort) => {
    if (isNavigating.value) return;

    navigateToServices({
        sort: newSort,
        page: 1, // Reset to page 1 when sorting changes
    });
});

// Sync local state when props change (back/forward navigation)
watch(
    () => props.filters,
    (newFilters) => {
        if (!isNavigating.value) {
            search.value = newFilters.search || "";
        }
    },
    { deep: true }
);

watch(
    () => props.currentSort,
    (newSort) => {
        if (!isNavigating.value) {
            sortBy.value = newSort || "name_asc";
        }
    }
);

// Handle sort dropdown change
const handleSortChange = (event) => {
    sortBy.value = event.target.value;
};

// Handle search button click
const handleSearch = () => {
    navigateToServices({
        search: search.value,
        page: 1,
    });
};

// Handle Enter key in search input
const handleKeyDown = (event) => {
    if (event.key === "Enter") {
        handleSearch();
    }
};

// Handle pagination clicks
const handlePageClick = (page) => {
    navigateToServices({ page });
};

// Reset all filters
const resetFilters = () => {
    search.value = "";
    sortBy.value = "name_asc";

    navigateToServices({
        search: "",
        sort: "name_asc",
        page: 1,
    });
};

// Computed property for pagination range
const paginationRange = computed(() => {
    const current = props.departments.current_page;
    const last = props.departments.last_page;
    const range = [];

    // Show up to 5 page numbers
    let start = Math.max(1, current - 2);
    let end = Math.min(last, current + 2);

    // Adjust if we're near the beginning or end
    if (end - start < 4) {
        if (start === 1) {
            end = Math.min(last, start + 4);
        } else if (end === last) {
            start = Math.max(1, end - 4);
        }
    }

    for (let i = start; i <= end; i++) {
        range.push(i);
    }

    return range;
});
</script>

<template>
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
        <section class="container mx-auto px-4 py-8 pt-36 flex-1">
            <!-- Hero Section -->
            <h2
                class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"
            >
                {{ title }}
            </h2>
            <div
                class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mb-4"
            ></div>
            <p
                class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl"
            >
                At Forward Kenya Party, our specialized departments work in harmony to implement our vision for a better Kenya. Each department focuses on key areas of national development, ensuring comprehensive progress that benefits every citizen while upholding our core values of transparency, accountability, and service to the people.
            </p>
            <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <label for="search" class="sr-only">Search party departments</label>
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        id="search"
                        v-model="search"
                        @keydown="handleKeyDown"
                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Search party departments..."
                    />
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                    >
                        <button
                            type="button"
                            @click="handleSearch"
                            class="p-1 text-gray-500 hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4"
            >
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    All Party Departments
                </h2>
                <div class="relative ml-auto">
                    <div class="flex items-center">
                        <span
                            class="hidden sm:inline-flex items-center text-sm text-gray-500 dark:text-gray-400 mr-2"
                        >
                            <i class="fas fa-sort mr-1"></i> Sort:
                        </span>
                        <div class="relative">
                            <select
                                :value="sortBy"
                                @change="handleSortChange"
                                class="block w-auto pl-3 pr-8 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none"
                            >
                                <option
                                    v-for="option in sortOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Section -->
            <!-- Departments Grid -->
            <template v-if="departments.data && departments.data.length > 0">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"
                >
                    <div
                        v-for="department in departments.data"
                        :key="department.id"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
                    >
                        <div class="p-6 flex flex-col h-full">
                            <div class="flex-1">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300"
                                    >
                                        <img
                                            v-if="department.logo_path"
                                            :src="department.logo_path"
                                            :alt="department.name"
                                            class="h-8 w-auto"
                                        />
                                        <i
                                            v-else
                                            class="fas fa-building text-2xl"
                                        ></i>
                                    </div>
                                    <h3
                                        class="ml-4 text-xl font-semibold text-gray-900 dark:text-white"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'frontend.show.department',
                                                    {
                                                        id: department.id,
                                                    }
                                                )
                                            "
                                            class="hover:text-blue-600 dark:hover:text-blue-400"
                                        >
                                            {{ department.name }}
                                        </Link>
                                    </h3>
                                </div>
                                <p
                                    class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3"
                                >
                                    {{
                                        department.short_description ||
                                        department.description ||
                                        "No description available"
                                    }}
                                </p>
                            </div>
                            <div class="mt-4">
                                <Link
                                    :href="
                                        route('frontend.show.department', {
                                            id: department.id,
                                        })
                                    "
                                    class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-medium"
                                >
                                    Learn more
                                    <i class="fas fa-arrow-right ml-1"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination Controls -->
                <div
                    class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4"
                >
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ departments.from }} to
                        {{ departments.to }} of
                        {{ departments.total }} departments
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Previous Button -->
                        <button
                            @click="
                                handlePageClick(departments.current_page - 1)
                            "
                            :disabled="departments.current_page === 1"
                            class="px-4 py-2 border rounded-md text-sm font-medium transition-colors"
                            :class="{
                                'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed':
                                    departments.current_page === 1,
                                'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700':
                                    departments.current_page > 1,
                            }"
                        >
                            Previous
                        </button>

                        <!-- Page Numbers -->
                        <div class="flex space-x-1">
                            <button
                                v-for="page in paginationRange"
                                :key="page"
                                @click="handlePageClick(page)"
                                class="w-10 h-10 flex items-center justify-center rounded-md text-sm font-medium transition-colors"
                                :class="{
                                    'bg-green-600 text-white':
                                        departments.current_page === page,
                                    'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700':
                                        departments.current_page !== page,
                                }"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <!-- Next Button -->
                        <button
                            @click="
                                handlePageClick(departments.current_page + 1)
                            "
                            :disabled="
                                departments.current_page ===
                                departments.last_page
                            "
                            class="px-4 py-2 border rounded-md text-sm font-medium transition-colors"
                            :class="{
                                'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed':
                                    departments.current_page ===
                                    departments.last_page,
                                'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700':
                                    departments.current_page <
                                    departments.last_page,
                            }"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </template>

            <!-- No Results -->
            <div v-else class="text-center py-12">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No departments found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Try adjusting your search or filter to find what you're
                    looking for.
                </p>

                <div
                    class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-left max-w-2xl mx-auto"
                >
                    <h4
                        class="font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Current search criteria:
                    </h4>
                    <ul class="space-y-2">
                        <li v-if="search" class="flex items-start">
                            <span
                                class="text-gray-500 dark:text-gray-400 w-32 flex-shrink-0"
                                >Search term:</span
                            >
                            <span
                                class="font-medium text-gray-900 dark:text-white"
                                >{{ search }}</span
                            >
                        </li>
                        <li class="flex items-start">
                            <span
                                class="text-gray-500 dark:text-gray-400 w-32 flex-shrink-0"
                                >Sort by:</span
                            >
                            <span
                                class="font-medium text-gray-900 dark:text-white"
                            >
                                {{
                                    sortOptions.find(
                                        (opt) => opt.value === sortBy
                                    )?.label || "Default"
                                }}
                            </span>
                        </li>
                    </ul>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 mb-6">
                    We couldn't find any departments matching your search
                    criteria. Try adjusting your search or reset the filters to
                    see all departments.
                </p>

                <div class="space-x-4">
                    <button
                        @click="resetFilters"
                        class="text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    >
                        <i class="fas fa-sync-alt mr-2"></i>
                        Reset all filters
                    </button>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
