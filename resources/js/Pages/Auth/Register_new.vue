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

// Define props
const props = defineProps({
    countries: Array,
    genders: Object,
});

// State for collapsible sections
const expandedSections = ref({
    personal: true,
    contact: false,
    education: false,
    security: false,
    documents: false,
    location: false, // New section for location details
});

const activeSection = ref("personal");

// Form state
const form = useForm({
    // Basic Details
    name: "",
    email: "",
    telephone: "",
    phoneNumber: "",
    phoneCountryCode: "254",
    password: "",
    password_confirmation: "",

    // Role and Identification
    role: "citizen",
    nationality: "Kenya",
    idNumber: "",
    idType: "national_id",

    // Personal Details
    first_name: "",
    middle_name: "",
    last_name: "",
    gender: "male",
    date_of_birth: "",

    // Contact Information
    address_line_1: "",
    address_line_2: "",
    city: "",
    state: "",

    // Location Details
    country_id: null,
    region_id: null,
    county_id: null,
    sub_county_id: null,
    constituency_id: null,
    ward_id: null,
    location_id: null,
    village_id: null,

    // Refugee-specific
    reason_for_refugee: "",

    // Education and Employment
    education_level: "",
    occupation: "",
    employer_details: "",

    // Document Uploads
    proof_of_identity: null,
    proof_of_identity_type: "national_id",
    proof_of_address: null,
    proof_of_address_type: "utility_bill",

    // Security
    security_question: "",
    security_answer: "",

    // Terms
    terms: false,
});

// Location data
const locations = ref({
    regions: [],
    counties: [],
    sub_counties: [],
    constituencies: [],
    wards: [],
    locations: [],
    villages: [],
});

// Loading states
const loading = ref({
    regions: false,
    counties: false,
    sub_counties: false,
    constituencies: false,
    wards: false,
    locations: false,
    villages: false,
    submit: false,
});

// Password strength
const passwordStrength = ref("");

const checkPasswordStrength = () => {
    const password = form.password;
    if (!password) {
        passwordStrength.value = "";
        return;
    }
    const hasLetters = /[a-zA-Z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const isLongEnough = password.length >= 8;
    passwordStrength.value =
        isLongEnough && hasLetters && hasNumbers && hasSpecial
            ? "strong"
            : isLongEnough &&
              ((hasLetters && hasNumbers) ||
                  (hasLetters && hasSpecial) ||
                  (hasNumbers && hasSpecial))
            ? "medium"
            : "weak";
};

// File upload handling
const uploadProgress = ref(0);
const dragOver = ref(false);

const handleFileDrop = (event, field) => {
    dragOver.value = false;
    const file = event.dataTransfer.files[0];
    if (file) processFile(file, field);
};

const processFile = (file, field) => {
    const validTypes = ["application/pdf", "image/jpeg", "image/png"];
    const maxSize = 5 * 1024 * 1024;
    if (!validTypes.includes(file.type)) {
        form.setError(
            field,
            "Invalid file type. Please upload a PDF, JPG, or PNG file."
        );
        window.Toast.fire({
            icon: "error",
            title: "Invalid file",
            text: "Please upload a valid document.",
        });
        return false;
    }
    if (file.size > maxSize) {
        form.setError(field, "File size must be less than 5MB");
        window.Toast.fire({
            icon: "error",
            title: "File too large",
            text: "File size exceeds the maximum limit of 5MB.",
        });
        return false;
    }
    const fileInfo = {
        file,
        name: file.name,
        size: file.size,
        type: file.type,
        preview: null,
    };
    if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = (e) => {
            fileInfo.preview = e.target.result;
            form[field] = { ...fileInfo };
        };
        reader.readAsDataURL(file);
    } else {
        form[field] = { ...fileInfo };
    }
    return true;
};

const handleFileUpload = (event, field) => {
    const file = event.target.files[0];
    if (!file) return;
    event.target.value = "";
    form.clearErrors(field);
    form[field] = null;
    processFile(file, field);
};

// Step navigation
const currentStep = ref(1);
const stepDescription = computed(() => {
    const descriptions = [
        "Enter profile details to proceed",
        "Select your role and provide identification details",
        "Provide additional personal details",
        "Add your contact information",
        "Select your location details",
        "Education and employment details",
        "Security information",
        "Upload required documents",
        "Review your details and confirm",
    ];
    return descriptions[currentStep.value - 1] || "";
});

// Computed properties
const idField = computed(() => {
    const roleToField = {
        citizen: "national_id",
        resident: "alien_id",
        refugee: "refugee_id",
        diplomat: "diplomat_id",
        foreigner: "passport_number",
    };
    return roleToField[form.role] || "id_number";
});

const idLabel = computed(() => {
    const roleToLabel = {
        citizen: "National ID Number",
        resident: "Alien ID Number",
        refugee: "Refugee ID Number",
        diplomat: "Diplomat ID Number",
        foreigner: "Passport Number",
    };
    return roleToLabel[form.role] || "ID Number";
});

const educationLevels = [
    "Primary School",
    "Secondary School",
    "High School",
    "Certificate",
    "Diploma",
    "Bachelor's Degree",
    "Master's Degree",
    "Doctorate",
    "Other",
];

const securityQuestions = [
    "What was your first pet's name?",
    "What was the name of your first school?",
    "What is your mother's maiden name?",
    "What city were you born in?",
    "What is your favorite book?",
    "What was the make of your first car?",
];

const countryCodes = [
    { code: "KE", name: "Kenya (+254)" },
    { code: "US", name: "USA (+1)" },
    { code: "GB", name: "UK (+44)" },
    { code: "CA", name: "Canada (+1)" },
    { code: "AU", name: "Australia (+61)" },
];

// Location fetching
const fetchLocations = async (type, parentId = null) => {
    loading.value[type] = true;
    try {
        const response = await axios.get(`/api/${type}`, {
            params: { parent_id: parentId },
        });
        locations.value[type] = response.data;
    } catch (error) {
        window.Toast.fire({
            icon: "error",
            title: `Failed to load ${type}`,
            text: error.message,
        });
    } finally {
        loading.value[type] = false;
    }
};

// Update phone number
const updatePhoneNumber = () => {
    if (form.phoneNumber) {
        form.telephone = `+${form.phoneCountryCode}${form.phoneNumber.replace(
            /^0+/,
            ""
        )}`;
    } else {
        form.telephone = "";
    }
};

// Initialize
onMounted(() => {
    if (form.telephone) {
        const match = form.telephone.match(/^\+(\d+)(\d+)$/);
        if (match) {
            form.phoneCountryCode = match[1];
            form.phoneNumber = match[2];
        }
    }
    fetchLocations("regions");
});

// Watchers
watch(() => form.phoneNumber, updatePhoneNumber);
watch(() => form.phoneCountryCode, updatePhoneNumber);
watch(
    () => form.country_id,
    () => {
        form.region_id = null;
        form.county_id = null;
        form.sub_county_id = null;
        form.constituency_id = null;
        form.ward_id = null;
        form.location_id = null;
        form.village_id = null;
        fetchLocations("regions", form.country_id);
    }
);
watch(
    () => form.region_id,
    () => {
        form.county_id = null;
        form.sub_county_id = null;
        form.constituency_id = null;
        form.ward_id = null;
        form.location_id = null;
        form.village_id = null;
        fetchLocations("counties", form.region_id);
    }
);
watch(
    () => form.county_id,
    () => {
        form.sub_county_id = null;
        form.constituency_id = null;
        form.ward_id = null;
        form.location_id = null;
        form.village_id = null;
        fetchLocations("sub_counties", form.county_id);
        fetchLocations("constituencies", form.county_id);
    }
);
watch(
    () => form.sub_county_id,
    () => {
        form.ward_id = null;
        form.location_id = null;
        form.village_id = null;
        fetchLocations("wards", form.sub_county_id);
    }
);
watch(
    () => form.constituency_id,
    () => {
        form.ward_id = null;
        form.location_id = null;
        form.village_id = null;
        fetchLocations("wards", form.constituency_id);
    }
);
watch(
    () => form.ward_id,
    () => {
        form.location_id = null;
        form.village_id = null;
        fetchLocations("locations", form.ward_id);
    }
);
watch(
    () => form.location_id,
    () => {
        form.village_id = null;
        fetchLocations("villages", form.location_id);
    }
);
watch(
    () => form.role,
    () => {
        if (form.role === "citizen") form.nationality = "Kenya";
        form.idNumber = "";
    }
);

// Navigation
const nextStep = () => {
    let isValid = true;
    if (currentStep.value === 1) {
        if (
            !form.name ||
            !form.email ||
            !form.telephone ||
            !form.password ||
            !form.password_confirmation
        ) {
            form.setError("form", "Please fill in all required fields");
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please complete all required fields.",
            });
            isValid = false;
        } else if (form.password !== form.password_confirmation) {
            form.setError("password_confirmation", "Passwords do not match");
            window.Toast.fire({
                icon: "error",
                title: "Password Mismatch",
                text: "The passwords you entered do not match.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 2) {
        if (
            !form.role ||
            !form.idNumber ||
            (form.role !== "citizen" && !form.nationality)
        ) {
            form.setError("form", "Please fill in all required fields");
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please complete all required fields.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 3) {
        if (
            !form.first_name ||
            !form.last_name ||
            !form.gender ||
            !form.date_of_birth
        ) {
            form.setError("form", "Please fill in all required fields");
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please complete all required fields.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 4) {
        if (!form.address_line_1 || !form.city || !form.state) {
            form.setError("form", "Please fill in all required address fields");
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please complete all required address fields.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 5) {
        if (
            !form.country_id ||
            !form.region_id ||
            !form.county_id ||
            !form.constituency_id ||
            !form.ward_id
        ) {
            form.setError("form", "Please select all required location fields");
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please select all required location fields.",
            });
            isValid = false;
        } else if (form.role === "refugee" && !form.reason_for_refugee) {
            form.setError(
                "reason_for_refugee",
                "Please provide the reason for refugee status"
            );
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please provide the reason for refugee status.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 6) {
        if (!form.education_level || !form.occupation) {
            form.setError("form", "Please fill in all required fields");
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please complete all required fields.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 7) {
        if (!form.security_question || !form.security_answer) {
            form.setError(
                "form",
                "Please provide a security question and answer"
            );
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please provide a security question and answer.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 8) {
        if (!form.proof_of_identity || !form.proof_of_address) {
            form.setError("form", "Please upload all required documents");
            window.Toast.fire({
                icon: "warning",
                title: "Documents Required",
                text: "Please upload all required documents.",
            });
            isValid = false;
        }
    }
    if (isValid && currentStep.value < 9) currentStep.value++;
};

const previousStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const resetForm = () => {
    form.reset();
    currentStep.value = 1;
};

// Submit form
const submit = () => {
    loading.value.submit = true;
    const formData = new FormData();
    Object.keys(form).forEach((key) => {
        if (
            typeof form[key] !== "function" &&
            !key.startsWith("_") &&
            ![
                "proof_of_identity",
                "proof_of_address",
                "errors",
                "hasErrors",
                "processing",
                "recentlySuccessful",
            ].includes(key)
        ) {
            formData.append(key, form[key]);
        }
    });
    if (form.proof_of_identity?.file)
        formData.append("proof_of_identity", form.proof_of_identity.file);
    if (form.proof_of_address?.file)
        formData.append("proof_of_address", form.proof_of_address.file);
    form.post(route("register"), {
        forceFormData: true,
        preserveState: true,
        onProgress: (progress) => {
            uploadProgress.value = Math.round(
                (progress.loaded / progress.total) * 100
            );
        },
        onSuccess: () => {
            window.Toast.fire({
                icon: "success",
                title: "Registration successful!",
                text: "Please check your email for verification.",
            });
            form.reset("password", "password_confirmation");
            window.location.href = route("dashboard");
        },
        onError: (errors) => {
            console.error("Registration failed:", errors);
            const firstError = Object.values(errors)[0];
            if (firstError) {
                window.Toast.fire({
                    icon: "error",
                    title: "Error",
                    text: Array.isArray(firstError)
                        ? firstError[0]
                        : firstError,
                });
            }
            const firstErrorField = Object.keys(errors)[0];
            if (firstErrorField) {
                const element = document.getElementById(firstErrorField);
                if (element) {
                    element.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    element.focus();
                }
            }
        },
        onFinish: () => {
            loading.value.submit = false;
            uploadProgress.value = 0;
        },
    });
};

// Toggle accordion sections
const toggleSection = (section) => {
    Object.keys(expandedSections.value).forEach((key) => {
        expandedSections.value[key] =
            key === section ? !expandedSections.value[key] : false;
    });
    activeSection.value = expandedSections.value[section] ? section : null;
};

// Format utilities
const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

const formatDocumentType = (type) => {
    const types = {
        national_id: "National ID",
        passport: "Passport",
        driving_license: "Driving License",
        utility_bill: "Utility Bill",
        bank_statement: "Bank Statement",
        rental_agreement: "Rental Agreement",
        other: "Other Document",
    };
    return types[type] || type;
};
</script>

<template>
    <Head title="Register" />
    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>
        <div class="p-2 space-y-4 sm:p-8">
            <h1
                class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white"
            >
                Sign Up for an account
            </h1>
            <form
                @submit.prevent="submit"
                class="space-y-2"
                enctype="multipart/form-data"
            >
                <!-- Step 1: Basic Details -->
                <div v-if="currentStep === 1" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div>
                        <InputLabel for="name" value="Username" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Enter your username"
                            class="mt-1 block w-full"
                            required
                            autofocus
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="your.email@example.com"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="telephone" value="Telephone" />
                        <div class="flex space-x-2">
                            <select
                                v-model="form.phoneCountryCode"
                                class="mt-1 w-1/3 rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            >
                                <option
                                    v-for="country in countryCodes"
                                    :key="country.code"
                                    :value="
                                        country.code === 'US' ||
                                        country.code === 'CA'
                                            ? '1'
                                            : country.code === 'GB'
                                            ? '44'
                                            : country.code === 'AU'
                                            ? '61'
                                            : '254'
                                    "
                                >
                                    {{ country.name }}
                                </option>
                            </select>
                            <TextInput
                                id="phoneNumber"
                                v-model="form.phoneNumber"
                                type="tel"
                                placeholder="e.g. 712345678"
                                class="mt-1 flex-1"
                                required
                                autocomplete="tel"
                            />
                            <input
                                type="hidden"
                                name="telephone"
                                v-model="form.telephone"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Enter your phone number with country code
                        </p>
                        <InputError
                            :message="
                                form.errors.telephone ||
                                form.errors.phoneCountryCode
                            "
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Create a strong password"
                            class="mt-1 block w-full"
                            required
                            @input="checkPasswordStrength"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Use at least 8 characters with a mix of letters,
                            numbers, and symbols
                        </p>
                        <p
                            v-if="passwordStrength"
                            class="mt-1 text-xs"
                            :class="{
                                'text-green-500': passwordStrength === 'strong',
                                'text-yellow-500':
                                    passwordStrength === 'medium',
                                'text-red-500': passwordStrength === 'weak',
                            }"
                        >
                            Password strength: {{ passwordStrength }}
                        </p>
                        <InputError
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="password_confirmation"
                            value="Confirm Password"
                        />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Re-enter your password"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.password_confirmation"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Step 2: Role Selection -->
                <div v-if="currentStep === 2" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div>
                        <InputLabel for="role" value="Role" />
                        <select
                            v-model="form.role"
                            id="role"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        >
                            <option value="" disabled selected>
                                Select your role
                            </option>
                            <option value="citizen">Citizen</option>
                            <option value="resident">Resident</option>
                            <option value="refugee">Refugee</option>
                            <option value="diplomat">Diplomat</option>
                            <option value="foreigner">Foreigner</option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="nationality" value="Nationality" />
                        <VueSelect
                            v-model="form.nationality"
                            :options="
                                form.role === 'citizen'
                                    ? ['Kenya']
                                    : countries.map((c) => c.name)
                            "
                            placeholder="Select your nationality"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.nationality,
                                'bg-gray-100 cursor-not-allowed':
                                    form.role === 'citizen',
                            }"
                            :disabled="form.role === 'citizen'"
                        />
                        <InputError
                            :message="form.errors.nationality"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel :for="idField" :value="idLabel" />
                        <TextInput
                            :id="idField"
                            v-model="form.idNumber"
                            type="text"
                            :placeholder="
                                form.role === 'citizen'
                                    ? 'e.g. 12345678'
                                    : 'Enter your ID or passport number'
                            "
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.idNumber"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Step 3: Personal Information -->
                <div v-if="currentStep === 3" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="first_name" value="First Name" />
                            <TextInput
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                placeholder="Enter your first name"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.first_name"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <InputLabel
                                for="middle_name"
                                value="Middle Name (Optional)"
                            />
                            <TextInput
                                id="middle_name"
                                v-model="form.middle_name"
                                type="text"
                                placeholder="Enter your middle name (if any)"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="form.errors.middle_name"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="last_name" value="Last Name" />
                            <TextInput
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                placeholder="Enter your last name"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.last_name"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <InputLabel for="gender" value="Gender" />
                            <select
                                id="gender"
                                v-model="form.gender"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                required
                            >
                                <option value="" disabled selected>
                                    Select your gender
                                </option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="prefer_not_to_say">
                                    Prefer not to say
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.gender"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="date_of_birth" value="Date of Birth" />
                        <TextInput
                            id="date_of_birth"
                            v-model="form.date_of_birth"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.date_of_birth"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Step 4: Address Information -->
                <div v-if="currentStep === 4" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div>
                        <InputLabel
                            for="address_line_1"
                            value="Address Line 1"
                        />
                        <TextInput
                            id="address_line_1"
                            v-model="form.address_line_1"
                            type="text"
                            placeholder="Enter your address line 1"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.address_line_1"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="address_line_2"
                            value="Address Line 2 (Optional)"
                        />
                        <TextInput
                            id="address_line_2"
                            v-model="form.address_line_2"
                            type="text"
                            placeholder="Enter your address line 2"
                            class="mt-1 block w-full"
                        />
                        <InputError
                            :message="form.errors.address_line_2"
                            class="mt-2"
                        />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="city" value="City/Town" />
                            <TextInput
                                id="city"
                                v-model="form.city"
                                type="text"
                                placeholder="Enter your city"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.city"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <InputLabel for="state" value="State/County" />
                            <TextInput
                                id="state"
                                v-model="form.state"
                                type="text"
                                placeholder="Enter your state"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.state"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>

                <!-- Step 5: Location Details -->
                <div v-if="currentStep === 5" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div>
                        <InputLabel for="country_id" value="Country" />
                        <VueSelect
                            v-model="form.country_id"
                            :options="
                                countries.map((c) => ({
                                    value: c.id,
                                    label: c.name,
                                }))
                            "
                            placeholder="Select your country"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.country_id,
                            }"
                        />
                        <InputError
                            :message="form.errors.country_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="region_id" value="Region" />
                        <VueSelect
                            v-model="form.region_id"
                            :options="
                                locations.regions.map((r) => ({
                                    value: r.id,
                                    label: r.name,
                                }))
                            "
                            placeholder="Select your region"
                            class="mt-1 block w-full"
                            :class="{ 'border-red-500': form.errors.region_id }"
                            :disabled="!form.country_id || loading.regions"
                        />
                        <InputError
                            :message="form.errors.region_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="county_id" value="County" />
                        <VueSelect
                            v-model="form.county_id"
                            :options="
                                locations.counties.map((c) => ({
                                    value: c.id,
                                    label: c.name,
                                }))
                            "
                            placeholder="Select your county"
                            class="mt-1 block w-full"
                            :class="{ 'border-red-500': form.errors.county_id }"
                            :disabled="!form.region_id || loading.counties"
                        />
                        <InputError
                            :message="form.errors.county_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="sub_county_id" value="Sub-County" />
                        <VueSelect
                            v-model="form.sub_county_id"
                            :options="
                                locations.sub_counties.map((s) => ({
                                    value: s.id,
                                    label: s.name,
                                }))
                            "
                            placeholder="Select your sub-county"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.sub_county_id,
                            }"
                            :disabled="!form.county_id || loading.sub_counties"
                        />
                        <InputError
                            :message="form.errors.sub_county_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="constituency_id"
                            value="Constituency"
                        />
                        <VueSelect
                            v-model="form.constituency_id"
                            :options="
                                locations.constituencies.map((c) => ({
                                    value: c.id,
                                    label: c.name,
                                }))
                            "
                            placeholder="Select your constituency"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.constituency_id,
                            }"
                            :disabled="
                                !form.county_id || loading.constituencies
                            "
                        />
                        <InputError
                            :message="form.errors.constituency_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="ward_id" value="Ward" />
                        <VueSelect
                            v-model="form.ward_id"
                            :options="
                                locations.wards.map((w) => ({
                                    value: w.id,
                                    label: w.name,
                                }))
                            "
                            placeholder="Select your ward"
                            class="mt-1 block w-full"
                            :class="{ 'border-red-500': form.errors.ward_id }"
                            :disabled="
                                !form.sub_county_id ||
                                !form.constituency_id ||
                                loading.wards
                            "
                        />
                        <InputError
                            :message="form.errors.ward_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="location_id"
                            value="Location (Optional)"
                        />
                        <VueSelect
                            v-model="form.location_id"
                            :options="
                                locations.locations.map((l) => ({
                                    value: l.id,
                                    label: l.name,
                                }))
                            "
                            placeholder="Select your location"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.location_id,
                            }"
                            :disabled="!form.ward_id || loading.locations"
                        />
                        <InputError
                            :message="form.errors.location_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="village_id"
                            value="Village (Optional)"
                        />
                        <VueSelect
                            v-model="form.village_id"
                            :options="
                                locations.villages.map((v) => ({
                                    value: v.id,
                                    label: v.name,
                                }))
                            "
                            placeholder="Select your village"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.village_id,
                            }"
                            :disabled="!form.location_id || loading.villages"
                        />
                        <InputError
                            :message="form.errors.village_id"
                            class="mt-2"
                        />
                    </div>
                    <div v-if="form.role === 'refugee'">
                        <InputLabel
                            for="reason_for_refugee"
                            value="Reason for Refugee Status"
                        />
                        <TextInput
                            id="reason_for_refugee"
                            v-model="form.reason_for_refugee"
                            type="text"
                            placeholder="Enter reason for refugee status"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.reason_for_refugee"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Step 6: Education & Employment -->
                <div v-if="currentStep === 6" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div>
                        <InputLabel
                            for="education_level"
                            value="Highest Education Level"
                        />
                        <select
                            id="education_level"
                            v-model="form.education_level"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        >
                            <option value="" disabled selected>
                                Select education level
                            </option>
                            <option
                                v-for="level in educationLevels"
                                :key="level"
                                :value="level"
                            >
                                {{ level }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.education_level"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="occupation"
                            value="Current Occupation"
                        />
                        <TextInput
                            id="occupation"
                            v-model="form.occupation"
                            type="text"
                            placeholder="e.g. Software Developer, Teacher"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.occupation"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="employer_details"
                            value="Employer Details"
                        />
                        <textarea
                            id="employer_details"
                            v-model="form.employer_details"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            placeholder="Current employer name, position, and address"
                        ></textarea>
                        <InputError
                            :message="form.errors.employer_details"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Step 7: Security Information -->
                <div v-if="currentStep === 7" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div>
                        <InputLabel
                            for="security_question"
                            value="Security Question"
                        />
                        <select
                            id="security_question"
                            v-model="form.security_question"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        >
                            <option value="" disabled selected>
                                Select a security question
                            </option>
                            <option
                                v-for="question in securityQuestions"
                                :key="question"
                                :value="question"
                            >
                                {{ question }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.security_question"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="security_answer" value="Your Answer" />
                        <TextInput
                            id="security_answer"
                            v-model="form.security_answer"
                            type="text"
                            placeholder="Enter your answer"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.security_answer"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Step 8: Document Uploads -->
                <div v-if="currentStep === 8" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div
                        class="border border-dashed border-gray-300 rounded-lg p-4"
                    >
                        <InputLabel
                            for="proof_of_identity"
                            value="Proof of Identity"
                        />
                        <div class="mt-4 space-y-2">
                            <div class="flex space-x-2">
                                <select
                                    v-model="form.proof_of_identity_type"
                                    class="w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                >
                                    <option value="national_id">
                                        National ID
                                    </option>
                                    <option value="passport">Passport</option>
                                    <option value="driving_license">
                                        Driving License
                                    </option>
                                    <option value="other">Other</option>
                                </select>
                                <input
                                    type="file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    @change="
                                        handleFileUpload(
                                            $event,
                                            'proof_of_identity'
                                        )
                                    "
                                    class="flex-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                            </div>
                            <div
                                v-if="form.proof_of_identity"
                                class="mt-2 p-3 border rounded-md bg-gray-50"
                            >
                                <div class="flex items-start space-x-3">
                                    <img
                                        v-if="
                                            form.proof_of_identity.preview &&
                                            form.proof_of_identity.type.startsWith(
                                                'image/'
                                            )
                                        "
                                        :src="form.proof_of_identity.preview"
                                        class="h-16 w-16 object-cover rounded"
                                        alt="ID Preview"
                                    />
                                    <div
                                        v-else
                                        class="flex-shrink-0 flex items-center justify-center h-16 w-16 bg-gray-200 rounded"
                                    >
                                        <i
                                            class="fas fa-file-alt text-gray-500 text-2xl"
                                        ></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 truncate"
                                        >
                                            {{ form.proof_of_identity.name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{
                                                formatFileSize(
                                                    form.proof_of_identity.size
                                                )
                                            }}
                                            • {{ form.proof_of_identity.type }}
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="form.proof_of_identity = null"
                                        class="text-red-600 hover:text-red-800"
                                        aria-label="Remove file"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <p v-else class="text-sm text-gray-500">
                                No file uploaded. Please upload a clear photo of
                                your ID.
                            </p>
                            <InputError
                                :message="form.errors.proof_of_identity"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div
                        class="border border-dashed border-gray-300 rounded-lg p-4"
                    >
                        <InputLabel
                            for="proof_of_address"
                            value="Proof of Address"
                        />
                        <div class="mt-4 space-y-2">
                            <div class="flex space-x-2">
                                <select
                                    v-model="form.proof_of_address_type"
                                    class="w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                >
                                    <option value="utility_bill">
                                        Utility Bill
                                    </option>
                                    <option value="bank_statement">
                                        Bank Statement
                                    </option>
                                    <option value="rental_agreement">
                                        Rental Agreement
                                    </option>
                                    <option value="other">Other</option>
                                </select>
                                <input
                                    type="file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    @change="
                                        handleFileUpload(
                                            $event,
                                            'proof_of_address'
                                        )
                                    "
                                    class="flex-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                            </div>
                            <div
                                v-if="form.proof_of_address"
                                class="mt-2 p-3 border rounded-md bg-gray-50"
                            >
                                <div class="flex items-start space-x-3">
                                    <img
                                        v-if="
                                            form.proof_of_address.preview &&
                                            form.proof_of_address.type.startsWith(
                                                'image/'
                                            )
                                        "
                                        :src="form.proof_of_address.preview"
                                        class="h-16 w-16 object-cover rounded"
                                        alt="Address Proof Preview"
                                    />
                                    <div
                                        v-else
                                        class="flex-shrink-0 flex items-center justify-center h-16 w-16 bg-gray-200 rounded"
                                    >
                                        <i
                                            class="fas fa-file-alt text-gray-500 text-2xl"
                                        ></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 truncate"
                                        >
                                            {{ form.proof_of_address.name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{
                                                formatFileSize(
                                                    form.proof_of_address.size
                                                )
                                            }}
                                            • {{ form.proof_of_address.type }}
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="form.proof_of_address = null"
                                        class="text-red-600 hover:text-red-800"
                                        aria-label="Remove file"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <p v-else class="text-sm text-gray-500">
                                No file uploaded. Please upload a clear document
                                showing your current address.
                            </p>
                            <InputError
                                :message="form.errors.proof_of_address"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>

                <!-- Step 9: Confirmation -->
                <div v-if="currentStep === 9" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <!-- Personal Information -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm"
                        :class="
                            expandedSections.personal
                                ? 'min-h-[120px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('personal')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 cursor-pointer"
                            role="button"
                            tabindex="0"
                            @keydown.enter="toggleSection('personal')"
                            @keydown.space.prevent="toggleSection('personal')"
                        >
                            <h3
                                class="text-base sm:text-lg font-medium text-gray-900"
                            >
                                Personal Information
                            </h3>
                            <i
                                class="fas fa-chevron-down text-gray-500"
                                :class="{
                                    'transform rotate-180':
                                        expandedSections.personal,
                                }"
                            ></i>
                        </div>
                        <div
                            v-show="expandedSections.personal"
                            class="p-4 border-t border-gray-200"
                        >
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        Full Name
                                    </p>
                                    <p class="font-medium text-gray-900">
                                        {{ form.first_name }}
                                        {{ form.middle_name }}
                                        {{ form.last_name }}
                                    </p>
                                </div>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div v-if="form.date_of_birth">
                                        <p class="text-sm text-gray-500">
                                            Date of Birth
                                        </p>
                                        <p class="font-medium text-gray-900">
                                            {{ form.date_of_birth }}
                                        </p>
                                    </div>
                                    <div v-if="form.gender">
                                        <p class="text-sm text-gray-500">
                                            Gender
                                        </p>
                                        <p class="font-medium text-gray-900">
                                            {{ form.gender }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Contact Information -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm"
                        :class="
                            expandedSections.contact
                                ? 'min-h-[180px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('contact')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 cursor-pointer"
                            role="button"
                            tabindex="0"
                            @keydown.enter="toggleSection('contact')"
                            @keydown.space.prevent="toggleSection('contact')"
                        >
                            <h3
                                class="text-base sm:text-lg font-medium text-gray-900"
                            >
                                Contact Information
                            </h3>
                            <i
                                class="fas fa-chevron-down text-gray-500"
                                :class="{
                                    'transform rotate-180':
                                        expandedSections.contact,
                                }"
                            ></i>
                        </div>
                        <div
                            v-show="expandedSections.contact"
                            class="p-4 border-t border-gray-200 space-y-4"
                        >
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">
                                    {{ form.email }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium text-gray-900">
                                    {{ form.telephone }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="font-medium text-gray-900">
                                    {{ form.address_line_1 }}
                                    {{
                                        form.address_line_2
                                            ? ", " + form.address_line_2
                                            : ""
                                    }}, {{ form.city }}, {{ form.state }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Location Information -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm"
                        :class="
                            expandedSections.location
                                ? 'min-h-[180px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('location')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 cursor-pointer"
                            role="button"
                            tabindex="0"
                            @keydown.enter="toggleSection('location')"
                            @keydown.space.prevent="toggleSection('location')"
                        >
                            <h3
                                class="text-base sm:text-lg font-medium text-gray-900"
                            >
                                Location Information
                            </h3>
                            <i
                                class="fas fa-chevron-down text-gray-500"
                                :class="{
                                    'transform rotate-180':
                                        expandedSections.location,
                                }"
                            ></i>
                        </div>
                        <div
                            v-show="expandedSections.location"
                            class="p-4 border-t border-gray-200 space-y-4"
                        >
                            <div>
                                <p class="text-sm text-gray-500">Country</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        countries.find(
                                            (c) => c.id === form.country_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Region</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        locations.regions.find(
                                            (r) => r.id === form.region_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">County</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        locations.counties.find(
                                            (c) => c.id === form.county_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Sub-County</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        locations.sub_counties.find(
                                            (s) => s.id === form.sub_county_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">
                                    Constituency
                                </p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        locations.constituencies.find(
                                            (c) => c.id === form.constituency_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Ward</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        locations.wards.find(
                                            (w) => w.id === form.ward_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Location</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        locations.locations.find(
                                            (l) => l.id === form.location_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Village</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        locations.villages.find(
                                            (v) => v.id === form.village_id
                                        )?.name || "Not specified"
                                    }}
                                </p>
                            </div>
                            <div v-if="form.role === 'refugee'">
                                <p class="text-sm text-gray-500">
                                    Reason for Refugee Status
                                </p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        form.reason_for_refugee ||
                                        "Not specified"
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Education & Employment -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm"
                        :class="
                            expandedSections.education
                                ? 'min-h-[180px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('education')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 cursor-pointer"
                            role="button"
                            tabindex="0"
                            @keydown.enter="toggleSection('education')"
                            @keydown.space.prevent="toggleSection('education')"
                        >
                            <h3
                                class="text-base sm:text-lg font-medium text-gray-900"
                            >
                                Education & Employment
                            </h3>
                            <i
                                class="fas fa-chevron-down text-gray-500"
                                :class="{
                                    'transform rotate-180':
                                        expandedSections.education,
                                }"
                            ></i>
                        </div>
                        <div
                            v-show="expandedSections.education"
                            class="p-4 border-t border-gray-200"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        Education Level
                                    </p>
                                    <p class="font-medium">
                                        {{
                                            form.education_level ||
                                            "Not specified"
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">
                                        Occupation
                                    </p>
                                    <p class="font-medium">
                                        {{ form.occupation || "Not specified" }}
                                    </p>
                                </div>
                                <div
                                    v-if="form.employer_details"
                                    class="md:col-span-2"
                                >
                                    <p class="text-sm text-gray-500">
                                        Employer Details
                                    </p>
                                    <p class="font-medium whitespace-pre-line">
                                        {{ form.employer_details }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Security Information -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm"
                        :class="
                            expandedSections.security
                                ? 'min-h-[200px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('security')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 cursor-pointer"
                            role="button"
                            tabindex="0"
                            @keydown.enter="toggleSection('security')"
                            @keydown.space.prevent="toggleSection('security')"
                        >
                            <h3
                                class="text-base sm:text-lg font-medium text-gray-900"
                            >
                                Security Information
                            </h3>
                            <i
                                class="fas fa-chevron-down text-gray-500"
                                :class="{
                                    'transform rotate-180':
                                        expandedSections.security,
                                }"
                            ></i>
                        </div>
                        <div
                            v-show="expandedSections.security"
                            class="p-4 border-t border-gray-200"
                        >
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        Security Question
                                    </p>
                                    <p class="font-medium">
                                        {{
                                            form.security_question || "Not set"
                                        }}
                                    </p>
                                </div>
                                <div v-if="form.security_answer">
                                    <p class="text-sm text-gray-500">
                                        Security Answer
                                    </p>
                                    <p class="font-medium">
                                        {{ form.security_answer }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Document Uploads -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm"
                        :class="
                            expandedSections.documents
                                ? 'min-h-[180px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('documents')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 cursor-pointer"
                            role="button"
                            tabindex="0"
                            @keydown.enter="toggleSection('documents')"
                            @keydown.space.prevent="toggleSection('documents')"
                        >
                            <h3
                                class="text-base sm:text-lg font-medium text-gray-900"
                            >
                                Document Uploads
                            </h3>
                            <i
                                class="fas fa-chevron-down text-gray-500"
                                :class="{
                                    'transform rotate-180':
                                        expandedSections.documents,
                                }"
                            ></i>
                        </div>
                        <div
                            v-show="expandedSections.documents"
                            class="p-4 border-t border-gray-200 space-y-6"
                        >
                            <div class="flex space-x-4 px-2">
                                <div class="space-y-2 w-1/2 px-2">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Proof of Identity
                                    </p>
                                    <div
                                        v-if="form.proof_of_identity"
                                        class="w-full"
                                    >
                                        <div
                                            class="bg-white p-3 rounded-lg border border-gray-200"
                                        >
                                            <div class="flex justify-center">
                                                <img
                                                    v-if="
                                                        form.proof_of_identity
                                                            .preview &&
                                                        form.proof_of_identity.type.startsWith(
                                                            'image/'
                                                        )
                                                    "
                                                    :src="
                                                        form.proof_of_identity
                                                            .preview
                                                    "
                                                    class="h-40 w-auto max-w-full object-contain rounded"
                                                    alt="ID Proof Preview"
                                                />
                                                <div
                                                    v-else
                                                    class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border border-gray-200 text-center w-full"
                                                >
                                                    <i
                                                        class="fas fa-file-alt text-4xl text-gray-400 mb-2"
                                                    ></i>
                                                    <p
                                                        class="text-xs text-gray-500"
                                                    >
                                                        Document Preview
                                                    </p>
                                                    <p
                                                        class="text-xs text-gray-400 mt-1"
                                                    >
                                                        {{
                                                            form
                                                                .proof_of_identity
                                                                .name ||
                                                            "Document"
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-3 p-3 bg-gray-50 rounded-lg space-y-2"
                                        >
                                            <p
                                                class="text-sm font-medium text-gray-900 text-center"
                                            >
                                                {{
                                                    form.proof_of_identity_type ||
                                                    "Identity Document"
                                                }}
                                            </p>
                                            <div
                                                class="flex items-center justify-between text-xs text-gray-600"
                                            >
                                                <div
                                                    class="flex items-center space-x-2"
                                                >
                                                    <i
                                                        class="fas fa-file text-gray-400"
                                                    ></i
                                                    ><span
                                                        class="truncate max-w-[120px] sm:max-w-[180px]"
                                                        >{{
                                                            form
                                                                .proof_of_identity
                                                                .name ||
                                                            "No file name"
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex items-center space-x-2"
                                                >
                                                    <i
                                                        class="fas fa-save text-gray-400"
                                                    ></i
                                                    ><span>{{
                                                        formatFileSize(
                                                            form
                                                                .proof_of_identity
                                                                .size
                                                        ) || "N/A"
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p
                                        v-else
                                        class="text-sm text-gray-500 italic"
                                    >
                                        No identity document uploaded
                                    </p>
                                </div>
                                <div class="space-y-2 w-1/2 px-2">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Proof of Address
                                    </p>
                                    <div
                                        v-if="form.proof_of_address"
                                        class="w-full"
                                    >
                                        <div
                                            class="bg-white p-3 rounded-lg border border-gray-200"
                                        >
                                            <div class="flex justify-center">
                                                <img
                                                    v-if="
                                                        form.proof_of_address
                                                            .preview &&
                                                        form.proof_of_address.type.startsWith(
                                                            'image/'
                                                        )
                                                    "
                                                    :src="
                                                        form.proof_of_address
                                                            .preview
                                                    "
                                                    class="h-40 w-auto max-w-full object-contain rounded"
                                                    alt="Address Proof Preview"
                                                />
                                                <div
                                                    v-else
                                                    class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border border-gray-200 text-center w-full"
                                                >
                                                    <i
                                                        class="fas fa-file-alt text-4xl text-gray-400 mb-2"
                                                    ></i>
                                                    <p
                                                        class="text-xs text-gray-500"
                                                    >
                                                        Document Preview
                                                    </p>
                                                    <p
                                                        class="text-xs text-gray-400 mt-1"
                                                    >
                                                        {{
                                                            form
                                                                .proof_of_address
                                                                .name ||
                                                            "Document"
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-3 p-3 bg-gray-50 rounded-lg space-y-2"
                                        >
                                            <p
                                                class="text-sm font-medium text-gray-900 text-center"
                                            >
                                                {{
                                                    form.proof_of_address_type ||
                                                    "Address Document"
                                                }}
                                            </p>
                                            <div
                                                class="flex items-center justify-between text-xs text-gray-600"
                                            >
                                                <div
                                                    class="flex items-center space-x-2"
                                                >
                                                    <i
                                                        class="fas fa-file text-gray-400"
                                                    ></i
                                                    ><span
                                                        class="truncate max-w-[120px] sm:max-w-[180px]"
                                                        >{{
                                                            form
                                                                .proof_of_address
                                                                .name ||
                                                            "No file name"
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex items-center space-x-2"
                                                >
                                                    <i
                                                        class="fas fa-save text-gray-400"
                                                    ></i
                                                    ><span>{{
                                                        formatFileSize(
                                                            form
                                                                .proof_of_address
                                                                .size
                                                        ) || "N/A"
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p
                                        v-else
                                        class="text-sm text-gray-500 italic"
                                    >
                                        No address document uploaded
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-500 text-center">
                        Scroll to view all sections
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-8 pt-6">
                    <div class="flex items-center justify-between">
                        <button
                            v-if="currentStep > 1"
                            type="button"
                            @click="previousStep"
                            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400"
                        >
                            <i
                                class="fas fa-arrow-left mr-2 text-emerald-600"
                            ></i
                            ><span class="hidden sm:inline">Previous</span>
                        </button>
                        <div v-else class="w-24"></div>
                        <div class="flex items-center space-x-4">
                            <button
                                v-if="currentStep < 9"
                                type="button"
                                @click="nextStep"
                                :disabled="form.processing || loading.submit"
                                :class="[
                                    'inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2',
                                    currentStep === 8
                                        ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 shadow-emerald-200'
                                        : 'bg-teal-500 hover:bg-teal-600 focus:ring-teal-400 shadow-teal-200',
                                    form.processing || loading.submit
                                        ? 'opacity-75 cursor-not-allowed'
                                        : '',
                                ]"
                            >
                                <i
                                    v-if="form.processing || loading.submit"
                                    class="fas fa-spinner fa-spin mr-2"
                                ></i>
                                <span class="hidden sm:inline">{{
                                    currentStep === 8
                                        ? "Review & Submit"
                                        : "Next"
                                }}</span>
                                <span class="sm:hidden">{{
                                    currentStep === 8 ? "Review" : "Next"
                                }}</span>
                                <i
                                    v-if="!form.processing && !loading.submit"
                                    class="fas fa-arrow-right ml-2"
                                ></i>
                            </button>
                            <button
                                v-else
                                type="submit"
                                :disabled="form.processing || loading.submit"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-emerald-200"
                                :class="{
                                    'opacity-75 cursor-not-allowed':
                                        form.processing || loading.submit,
                                }"
                            >
                                <i
                                    v-if="form.processing || loading.submit"
                                    class="fas fa-spinner fa-spin mr-2"
                                ></i>
                                <span class="hidden sm:inline"
                                    >Submit Application</span
                                >
                                <span class="sm:hidden">Submit</span>
                                <i
                                    v-if="!form.processing && !loading.submit"
                                    class="fas fa-check ml-2"
                                ></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="w-full bg-gray-200 rounded-full h-2 overflow-hidden"
                        >
                            <div
                                class="bg-gradient-to-r from-teal-400 via-emerald-500 to-green-600 h-2 rounded-full transition-all duration-500 ease-out"
                                :style="{
                                    width: `${(currentStep / 9) * 100}%`,
                                }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500 text-right mt-1">
                            Step {{ currentStep }} of 9
                        </p>
                    </div>
                    <div
                        v-if="uploadProgress > 0 && uploadProgress < 100"
                        class="mt-2"
                    >
                        <div
                            class="w-full bg-gray-200 rounded-full h-2 overflow-hidden"
                        >
                            <div
                                class="bg-blue-500 h-2 rounded-full"
                                :style="{ width: `${uploadProgress}%` }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500 text-right mt-1">
                            Uploading: {{ uploadProgress }}%
                        </p>
                    </div>
                </div>

                <!-- Terms and Privacy Policy -->
                <div v-if="currentStep === 9" class="mt-4">
                    <div class="mb-4">
                        <label class="flex items-start">
                            <Checkbox
                                v-model:checked="form.terms"
                                name="terms"
                                class="mt-1"
                                required
                            />
                            <span class="ms-2 text-sm text-gray-600">
                                I certify that all the information provided is
                                accurate and complete. I understand that
                                providing false information may result in the
                                rejection of my application or termination of my
                                account. By signing up, I agree to the
                                <a
                                    :href="
                                        route('frontend.terms-and-conditions')
                                    "
                                    target="_blank"
                                    class="text-sm text-green-600 hover:text-green-900 font-medium hover:underline"
                                    >Terms of Service</a
                                >
                                and
                                <a
                                    :href="route('frontend.privacy-policy')"
                                    target="_blank"
                                    class="text-sm text-green-600 hover:text-green-900 font-medium hover:underline"
                                    >Privacy Policy</a
                                >.
                            </span>
                        </label>
                        <InputError :message="form.errors.terms" class="mt-2" />
                    </div>
                </div>

                <!-- Sign In Link -->
                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Already have an account?
                    <a
                        :href="route('login')"
                        class="font-medium text-green-600 hover:underline"
                        >Sign In</a
                    >
                </p>
            </form>
        </div>
    </AuthenticationCard>
</template>
