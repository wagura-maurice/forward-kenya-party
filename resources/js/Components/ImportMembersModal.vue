<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'import']);

const form = useForm({
    file: null,
    type: 'excel',
    has_headers: true,
    update_existing: false,
    match_columns: {}
});

const fileInput = ref(null);
const showColumnMapping = ref(false);
const fileHeaders = ref([]);
const isDragging = ref(false);

const handleDragOver = (e) => {
    e.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const handleDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.name.endsWith('.xlsx')) {
            handleFileChange({ target: { files: [file] } });
        } else {
            alert('Please upload a valid .xlsx file');
        }
    }
};

// Template columns that should be in the Excel file
const templateColumns = [
    'surname',
    'other_names',
    'id_number',
    'phone',
    'email',
    'gender',
    'date_of_birth',
    'county',
    'constituency',
    'ward',
    'polling_station',
    'occupation',
    'education_level',
    'disability_status',
    'disability_description',
    'ncpwd_number'
];

// Download the template file
const downloadTemplate = () => {
    // Create a simple Excel file with headers
    const headers = templateColumns.join('\t') + '\n';
    const blob = new Blob([headers], { type: 'application/vnd.ms-excel' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'members_import_template.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};

// Handle file selection
const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    if (!file.name.endsWith('.xlsx')) {
        alert('Please upload a valid .xlsx file');
        return;
    }

    form.file = file;
    readFileHeaders(file);
};

// Read file headers for column mapping
const readFileHeaders = (file) => {
    if (file.name.endsWith('.csv')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const text = e.target.result;
            const firstLine = text.split('\n')[0];
            fileHeaders.value = firstLine.split(',').map(h => h.trim().toLowerCase());
            initializeColumnMapping();
        };
        reader.readAsText(file);
    } else if (file.name.match(/\.xlsx?$/i)) {
        // For Excel files, we'll just show the column mapping UI
        // In a real app, you might want to use a library like xlsx to read the file
        showColumnMapping.value = true;
    }
};

// Initialize column mapping with best guesses
const initializeColumnMapping = () => {
    templateColumns.forEach(col => {
        // Try to find a matching header
        const match = fileHeaders.value.find(h => 
            h.toLowerCase().includes(col) || 
            col.toLowerCase().includes(h)
        );
        
        if (match) {
            form.match_columns[col] = match;
        } else {
            form.match_columns[col] = '';
        }
    });
};

const close = () => {
    form.reset();
    emit('close');
};

const importFile = () => {
    if (!form.file) {
        alert('Please select a file to import');
        return;
    }

    const formData = new FormData();
    formData.append('file', form.file);
    formData.append('type', form.type);
    formData.append('has_headers', form.has_headers);
    formData.append('update_existing', form.update_existing);

    form.post(route('members.import'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('import');
            close();
        },
        forceFormData: true
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8 relative">
                <div class="w-full">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Import Members
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
                        <div class="space-y-6">
                            <!-- Template Download -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                            Download Template
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                            <p>
                                                Download our Excel template to ensure your file has the correct format.
                                            </p>
                                        </div>
                                        <div class="mt-4">
                                            <button
                                                type="button"
                                                @click="downloadTemplate"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                            >
                                                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                Download Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- File input with dropzone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Upload Excel File
                                </label>
                                <div 
                                    @dragover.prevent="handleDragOver"
                                    @dragleave="handleDragLeave"
                                    @drop="handleDrop"
                                    @click="fileInput.click()"
                                    :class="{
                                        'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': isDragging,
                                        'border-gray-300 dark:border-gray-600': !isDragging
                                    }"
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md transition-colors duration-150 cursor-pointer"
                                >
                                    <div class="space-y-1 text-center">
                                        <svg
                                            class="mx-auto h-12 w-12 text-gray-400"
                                            stroke="currentColor"
                                            fill="none"
                                            viewBox="0 0 48 48"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-300">
                                            <span class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none">
                                                <span>Upload a file</span>
                                                <input
                                                    type="file"
                                                    ref="fileInput"
                                                    @change="handleFileChange"
                                                    accept=".xlsx"
                                                    class="sr-only"
                                                />
                                            </span>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Excel (.xlsx) up to 5MB
                                        </p>
                                        <p v-if="form.file" class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-2">
                                            {{ form.file.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>


                            <!-- Column Mapping (shown when file is selected) -->
                            <div v-if="showColumnMapping && fileHeaders.length > 0" class="space-y-4">
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Map Columns
                                    </h4>
                                    <div class="space-y-3">
                                        <div v-for="col in templateColumns" :key="col" class="grid grid-cols-3 gap-4 items-center">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ col.replace(/_/g, ' ') }}
                                            </label>
                                            <div class="col-span-2">
                                                <select 
                                                    v-model="form.match_columns[col]"
                                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                                >
                                                    <option value="">-- Select Column --</option>
                                                    <option v-for="header in fileHeaders" :key="header" :value="header">
                                                        {{ header }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Import Options -->
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input
                                        id="update-existing"
                                        v-model="form.update_existing"
                                        type="checkbox"
                                        checked
                                        disabled
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded bg-gray-100 dark:bg-gray-700 cursor-not-allowed"
                                    />
                                    <label for="update-existing" class="ml-2 block text-sm text-gray-700 dark:text-gray-400">
                                        Update existing members (match by ID Number)
                                    </label>
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
                            @click="importFile"
                            :disabled="!form.file"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Import
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
