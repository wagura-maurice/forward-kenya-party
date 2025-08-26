<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    show: {
        type: Boolean,
        default: false
    },
    onClose: {
        type: Function,
        default: () => {}
    },
    onExport: {
        type: Function,
        default: () => {}
    }
});

const emit = defineEmits(['close', 'export']);

// Track expanded/collapsed state of sections
const isFieldsExpanded = ref(true);
const isLocationFiltersExpanded = ref(true);
const isDateFiltersExpanded = ref(true);

const form = useForm({
    format: 'excel',
    include_headers: true,
    fields: ['surname', 'other_names', 'id_number', 'phone', 'email', 'county', 'constituency', 'ward', 'polling_station'],
    filters: {
        county_id: null,
        constituency_id: null,
        ward_id: null,
        polling_station: null,
        date_from: '',
        date_to: ''
    }
});

const availableFields = [
    { value: 'surname', label: 'Surname' },
    { value: 'other_names', label: 'Other Names' },
    { value: 'id_number', label: 'ID Number' },
    { value: 'phone', label: 'Phone' },
    { value: 'email', label: 'Email' },
    { value: 'gender', label: 'Gender' },
    { value: 'date_of_birth', label: 'Date of Birth' },
    { value: 'county', label: 'County' },
    { value: 'constituency', label: 'Constituency' },
    { value: 'ward', label: 'Ward' },
    { value: 'polling_station', label: 'Polling Station' },
    { value: 'occupation', label: 'Occupation' },
    { value: 'education_level', label: 'Education Level' },
    { value: 'is_pwd', label: 'Is PWD' },
    { value: 'pwd_disability', label: 'PWD Disability' },
    { value: 'ncpwd_number', label: 'NCPWD Number' },
    { value: 'created_at', label: 'Date Registered' }
];

const toggleField = (field) => {
    const index = form.fields.indexOf(field);
    if (index === -1) {
        form.fields.push(field);
    } else {
        form.fields.splice(index, 1);
    }
};

const close = () => {
    form.reset();
    emit('close');
};

const exportData = () => {
    const queryParams = new URLSearchParams();
    
    // Add format
    queryParams.append('format', form.format);
    
    // Add headers flag
    if (form.include_headers) {
        queryParams.append('include_headers', '1');
    }
    
    // Add selected fields
    form.fields.forEach(field => {
        queryParams.append('fields[]', field);
    });
    
    // Add filters
    Object.entries(form.filters).forEach(([key, value]) => {
        if (value) {
            queryParams.append(`filters[${key}]`, value);
        }
    });
    
    // Trigger download
    window.location.href = route('members.export') + '?' + queryParams.toString();
    
    // Emit event and close
    emit('export');
    close();
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-8 relative">
                <div class="w-full">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Export Members
                        </h3>
                        <button 
                            type="button" 
                            @click="close"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal content -->
                    <div class="mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Export Format -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Export Format
                                </label>
                                <select
                                    v-model="form.format"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                >
                                    <option value="excel">Excel (.xlsx)</option>
                                    <option value="csv">CSV (.csv)</option>
                                    <option value="pdf">PDF (.pdf)</option>
                                </select>
                            </div>

                            <!-- Include Headers -->
                            <div class="flex items-center">
                                <input
                                    id="include-headers"
                                    v-model="form.include_headers"
                                    type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                />
                                <label for="include-headers" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Include Headers
                                </label>
                            </div>

                            <!-- Fields to Export -->
                            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <button 
                                    type="button" 
                                    @click="isFieldsExpanded = !isFieldsExpanded"
                                    class="w-full flex justify-between items-center text-left focus:outline-none"
                                >
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Select Fields to Export
                                    </label>
                                    <svg 
                                        :class="{
                                            'transform rotate-180': isFieldsExpanded,
                                            'transform rotate-0': !isFieldsExpanded
                                        }" 
                                        class="h-5 w-5 text-gray-500 transition-transform duration-200" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        viewBox="0 0 20 20" 
                                        fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div v-if="isFieldsExpanded" class="mt-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                    <div v-for="field in availableFields" :key="field.value" class="flex items-center">
                                        <input
                                            :id="`field-${field.value}`"
                                            v-model="form.fields"
                                            :value="field.value"
                                            type="checkbox"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                        />
                                        <label :for="`field-${field.value}`" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ field.label }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Filters -->
                            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <button 
                                    type="button" 
                                    @click="isLocationFiltersExpanded = !isLocationFiltersExpanded"
                                    class="w-full flex justify-between items-center text-left focus:outline-none"
                                >
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Location Filters (Optional)
                                    </h4>
                                    <svg 
                                        :class="{
                                            'transform rotate-180': isLocationFiltersExpanded,
                                            'transform rotate-0': !isLocationFiltersExpanded
                                        }" 
                                        class="h-5 w-5 text-gray-500 transition-transform duration-200" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        viewBox="0 0 20 20" 
                                        fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div v-if="isLocationFiltersExpanded" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            County
                                        </label>
                                        <select
                                            v-model="form.filters.county_id"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        >
                                            <option :value="null">All Counties</option>
                                            <!-- Add county options here if needed -->
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Constituency
                                        </label>
                                        <select
                                            v-model="form.filters.constituency_id"
                                            :disabled="!form.filters.county_id"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md disabled:opacity-50"
                                        >
                                            <option :value="null">All Constituencies</option>
                                            <!-- Add constituency options here if needed -->
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Ward
                                        </label>
                                        <select
                                            v-model="form.filters.ward_id"
                                            :disabled="!form.filters.constituency_id"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md disabled:opacity-50"
                                        >
                                            <option :value="null">All Wards</option>
                                            <!-- Add ward options here if needed -->
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Polling Station
                                        </label>
                                        <select
                                            v-model="form.filters.polling_station"
                                            :disabled="!form.filters.ward_id"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md disabled:opacity-50"
                                        >
                                            <option :value="null">All Polling Stations</option>
                                            <!-- Add polling station options here if needed -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Date Filters -->
                            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <button 
                                    type="button" 
                                    @click="isDateFiltersExpanded = !isDateFiltersExpanded"
                                    class="w-full flex justify-between items-center text-left focus:outline-none"
                                >
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Date Filters (Optional)
                                    </h4>
                                    <svg 
                                        :class="{
                                            'transform rotate-180': isDateFiltersExpanded,
                                            'transform rotate-0': !isDateFiltersExpanded
                                        }" 
                                        class="h-5 w-5 text-gray-500 transition-transform duration-200" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        viewBox="0 0 20 20" 
                                        fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div v-if="isDateFiltersExpanded" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            From Date
                                        </label>
                                        <input
                                            v-model="form.filters.date_from"
                                            type="date"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            To Date
                                        </label>
                                        <input
                                            v-model="form.filters.date_to"
                                            type="date"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="close"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            @click="exportData"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
