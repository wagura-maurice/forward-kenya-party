<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    logoUrl: {
        type: String,
        required: true,
    },
    service: {
        type: Object,
        required: true,
    },
    relatedServices: {
        type: Array,
        default: () => [],
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Check if the service requires payment
const requiresPayment = computed(() => {
    return props.service.requires_payment || false;
});

// Get the department name with link
const departmentLink = computed(() => {
    if (!props.service.department) return null;
    return {
        name: props.service.department.name,
        url: route('frontend.show.department', { id: props.service.department.id })
    };
});
</script>

<template>
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
        <Head :title="title" />

        <!-- Breadcrumb -->
        <nav class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="container mx-auto px-4 py-3">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li v-for="(item, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                        <Link v-if="item.url" :href="item.url" class="text-gray-700 hover:text-green-600 dark:text-gray-400 dark:hover:text-white text-sm font-medium">
                            {{ item.label }}
                        </Link>
                        <span v-else class="text-gray-500 dark:text-gray-300 text-sm font-medium">
                            {{ item.label }}
                        </span>
                        <svg v-if="index < breadcrumbs.length - 1" class="w-4 h-4 mx-2 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                        </svg>
                    </li>
                </ol>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Service Header -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                    <i :class="service.icon_path || 'fas fa-cog text-2xl'"></i>
                                </div>
                                <h1 class="ml-4 text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ service.name }}
                                </h1>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                                    :class="requiresPayment ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'">
                                    <i :class="requiresPayment ? 'fas fa-dollar-sign mr-1' : 'fas fa-check-circle mr-1'"></i>
                                    {{ requiresPayment ? 'Paid Service' : 'Free Service' }}
                                </span>
                            </div>
                        </div>

                        <!-- Department Badge -->
                        <div v-if="departmentLink" class="mb-6">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Department:</span>
                            <Link :href="departmentLink.url" class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800">
                                <i class="fas fa-building mr-1"></i>
                                {{ departmentLink.name }}
                            </Link>
                        </div>

                        <!-- Service Description -->
                        <div class="prose max-w-none dark:prose-invert mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Service Description</h3>
                            <div v-html="service.description || 'No description available.'"></div>
                        </div>

                        <!-- Requirements -->
                        <div v-if="service.requirements && service.requirements.length > 0" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Requirements</h3>
                            <ul class="space-y-2">
                                <li v-for="(requirement, index) in service.requirements" :key="index" class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ requirement.name || requirement.description }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Additional Information -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-6">
                            <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                                <div>
                                    <span>Last updated: {{ formatDate(service.updated_at) }}</span>
                                </div>
                                <div v-if="service.type">
                                    <span>Type: {{ service.type.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Services -->
                    <div v-if="relatedServices.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Related Services</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="related in relatedServices" :key="related.id" class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <Link :href="route('frontend.show.service', { id: related.id })" class="block">
                                    <div class="flex items-start">
                                        <div class="p-2 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 mr-3">
                                            <i :class="related.icon_path || 'fas fa-cog'"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white hover:text-green-600 dark:hover:text-green-400">
                                                {{ related.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                                {{ related.short_description || related.description?.substring(0, 100) + '...' }}
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Info</h3>
                        <div class="space-y-4">
                            <div v-if="departmentLink">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Department</h4>
                                <Link :href="departmentLink.url" class="text-blue-600 hover:underline dark:text-blue-400">
                                    {{ departmentLink.name }}
                                </Link>
                            </div>
                            
                            <div v-if="service.contact_email">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Email</h4>
                                <a :href="'mailto:' + service.contact_email" class="text-blue-600 hover:underline dark:text-blue-400">
                                    {{ service.contact_email }}
                                </a>
                            </div>

                            <div v-if="service.contact_phone">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Phone</h4>
                                <a :href="'tel:' + service.contact_phone" class="text-blue-600 hover:underline dark:text-blue-400">
                                    {{ service.contact_phone }}
                                </a>
                            </div>

                            <div v-if="service.website_url">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</h4>
                                <a :href="service.website_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline dark:text-blue-400">
                                    {{ service.website_url.replace(/^https?:\/\//, '') }}
                                </a>
                            </div>

                            <div v-if="service.average_processing_time">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Processing Time</h4>
                                <p class="text-gray-700 dark:text-gray-300">{{ service.average_processing_time }}</p>
                            </div>

                            <div v-if="requiresPayment">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Fee</h4>
                                <p class="text-gray-700 dark:text-gray-300">
                                    {{ service.fee_amount ? `KES ${parseFloat(service.fee_amount).toFixed(2)}` : 'Contact for pricing' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Service Categories -->
                    <div v-if="service.category" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Category</h3>
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 mr-3">
                                <i :class="service.category.icon_path || 'fas fa-folder'"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ service.category.name }}</h4>
                                <p v-if="service.category.description" class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ service.category.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.prose :deep(h1),
.prose :deep(h2),
.prose :deep(h3),
.prose :deep(h4),
.prose :deep(h5),
.prose :deep(h6) {
    @apply text-gray-900 dark:text-white;
}

.prose :deep(p),
.prose :deep(ul),
.prose :deep(ol),
.prose :deep(li) {
    @apply text-gray-700 dark:text-gray-300;
}

.prose :deep(a) {
    @apply text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
