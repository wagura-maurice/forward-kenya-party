<!-- resources/js/Components/MemberFormModal.vue -->
<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import InputError from '@/Components/InputError.vue';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    isEditMode: {
        type: Boolean,
        default: false
    },
    memberData: {
        type: Object,
        default: () => ({})
    },
    counties: {
        type: Array,
        default: () => []
    },
    constituencies: {
        type: Array,
        default: () => []
    },
    wards: {
        type: Array,
        default: () => []
    },
    subCounties: {
        type: Array,
        default: () => []
    },
    subLocations: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'save']);

// Initialize form with default values
const defaultFormData = {
    // Step 1: Personal Information
    surname: '',
    other_names: '',
    id_number: '',
    phone: '',
    email: '',
    gender: '',
    date_of_birth: '',
    
    // Step 2: Contact Information
    county_id: null,
    constituency_id: null,
    ward_id: null,
    polling_station: '',
    
    // Initialize other form fields with default values
    occupation: '',
    education_level: '',
    disability_status: false,
    disability_description: '',
    next_of_kin_name: '',
    next_of_kin_phone: '',
    next_of_kin_relationship: ''
};

// Initialize form with member data if in edit mode
const initializeForm = () => {
    if (props.isEditMode && props.memberData) {
        return { ...defaultFormData, ...props.memberData };
    }
    return { ...defaultFormData };
};

// Form state
const form = useForm(initializeForm());

// Location data and loading states
const isLoadingConstituencies = ref(false);
const isLoadingWards = ref(false);

// Filter constituencies based on selected county
const filteredConstituencies = computed(() => {
    if (!form.county_id) return [];
    return constituencies.value.filter(c => c.county_id == form.county_id);
});

// Filter wards based on selected constituency
const filteredWards = computed(() => {
    if (!form.constituency_id) return [];
    return props.wards.filter(w => w.constituency_id == form.constituency_id);
});

// Watch for county changes to reset dependent fields
watch(() => form.county_id, (newCountyId, oldCountyId) => {
    if (newCountyId !== oldCountyId) {
        form.constituency_id = null;
        form.ward_id = null;
    }
});

// Watch for constituency changes to reset dependent field
watch(() => form.constituency_id, (newConstituencyId, oldConstituencyId) => {
    if (newConstituencyId !== oldConstituencyId) {
        form.ward_id = null;
    }
});

// Wizard state
const currentStep = ref(1);
const totalSteps = 3;

// Close modal
const close = () => {
    emit('close');
    resetForm();
};

// Save form data
const save = () => {
    if (props.isEditMode) {
        // Handle update
        form.put(route('members.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit('save', form);
                close();
            },
        });
    } else {
        // Handle create
        form.post(route('members.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit('save', form);
                close();
            },
        });
    }
};

// Navigation functions
const nextStep = () => {
    if (validateStep(currentStep.value)) {
        currentStep.value++;
    }
};

const prevStep = () => {
    currentStep.value--;
};

const goToStep = (step) => {
    currentStep.value = step;
};

// Form validation
const validateStep = (step) => {
    let isValid = true;
    let message = '';
    
    switch (step) {
        case 1:
            if (!form.surname.trim()) {
                message = 'Surname is required';
                isValid = false;
            } else if (!form.other_names.trim()) {
                message = 'Other names are required';
                isValid = false;
            } else if (!form.id_number.trim()) {
                message = 'ID number is required';
                isValid = false;
            } else if (!form.phone.trim()) {
                message = 'Phone number is required';
                isValid = false;
            } else if (form.phone.length < 9) {
                message = 'Please enter a valid phone number';
                isValid = false;
            }
            break;
            
        case 2:
            if (!form.county_id) {
                message = 'County is required';
                isValid = false;
            } else if (!form.constituency_id) {
                message = 'Constituency is required';
                isValid = false;
            } else if (!form.ward_id) {
                message = 'Ward is required';
                isValid = false;
            }
            break;
            
        case 3:
            if (!form.occupation) {
                message = 'Occupation is required';
                isValid = false;
            } else if (!form.education_level) {
                message = 'Education level is required';
                isValid = false;
            }
            break;
    }
    
    if (!isValid && message) {
        alert(message); // Replace with your preferred notification system
    }
    
    return isValid;
};

// Reset form
const resetForm = () => {
    form.reset();
    form.fill(initializeForm());
    currentStep.value = 1;
};

// Close on escape key
const onKeydown = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

// Add event listener for escape key
onMounted(() => {
    document.addEventListener('keydown', onKeydown);
});

// Clean up event listener
onBeforeUnmount(() => {
    document.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Modal content -->
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8 relative">
                <div class="w-full">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ isEditMode ? 'Update Member' : 'Add New Member' }}
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

                    <!-- Progress Steps -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Step {{ currentStep }} of {{ totalSteps }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div 
                                class="bg-green-600 h-2.5 rounded-full" 
                                :style="'width: ' + (currentStep / totalSteps * 100) + '%'"
                            ></div>
                        </div>
                    </div>

                    <!-- Step 1: Personal Information -->
                    <div v-if="currentStep === 1" class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Surname -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Surname <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.surname"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                >
                            </div>
                            
                            <!-- Other Names -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Other Names <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.other_names"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                >
                            </div>
                            
                            <!-- ID Number -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    ID Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.id_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                >
                            </div>
                            
                            <!-- Phone Number -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    v-model="form.phone"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="e.g. 712345678"
                                    required
                                >
                            </div>
                            
                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email
                                </label>
                                <input 
                                    type="email" 
                                    v-model="form.email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                            </div>
                            
                            <!-- Gender -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Gender
                                </label>
                                <select 
                                    v-model="form.gender"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Date of Birth
                                </label>
                                <input 
                                    type="date" 
                                    v-model="form.date_of_birth"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-8 flex justify-between">
                            <div></div> <!-- Empty div for spacing -->
                            <button
                                type="button"
                                @click="nextStep"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                Next
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Contact Information -->
                    <div v-else-if="currentStep === 2" class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- County -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    County <span class="text-red-500">*</span>
                                </label>
                                <VueSelect
                                    v-model="form.county_id"
                                    :options="counties"
                                    label="name"
                                    :reduce="county => county.id"
                                    placeholder="Select County"
                                    class="w-full"
                                    :class="{ 'border-red-500': form.errors.county_id }"
                                    :clearable="false"
                                >
                                    <template #no-options>
                                        <div class="text-sm text-gray-500 py-2 px-3">
                                            No counties available
                                        </div>
                                    </template>
                                </VueSelect>
                                <InputError :message="form.errors.county_id" class="mt-1 text-sm text-red-600" />
                            </div>

                            <!-- Constituency -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Constituency <span class="text-red-500">*</span>
                                </label>
                                <VueSelect
                                    v-model="form.constituency_id"
                                    :options="filteredConstituencies"
                                    label="name"
                                    :reduce="constituency => constituency.id"
                                    placeholder="Select Constituency"
                                    class="w-full"
                                    :class="{ 'border-red-500': form.errors.constituency_id }"
                                    :disabled="!form.county_id"
                                    :clearable="false"
                                >
                                    <template #no-options>
                                        <div class="text-sm text-gray-500 py-2 px-3">
                                            {{ !form.county_id ? 'Please select a county first' : 'No constituencies found' }}
                                        </div>
                                    </template>
                                </VueSelect>
                                <InputError :message="form.errors.constituency_id" class="mt-1 text-sm text-red-600" />
                            </div>

                            <!-- Ward -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ward <span class="text-red-500">*</span>
                                </label>
                                <VueSelect
                                    v-model="form.ward_id"
                                    :options="filteredWards"
                                    label="name"
                                    :reduce="ward => ward.id"
                                    placeholder="Select Ward"
                                    class="w-full"
                                    :class="{ 'border-red-500': form.errors.ward_id }"
                                    :disabled="!form.constituency_id"
                                    :clearable="false"
                                >
                                    <template #no-options>
                                        <div class="text-sm text-gray-500 py-2 px-3">
                                            {{ !form.constituency_id ? 'Please select a constituency first' : 'No wards found' }}
                                        </div>
                                    </template>
                                </VueSelect>
                                <InputError :message="form.errors.ward_id" class="mt-1 text-sm text-red-600" />
                            </div>

                            <!-- Polling Station -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Polling Station
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.polling_station"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter your polling station"
                                >
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-8 flex justify-between">
                            <button
                                type="button"
                                @click="prevStep"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Previous
                            </button>
                            <button
                                type="button"
                                @click="nextStep"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                Next
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Additional Information -->
                    <div v-else-if="currentStep === 3" class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Occupation -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Occupation
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.occupation"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                            </div>

                            <!-- Education Level -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Education Level
                                </label>
                                <select 
                                    v-model="form.education_level"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="">Select Education Level</option>
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="degree">Degree</option>
                                    <option value="masters">Masters</option>
                                    <option value="phd">PhD</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- PWD Status -->
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input 
                                        id="is_pwd" 
                                        type="checkbox" 
                                        v-model="form.is_pwd"
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                    >
                                    <label for="is_pwd" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Person with Disability (PWD)
                                    </label>
                                </div>
                            </div>

                            <!-- PWD Details (Conditional) -->
                            <template v-if="form.is_pwd">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Type of Disability
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.pwd_disability"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        NCPWD Number
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.ncpwd_number"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                            </template>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-8 flex justify-between">
                            <button
                                type="button"
                                @click="prevStep"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Previous
                            </button>
                            <button
                                type="button"
                                @click="save"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                Save Member
                                <i class="fas fa-save ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>