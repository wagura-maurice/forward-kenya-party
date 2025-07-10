<template>
  <AppLayout :title="department.name">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ department.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Welcome to {{ department.name }}</h3>
            <p class="mt-2 text-gray-600">{{ department.description }}</p>
            
            <div class="mt-8">
              <h4 class="text-md font-medium text-gray-900">Our Services</h4>
              <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2 lg:grid-cols-3">
                <div 
                  v-for="service in services" 
                  :key="service.id"
                  class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
                >
                  <h5 class="font-medium text-gray-900">{{ service.name }}</h5>
                  <p class="mt-1 text-sm text-gray-600">{{ service.description }}</p>
                  <Link 
                    :href="service.url" 
                    class="inline-block mt-2 text-sm font-medium text-blue-600 hover:text-blue-800"
                  >
                    View Service →
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  department: {
    type: Object,
    required: true
  },
  services: {
    type: Array,
    required: true
  }
});
</script>
