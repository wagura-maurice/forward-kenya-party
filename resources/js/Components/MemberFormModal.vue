<!-- resources/js/Components/MemberFormModal.vue -->
<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import InputError from '@/Components/InputError.vue';
import axios from 'axios';
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
    },
    ethnicities: {
        type: Array,
        default: () => []
    },
    religions: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'save']);

// Generate membership number for new members
const generateMembershipNumber = () => {
    // Generate a unique membership number matching server-side format: FKP-XXXXXX
    const prefix = 'FKP';
    const random = Math.floor(Math.random() * 1000000); // 0 to 999999
    const paddedNumber = String(random).padStart(6, '0');
    return `${prefix}-${paddedNumber}`;
};

// Initialize form with default values
const defaultFormData = {
    // Step 1: Party Registration Check
    understood_instructions: false,
    
    // Step 2: Account and Personal Information
    surname: '',
    other_name: '', // Changed from other_names to match Register.vue
    telephone: '', // Changed from phone to match Register.vue
    identification_type: 'national_identification_number', // Added - matches Register.vue
    identification_number: '', // Changed from id_number to match Register.vue
    party_membership_number: props.memberData?.party_membership_number || generateMembershipNumber(), // Pre-populate from member data or generate
    date_of_birth: '',
    special_interest_groups: [], // Added - matches Register.vue
    gender: '',
    ethnicity_id: '', // Added - matches Register.vue
    religion_id: '', // Added - matches Register.vue
    disability_status: false, // 'false', 'true'
    ncpwd_number: '', // Added - matches Register.vue

    // Step 3: Location and Enlisting Information
    county_id: null,
    constituency_id: null,
    ward_id: null,
    enlisting_date: new Date().toISOString().split("T")[0], // Added - matches Register.vue
    polling_station: '',

    // Step 4: Confirmation
    terms: false, // Added - matches Register.vue
    
    // Additional fields not in Register.vue but needed for member management
    email: '',
    occupation: '',
    education_level: '',
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
    return props.constituencies.filter(c => c.county_id == form.county_id);
});

// Filter wards based on selected constituency
const filteredWards = computed(() => {
    if (!form.constituency_id) return [];
    return props.wards.filter(w => w.constituency_id == form.constituency_id);
});

// Watch for disability status changes to handle NCPWD number field
watch(
    () => form.disability_status,
    (newStatus) => {
        if (newStatus === false) {
            // Clear NCPWD number when disability status is set to 'no'
            form.ncpwd_number = "";
        }
    }
);

// Accordion state
const activeAccordion = ref('');

// Toggle accordion
const toggleAccordion = (accordion) => {
    activeAccordion.value = activeAccordion.value === accordion ? '' : accordion;
};

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
const totalSteps = 4;

// Step descriptions matching Register.vue
const stepDescription = computed(() => {
    switch (currentStep.value) {
        case 1:
            return "Party Registration Check";
        case 2:
            return "Account and Personal Information";
        case 3:
            return "Location and Enlisting Information";
        case 4:
            return "Confirmation";
        default:
            return "";
    }
});

// Check if Step 2 required fields are filled
const isStep2Valid = computed(() => {
    return !!form.surname?.trim() &&
           !!form.other_name?.trim() &&
           !!form.telephone?.trim() &&
           !!form.identification_type &&
           !!form.identification_number?.trim() &&
           !!form.date_of_birth &&
           !!form.gender &&
           !!form.ethnicity_id &&
           !!form.religion_id &&
           (form.disability_status === false || (form.disability_status === true && !!form.ncpwd_number?.trim()));
});

// Check if Step 3 required fields are filled
const isStep3Valid = computed(() => {
    return !!form.county_id &&
           !!form.constituency_id &&
           !!form.ward_id;
});

// Get selected location names for confirmation display
const selectedCountyName = computed(() => {
    const county = props.counties.find(c => c.id === form.county_id);
    return county?.name || 'Not specified';
});

const selectedConstituencyName = computed(() => {
    const constituency = props.constituencies.find(c => c.id === form.constituency_id);
    return constituency?.name || 'Not specified';
});

const selectedWardName = computed(() => {
    const ward = props.wards.find(w => w.id === form.ward_id);
    return ward?.name || 'Not specified';
});

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
        // Handle create - using axios for JSON response
        axios.post(route('members.store'), form.data())
            .then(response => {
                console.log('Response received:', response.data); // Debug
                if (response.data.success) {
                    console.log('Processing success response'); // Debug
                    try {
                        console.log('About to emit save'); // Debug
                        emit('save', form);
                        console.log('Save emitted successfully'); // Debug
                        
                        console.log('About to close modal'); // Debug
                        close();
                        console.log('Modal closed successfully'); // Debug
                        
                        console.log('About to show toast'); // Debug
                        // Show success toast like Register.vue
                        if (typeof window.Toast !== 'undefined') {
                            window.Toast.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.data.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', window.Toast.stopTimer);
                                    toast.addEventListener('mouseleave', window.Toast.resumeTimer);
                                },
                            });
                            console.log('Toast fired successfully'); // Debug
                        } else {
                            // Fallback to browser alert
                            alert(response.data.message || 'Member created successfully!');
                        }
                        
                        console.log('About to set page refresh'); // Debug
                        // Refresh page after showing toast
                        setTimeout(() => {
                            console.log('Refreshing page now'); // Debug
                            window.location.reload();
                        }, 3500);
                    } catch (error) {
                        console.error('Error in success callback:', error); // Debug
                        // Show error toast
                        if (typeof window.Toast !== 'undefined') {
                            window.Toast.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'An error occurred while saving member.',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            });
                        } else {
                            alert(error.message || 'An error occurred while saving member.');
                        }
                    }
                } else {
                    console.log('Response success is false'); // Debug
                    // Handle case where success is false but no HTTP error
                    const message = response.data.message || 'An error occurred while saving member.';
                    if (typeof window.Toast !== 'undefined') {
                        window.Toast.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', window.Toast.stopTimer);
                                toast.addEventListener('mouseleave', window.Toast.resumeTimer);
                            },
                        });
                    } else {
                        alert(message);
                    }
                }
            })
            .catch(error => {
                if (error.response?.status === 422) {
                    // Validation errors
                    form.errors = error.response.data.errors;
                } else {
                    // Other errors
                    const message = error.response?.data?.message || 'An error occurred while saving member.';
                    if (typeof window.Toast !== 'undefined') {
                        window.Toast.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', window.Toast.stopTimer);
                                toast.addEventListener('mouseleave', window.Toast.resumeTimer);
                            },
                        });
                    } else {
                        // Fallback to browser alert
                        alert(message);
                    }
                }
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

// Form validation matching Register.vue exactly
const validateStep = (step) => {
    let isValid = true;
    let message = '';
    
    if (step === 1) {
        // Step 1: Party Registration Check - Only validate acknowledgment
        if (!form.understood_instructions) {
            return { 
                isValid: false, 
                message: "You must acknowledge that you understand the registration process" 
            };
        }
    }
    
    if (step === 2) {
        // Step 2: Account and Personal Information (exact match to Register.vue)
        const nameRegex = /^([a-zA-Z]+)(\s[a-zA-Z]+)?(\s[a-zA-Z]+)?$/;
        if (!form.surname?.trim().match(nameRegex))
            return { isValid: false, message: "Surname must be a valid human name" };
        if (form.other_name?.trim().split(" ").length > 2)
            return { isValid: false, message: "Other name must be a valid human name" };
        if (!form.telephone?.trim())
            return {
                isValid: false,
                message: "Member's mobile no. is required",
            };
        if (!form.identification_type)
            return {
                isValid: false,
                message: "Identification type is required",
            };
        if (!form.identification_number)
            return {
                isValid: false,
                message: "National ID/Passport Number is required",
            };
            
        // Validate identification number length (exactly 8 characters)
        if (form.identification_number.length !== 8) {
            return {
                isValid: false,
                message: "Identification number must be exactly 8 characters long"
            };
        }
        if (!form.identification_number?.trim())
            return {
                isValid: false,
                message: `${
                    form.identification_type === "national_identification_number"
                        ? "National Identification Number"
                        : "Passport Number"
                } is required`,
            };
        if (!form.date_of_birth)
            return { isValid: false, message: "Date of birth is required" };
        if (!form.gender)
            return { isValid: false, message: "Sex is required" };
        if (!form.ethnicity_id)
            return { isValid: false, message: "Ethnicity is required" };
        if (!form.religion_id)
            return { isValid: false, message: "Religion is required" };
        if (form.disability_status === true && !form.ncpwd_number?.trim())
            return {
                isValid: false,
                message: "NCPWD number is required when a disability is selected",
            };
    }

    if (step === 3) {
        // Step 3: Location and Enlisting Information
        if (!form.county_id)
            return {
                isValid: false,
                message: "County of Member registration is required",
            };
        if (!form.constituency_id)
            return {
                isValid: false,
                message: "Constituency of Member registration is required",
            };
        if (!form.ward_id)
            return {
                isValid: false,
                message: "Ward of Member registration is required",
            };
    }
    
    if (!isValid && message) {
        alert(message); // Replace with your preferred notification system
    }
    
    return isValid;
};

// Reset form
const resetForm = () => {
    form.reset();
    Object.assign(form, initializeForm());
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

                    <!-- Step 1: Party Registration Check -->
                    <div v-if="currentStep === 1" class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Step 1: Party Registration Check</h3>
                        
                        <!-- Registration Instructions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">Registration Instructions</h4>
                            <p class="text-blue-800 text-sm mb-3">
                                Please read and understand the following registration process before proceeding:
                            </p>
                            <ul class="list-disc list-inside text-blue-800 text-sm space-y-1">
                                <li>You will need to provide personal identification details</li>
                                <li>Your information will be verified for accuracy</li>
                                <li>You must be 18 years or older to register</li>
                                <li>All required fields must be completed</li>
                                <li>Make sure all information provided is accurate and truthful</li>
                                <li>Have your identification documents ready</li>
                            </ul>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-900 mb-2">Important Information</h4>
                            <div class="text-yellow-800 text-sm space-y-2">
                                <p><strong>Before you begin:</strong></p>
                                <ul class="list-disc list-inside ml-4 space-y-1">
                                    <li>Ensure you have a valid email address</li>
                                    <li>Prepare your National ID or Passport details</li>
                                    <li>Know your county, constituency, and ward information</li>
                                    <li>Have your phone number readily available</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Acknowledgment Checkbox -->
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    v-model="form.understood_instructions"
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    I understand the registration process and agree to proceed
                                </span>
                            </label>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-8 flex justify-between">
                            <div></div> <!-- Empty div for spacing -->
                            <button
                                type="button"
                                @click="nextStep"
                                :disabled="!form.understood_instructions"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ Math.round((currentStep / totalSteps) * 100) }}% Complete
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div 
                                    class="bg-green-600 h-2.5 rounded-full transition-all duration-300 ease-in-out" 
                                    :style="'width: ' + (currentStep / totalSteps * 100) + '%'"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Account and Personal Information -->
                    <div v-else-if="currentStep === 2" class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Step 2: Account and Personal Information</h3>
                        
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
                                    placeholder="Enter surname"
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
                                    v-model="form.other_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter other names"
                                    required
                                >
                            </div>
                            
                            <!-- Phone Number and Email Address -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    v-model="form.telephone"
                                    @input="form.telephone = form.telephone.replace(/\D/g, '').slice(0, 10)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="e.g. 0712345678"
                                    pattern="\d{10}"
                                    maxlength="10"
                                    required
                                >
                                <p class="text-xs text-gray-500">Enter 10 digits starting with 07 (e.g. 0712345678)</p>
                            </div>
                            
                            <!-- Email Address -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email Address
                                </label>
                                <input 
                                    type="email" 
                                    v-model="form.email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="e.g. example@email.com"
                                >
                            </div>
                            
                            <!-- National Identification Number -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    National Identification Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.identification_number"
                                    @input="form.identification_number = form.identification_number.replace(/\D/g, '').slice(0, 8)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    maxlength="8"
                                    pattern="\d{8}"
                                    placeholder="8 digits only (e.g. 12345678)"
                                    required
                                >
                                <p class="text-xs text-gray-500">Enter exactly 8 digits</p>
                            </div>
                            
                                                        
                            <!-- Gender -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Gender <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.gender"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="">Select Gender</option>
                                    <option value="XY">Male</option>
                                    <option value="XX">Female</option>
                                </select>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Date of Birth <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    v-model="form.date_of_birth"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :max="
                                        new Date(
                                            Date.now() -
                                                1000 *
                                                    60 *
                                                    60 *
                                                    24 *
                                                    365.25 *
                                                    18
                                        )
                                            .toISOString()
                                            .split('T')[0]
                                    "
                                >
                            </div>
                            
                            <!-- Ethnicity -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ethnicity <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.ethnicity_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="">Select Ethnicity</option>
                                    <option 
                                        v-for="ethnicity in ethnicities" 
                                        :key="ethnicity.id" 
                                        :value="ethnicity.id"
                                    >
                                        {{ ethnicity.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Religion -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Religion <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.religion_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="">Select Religion</option>
                                    <option 
                                        v-for="religion in religions" 
                                        :key="religion.id" 
                                        :value="religion.id"
                                    >
                                        {{ religion.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Special Interest Groups -->
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Special Interest Group (optional)
                                    </label>
                                </div>
                                <VueSelect
                                    id="special_interest_groups"
                                    v-model="form.special_interest_groups"
                                    :options="[
                                        { value: 'Women', label: 'Women' },
                                        { value: 'Youth', label: 'Youth' },
                                        { value: 'Persons With Disabilty', label: 'Persons with Disability' },
                                        { value: 'Persons Marginalized', label: 'Persons Marginalized' },
                                        { value: 'Persons Minority', label: 'Persons Minority' }
                                    ]"
                                    label="label"
                                    multiple
                                    :reduce="option => option.value"
                                    placeholder="Select special interest groups (optional)"
                                    class="w-full"
                                    :class="{
                                        'border-red-300': form.errors.special_interest_groups,
                                    }"
                                    :clearable="true"
                                    :close-on-select="false"
                                    :taggable="false"
                                    :selectable="() => form.special_interest_groups.length < 5"
                                >
                                    <template #no-options>
                                        <div class="text-sm text-gray-500 p-2">
                                            No special interest groups found
                                        </div>
                                    </template>
                                    <template #option="{ label }">
                                        <div>
                                            <span>{{ label }}</span>
                                        </div>
                                    </template>
                                    <template #selected-option="{ label }">
                                        <span>{{ label }}</span>
                                    </template>
                                </VueSelect>
                                <div v-if="form.errors.special_interest_groups" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.special_interest_groups }}
                                </div>
                            </div>
                            
                            <!-- Disability Status -->
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Are you a PWD?
                                    </label>
                                    <i
                                        class="fas fa-info-circle text-gray-400 ml-1"
                                        title="Person with Disability"
                                    ></i>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.disability_status"
                                            :value="false"
                                            class="text-green-600 border-gray-300 focus:ring-green-500"
                                            required
                                        />
                                        <span
                                            class="ml-1 text-sm text-gray-700 dark:text-gray-300"
                                            >No</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.disability_status"
                                            :value="true"
                                            class="text-green-600 border-gray-300 focus:ring-green-500"
                                        />
                                        <span
                                            class="ml-1 text-sm text-gray-700 dark:text-gray-300"
                                            >Yes</span>
                                    </label>
                                </div>
                                <div v-if="form.errors.disability_status" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.disability_status }}
                                </div>
                            </div>

                            <!-- NCPWD Number (Conditional) -->
                            <div
                                v-if="form.disability_status"
                                class="space-y-2"
                            >
                                <div class="flex items-center">
                                    <label
                                        for="ncpwd_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        NCPWD Number
                                        <i
                                            class="fas fa-info-circle text-gray-400 ml-1"
                                            title="National Council for Persons with Disabilities"
                                        ></i>
                                    </label>
                                </div>
                                <input
                                    id="ncpwd_number"
                                    v-model="form.ncpwd_number"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :class="[
                                        'block w-full rounded-md shadow-sm sm:text-sm py-2 px-3 border transition duration-150 ease-in-out',
                                        form.errors.ncpwd_number
                                            ? 'border-red-500'
                                            : 'border-gray-300 focus:border-green-500 focus:ring-green-500',
                                    ]"
                                    required
                                    placeholder="Enter your NCPWD number"
                                />
                                <div v-if="form.errors.ncpwd_number" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.ncpwd_number }}
                                </div>
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
                                :disabled="!isStep2Valid"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ Math.round((currentStep / totalSteps) * 100) }}% Complete
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div 
                                    class="bg-green-600 h-2.5 rounded-full transition-all duration-300 ease-in-out" 
                                    :style="'width: ' + (currentStep / totalSteps * 100) + '%'"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Location and Enlisting Information -->
                    <div v-else-if="currentStep === 3" class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Step 3: Location and Enlisting Information</h3>
                        
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
                                    class="w-full capitalize-select"
                                    :class="{ 'border-red-500': form.errors.county_id }"
                                    :clearable="false"
                                >
                                    <template #option="{ name }">
                                        <span class="capitalize">{{ name }}</span>
                                    </template>
                                    <template #selected-option="{ name }">
                                        <span class="capitalize">{{ name }}</span>
                                    </template>
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
                                    class="w-full capitalize-select"
                                    :class="{ 'border-red-500': form.errors.constituency_id }"
                                    :disabled="!form.county_id"
                                    :clearable="false"
                                >
                                    <template #option="{ name }">
                                        <span class="capitalize">{{ name }}</span>
                                    </template>
                                    <template #selected-option="{ name }">
                                        <span class="capitalize">{{ name }}</span>
                                    </template>
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
                                    class="w-full capitalize-select"
                                    :class="{ 'border-red-500': form.errors.ward_id }"
                                    :disabled="!form.constituency_id"
                                    :clearable="false"
                                >
                                    <template #option="{ name }">
                                        <span class="capitalize">{{ name }}</span>
                                    </template>
                                    <template #selected-option="{ name }">
                                        <span class="capitalize">{{ name }}</span>
                                    </template>
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
                            
                            <!-- Enlisting Date -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Enlisting Date
                                </label>
                                <input 
                                    type="date" 
                                    v-model="form.enlisting_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white bg-gray-200 text-gray-500"
                                    readonly
                                >
                                <div class="text-xs text-gray-500 mt-1">
                                    Auto-set to today's date
                                </div>
                            </div>
                            
                            <!-- Party Membership Number -->
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Party Membership Number
                                    </label>
                                    <i
                                        class="fas fa-info-circle text-gray-400 ml-1"
                                        title="Party Membership Number"
                                    ></i>
                                </div>
                                <input 
                                    type="text" 
                                    v-model="form.party_membership_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white bg-gray-200 text-gray-500"
                                    readonly
                                    placeholder="Auto-generated after registration"
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
                                :disabled="!isStep3Valid"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ Math.round((currentStep / totalSteps) * 100) }}% Complete
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div 
                                    class="bg-green-600 h-2.5 rounded-full transition-all duration-300 ease-in-out" 
                                    :style="'width: ' + (currentStep / totalSteps * 100) + '%'"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Confirmation -->
                    <div v-else-if="currentStep === 4" class="space-y-6">
                        <h2 class="text-base font-medium text-gray-700">
                            Step 4: Confirmation
                        </h2>

                        <!-- Account Information Card -->
                        <div
                            class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                        >
                            <div
                                class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                @click="toggleAccordion('account information')"
                            >
                                <h3
                                    class="text-lg font-medium text-gray-900"
                                >
                                    Account Information
                                    <i
                                        class="fas fa-chevron-down w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                activeAccordion ===
                                                    'account information',
                                        }"
                                    ></i>
                                </h3>
                            </div>
                            <div
                                v-if="
                                    activeAccordion ===
                                    'account information'
                                "
                                class="p-6"
                            >
                                <dl class="space-y-4">
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Name of Member
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.surname +
                                                " " +
                                                form.other_name ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Identification No.
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.identification_number ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Party Membership No.
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.party_membership_number ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Personal Information Card -->
                        <div
                            class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                        >
                            <div
                                class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                @click="toggleAccordion('personal information')"
                            >
                                <h3
                                    class="text-lg font-medium text-gray-900"
                                >
                                    Personal Information
                                    <i
                                        class="fas fa-chevron-down w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                activeAccordion ===
                                                    'personal information',
                                        }"
                                    ></i>
                                </h3>
                            </div>
                            <div
                                v-if="
                                    activeAccordion ===
                                    'personal information'
                                "
                                class="p-6"
                            >
                                <dl class="space-y-4">
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Date of Birth
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.date_of_birth ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Sex
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.gender === 'XY' ? 'Male' : (form.gender === 'XX' ? 'Female' : 'Not specified')
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Ethnicity
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.ethnicity_id ? 'Selected' : 'Not specified'
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Religion
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.religion_id ? 'Selected' : 'Not specified'
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Are you a PWD
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.disability_status
                                                    ? "Yes"
                                                    : "No"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        v-if="form.disability_status"
                                        class="sm:grid sm:grid-cols-3 sm:gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            NCPWD Number
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.ncpwd_number ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Member's Mobile No.
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.telephone ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Email Address
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.email ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        v-if="form.special_interest_groups && form.special_interest_groups.length > 0"
                                        class="sm:grid sm:grid-cols-3 sm:gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Special Interest Groups
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            <div class="flex flex-wrap gap-2">
                                                <span 
                                                    v-for="(interest, index) in form.special_interest_groups" 
                                                    :key="index"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                >
                                                    {{ interest }}
                                                </span>
                                            </div>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div
                            class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                        >
                            <div
                                class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                @click="toggleAccordion('location and enlisting information')"
                            >
                                <h3
                                    class="text-lg font-medium text-gray-900"
                                >
                                    Location and Enlisting Information
                                    <i
                                        class="fas fa-chevron-down w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                activeAccordion ===
                                                    'location and enlisting information',
                                        }"
                                    ></i>
                                </h3>
                            </div>
                            <div
                                v-if="
                                    activeAccordion ===
                                    'location and enlisting information'
                                "
                                class="p-6"
                            >
                                <dl class="space-y-4">
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            County of Member Registration
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2 capitalize"
                                        >
                                            {{ selectedCountyName }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Constituency of Member
                                            Registration
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2 capitalize"
                                        >
                                            {{ selectedConstituencyName }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Ward of Member Registration
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2 capitalize"
                                        >
                                            {{ selectedWardName }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Polling Station
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.polling_station ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                    >
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Enlisting Date
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                        >
                                            {{
                                                form.enlisting_date ||
                                                "Not specified"
                                            }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    id="terms"
                                    name="terms"
                                    type="checkbox"
                                    v-model="form.terms"
                                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-medium text-gray-700">
                                    I, undersigned, do hereby affirm/declare/confirm/verify that I am not a registered member of any other registered political party in Kenya. By signing up, I agree to
                                    <a
                                        href="javascript:void(0)"
                                        target="_blank"
                                        class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                    >
                                        Terms of Service
                                    </a>
                                    <span class="mx-1"
                                        >and</span
                                    >
                                    <a
                                        href="javascript:void(0)"
                                        target="_blank"
                                        class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                    >
                                        Privacy Policy
                                    </a>
                                </label>
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
                                @click="save"
                                :disabled="!form.terms"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Save Member
                                <i class="fas fa-save ml-2"></i>
                            </button>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ Math.round((currentStep / totalSteps) * 100) }}% Complete
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div 
                                    class="bg-green-600 h-2.5 rounded-full transition-all duration-300 ease-in-out" 
                                    :style="'width: ' + (currentStep / totalSteps * 100) + '%'"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>