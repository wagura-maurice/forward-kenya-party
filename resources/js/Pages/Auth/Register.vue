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
});

const currentStep = ref(1);
const totalSteps = 4;
const activeAccordion = ref("personal");
const uploadProgress = ref(0);

const stepNames = [
    "Account Information",
    "Personal Details",
    "Contact & Location",
    "Review & Submit",
];

const stepDescription = computed(() => stepNames[currentStep.value - 1]);

const toggleAccordion = (section) => {
    activeAccordion.value = activeAccordion.value === section ? null : section;
};

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

const subCounties = ref([]);
const constituencies = ref([]);
const wards = ref([]);
const ethnicities = ref([]);
const religions = ref([]);

const loadingEthnicities = ref(false);
const loadingReligions = ref(false);
const loadingSubCounties = ref(false);
const loadingConstituencies = ref(false);
const loadingWards = ref(false);

const form = useForm({
    name: "",
    email: "",
    gender: "",
    date_of_birth: "",
    disability_status: "",
    plwd_number: "",
    ethnicity_id: null,
    religion_id: null,
    national_id: "",
    telephone: "",
    county_id: null,
    sub_county_id: null,
    constituency_id: null,
    ward_id: null,
    terms: false,
});

const fetchEthnicities = async () => {
    try {
        loadingEthnicities.value = true;
        const response = await axios.get("/api/ethnicities");
        ethnicities.value = response.data?.success ? response.data.data : [];
    } catch (error) {
        console.error("Error fetching ethnicities:", error);
        ethnicities.value = [];
        showToast("error", "Error", "Failed to load ethnicities");
    } finally {
        loadingEthnicities.value = false;
    }
};

const fetchReligions = async () => {
    try {
        loadingReligions.value = true;
        const response = await axios.get("/api/religions");
        religions.value = response.data?.success ? response.data.data : [];
    } catch (error) {
        console.error("Error fetching religions:", error);
        religions.value = [];
        showToast("error", "Error", "Failed to load religions");
    } finally {
        loadingReligions.value = false;
    }
};

onMounted(() => {
    fetchEthnicities();
    fetchReligions();
});

const disabilityStatusOptions = [
    { value: "", label: "None" },
    { value: "physical", label: "Physical" },
    { value: "visual", label: "Visual" },
    { value: "hearing", label: "Hearing" },
    { value: "mental", label: "Mental" },
    { value: "other", label: "Other" },
];

const securityQuestions = [
    "What was your first pet's name?",
    "What was the name of your first school?",
    "What is your mother's maiden name?",
    "What city were you born in?",
];

const showPlwdNumber = computed(
    () => form.disability_status && form.disability_status !== ""
);

watch(
    () => form.county_id,
    async (newCountyId) => {
        if (newCountyId) {
            try {
                form.constituency_id = null;
                form.ward_id = null;
                subCounties.value = [];
                constituencies.value = [];
                wards.value = [];
                loadingSubCounties.value = true;
                loadingConstituencies.value = true;

                const [subCountiesResponse, constituenciesResponse] =
                    await Promise.all([
                        axios.get(
                            `/api/locations/counties/${newCountyId}/sub-counties`
                        ),
                        axios.get(
                            `/api/locations/counties/${newCountyId}/constituencies`
                        ),
                    ]);

                subCounties.value = subCountiesResponse.data?.success
                    ? subCountiesResponse.data.data
                    : [];
                constituencies.value = constituenciesResponse.data?.success
                    ? constituenciesResponse.data.data
                    : [];
            } catch (error) {
                console.error("Error fetching location data:", error);
                subCounties.value = [];
                constituencies.value = [];
                showToast("error", "Error", "Failed to load location data");
            } finally {
                loadingSubCounties.value = false;
                loadingConstituencies.value = false;
            }
        } else {
            subCounties.value = [];
            constituencies.value = [];
            wards.value = [];
            form.sub_county_id = null;
            form.constituency_id = null;
            form.ward_id = null;
        }
    }
);

watch(
    () => form.constituency_id,
    async (newConstituencyId) => {
        if (newConstituencyId) {
            try {
                form.ward_id = null;
                wards.value = [];
                loadingWards.value = true;

                const { data } = await axios.get(
                    `/api/locations/constituencies/${newConstituencyId}/wards`
                );
                wards.value = data?.success ? data.data : [];
            } catch (error) {
                console.error("Error fetching wards:", error);
                wards.value = [];
                showToast("error", "Error", "Failed to load wards");
            } finally {
                loadingWards.value = false;
            }
        }
    }
);

const validateStep = (step) => {
    if (step === 1) {
        if (!form.name)
            return { isValid: false, message: "Full name is required" };
        if (!form.email)
            return { isValid: false, message: "Email is required" };
        if (!form.telephone)
            return { isValid: false, message: "Phone number is required" };
        return { isValid: true };
    } else if (step === 2) {
        if (!form.gender)
            return { isValid: false, message: "Gender is required" };
        if (!form.date_of_birth)
            return { isValid: false, message: "Date of birth is required" };
        if (!form.national_id)
            return { isValid: false, message: "ID number is required" };
        if (!form.ethnicity_id)
            return { isValid: false, message: "Ethnicity is required" };
        if (!form.religion_id)
            return { isValid: false, message: "Religion is required" };
        /* if (!form.disability_status)
            return { isValid: false, message: "Disability status is required" }; */
        if (form.disability_status === "yes" && !form.plwd_number) {
            return {
                isValid: false,
                message:
                    "PLWD number is required for persons with disabilities",
            };
        }
        return { isValid: true };
    } else if (step === 3) {
        if (!form.county_id)
            return { isValid: false, message: "County is required" };
        if (!form.sub_county_id)
            return { isValid: false, message: "Sub-county is required" };
        if (!form.constituency_id)
            return { isValid: false, message: "Constituency is required" };
        if (!form.ward_id)
            return { isValid: false, message: "Ward is required" };
        return { isValid: true };
    }
    return { isValid: true };
};

const nextStep = async () => {
    const { isValid, message } = validateStep(currentStep.value);
    if (!isValid) {
        showToast("error", "Validation Error", message);
        return false;
    }

    if (currentStep.value < totalSteps) {
        currentStep.value++;
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
    return true;
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};

const handleSubmit = async () => {
    if (currentStep.value < totalSteps) {
        const success = await nextStep();
        if (success) {
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    } else {
        await submit();
    }
};

const submit = async () => {
    // Final validation before submission
    for (let i = 1; i <= totalSteps; i++) {
        const { isValid, message } = validateStep(i);
        if (!isValid) {
            showToast("error", "Validation Error", `Step ${i}: ${message}`);
            currentStep.value = i;
            window.scrollTo({ top: 0, behavior: "smooth" });
            return;
        }
    }

    if (!form.terms) {
        showToast(
            "error",
            "Terms Required",
            "You must agree to the terms and conditions"
        );
        return;
    }

    try {
        form.processing = true;
        const cleanData = cleanFormData(form);

        const response = await axios.post(route("register"), cleanData, {
            headers: { "Content-Type": "multipart/form-data" },
            onUploadProgress: (progressEvent) => {
                uploadProgress.value = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total
                );
            },
        });

        showToast(
            "success",
            "Success",
            "Registration successful! Redirecting..."
        );
        setTimeout(() => {
            window.location.href = route("dashboard");
        }, 2000);
    } catch (error) {
        let errorMessage = "An error occurred during registration";
        if (error.response?.data?.errors) {
            errorMessage =
                Object.values(error.response.data.errors)[0]?.[0] ||
                errorMessage;
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        } else if (!error.response) {
            errorMessage =
                "No response from server. Please check your connection.";
        }
        showToast("error", "Registration Failed", errorMessage);
        console.error("Submission error:", errorMessage, error);
    } finally {
        form.processing = false;
    }
};

const getSelectedLabel = (value, options) => {
    const option = options.find((opt) => opt.value === value);
    return option ? option.label : "Not specified";
};

const getLocationName = (id, locations) => {
    const location = locations.find((loc) => loc.id == id);
    return location ? toTitleCase(location.name) : "Not specified";
};

const getItemName = (id, items) => {
    const item = items.find((item) => item.id == id);
    return item ? toTitleCase(item.name) : "Not specified";
};

const toTitleCase = (str) => {
    return str.replace(/\b\w/g, (match) => match.toUpperCase());
};

const cleanFormData = (formData) => {
    const cleanData = { ...formData };

    // List of fields that should be in the final submission
    const allowedFields = [
        "name",
        "email",
        "gender",
        "date_of_birth",
        "disability_status",
        "plwd_number",
        "ethnicity_id",
        "religion_id",
        "national_id",
        "telephone",
        "county_id",
        "sub_county_id",
        "constituency_id",
        "ward_id",
        "terms",
    ];

    // Remove any fields not in the allowed list
    Object.keys(cleanData).forEach((key) => {
        if (!allowedFields.includes(key)) {
            delete cleanData[key];
        }
    });

    // Ensure boolean values are properly formatted
    if (cleanData.terms === "true") cleanData.terms = true;
    if (cleanData.terms === "false") cleanData.terms = false;

    return cleanData;
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

                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <!-- Step 1: Basic Details -->
                        <div v-if="currentStep === 1" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 1: Basic account information
                            </h2>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <InputLabel
                                        for="name"
                                        value="Full name"
                                        class="block text-sm font-medium text-gray-700"
                                    />
                                    <div class="ml-1 group relative">
                                        <i
                                            class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                        ></i>
                                        <div
                                            class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                        >
                                            This is the name that will be
                                            displayed on your profile.
                                        </div>
                                    </div>

                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </div>
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                    placeholder="Enter your Full name"
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

                                    <div class="ml-1 group relative">
                                        <i
                                            class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                        ></i>
                                        <div
                                            class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                        >
                                            This is the email address that will
                                            be used to send you notifications.
                                        </div>
                                    </div>

                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
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
                                    <div class="ml-1 group relative">
                                        <i
                                            class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                        ></i>
                                        <div
                                            class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                        >
                                            This is the telephone number that
                                            will be used to send you
                                            notifications.
                                        </div>
                                    </div>

                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
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
                        </div>

                        <!-- Step 2: Personal Information -->
                        <div v-if="currentStep === 2" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 2: Personal details
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="gender"
                                            value="Gender"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your gender as it appears
                                                on official documents.
                                            </div>
                                        </div>

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
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="date_of_birth"
                                            value="Date of Birth"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Enter your date of birth as it
                                                appears on official documents.
                                            </div>
                                        </div>

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
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="national_id"
                                            value="National ID Number"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Enter your national
                                                identification number as it
                                                appears on official documents.
                                            </div>
                                        </div>

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
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="ethnicity_id"
                                            value="Ethnicity"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your ethnicity as it
                                                appears on official documents.
                                            </div>
                                        </div>

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
                                                    >{{ description }}</span
                                                >
                                            </div>
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.ethnicity_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="religion_id"
                                            value="Religion"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your religion as it
                                                appears on official documents.
                                            </div>
                                        </div>

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
                                                    >{{ description }}</span
                                                >
                                            </div>
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.religion_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="disability_status"
                                            value="Disability Status"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your disability status as
                                                it appears on official
                                                documents.
                                            </div>
                                        </div>
                                    </div>
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
                                <div v-if="showPlwdNumber" class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="plwd_number"
                                            value="PLWD Registration Number"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Enter your PLWD registration
                                                number as it appears on official
                                                documents.
                                            </div>
                                        </div>

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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="county_id"
                                            value="County"
                                            class="block text-sm font-medium text-gray-700"
                                        />
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

                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.county_id"
                                        :options="counties"
                                        placeholder="Select county"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'opacity-50': loadingSubCounties,
                                        }"
                                        :loading="loadingSubCounties"
                                        :disabled="loadingSubCounties"
                                        loading-text="Loading..."
                                        :clearable="true"
                                        required
                                    >
                                        <template #no-options>
                                            {{
                                                loadingSubCounties
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
                                        <div class="ml-1 group relative">
                                            <i
                                                class="fas fa-info-circle text-gray-400 hover:text-gray-500 cursor-pointer"
                                            ></i>
                                            <div
                                                class="hidden group-hover:block absolute z-10 mt-1 w-64 p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded shadow-lg"
                                            >
                                                Select your sub-county of
                                                residence. This helps us provide
                                                location-specific services.
                                            </div>
                                        </div>

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

                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
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

                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
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
                        </div>

                        <!-- Step 4: Confirmation -->
                        <div v-if="currentStep === 4" class="space-y-6">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 4: Please verify all the information before
                                submitting
                            </h2>
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
                                                activeAccordion === 'personal',
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
                                                {{ form.name }}
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
                                                    form.gender
                                                        ? getSelectedLabel(
                                                              form.gender,
                                                              [
                                                                  {
                                                                      value: "male",
                                                                      label: "Male",
                                                                  },
                                                                  {
                                                                      value: "female",
                                                                      label: "Female",
                                                                  },
                                                                  {
                                                                      value: "other",
                                                                      label: "Other",
                                                                  },
                                                              ]
                                                          )
                                                        : "Not specified"
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
                                                    form.national_id ||
                                                    "Not specified"
                                                }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
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
                                                activeAccordion === 'contact',
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
                                                    form.postal_address ||
                                                    "Not specified"
                                                }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
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
                                                activeAccordion === 'location',
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
                                    </dl>
                                </div>
                            </div>
                            <div
                                class="p-4 sm:p-6 bg-white rounded-lg shadow border border-gray-200"
                            >
                                <div
                                    class="flex flex-col sm:flex-row items-start space-y-3 sm:space-y-0 sm:space-x-4"
                                >
                                    <div class="flex items-start pt-1">
                                        <Checkbox
                                            id="terms"
                                            v-model:checked="form.terms"
                                            required
                                            class="mt-0.5"
                                        />
                                    </div>
                                    <div class="text-sm flex-1">
                                        <InputLabel
                                            for="terms"
                                            class="font-medium text-gray-700 block"
                                        >
                                            <div class="flex flex-wrap">
                                                <span class="mr-1 w-full">
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
                                                        class="text-emerald-600 hover:text-emerald-500 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                    >
                                                        Terms of Service
                                                    </a>
                                                    <span class="mx-1"
                                                        >and</span
                                                    >
                                                    <a
                                                        href="#"
                                                        target="_blank"
                                                        class="text-emerald-600 hover:text-emerald-500 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                    >
                                                        Privacy Policy
                                                    </a>
                                                </span>
                                            </div>
                                        </InputLabel>
                                        <p
                                            class="text-xs text-gray-500 mt-2 sm:mt-1"
                                        >
                                            You must agree to the terms and
                                            conditions to create an account.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shared Navigation Buttons -->
                        <div class="flex items-center justify-between pt-6">
                            <button
                                v-if="currentStep > 1"
                                type="button"
                                @click="prevStep"
                                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400 transition-colors duration-200"
                            >
                                <i
                                    class="fas fa-arrow-left mr-2 text-emerald-600"
                                ></i>
                                <span class="hidden sm:inline">Previous</span>
                            </button>
                            <div v-else class="w-24"></div>
                            <!-- Spacer for alignment -->

                            <button
                                type="submit"
                                :disabled="
                                    form.processing ||
                                    (currentStep === totalSteps && !form.terms)
                                "
                                class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                                :class="{
                                    'opacity-75 cursor-not-allowed':
                                        form.processing ||
                                        (currentStep === totalSteps &&
                                            !form.terms),
                                    'shadow-emerald-200':
                                        !form.processing &&
                                        (currentStep !== totalSteps ||
                                            form.terms),
                                }"
                            >
                                <i
                                    v-if="form.processing"
                                    class="fas fa-spinner fa-spin mr-2"
                                ></i>
                                <template v-else>
                                    <span class="hidden sm:inline">
                                        {{
                                            currentStep === totalSteps
                                                ? "Submit Application"
                                                : "Next: " +
                                                  stepNames[currentStep]
                                        }}
                                    </span>
                                    <span class="sm:hidden">{{
                                        currentStep === totalSteps
                                            ? "Submit"
                                            : "Next"
                                    }}</span>
                                    <i
                                        :class="
                                            currentStep === totalSteps
                                                ? 'fas fa-check ml-2'
                                                : 'fas fa-arrow-right ml-2'
                                        "
                                    ></i>
                                </template>
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">
                                    Step {{ currentStep }} of {{ totalSteps }}:
                                    {{ stepDescription }}
                                </span>
                                <span class="text-sm font-medium text-gray-700">
                                    {{
                                        Math.round(
                                            (currentStep / totalSteps) * 100
                                        )
                                    }}% Complete
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div
                                    class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300"
                                    :style="{
                                        width: `${
                                            (currentStep / totalSteps) * 100
                                        }%`,
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Sign In Link -->
                        <div class="mt-6">
                            <p
                                class="text-sm font-light text-gray-500 dark:text-gray-400"
                            >
                                Already have an account?
                                <Link
                                    :href="route('login')"
                                    class="text-emerald-600 hover:text-emerald-500 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 rounded"
                                >
                                    Sign in
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
