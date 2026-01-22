<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

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

// Location data handling
const constituencies = ref([]);
const wards = ref([]);
const pollingCenters = ref([]);
const isLoadingConstituencies = ref(false);
const isLoadingWards = ref(false);
const isLoadingPollingCenters = ref(false);

// Get location data from the page props
const page = usePage();
// console.log('Page props:', page.props.formData.locations.counties);

// Use the form data from props
const counties = computed(() => page.props.formData.locations.counties || []);

// Initialize form
const form = useForm({
    format: 'excel',
    include_headers: true,
    fields: ['surname', 'other_names', 'id_number', 'telephone', 'email', 'gender', 'date_of_birth', 'county', 'constituency', 'ward', 'polling_center', 'occupation', 'education_level', 'disability_status', 'ncpwd_number', 'created_at'],
    filters: {
        county_id: null,
        constituency_id: null,
        ward_id: null,
        polling_center_id: null,
        date_from: '',
        date_to: ''
    }
});

// Watch for county changes to filter constituencies
watch(
    () => form.filters.county_id,
    (newCountyId) => {
        if (newCountyId) {
            const filtered = page.props.formData.locations?.constituencies?.filter(
                (c) => c.county_id == newCountyId
            ) || [];
            constituencies.value = filtered;
        } else {
            constituencies.value = [];
        }
        // Reset dependent fields
        form.filters.constituency_id = null;
        form.filters.ward_id = null;
        form.filters.polling_center_id = null;
        wards.value = [];
        pollingCenters.value = [];
    },
    { immediate: true }
);

// Watch for constituency changes to filter wards
watch(
    () => form.filters.constituency_id,
    (newConstituencyId) => {
        if (newConstituencyId) {
            const filtered = page.props.formData.locations?.wards?.filter(
                (w) => w.constituency_id == newConstituencyId
            ) || [];
            wards.value = filtered;
        } else {
            wards.value = [];
        }
        // Reset dependent field
        form.filters.ward_id = null;
        form.filters.polling_center_id = null;
        pollingCenters.value = [];
    },
    { immediate: true }
);

// Watch for ward changes to filter polling centers
watch(
    () => form.filters.ward_id,
    (newWardId) => {
        if (newWardId) {
            const filtered = page.props.formData.locations?.polling_centers?.filter(
                (ps) => ps.ward_id == newWardId
            ) || [];
            pollingCenters.value = filtered;
        } else {
            pollingCenters.value = [];
            form.filters.polling_center_id = null;
        }
    },
    { immediate: true }
);

const availableFields = [
    { value: 'surname', label: 'Surname' },
    { value: 'other_names', label: 'Other Names' },
    { value: 'id_number', label: 'ID Number' },
    { value: 'telephone', label: 'Telephone' },
    { value: 'email', label: 'Email' },
    { value: 'gender', label: 'Gender' },
    { value: 'date_of_birth', label: 'Date of Birth' },
    { value: 'county', label: 'County' },
    { value: 'constituency', label: 'Constituency' },
    { value: 'ward', label: 'Ward' },
    { value: 'polling_center', label: 'Polling Center' },
    { value: 'occupation', label: 'Occupation' },
    { value: 'education_level', label: 'Education Level' },
    { value: 'disability_status', label: 'Disability Status' },
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
    window.location.href = route('export.membership') + '?' + queryParams.toString();
    
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
                            <div class="md:col-span-2 space-y-4">
                                <!-- Export Format -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Export Format
                                    </label>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <input
                                                id="format-excel"
                                                v-model="form.format"
                                                type="radio"
                                                value="excel"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                                            />
                                            <label for="format-excel" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <span class="block">Excel</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">.xlsx</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <input
                                                id="format-csv"
                                                v-model="form.format"
                                                type="radio"
                                                value="csv"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                                            />
                                            <label for="format-csv" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <span class="block">CSV</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">.csv</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <input
                                                id="format-pdf"
                                                v-model="form.format"
                                                type="radio"
                                                value="pdf"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                                            />
                                            <label for="format-pdf" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <span class="block">PDF</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">.pdf</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Include Headers -->
                                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <input
                                        id="include-headers"
                                        v-model="form.include_headers"
                                        type="checkbox"
                                        checked
                                        disabled
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                    />
                                    <label for="include-headers" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <span class="block">Include Headers</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Column headers will be included in the export</span>
                                    </label>
                                </div>
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
                                <div v-if="isFieldsExpanded" class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <div v-for="field in availableFields" :key="field.value" class="relative flex items-start">
                                        <div class="flex items-center h-5">
                                            <input
                                                :id="`field-${field.value}`"
                                                v-model="form.fields"
                                                :value="field.value"
                                                type="checkbox"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded"
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label :for="`field-${field.value}`" class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ field.label }}
                                            </label>
                                        </div>
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
                                    <!-- County -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            County
                                        </label>
                                        <select
                                            v-model="form.filters.county_id"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md capitalize"
                                            :class="{ 'opacity-50': isLoadingConstituencies }"
                                            :disabled="isLoadingConstituencies"
                                        >
                                            <option :value="null">Select County</option>
                                            <option v-for="county in counties" :key="county.id" :value="county.id" class="location-option">
                                                {{ county.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Constituency -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Constituency
                                        </label>
                                        <select
                                            v-model="form.filters.constituency_id"
                                            :disabled="!form.filters.county_id || isLoadingWards"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md disabled:opacity-50 capitalize"
                                        >
                                            <option :value="null">Select Constituency</option>
                                            <option v-for="constituency in constituencies" :key="constituency.id" :value="constituency.id" class="location-option">
                                                {{ constituency.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Ward -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Ward
                                        </label>
                                        <select
                                            v-model="form.filters.ward_id"
                                            :disabled="!form.filters.constituency_id || isLoadingPollingCenters"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md disabled:opacity-50 capitalize"
                                        >
                                            <option :value="null">Select Ward</option>
                                            <option v-for="ward in wards" :key="ward.id" :value="ward.id" class="location-option">
                                                {{ ward.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Polling Center -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Polling Center
                                        </label>
                                        <select
                                            v-model="form.filters.polling_center_id"
                                            :disabled="!form.filters.ward_id || pollingCenters.length === 0"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md disabled:opacity-50 capitalize"
                                        >
                                            <option :value="null">Select Polling Center</option>
                                            <option v-for="center in pollingCenters" :key="center.id" :value="center.id" class="location-option">
                                                {{ center.name }}
                                            </option>
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

<style scoped>
/* Location dropdown options */
select option.location-option {
    text-transform: capitalize;
}

/* Ensure the select element itself doesn't inherit text-transform */
select {
    text-transform: none;
}
</style>
