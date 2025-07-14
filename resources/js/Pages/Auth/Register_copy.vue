<!-- resources/js/Pages/Auth/Register.vue -->
<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed, onMounted, watch } from "vue";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import axios from "axios";

const props = defineProps({
    counties: Array,
    genders: Object,
    // Remove ethnicities and religions from props since we'll fetch them from API
});

const currentStep = ref(1);
const totalSteps = 4; // 3 form steps + 1 confirmation step
const activeAccordion = ref("personal");
const uploadProgress = ref(0);

// Step name mapping for navigation buttons
const stepNames = [
    "Account Information", // 1
    "Personal Details", // 2
    "Contact & Location", // 3
    "Review & Submit", // 4
];

// Get current step description
const stepDescription = computed(() => {
    return stepNames[currentStep.value - 1];
});

// Toggle accordion sections
const toggleAccordion = (section) => {
    activeAccordion.value = activeAccordion.value === section ? null : section;
};

// Show toast notification
const showToast = (type, title, message) => {
    window.Toast.fire({
        icon: type,
        title: title,
        text: message,
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", window.Toast.stopTimer);
            toast.addEventListener("mouseleave", window.Toast.resumeTimer);
        },
    });
};

// Reactive references
const subCounties = ref([]);
const constituencies = ref([]);
const wards = ref([]);
const ethnicities = ref([]);
const religions = ref([]);

// Loading states
const loadingEthnicities = ref(false);
const loadingReligions = ref(false);

// Loading states
const loadingSubCounties = ref(false);
const loadingConstituencies = ref(false);
const loadingWards = ref(false);

const form = useForm({
    // Basic Details
    name: "",
    email: "",
    password: "",
    password_confirmation: "",

    // Personal Information
    first_name: "",
    middle_name: "",
    last_name: "",
    gender: "",
    date_of_birth: "",
    disability_status: "",
    plwd_number: "", // Only shown if disability_status is selected
    ethnicity_id: null,
    religion_id: null,
    national_id: "",

    // Contact Information
    telephone: "",
    address_line_1: "",
    address_line_2: "",
    city: "",
    state: "",

    // Location Details
    county_id: null,
    sub_county_id: null,
    constituency_id: null,
    ward_id: null,

    // Security
    security_question: "",
    security_answer: "",

    // Terms
    terms: false,
});

// Fetch ethnicities from API
const fetchEthnicities = async () => {
    try {
        loadingEthnicities.value = true;
        const response = await axios.get("/api/ethnicities");
        if (response.data && response.data.success) {
            ethnicities.value = response.data.data;
        } else {
            console.error(
                "Failed to load ethnicities:",
                response.data?.message || "Unknown error"
            );
            ethnicities.value = [];
        }
    } catch (error) {
        console.error("Error fetching ethnicities:", error);
        ethnicities.value = [];
    } finally {
        loadingEthnicities.value = false;
    }
};

// Fetch religions from API
const fetchReligions = async () => {
    try {
        loadingReligions.value = true;
        const response = await axios.get("/api/religions");
        if (response.data && response.data.success) {
            religions.value = response.data.data;
        } else {
            console.error(
                "Failed to load religions:",
                response.data?.message || "Unknown error"
            );
            religions.value = [];
        }
    } catch (error) {
        console.error("Error fetching religions:", error);
        religions.value = [];
    } finally {
        loadingReligions.value = false;
    }
};

// Fetch data when component mounts
onMounted(() => {
    fetchEthnicities();
    fetchReligions();
});

// Disability options
const disabilityStatusOptions = [
    { value: "", label: "None" },
    { value: "physical", label: "Physical" },
    { value: "visual", label: "Visual" },
    { value: "hearing", label: "Hearing" },
    { value: "mental", label: "Mental" },
    { value: "other", label: "Other" },
];

// Security questions
const securityQuestions = [
    "What was your first pet's name?",
    "What was the name of your first school?",
    "What is your mother's maiden name?",
    "What city were you born in?",
];

// Watch for disability status to show PLWD number field
const showPlwdNumber = computed(() => {
    return form.disability_status && form.disability_status !== "";
});

// Watch for county changes to load sub-counties
// Removed redundant watch for county_id as it's now handled by the updated watch above

// Watch for sub-county changes to load constituencies
watch(
    () => form.county_id,
    async (newCountyId) => {
        if (newCountyId) {
            try {
                // Reset dependent fields when county changes
                form.constituency_id = null;
                form.ward_id = null;
                subCounties.value = [];
                constituencies.value = [];
                wards.value = [];

                // Set loading state
                loadingSubCounties.value = true;
                loadingConstituencies.value = true;

                // Fetch sub-counties for the selected county
                const subCountiesResponse = await axios.get(
                    `/api/locations/counties/${newCountyId}/sub-counties`
                );

                // Fetch constituencies for the selected county
                const constituenciesResponse = await axios.get(
                    `/api/locations/counties/${newCountyId}/constituencies`
                );

                if (
                    subCountiesResponse.data &&
                    subCountiesResponse.data.success
                ) {
                    subCounties.value = subCountiesResponse.data.data || [];
                } else {
                    console.error(
                        "Failed to load sub-counties:",
                        subCountiesResponse.data?.message || "Unknown error"
                    );
                    subCounties.value = [];
                }

                if (
                    constituenciesResponse.data &&
                    constituenciesResponse.data.success
                ) {
                    constituencies.value =
                        constituenciesResponse.data.data || [];
                } else {
                    console.error(
                        "Failed to load constituencies:",
                        constituenciesResponse.data?.message || "Unknown error"
                    );
                    constituencies.value = [];
                }
            } catch (error) {
                console.error("Error fetching location data:", error);
                subCounties.value = [];
                constituencies.value = [];
            } finally {
                loadingSubCounties.value = false;
                loadingConstituencies.value = false;
            }
        } else {
            // Reset all dependent fields if no county is selected
            subCounties.value = [];
            constituencies.value = [];
            wards.value = [];
            form.sub_county_id = null;
            form.constituency_id = null;
            form.ward_id = null;
        }
    }
);

// Watch for constituency changes to load wards
watch(
    () => form.constituency_id,
    async (newConstituencyId) => {
        if (newConstituencyId) {
            try {
                // Reset dependent field when constituency changes
                form.ward_id = null;
                wards.value = [];

                // Set loading state
                loadingWards.value = true;

                // Fetch wards for the selected constituency
                const { data } = await axios.get(
                    `/api/locations/constituencies/${newConstituencyId}/wards`
                );
                if (data && data.success) {
                    wards.value = data.data || [];
                } else {
                    console.error(
                        "Failed to load wards:",
                        data?.message || "Unknown error"
                    );
                    // Reset wards if there was an error
                    wards.value = [];
                }
            } catch (error) {
                console.error("Error fetching wards:", error);
                // Reset wards on error
                wards.value = [];
            } finally {
                loadingWards.value = false;
            }
        }
    }
);

// Navigation methods
const nextStep = async () => {
    // Validate current step before proceeding
    let isValid = true;

    // Basic validation for each step
    if (currentStep.value === 1) {
        if (
            !form.name ||
            !form.email ||
            !form.password ||
            !form.password_confirmation
        ) {
            showToast(
                "error",
                "Validation Error",
                "Please fill in all required fields"
            );
            isValid = false;
        } else if (form.password !== form.password_confirmation) {
            showToast("error", "Validation Error", "Passwords do not match");
            isValid = false;
        } else if (form.password.length < 8) {
            showToast(
                "error",
                "Validation Error",
                "Password must be at least 8 characters"
            );
            isValid = false;
        }
    } else if (currentStep.value === 2) {
        if (
            !form.first_name ||
            !form.last_name ||
            !form.gender ||
            !form.date_of_birth ||
            !form.national_id ||
            !form.disability_status
        ) {
            showToast(
                "error",
                "Validation Error",
                "Please fill in all required personal details including disability status"
            );
            isValid = false;
        }
    } else if (currentStep.value === 3) {
        if (
            !form.telephone ||
            !form.address_line_1 ||
            !form.city ||
            !form.county_id
        ) {
            showToast(
                "error",
                "Validation Error",
                "Please fill in all required contact and location details"
            );
            isValid = false;
        }
    }

    if (isValid && currentStep.value < totalSteps) {
        currentStep.value++;
        // Scroll to top of form when changing steps
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        // Scroll to top of form when changing steps
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};

const submit = async () => {
    try {
        // Show loading state
        form.processing = true;

        // Submit the form
        const response = await axios.post(route("register"), form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
            onUploadProgress: (progressEvent) => {
                uploadProgress.value = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total
                );
            },
        });

        // Show success message
        showToast(
            "success",
            "Success",
            "Registration successful! Redirecting..."
        );

        // Redirect after a short delay
        setTimeout(() => {
            window.location.href = route("dashboard");
        }, 2000);
    } catch (error) {
        let errorMessage = "An error occurred during registration";

        if (error.response) {
            // Server responded with an error status code
            const { data } = error.response;

            if (data.errors) {
                // Show first error message
                const firstError = Object.values(data.errors)[0];
                errorMessage = Array.isArray(firstError)
                    ? firstError[0]
                    : firstError;
            } else if (data.message) {
                errorMessage = data.message;
            }
        } else if (error.request) {
            // Request was made but no response received
            errorMessage =
                "No response from server. Please check your connection.";
        }

        showToast("error", "Registration Failed", errorMessage);
    } finally {
        form.processing = false;
    }
};

// Get the selected option label from an array of options
const getSelectedLabel = (value, options) => {
    const option = options.find((opt) => opt.value === value);
    return option ? option.label : "Not specified";
};

// Get location name by ID
const getLocationName = (id, locations) => {
    const location = locations.find((loc) => loc.id == id);
    return location ? location.name : "Not specified";
};

// Get religion/ethnicity name by ID
const getItemName = (id, items) => {
    const item = items.find((item) => item.id == id);
    return item ? item.name : "Not specified";
};
</script>

<template>
    <Head title="Register" />

    <div
        class="min-h-screen flex flex-col justify-center items-center bg-gray-100 px-4 py-8 md:h-screen"
    >
        <div
            class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white"
        >
            <AuthenticationCardLogo />
        </div>
        <div class="flex-1 w-full flex flex-col items-center justify-center">
            <div
                class="relative flex flex-col w-full max-w-2xl px-8 py-6 bg-white shadow-md overflow-y-auto sm:rounded-lg rounded-lg dark:border xl:p-0 dark:bg-gray-800 dark:border-gray-700"
                style="min-height: 0; max-height: 90vh"
            >
                <div class="p-2 space-y-4 sm:p-8">
                    <div class="w-full">
                        <h1
                            class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white"
                        >
                            Create your account
                        </h1>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Step 1: Basic Details -->
                        <div v-if="currentStep === 1" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 1: Basic account information
                            </h2>

                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <InputLabel
                                        for="name"
                                        value="Username"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </div>
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="Enter your username"
                                    required
                                    autofocus
                                />
                                <InputError
                                    :message="form.errors.name"
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <InputLabel
                                        for="email"
                                        value="Email"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                    <div class="ml-1 group relative">
                                        <i
                                            class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                        ></i>
                                        <div
                                            class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                        >
                                            We'll send a confirmation email to
                                            this address.
                                        </div>
                                    </div>
                                </div>
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="your.email@example.com"
                                    required
                                />
                                <InputError
                                    :message="form.errors.email"
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <InputLabel
                                        for="telephone"
                                        value="Telephone"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                    <div class="ml-1 group relative">
                                        <i
                                            class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                        ></i>
                                        <div
                                            class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                        >
                                            Include country code (e.g., +254 for
                                            Kenya). We'll use this number for
                                            account verification.
                                        </div>
                                    </div>
                                </div>
                                <TextInput
                                    id="telephone"
                                    v-model="form.telephone"
                                    type="tel"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="e.g. +254712345678"
                                    required
                                />
                                <InputError
                                    :message="form.errors.telephone"
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <InputLabel
                                        for="password"
                                        value="Password"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                    <div class="ml-1 group relative">
                                        <i
                                            class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                        ></i>
                                        <div
                                            class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                        >
                                            Must be at least 8 characters long
                                            and include a mix of letters,
                                            numbers, and symbols.
                                        </div>
                                    </div>
                                </div>
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="Enter your password"
                                    required
                                />
                                <InputError
                                    :message="form.errors.password"
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <InputLabel
                                        for="password_confirmation"
                                        value="Confirm Password"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </div>
                                <TextInput
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="Confirm your password"
                                    required
                                />
                                <InputError
                                    :message="form.errors.password_confirmation"
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>

                            <!-- Navigation Buttons for Step 1 -->
                            <div class="flex items-center justify-between pt-6">
                                <div></div>
                                <!-- Empty div for spacing -->
                                <button
                                    type="button"
                                    @click="nextStep"
                                    class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                                >
                                    Next: Personal Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Personal Information -->
                        <div v-if="currentStep === 2" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 2: Personal details
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Row 1: First Name -->
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="first_name"
                                            value="First Name"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Enter your full legal name as it
                                                appears on official documents.
                                            </div>
                                        </div>
                                    </div>
                                    <TextInput
                                        id="first_name"
                                        v-model="form.first_name"
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        placeholder="Enter your first name"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.first_name"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <!-- Row 1: Middle Name -->
                                <div class="space-y-2">
                                    <InputLabel
                                        for="middle_name"
                                        value="Middle Name (Optional)"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <TextInput
                                        id="middle_name"
                                        v-model="form.middle_name"
                                        placeholder="Enter your middle name (if any)"
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    />
                                </div>

                                <!-- Row 2: Last Name -->
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="last_name"
                                            value="Last Name"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="last_name"
                                        v-model="form.last_name"
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        placeholder="Enter your last name"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.last_name"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <!-- Row 2: Gender -->
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="gender"
                                            value="Gender"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.gender"
                                        :options="[
                                            { value: 'male', label: 'Male' },
                                            {
                                                value: 'female',
                                                label: 'Female',
                                            },
                                            { value: 'other', label: 'Other' },
                                        ]"
                                        placeholder="Select gender"
                                        label="label"
                                        :reduce="(option) => option.value"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50': form.processing,
                                        }"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.gender"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <!-- Row 3: Date of Birth -->
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="date_of_birth"
                                            value="Date of Birth"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="date_of_birth"
                                        v-model="form.date_of_birth"
                                        type="date"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        placeholder="YYYY-MM-DD"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.date_of_birth"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <!-- Row 3: National ID -->
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="national_id"
                                            value="ID Number"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="national_id"
                                        v-model="form.national_id"
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        placeholder="Enter your National ID number"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.national_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <!-- Row 4: Ethnicity -->
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="ethnicity_id"
                                            value="Ethnicity"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.ethnicity_id"
                                        :options="ethnicities"
                                        placeholder="Select ethnicity"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50': loadingEthnicities,
                                        }"
                                        :loading="loadingEthnicities"
                                        :disabled="loadingEthnicities"
                                        loading-text="Loading..."
                                        :clearable="true"
                                        :searchable="true"
                                        required
                                    >
                                        <template #no-options>
                                            {{
                                                loadingEthnicities
                                                    ? "Loading..."
                                                    : "No ethnicities found"
                                            }}
                                        </template>
                                        <template
                                            #option="{ name, description }"
                                        >
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{
                                                    name
                                                }}</span>
                                                <span
                                                    v-if="description"
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{ description }}
                                                </span>
                                            </div>
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.ethnicity_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <!-- Row 4: Religion -->
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="religion_id"
                                            value="Religion"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.religion_id"
                                        :options="religions"
                                        placeholder="Select religion"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50': loadingReligions,
                                        }"
                                        :loading="loadingReligions"
                                        :disabled="loadingReligions"
                                        loading-text="Loading..."
                                        :clearable="true"
                                        :searchable="true"
                                        required
                                    >
                                        <template #no-options>
                                            {{
                                                loadingReligions
                                                    ? "Loading..."
                                                    : "No religions found"
                                            }}
                                        </template>
                                        <template
                                            #option="{ name, description }"
                                        >
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{
                                                    name
                                                }}</span>
                                                <span
                                                    v-if="description"
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{ description }}
                                                </span>
                                            </div>
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.religion_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <!-- Row 5: Disability Status -->
                                <div class="space-y-2">
                                    <InputLabel
                                        for="disability_status"
                                        value="Disability Status"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <VueSelect
                                        v-model="form.disability_status"
                                        :options="disabilityStatusOptions"
                                        placeholder="Select disability status"
                                        label="label"
                                        :reduce="(option) => option.value"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50': form.processing,
                                        }"
                                        @update:modelValue="
                                            (val) =>
                                                (form.disability_status = val)
                                        "
                                    />
                                </div>

                                <!-- Row 5: PLWD Number (Conditional) -->
                                <div v-if="showPlwdNumber" class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="plwd_number"
                                            value="PLWD Registration Number"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="plwd_number"
                                        v-model="form.plwd_number"
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        placeholder="Enter your Persons Living with Disability registration number"
                                    />
                                    <InputError
                                        :message="form.errors.plwd_number"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Contact & Location -->
                        <div v-if="currentStep === 3" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 3: Contact and location information
                            </h2>

                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <InputLabel
                                        for="address_line_1"
                                        value="Address Line 1"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </div>
                                <TextInput
                                    id="address_line_1"
                                    v-model="form.address_line_1"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="Enter your street address"
                                    required
                                />
                                <InputError
                                    :message="form.errors.address_line_1"
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>

                            <div class="space-y-2">
                                <InputLabel
                                    for="address_line_2"
                                    value="Address Line 2 (Optional)"
                                    class="block text-sm font-medium text-gray-700"
                                />
                                <TextInput
                                    id="address_line_2"
                                    v-model="form.address_line_2"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="Apartment, suite, etc. (optional)"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel
                                        for="city"
                                        value="City/Town"
                                        required
                                    />
                                    <TextInput
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Enter your city or town"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.city"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="state"
                                            value="State/County"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="state"
                                        v-model="form.state"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Enter your state or county"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.state"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="county_id"
                                            value="County"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your county of residence.
                                                This helps us provide
                                                location-specific services.
                                            </div>
                                        </div>
                                    </div>
                                    <VueSelect
                                        v-model="form.county_id"
                                        :options="counties"
                                        placeholder="Select county"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50': loadingCounties,
                                        }"
                                        @update:modelValue="fetchSubCounties"
                                        :loading="loadingCounties"
                                        :disabled="loadingCounties"
                                        loading-text="Loading..."
                                        :clearable="true"
                                        required
                                    >
                                        <template #no-options>
                                            {{
                                                loadingCounties
                                                    ? "Loading..."
                                                    : "No counties found"
                                            }}
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.county_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="sub_county_id"
                                            value="Sub-County"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.sub_county_id"
                                        :options="subCounties"
                                        placeholder="Select sub-county"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50':
                                                loadingSubCounties ||
                                                !form.county_id,
                                        }"
                                        @update:modelValue="fetchWards"
                                        :loading="loadingSubCounties"
                                        :disabled="
                                            !form.county_id ||
                                            loadingSubCounties
                                        "
                                        loading-text="Loading..."
                                        :clearable="true"
                                        required
                                    >
                                        <template #no-options>
                                            {{
                                                loadingSubCounties
                                                    ? "Loading..."
                                                    : !form.county_id
                                                    ? "Please select a county first"
                                                    : "No sub-counties found"
                                            }}
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.sub_county_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="constituency_id"
                                            value="Constituency"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your constituency. This
                                                helps us direct you to the right
                                                local services.
                                            </div>
                                        </div>
                                    </div>
                                    <VueSelect
                                        v-model="form.constituency_id"
                                        :options="constituencies"
                                        placeholder="Select constituency"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50':
                                                loadingConstituencies ||
                                                !form.county_id,
                                        }"
                                        :loading="loadingConstituencies"
                                        :disabled="
                                            !form.county_id ||
                                            loadingConstituencies
                                        "
                                        loading-text="Loading..."
                                        :clearable="true"
                                        required
                                    >
                                        <template #no-options>
                                            {{
                                                loadingConstituencies
                                                    ? "Loading..."
                                                    : !form.county_id
                                                    ? "Please select a county first"
                                                    : "No constituencies found"
                                            }}
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.constituency_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="ward_id"
                                            value="Ward"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your ward for more
                                                precise service delivery and
                                                information.
                                            </div>
                                        </div>
                                    </div>
                                    <VueSelect
                                        v-model="form.ward_id"
                                        :options="wards"
                                        placeholder="Select ward"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50':
                                                loadingWards ||
                                                !form.sub_county_id,
                                        }"
                                        :loading="loadingWards"
                                        :disabled="
                                            !form.sub_county_id || loadingWards
                                        "
                                        loading-text="Loading..."
                                        :clearable="true"
                                        required
                                    >
                                        <template #no-options>
                                            {{
                                                loadingWards
                                                    ? "Loading..."
                                                    : !form.sub_county_id
                                                    ? "Please select a sub-county first"
                                                    : "No wards found"
                                            }}
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.ward_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- Security Question and Answer -->
                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="security_question"
                                            value="Security Question"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <select
                                        id="security_question"
                                        v-model="form.security_question"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        :class="{
                                            'opacity-50': form.processing,
                                        }"
                                        required
                                    >
                                        <option value="" disabled>
                                            Select a security question
                                        </option>
                                        <option
                                            v-for="(
                                                question, index
                                            ) in securityQuestions"
                                            :key="index"
                                            :value="question"
                                        >
                                            {{ question }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.security_question"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="security_answer"
                                            value="Security Answer"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="security_answer"
                                        v-model="form.security_answer"
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        placeholder="Enter your answer"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.security_answer"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- Navigation Buttons for Confirmation Step -->
                            <div class="flex items-center justify-between pt-6">
                                <button
                                    type="button"
                                    @click="prevStep"
                                    class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400 transition-colors duration-200"
                                >
                                    <i
                                        class="fas fa-arrow-left mr-2 text-emerald-600"
                                    ></i>
                                    <span class="hidden sm:inline"
                                        >Previous</span
                                    >
                                </button>

                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.terms"
                                    class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                                    :class="{
                                        'opacity-75 cursor-not-allowed':
                                            form.processing || !form.terms,
                                        'shadow-emerald-200':
                                            !form.processing && form.terms,
                                    }"
                                >
                                    <i
                                        v-if="form.processing"
                                        class="fas fa-spinner fa-spin mr-2"
                                    ></i>
                                    <template v-else>
                                        <span class="hidden sm:inline"
                                            >Submit Application</span
                                        >
                                        <span class="sm:hidden">Submit</span>
                                        <i class="fas fa-check ml-2"></i>
                                    </template>
                                </button>
                            </div>
                        </div>

                        <!-- Confirmation Step -->
                        <div v-if="currentStep === 4" class="space-y-6">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 4: Please verify all the information below
                                before submitting your application
                            </h2>

                            <div class="space-y-4">
                                <!-- Personal Details Card -->
                                <div
                                    class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                                >
                                    <div
                                        class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                        @click="toggleAccordion('personal')"
                                    >
                                        <h3
                                            class="text-lg font-medium text-gray-900"
                                        >
                                            Personal Details
                                        </h3>
                                        <svg
                                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                            :class="{
                                                'rotate-180':
                                                    activeAccordion ===
                                                    'personal',
                                            }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        v-if="activeAccordion === 'personal'"
                                        class="p-6"
                                    >
                                        <dl class="space-y-4">
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Full Name
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{ form.first_name }}
                                                    {{
                                                        form.middle_name
                                                            ? form.middle_name +
                                                              " "
                                                            : ""
                                                    }}{{ form.last_name }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Gender
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        form.gender ||
                                                        "Not specified"
                                                    }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
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
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    ID Number
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        form.id_number ||
                                                        "Not specified"
                                                    }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <!-- Contact Information Card -->
                                <div
                                    class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                                >
                                    <div
                                        class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                        @click="toggleAccordion('contact')"
                                    >
                                        <h3
                                            class="text-lg font-medium text-gray-900"
                                        >
                                            Contact Information
                                        </h3>
                                        <svg
                                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                            :class="{
                                                'rotate-180':
                                                    activeAccordion ===
                                                    'contact',
                                            }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        v-if="activeAccordion === 'contact'"
                                        class="p-6"
                                    >
                                        <dl class="space-y-4">
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Email
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
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Phone Number
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
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Address
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        [
                                                            form.address_line_1,
                                                            form.address_line_2,
                                                            form.city,
                                                            form.state,
                                                        ]
                                                            .filter(Boolean)
                                                            .join(", ") ||
                                                        "Not specified"
                                                    }}
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
                                        @click="toggleAccordion('location')"
                                    >
                                        <h3
                                            class="text-lg font-medium text-gray-900"
                                        >
                                            Location Information
                                        </h3>
                                        <svg
                                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                            :class="{
                                                'rotate-180':
                                                    activeAccordion ===
                                                    'location',
                                            }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        v-if="activeAccordion === 'location'"
                                        class="p-6"
                                    >
                                        <dl class="space-y-4">
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    County
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        getLocationName(
                                                            form.county_id,
                                                            counties
                                                        )
                                                    }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Sub-County
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        getLocationName(
                                                            form.sub_county_id,
                                                            subCounties
                                                        )
                                                    }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Constituency
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        getLocationName(
                                                            form.constituency_id,
                                                            constituencies
                                                        )
                                                    }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Ward
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        getLocationName(
                                                            form.ward_id,
                                                            wards
                                                        )
                                                    }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <!-- Additional Information Card -->
                                <div
                                    class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                                >
                                    <div
                                        class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                        @click="toggleAccordion('additional')"
                                    >
                                        <h3
                                            class="text-lg font-medium text-gray-900"
                                        >
                                            Additional Information
                                        </h3>
                                        <svg
                                            class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                            :class="{
                                                'rotate-180':
                                                    activeAccordion ===
                                                    'additional',
                                            }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        v-if="activeAccordion === 'additional'"
                                        class="p-6"
                                    >
                                        <dl class="space-y-4">
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
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
                                                        getItemName(
                                                            form.ethnicity_id,
                                                            ethnicities
                                                        )
                                                    }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
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
                                                        getItemName(
                                                            form.religion_id,
                                                            religions
                                                        )
                                                    }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Disability Status
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        form.disability_status
                                                            ? getSelectedLabel(
                                                                  form.disability_status,
                                                                  disabilityStatusOptions
                                                              )
                                                            : "None"
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
                                                    PLWD Number
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        form.plwd_number ||
                                                        "Not provided"
                                                    }}
                                                </dd>
                                            </div>
                                            <div
                                                class="sm:grid sm:grid-cols-3 sm:gap-4"
                                            >
                                                <dt
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Security Question
                                                </dt>
                                                <dd
                                                    class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                                >
                                                    {{
                                                        form.security_question ||
                                                        "Not specified"
                                                    }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div
                                    class="p-6 bg-white rounded-lg shadow border border-gray-200"
                                >
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox
                                                id="terms"
                                                v-model:checked="form.terms"
                                                required
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <InputLabel
                                                for="terms"
                                                class="font-medium text-gray-700"
                                            >
                                                <div class="flex items-center">
                                                    I certify that all the
                                                    information provided is
                                                    accurate and complete. I
                                                    understand that providing
                                                    false information may result
                                                    in the rejection of my
                                                    application or termination
                                                    of my account. By signing
                                                    up, I agree to the
                                                    <a
                                                        href="#"
                                                        target="_blank"
                                                        class="ml-1 text-emerald-600 hover:text-emerald-500 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 rounded"
                                                        >Terms of Service</a
                                                    >
                                                    and
                                                    <a
                                                        href="#"
                                                        target="_blank"
                                                        class="ml-1 text-emerald-600 hover:text-emerald-500 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 rounded"
                                                        >Privacy Policy</a
                                                    >
                                                    <i
                                                        class="fas fa-star text-red-500 text-xs ml-1"
                                                    ></i>
                                                </div>
                                            </InputLabel>
                                            <p
                                                class="text-xs text-gray-500 mt-1"
                                            >
                                                You must agree to the terms and
                                                conditions to create an account.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div
                                    class="flex items-center justify-between pt-6"
                                >
                                    <button
                                        type="button"
                                        @click="prevStep"
                                        class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400 transition-colors duration-200"
                                    >
                                        <i
                                            class="fas fa-arrow-left mr-2 text-emerald-600"
                                        ></i>
                                        <span class="hidden sm:inline"
                                            >Previous</span
                                        >
                                    </button>

                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-emerald-200 transition-colors duration-200"
                                    >
                                        <i
                                            v-if="form.processing"
                                            class="fas fa-spinner fa-spin mr-2"
                                        ></i>
                                        <span class="hidden sm:inline"
                                            >Submit Application</span
                                        >
                                        <span class="sm:hidden">Submit</span>
                                        <i
                                            v-if="!form.processing"
                                            class="fas fa-check ml-2"
                                        ></i>
                                    </button>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-6">
                                    <div
                                        class="flex items-center justify-between mb-1"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-700"
                                        >
                                            Step {{ currentStep }} of
                                            {{ totalSteps }}
                                        </span>
                                        <span
                                            class="text-sm font-medium text-gray-700"
                                        >
                                            {{
                                                Math.round(
                                                    (currentStep / totalSteps) *
                                                        100
                                                )
                                            }}% Complete
                                        </span>
                                    </div>
                                    <div
                                        class="w-full bg-gray-200 rounded-full h-2.5"
                                    >
                                        <div
                                            class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300"
                                            :style="{
                                                width: `${
                                                    (currentStep / totalSteps) *
                                                    100
                                                }%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Sign In Link -->
                                <div class="mt-6 text-center">
                                    <p class="text-sm text-gray-600">
                                        Already have an account?
                                        <Link
                                            :href="route('login')"
                                            class="text-emerald-600 hover:text-emerald-500 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 rounded"
                                        >
                                            Sign in
                                        </Link>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
