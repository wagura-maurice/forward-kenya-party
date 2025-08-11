<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
});

const activities = ref({
    data: props.data.activities?.data || [],
    current_page: props.data.activities?.current_page || 1,
    first_page_url: props.data.activities?.first_page_url || '',
    from: props.data.activities?.from || 0,
    last_page: props.data.activities?.last_page || 1,
    last_page_url: props.data.activities?.last_page_url || '',
    links: props.data.activities?.links || [],
    next_page_url: props.data.activities?.next_page_url || null,
    path: props.data.activities?.path || '',
    per_page: props.data.activities?.per_page || 5,
    prev_page_url: props.data.activities?.prev_page_url || null,
    to: props.data.activities?.to || 0,
    total: props.data.activities?.total || 0
});

const user = ref(props.data.user || null);
const isLoading = ref(false);

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getStatusBadgeClass = (status) => {
    const statusClasses = {
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        'failed': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
        'in-progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    };
    return statusClasses[status?.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const activeDropdown = ref(null);

const toggleDropdown = (id, event) => {
    event.stopPropagation();
    activeDropdown.value = activeDropdown.value === id ? null : id;
};

const closeAllDropdowns = () => {
    activeDropdown.value = null;
};

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.dropdown-container')) {
        closeAllDropdowns();
    }
};

// Add click outside listener when component mounts
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

// Clean up event listener when component unmounts
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const handlePageChange = (url) => {
    if (!url || isLoading.value) return;
    
    closeAllDropdowns();
    isLoading.value = true;
    
    // Use Inertia's get method to handle the page change
    router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['data'],
        onSuccess: (page) => {
            // Update the activities ref with the new pagination data
            const newActivities = page.props.data.activities;
            activities.value = {
                ...activities.value,
                data: newActivities.data,
                current_page: newActivities.current_page,
                first_page_url: newActivities.first_page_url,
                from: newActivities.from,
                last_page: newActivities.last_page,
                last_page_url: newActivities.last_page_url,
                links: newActivities.links,
                next_page_url: newActivities.next_page_url,
                path: newActivities.path,
                per_page: newActivities.per_page,
                prev_page_url: newActivities.prev_page_url,
                to: newActivities.to,
                total: newActivities.total
            };
            
            isLoading.value = false;
            
            // Smooth scroll to top of the table
            const table = document.querySelector('.activity-table');
            if (table) {
                table.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },
        onError: () => {
            isLoading.value = false;
            showToast('Error', 'Failed to load activities', 'error');
        }
    });
};

const copyActivityDetails = async (activity) => {
    try {
        // Format the activity data for better readability
        const activityDetails = {
            'ID': activity.id,
            'Action': activity.action,
            'Description': activity.description,
            'Status': activity.status,
            'Created At': activity.created_at,
            'User': activity.user_name,
            'Department': activity.department_name || 'N/A',
            'Service': activity.service_name || 'N/A'
        };

        const formattedDetails = Object.entries(activityDetails)
            .map(([key, value]) => `${key}: ${value}`)
            .join('\n');

        // Try modern clipboard API first
        if (navigator.clipboard) {
            await navigator.clipboard.writeText(formattedDetails);
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = formattedDetails;
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }

        // Show success toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: 'success',
            title: 'Activity details copied to clipboard!'
        });
    } catch (err) {
        console.error('Failed to copy activity details: ', err);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to copy activity details',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
};

const showToast = (title, message, icon = 'success') => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: icon,
        title: title,
        text: message
    });
};

const dropdownRefs = ref({});

const setDropdownRef = (el, id) => {
    if (el) {
        dropdownRefs.value[id] = el;
    }
};

const getDropdownPosition = (activityId) => {
    const button = dropdownRefs.value[`button-${activityId}`];
    if (!button) return { display: 'none' };
    
    const rect = button.getBoundingClientRect();
    const scrollY = window.scrollY || document.documentElement.scrollTop;
    
    return {
        position: 'fixed',
        top: `${rect.bottom + scrollY}px`,
        left: `${rect.left + rect.width - 160}px`,
        width: '160px',
        zIndex: 9999
    };
};

const handleDelete = (activityId) => {
    Swal.fire({
        title: 'Confirm Deletion',
        html: '<div class="text-center"><p class="text-lg font-medium mb-4">Are you sure you want to delete this activity?</p><p class="text-red-600 dark:text-red-400 font-medium">This action cannot be undone!</p></div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2 text-red-600 dark:text-red-400"></i> Delete',
        cancelButtonText: 'Cancel',
        buttonsStyling: true,
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            confirmButton: 'px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500',
            cancelButton: 'px-4 py-2 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500',
            popup: 'rounded-lg border border-gray-200 dark:border-gray-700'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('activity.delete', activityId), {
                preserveScroll: true,
                onSuccess: () => {
                    showToast('Success', 'Activity has been deleted successfully', 'success');
                },
                onError: () => {
                    showToast('Error', 'Failed to delete activity', 'error');
                }
            });
        }
    });
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
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400"
                            >
                                <i class="fas fa-home mr-2"></i>
                                Dashboard
                            </Link>
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="flex flex-col md:flex-row h-full">
                        <!-- Main Content Area -->
                        <div class="w-full p-6">
                            <div class="relative">
                                <!-- Loading Overlay -->
                                <div v-if="isLoading" class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 z-10 flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500"></div>
                                </div>
                                <!-- Desktop Table -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 hidden md:table">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                #
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Details
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Created
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-if="activities.data && activities.data.length === 0">
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                                No activities found.
                                            </td>
                                        </tr>
                                        <tr v-else v-for="activity in activities.data" :key="activity.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="pl-6 pr-2 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    #{{ activity.id }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img v-if="activity.user_avatar" :src="activity.user_avatar" :alt="activity.user_name" class="h-10 w-10 rounded-full">
                                                        <div v-else class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-500"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            <Link v-if="activity.user_id" :href="route('profile.view', { user_id: activity.user_id })" class="text-green-600 hover:underline underline-offset-4 capitalize">
                                                                {{ activity.user_name }}
                                                            </Link>
                                                            <span v-else>System</span>
                                                        </div>
                                                        <div v-if="activity.department_name" class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ activity.department_name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ activity.description }}</div>
                                                <div v-if="activity.details" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ activity.details }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div class="text-xs text-gray-400">
                                                    {{ activity.created_at }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusBadgeClass(activity.status)">
                                                    {{ activity.status || 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="relative inline-block text-left">
                                                    <div>
                                                        <button 
                                                            @click.stop="toggleDropdown(activity.id, $event)" 
                                                            type="button" 
                                                            class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                                            :aria-expanded="activeDropdown === activity.id"
                                                            aria-haspopup="true"
                                                            :ref="el => setDropdownRef(el, `button-${activity.id}`)"
                                                        >
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                    </div>
                                                    <teleport to="body">
                                                        <transition
                                                            enter-active-class="transition ease-out duration-100"
                                                            enter-from-class="transform opacity-0 scale-95"
                                                            enter-to-class="transform opacity-100 scale-100"
                                                            leave-active-class="transition ease-in duration-75"
                                                            leave-from-class="transform opacity-100 scale-100"
                                                            leave-to-class="transform opacity-0 scale-95"
                                                        >
                                                            <div 
                                                                v-show="activeDropdown === activity.id"
                                                                class="fixed z-[9999] mt-2 w-40 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                                role="menu"
                                                                :style="getDropdownPosition(activity.id)"
                                                        >
                                                            <div class="py-1" role="none">
                                                                <button 
                                                                    @click.stop="copyActivityDetails(activity); closeAllDropdowns();"
                                                                    class="w-full text-left px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 focus:outline-none focus:bg-blue-50 dark:focus:bg-blue-900/30 flex items-center"
                                                                    role="menuitem"
                                                                    tabindex="-1"
                                                                >
                                                                    <i class="far fa-copy mr-2"></i> Copy
                                                                </button>
                                                                <button 
                                                                    @click.stop="handleDelete(activity.id); closeAllDropdowns();"
                                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 focus:outline-none focus:bg-red-50 dark:focus:bg-red-900/30 flex items-center"
                                                                    role="menuitem"
                                                                    tabindex="-1"
                                                                >
                                                                    <i class="far fa-trash-alt mr-2"></i> Delete
                                                                </button>
                                                                </div>
                                                            </div>
                                                        </transition>
                                                    </teleport>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>

                                <!-- Mobile Cards -->
                                <div class="space-y-4 md:hidden">
                                    <div v-for="activity in activities.data" :key="activity.id" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-full flex items-center justify-center text-white text-xl" :class="activity.color || 'bg-blue-500'">
                                                <i :class="activity.icon || 'fas fa-info-circle'"></i>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <div class="flex justify-between items-start">
                                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ activity.title }}</h4>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusBadgeClass(activity.status)">
                                                        {{ activity.status || 'N/A' }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ activity.description }}</p>
                                                
                                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                    <i class="far fa-user mr-1"></i>
                                                    <Link v-if="activity.user_id" :href="route('profile.view', { user_id: activity.user_id })" class="text-green-600 hover:underline">
                                                        {{ activity.user_name || 'System' }}
                                                    </Link>
                                                    <span v-else>System</span>
                                                </div>
                                                
                                                <div v-if="activity.department_name" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <i class="far fa-building mr-1"></i>
                                                    {{ activity.department_name }}
                                                </div>
                                                
                                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    <div><i class="far fa-clock mr-1"></i> {{ formatDate(activity.created_at) }}</div>
                                                    <div v-if="activity.completed_at" class="mt-1">
                                                        <i class="far fa-check-circle mr-1"></i> {{ formatDate(activity.completed_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- No Results Message -->
                                <div v-if="activities.data && activities.data.length === 0" class="text-center py-8">
                                    <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500 dark:text-gray-400">No activities found</p>
                                </div>

                                <!-- Pagination -->
                                <div v-if="activities.links && activities.links.length > 3" class="mt-6 px-4 py-3 sm:px-6">
                                    <!-- Mobile Pagination Info -->
                                    <div class="sm:hidden flex justify-between items-center mb-4">
                                        <button 
                                            v-if="activities.prev_page_url"
                                            @click="handlePageChange(activities.prev_page_url)"
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600"
                                            :disabled="isLoading"
                                        >
                                            Previous
                                        </button>
                                        <div class="flex-1 flex justify-center">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                Page {{ activities.current_page }} of {{ activities.last_page }}
                                            </span>
                                        </div>
                                        <button 
                                            v-if="activities.next_page_url"
                                            @click="handlePageChange(activities.next_page_url)"
                                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600"
                                            :disabled="isLoading"
                                        >
                                            Next
                                        </button>
                                    </div>

                                    <!-- Desktop Pagination -->
                                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                Showing
                                                <span class="font-medium">{{ activities.from || 0 }}</span>
                                                to
                                                <span class="font-medium">{{ activities.to || 0 }}</span>
                                                of
                                                <span class="font-medium">{{ activities.total }}</span>
                                                results
                                            </p>
                                        </div>
                                        <div>
                                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                                <button 
                                                    v-for="(link, index) in activities.links" 
                                                    :key="index"
                                                    @click="link.url ? handlePageChange(link.url) : null"
                                                    :class="[
                                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                        link.active 
                                                            ? 'z-10 bg-green-50 border-green-500 text-green-600 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300' 
                                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600',
                                                        index === 0 ? 'rounded-l-md' : '',
                                                        index === activities.links.length - 1 ? 'rounded-r-md' : '',
                                                        !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                                    ]"
                                                    :disabled="!link.url || isLoading"
                                                    v-html="link.label"
                                                ></button>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
