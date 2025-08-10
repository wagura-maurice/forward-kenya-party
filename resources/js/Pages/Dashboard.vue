<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

const { title, data } = defineProps({
    title: {
        type: String,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
});
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
                    </ol>
                </nav>
            </div>
        </template>

        <div class="py-0 md:py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome 
                        :activities="data.activities"
                        :stats="data.stats"
                        :featured-services="data.featuredServices"
                        :featured-departments="data.featuredDepartments"
                        :featured-projects="data.featuredProjects"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
