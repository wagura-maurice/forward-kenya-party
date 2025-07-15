<template>
    <AppLayout :title="service.name">
        <template #header>
            <div class="flex items-center">
                <Link
                    :href="
                        route('platform.department.show', {
                            department: department.slug,
                        })
                    "
                    class="mr-2 text-gray-500 hover:text-gray-700"
                >
                    {{ department.name }}
                </Link>
                <span class="mx-2 text-gray-400">/</span>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ service.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ service.name }}
                        </h3>
                        <p class="mt-2 text-gray-600">
                            {{ service.description }}
                        </p>

                        <div class="mt-8">
                            <!-- Add service-specific content here -->
                            <div class="p-4 mt-4 bg-gray-50 rounded-lg">
                                <h4 class="font-medium text-gray-900">
                                    Service Details
                                </h4>
                                <p class="mt-2 text-sm text-gray-600">
                                    This is a placeholder for the service
                                    details. You can add any service-specific
                                    content here.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    department: {
        type: Object,
        required: true,
    },
    service: {
        type: Object,
        required: true,
    },
});
</script>
