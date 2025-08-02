<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const { title } = defineProps({
    title: {
        type: String,
        required: true,
    },
});

const activeTab = ref('core');

const tabs = [
    { id: 'core', name: 'Core Settings', icon: 'cog' },
    { id: 'media', name: 'Media Management', icon: 'images' },
    { id: 'roles', name: 'Roles & Permissions', icon: 'user-shield' },
    { id: 'regions', name: 'Regions', icon: 'map-marked-alt' },
    { id: 'demographics', name: 'Demographics', icon: 'users' },
    { id: 'departments', name: 'Departments', icon: 'sitemap' },
    { id: 'services', name: 'Services', icon: 'concierge-bell' },
    { id: 'communications', name: 'Communications', icon: 'comments' },
];

const subTabs = {
    media: [
        { id: 'media-types', name: 'Media Types', icon: 'list' },
        { id: 'media-categories', name: 'Media Categories', icon: 'folder' },
    ],
    regions: [
        { id: 'counties', name: 'Counties', icon: 'map' },
        { id: 'subcounties', name: 'Sub-Counties', icon: 'map-marked' },
        { id: 'constituencies', name: 'Constituencies', icon: 'map-pin' },
        { id: 'wards', name: 'Wards', icon: 'map-signs' },
        { id: 'locations', name: 'Locations', icon: 'location-arrow' },
        { id: 'villages', name: 'Villages', icon: 'home' },
        { id: 'polling-centers', name: 'Polling Centers', icon: 'vote-yea' },
        { id: 'polling-stations', name: 'Polling Stations', icon: 'vote-yea' },
    ],
    demographics: [
        { id: 'ethnicities', name: 'Ethnic Groups', icon: 'users' },
    ],
    departments: [
        { id: 'department-types', name: 'Department Types', icon: 'sitemap' },
        { id: 'department-categories', name: 'Categories', icon: 'folder' },
    ],
    services: [
        { id: 'service-types', name: 'Service Types', icon: 'concierge-bell' },
        { id: 'service-categories', name: 'Categories', icon: 'folder' },
    ],
    communications: [
        { id: 'communication-types', name: 'Communication Types', icon: 'comments' },
        { id: 'communication-categories', name: 'Categories', icon: 'folder' },
    ],
};

const hasSubTabs = (tabId) => {
    return Object.keys(subTabs).includes(tabId);
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
                                :href="route('settings')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400"
                            >
                                <i class="fas fa-home mr-2"></i>
                                Settings
                            </a>
                        </li>
                    </ol>
                </nav>
            </div>
        </template>

        <div class="py-0 md:py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="flex flex-col md:flex-row h-full">
                        <!-- Vertical Tabs -->
                        <div class="w-full md:w-64 bg-gray-50 dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600">
                            <nav class="space-y-1 p-4">
                                <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                                    Settings Menu
                                </h3>
                                <div v-for="tab in tabs" :key="tab.id">
                                    <button
                                        @click="activeTab = tab.id"
                                        :class="[
                                            activeTab === tab.id
                                                ? 'bg-green-50 text-green-700 dark:bg-gray-600 dark:text-white'
                                                : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600',
                                            'group w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                                        ]"
                                    >
                                        <i :class="['fas', 'fa-' + tab.icon, 'mr-3 flex-shrink-0 h-6 w-6']"></i>
                                        <span class="truncate">{{ tab.name }}</span>
                                        <i 
                                            v-if="hasSubTabs(tab.id)" 
                                            :class="[
                                                'ml-auto',
                                                'fas',
                                                activeTab === tab.id ? 'fa-chevron-down' : 'fa-chevron-right',
                                                'text-xs',
                                                'transition-transform',
                                                'duration-200',
                                                activeTab === tab.id ? 'transform rotate-90' : ''
                                            ]"
                                        ></i>
                                    </button>

                                    <!-- Sub Tabs -->
                                    <div v-if="hasSubTabs(tab.id) && activeTab === tab.id" class="mt-1 ml-8 space-y-1">
                                        <button
                                            v-for="subTab in subTabs[tab.id]"
                                            :key="subTab.id"
                                            @click=""
                                            class="group w-full flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white transition-colors duration-150"
                                        >
                                            <i :class="['fas', 'fa-' + subTab.icon, 'mr-3 flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300']"></i>
                                            <span class="truncate">{{ subTab.name }}</span>
                                        </button>
                                    </div>
                                </div>
                            </nav>
                        </div>

                        <!-- Main Content Area -->
                        <div class="flex-1 p-6">
                            <div v-if="activeTab === 'core'" class="space-y-6">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Core Settings</h2>
                                <p class="text-gray-600 dark:text-gray-300">General application settings and configurations.</p>
                            </div>

                            <div v-else class="space-y-6">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                                    {{ tabs.find(t => t.id === activeTab)?.name }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-300">
                                    Manage {{ tabs.find(t => t.id === activeTab)?.name.toLowerCase() }} settings and configurations.
                                </p>
                                
                                <!-- Placeholder for dynamic content -->
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-center text-gray-500 dark:text-gray-400">
                                        Select an item from the menu to view or edit settings.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
