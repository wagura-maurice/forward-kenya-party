<!-- resources/js/Pages/Auth/Register.vue -->
<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    ref,
    computed,
    onMounted,
    nextTick,
    watch,
    getCurrentInstance,
} from "vue";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import "@/Components/VueSelectCustom.css";

// Define the countries prop
const props = defineProps({
    countries: Array,
    genders: Object,
});

// Options for Step 3 enhanced fields
const disabilityStatusOptions = [
    { value: '', label: 'None' },
    { value: 'physical', label: 'Physical' },
    { value: 'visual', label: 'Visual' },
    { value: 'hearing', label: 'Hearing' },
    { value: 'mental', label: 'Mental' },
    { value: 'other', label: 'Other' },
];
const maritalStatusOptions = [
    { value: 0, label: 'Single' },
    { value: 1, label: 'Married' },
    { value: 2, label: 'Divorced' },
    { value: 3, label: 'Separated' },
    { value: 4, label: 'Widowed' },
];
const educationLevelOptions = [
    { value: 0, label: 'Primary' },
    { value: 1, label: 'Secondary' },
    { value: 2, label: 'High School' },
    { value: 3, label: 'University' },
    { value: 4, label: 'Other' },
];
const ethnicityOptions = [
    { value: 1, label: 'Ethnicity 1' },
    { value: 2, label: 'Ethnicity 2' },
    { value: 3, label: 'Ethnicity 3' },
];
const languageOptions = [
    { value: 1, label: 'Language 1' },
    { value: 2, label: 'Language 2' },
    { value: 3, label: 'Language 3' },
];
const religionOptions = [
    { value: 1, label: 'Religion 1' },
    { value: 2, label: 'Religion 2' },
    { value: 3, label: 'Religion 3' },
];

// Step name mapping for navigation buttons
const stepNames = [
    "Basic Details", // 1
    "Role Selection", // 2
    "Personal Info", // 3
    "Address Info", // 4
    "Location Details", // 5
    "Education & Employment", // 6
    "Document Uploads", // 7
    "Review & Submit", // 8
];

// Residence reasons for residents
const residenceReasons = ref([
    "Work",
    "Family",
    "Education",
    "Business",
    "Retirement",
    "Other",
]);

// Refugee reasons for demonstration (replace with real data as needed)
const refugeeReasons = ref([
    "Conflict/War",
    "Persecution",
    "Natural Disaster",
    "Economic Hardship",
    "Other",
]);

// Static location options for demonstration (replace with dynamic data/API later)
const purposeOfVisitOptions = [
    { value: "Tourism", label: "Tourism" },
    { value: "Business", label: "Business" },
    { value: "Study", label: "Study" },
    { value: "Work", label: "Work" },
    { value: "Visiting Family", label: "Visiting Family" },
    { value: "Transit", label: "Transit" },
    { value: "Medical", label: "Medical" },
    { value: "Other", label: "Other" },
];

const counties = [
    { value: 1, label: "Nairobi" },
    { value: 2, label: "Mombasa" },
    { value: 3, label: "Kisumu" },
];

const subCounties = [
    { value: 11, label: "Westlands" },
    { value: 12, label: "Lang'ata" },
    { value: 13, label: "Kisauni" },
];

const constituencies = [
    { value: 21, label: "Westlands Constituency" },
    { value: 22, label: "Lang'ata Constituency" },
    { value: 23, label: "Kisauni Constituency" },
];

const wards = [
    { value: 31, label: "Kangemi" },
    { value: 32, label: "Karen" },
    { value: 33, label: "Mkomani" },
];

// State for collapsible sections
const expandedSections = ref({
    personal: true, // Personal Information
    contact: false, // Contact Information
    education: false, // Education & Employment
    security: false, // Security Information
    documents: false, // Document Uploads
});

const activeSection = ref("personal"); // Track the currently active section

/**
 * Form State
 */
// Access the global Toast instance
const { proxy } = getCurrentInstance();
const passwordStrength = ref("");
const uploadProgress = ref(0);
const dragOver = ref(false);

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

    if (isLongEnough && hasLetters && hasNumbers && hasSpecial) {
        passwordStrength.value = "strong";
    } else if (
        isLongEnough &&
        ((hasLetters && hasNumbers) ||
            (hasLetters && hasSpecial) ||
            (hasNumbers && hasSpecial))
    ) {
        passwordStrength.value = "medium";
    } else {
        passwordStrength.value = "weak";
    }
};

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
    purpose_of_visit: "", // Only for foreigner role

    // Personal Details
    first_name: "",
    middle_name: "",
    last_name: "",
    gender: "male",
    marital_status: "",
    disability: "",
    plwd_number: "",
    disability_status: "",
    ethnicity_id: null,
    language_id: null,
    religion_id: null,
    highest_level_of_education: null,
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

    // Refugee-specific (for role: 'refugee')
    reason_for_refugee: "",
    reason_for_refugee_other: "", // If 'Other' is selected, this holds the custom value
    refugee_center_id: null,

    // Resident-specific (for role: 'resident')
    reason_for_residence: "",
    reason_for_residence_other: "", // If 'Other' is selected, this holds the custom value

    // Role-specific
    consulate_id: null,
    polling_station_id: null,

    // Education and Employment
    education_level: "",
    occupation: "",
    employer_details: "",

    // Document Uploads
    proof_of_identity: null,
    proof_of_identity_type: "national_id",
    proof_of_address: null,
    proof_of_address_type: "utility_bill",
    documents: {},

    // Security
    security_question: "",
    security_answer: "",

    // Documents
    documents: {
        identity: {
            file: null,
            type: "national_id",
            preview: null,
            name: "",
            size: 0,
        },
        address: {
            file: null,
            type: "utility_bill",
            preview: null,
            name: "",
            size: 0,
        },
        education: [], // Array to store education documents
    },

    // Terms
    terms: false,
});

/**
 * Step Navigation State
 */
const currentStep = ref(1);

/**
 * Computed Properties
 */
const stepDescription = computed(() => {
    const descriptions = [
        "Enter profile details to proceed",
        "Select your role and provide identification details",
        "Provide additional personal details",
        "Add your contact information",
        "Education and employment details",
        "Security information",
        "Upload required documents",
        "Review your details and confirm",
    ];
    return descriptions[currentStep.value - 1] || "";
});

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

// Watchers for role and location fields
watch(
    () => form.role,
    (newRole, oldRole) => {
        // Reset role-specific fields
        form.idNumber = "";
        form.consulate_id = null;
        form.polling_station_id = null;
        form.refugee_center_id = null;
        form.reason_for_refugee = "";
        if (newRole === "citizen") {
            form.nationality = "Kenya";
        }
    }
);
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
    }
);
watch(
    () => form.sub_county_id,
    () => {
        form.ward_id = null;
        form.location_id = null;
        form.village_id = null;
    }
);
watch(
    () => form.constituency_id,
    () => {
        form.ward_id = null;
        form.location_id = null;
        form.village_id = null;
    }
);
watch(
    () => form.ward_id,
    () => {
        form.location_id = null;
        form.village_id = null;
    }
);
watch(
    () => form.location_id,
    () => {
        form.village_id = null;
    }
);

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

const handleFileDrop = (event, field) => {
    dragOver.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
        processFile(file, field);
    }
};

const processFile = (file, field) => {
    // Check file type
    const validTypes = ["application/pdf", "image/jpeg", "image/png"];
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

    // Check file size (5MB max)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        form.setError(field, "File size must be less than 5MB");
        window.Toast.fire({
            icon: "error",
            title: "File too large",
            text: "File size exceeds the maximum limit of 5MB.",
        });
        return false;
    }

    // Handle document uploads
    const fileInfo = {
        file,
        name: file.name,
        size: file.size,
        type: file.type,
        preview: null,
    };

    // Create preview for images
    if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = (e) => {
            fileInfo.preview = e.target.result;
            // Update both the specific field and the documents object
            form[field] = { ...fileInfo };
            form.documents[field] = { ...fileInfo };
        };
        reader.readAsDataURL(file);
    } else {
        // For non-image files, update both the field and documents object
        form[field] = { ...fileInfo };
        form.documents[field] = { ...fileInfo };
    }

    return true;
};

const handleFileUpload = (event, field) => {
    const file = event.target.files[0];
    if (!file) return;

    // Reset file input to allow re-uploading the same file
    event.target.value = "";

    // Clear any previous errors for this field
    form.clearErrors(field);

    // Clear any previous file data for this field
    form[field] = null;
    form.documents[field] = null;

    // Process the file
    processFile(file, field);
};

const selectedCountry = computed(() => {
    return props.countries.find((country) => country.name === form.nationality);
});

const selectedGender = computed(() => {
    return props.genders[form.gender] || "N/A";
});

/**
 * Methods
 */
const nextStep = () => {
    // Basic validation before proceeding to next step
    let isValid = true;

    // Step-specific validation
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
                text: "Please complete all required fields before proceeding.",
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
        let missing = [];
        // Role-specific validation
        switch (form.role) {
            case "citizen":
                if (!form.idNumber) missing.push("National ID Number");
                break;
            case "resident":
                if (!form.idNumber) missing.push("Alien ID Number");
                if (!form.reason_for_residence) {
                    missing.push("Reason for Residence");
                } else if (
                    form.reason_for_residence === "Other" &&
                    !form.reason_for_residence_other
                ) {
                    missing.push("Custom Reason for Residence");
                }
                break;
            case "refugee":
                if (!form.idNumber) missing.push("Refugee ID Number");
                if (!form.reason_for_refugee) {
                    missing.push("Reason for Refugee Status");
                } else if (
                    form.reason_for_refugee === "Other" &&
                    !form.reason_for_refugee_other
                ) {
                    missing.push("Custom Reason for Refugee Status");
                }
                break;
            case "diplomat":
                if (!form.idNumber) missing.push("Diplomat ID Number");
                if (!form.consulate_id) missing.push("Consulate");
                break;
            case "foreigner":
                if (!form.idNumber) missing.push("Passport Number");
                break;
            case "guest":
                // No extra required fields
                break;
        }
        if (missing.length > 0) {
            form.setError("form", `Please fill in: ${missing.join(", ")}`);
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: `Please complete: ${missing.join(
                    ", "
                )} before proceeding.`,
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
                text: "Please complete all required fields before proceeding.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 4) {
        if (!form.address_line_1 || !form.city || !form.state) {
            form.setError("form", "Please fill in all required address fields");
            isValid = false;
        }
    } else if (currentStep.value === 5) {
        if (!form.education_level || !form.occupation) {
            form.setError("form", "Please fill in all required fields");
            window.Toast.fire({
                icon: "warning",
                title: "Incomplete Form",
                text: "Please complete all required fields before proceeding.",
            });
            isValid = false;
        }
    } else if (currentStep.value === 6) {
        if (!form.security_question || !form.security_answer) {
            form.setError(
                "form",
                "Please provide a security question and answer"
            );
            isValid = false;
        }
    } else if (currentStep.value === 7) {
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

    if (isValid && currentStep.value < 8) {
        currentStep.value++;
    }
};

const previousStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const resetForm = () => {
    form.reset();
    currentStep.value = 1;
};

const submit = () => {
    // If 'Other' is selected, use the custom value for submission
    if (form.reason_for_residence === "Other") {
        form.reason_for_residence = form.reason_for_residence_other;
    }
    if (form.reason_for_refugee === "Other") {
        form.reason_for_refugee = form.reason_for_refugee_other;
    }

    // Create FormData for file uploads
    const formData = new FormData();

    // Add all form fields to FormData
    Object.keys(form).forEach((key) => {
        // Skip private properties and methods
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
                // Do not send _other fields
                "reason_for_residence_other",
                "reason_for_refugee_other",
            ].includes(key)
        ) {
            formData.append(key, form[key]);
        }
    });

    // Handle file uploads
    if (form.proof_of_identity) {
        if (form.proof_of_identity instanceof File) {
            formData.append("proof_of_identity", form.proof_of_identity);
        } else if (form.proof_of_identity.file) {
            formData.append("proof_of_identity", form.proof_of_identity.file);
        }
    }

    if (form.proof_of_address) {
        if (form.proof_of_address instanceof File) {
            formData.append("proof_of_address", form.proof_of_address);
        } else if (form.proof_of_address.file) {
            formData.append("proof_of_address", form.proof_of_address.file);
        }
    }

    form.post(route("register"), {
        _method: "post",
        forceFormData: true,
        preserveState: true,
        onSuccess: () => {
            window.Toast.fire({
                icon: "success",
                title: "Registration successful!",
                text: "Please check your email for verification.",
            });
            form.reset("password", "password_confirmation");
            // Redirect to dashboard or verification page
            window.location.href = route("dashboard");
        },
        onError: (errors) => {
            // Handle errors
            console.error("Registration failed:", errors);

            if (errors.server) {
                window.Toast.fire({
                    icon: "error",
                    title: "Server error",
                    text: "Please try again later.",
                });
            } else {
                // Show all error messages as toast notifications, one after another
                Object.values(errors).forEach((msg, idx) => {
                    const messages = Array.isArray(msg) ? msg : [msg];
                    messages.forEach((message, j) => {
                        setTimeout(() => {
                            window.Toast.fire({
                                icon: "error",
                                title: "Error",
                                text: message,
                            });
                        }, (idx + j) * 700); // 700ms apart, adjust as needed
                    });
                });

                // Focus on first error field
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
            }
        },
        onFinish: () => {
            form.processing = false;
        },
    });
};

// Update phone number when country code or number changes
const updatePhoneNumber = () => {
    // Combine country code and phone number
    if (form.phoneNumber) {
        form.telephone = `+${form.phoneCountryCode}${form.phoneNumber.replace(
            /^0+/,
            ""
        )}`;
    } else {
        form.telephone = "";
    }
};

// Country codes for the dropdown
const countryCodes = [
    { code: "KE", name: "Kenya (+254)" },
    { code: "US", name: "USA (+1)" },
    { code: "GB", name: "UK (+44)" },
    { code: "CA", name: "Canada (+1)" },
    { code: "AU", name: "Australia (+61)" },
    // Add more countries as needed
];

// Set default country code to Kenya
form.phoneCountryCode = "254";

// Watch for changes in phone number or country code
watch([() => form.phoneNumber, () => form.phoneCountryCode], () => {
    updatePhoneNumber();
});

// Initialize phone number on mount
onMounted(() => {
    // If we have a telephone number, parse it
    if (form.telephone) {
        const match = form.telephone.match(/^\+(\d+)(\d+)$/);
        if (match) {
            form.phoneCountryCode = match[1];
            form.phoneNumber = match[2];
        }
    }
});

// Format file size to human readable format
const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

// Format document type for display
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

// Watch for step changes to handle any step-specific logic
watch(
    () => currentStep,
    (newStep) => {
        // Add any step-specific logic here if needed
    }
);

// Toggle accordion sections
const toggleSection = (section) => {
    // Close all sections first
    Object.keys(expandedSections.value).forEach((key) => {
        expandedSections.value[key] = false;
    });
    // Toggle the clicked section if it's different from the active one
    if (activeSection.value !== section) {
        expandedSections.value[section] = true;
        activeSection.value = section;
    } else {
        activeSection.value = null;
    }
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

            <!-- Multi-Step Form -->
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
                        <div class="flex items-center">
                            <InputLabel for="name" value="Username"
                                >Username</InputLabel
                            ><i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Enter your username"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                            autofocus
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center">
                            <InputLabel for="email" value="Email"
                                >Email</InputLabel
                            ><i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="your.email@example.com"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center">
                            <InputLabel for="telephone" value="Telephone" />
                            <i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <div class="flex space-x-2">
                            <div class="w-1/3">
                                <VueSelect
                                    v-model="form.phoneCountryCode"
                                    :options="
                                        countryCodes.map((country) => ({
                                            value: country.code,
                                            label: country.name,
                                        }))
                                    "
                                    placeholder="Select country code"
                                    label="label"
                                    :reduce="(option) => option.value"
                                    class="mt-1 block w-full"
                                />
                            </div>
                            <div class="flex-1">
                                <input
                                    id="phoneNumber"
                                    v-model="form.phoneNumber"
                                    type="tel"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                    placeholder="e.g. 712345678"
                                    required
                                    autocomplete="tel"
                                />
                                <input
                                    type="hidden"
                                    name="telephone"
                                    v-model="form.telephone"
                                />
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Enter your phone number with country code
                        </p>
                        <InputError
                            class="mt-2"
                            :message="
                                form.errors.telephone ||
                                form.errors.phoneCountryCode
                            "
                        />
                    </div>

                    <div>
                        <div class="flex items-center">
                            <InputLabel for="password" value="Password"
                                >Password</InputLabel
                            ><i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Create a strong password"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Use at least 8 characters with a mix of letters,
                            numbers, and symbols
                        </p>
                        <InputError
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <div class="flex items-center">
                            <InputLabel
                                for="password_confirmation"
                                value="Confirm Password"
                                >Confirm Password</InputLabel
                            ><i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Re-enter your password"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
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
                        <div class="flex items-center">
                            <InputLabel for="role" value="Role" />
                            <i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <VueSelect
                            v-model="form.role"
                            :options="[
                                { value: 'citizen', label: 'Citizen' },
                                { value: 'resident', label: 'Resident' },
                                { value: 'refugee', label: 'Refugee' },
                                { value: 'diplomat', label: 'Diplomat' },
                                { value: 'foreigner', label: 'Foreigner' },
                            ]"
                            placeholder="Select your role"
                            label="label"
                            :reduce="(option) => option.value"
                            required
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="nationality" value="Nationality" />
                        <VueSelect
                            v-model="form.nationality"
                            :options="
                                form.role === 'citizen'
                                    ? ['Kenya']
                                    : countries.map((country) => country.name)
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
                            class="mt-2"
                            :message="form.errors.nationality"
                        />
                    </div>

                    <div>
                        <div class="flex items-center">
                            <InputLabel :for="idField" :value="idLabel" />
                            <i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <TextInput
                            :id="idField"
                            v-model="form.idNumber"
                            type="text"
                            :placeholder="
                                form.role === 'citizen'
                                    ? 'e.g. 12345678'
                                    : 'Enter your ID or passport number'
                            "
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        />
                        <InputError
                            :message="form.errors.idNumber"
                            class="mt-2"
                        />
                    </div>

                    <!-- Foreigner: Purpose of Visit -->
                    <div v-if="form.role === 'foreigner'">
                        <InputLabel
                            for="purpose_of_visit"
                            value="Purpose of Visit"
                        >
                            <template #default>
                                <span
                                    >Purpose of Visit
                                    <i
                                        class="fas fa-star text-red-500 text-xs align-middle ml-1"
                                        aria-hidden="true"
                                    ></i
                                ></span>
                            </template>
                        </InputLabel>
                        <VueSelect
                            id="purpose_of_visit"
                            v-model="form.purpose_of_visit"
                            :options="purposeOfVisitOptions"
                            placeholder="Select purpose of visit"
                            class="mt-1 block w-full"
                            :reduce="(option) => option.value"
                            required
                        />
                        <InputError
                            :message="form.errors.purpose_of_visit"
                            class="mt-2"
                        />
                    </div>

                    <!-- Diplomat: Consulate -->
                    <div v-if="form.role === 'diplomat'">
                        <InputLabel for="consulate_id" value="Consulate" />
                        <VueSelect
                            v-model="form.consulate_id"
                            :options="consulates"
                            placeholder="Select consulate"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.consulate_id,
                            }"
                        />
                        <InputError
                            :message="form.errors.consulate_id"
                            class="mt-2"
                        />
                    </div>

                    <!-- Refugee: Reason & Center -->
                    <div v-if="form.role === 'refugee'">
                        <InputLabel
                            for="reason_for_refugee"
                            value="Reason for Refugee Status"
                        />
                        <VueSelect
                            v-model="form.reason_for_refugee"
                            :options="[...refugeeReasons, 'Other']"
                            placeholder="Select reason for refugee status"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500':
                                    form.errors.reason_for_refugee,
                            }"
                        />
                        <!-- Show text input if 'Other' is selected -->
                        <div v-if="form.reason_for_refugee === 'Other'">
                            <TextInput
                                v-model="form.reason_for_refugee_other"
                                placeholder="Please specify your reason"
                                class="mt-2 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            />
                            <InputError
                                :message="form.errors.reason_for_refugee_other"
                                class="mt-2"
                            />
                        </div>
                        <InputError
                            :message="form.errors.reason_for_refugee"
                            class="mt-2"
                        />
                        <InputLabel
                            for="refugee_center_id"
                            value="Refugee Center"
                            class="mt-4"
                        />
                        <VueSelect
                            v-model="form.refugee_center_id"
                            :options="refugeeCenters"
                            placeholder="Select refugee center"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.refugee_center_id,
                            }"
                        />
                        <InputError
                            :message="form.errors.refugee_center_id"
                            class="mt-2"
                        />
                    </div>

                    <!-- Resident: Reason for Residence -->
                    <div v-if="form.role === 'resident'">
                        <InputLabel
                            for="reason_for_residence"
                            value="Reason for Residence"
                        />
                        <VueSelect
                            v-model="form.reason_for_residence"
                            :options="[
                                ...(residenceReasons.value || []),
                                'Other',
                            ]"
                            placeholder="Select reason for residence"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500':
                                    form.errors.reason_for_residence,
                            }"
                        />
                        <!-- Show text input if 'Other' is selected -->
                        <div v-if="form.reason_for_residence === 'Other'">
                            <TextInput
                                v-model="form.reason_for_residence_other"
                                placeholder="Please specify your reason"
                                class="mt-2 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            />
                            <InputError
                                :message="
                                    form.errors.reason_for_residence_other
                                "
                                class="mt-2"
                            />
                        </div>
                        <InputError
                            :message="form.errors.reason_for_residence"
                            class="mt-2"
                        />
                    </div>

                    <!-- Citizen: Polling Station (if applicable) -->
                    <div v-if="form.role === 'citizen'">
                        <InputLabel for="county_id" value="County" />
                        <VueSelect
                            v-model="form.county_id"
                            :options="counties"
                            placeholder="Select your county"
                            class="mt-1 block w-full"
                            :class="{ 'border-red-500': form.errors.county_id }"
                        />
                        <InputError
                            :message="form.errors.county_id"
                            class="mt-2"
                        />

                        <InputLabel
                            for="sub_county_id"
                            value="Sub-County"
                            class="mt-4"
                        />
                        <VueSelect
                            v-model="form.sub_county_id"
                            :options="subCounties"
                            placeholder="Select your sub-county"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.sub_county_id,
                            }"
                            :disabled="!form.county_id"
                        />
                        <InputError
                            :message="form.errors.sub_county_id"
                            class="mt-2"
                        />

                        <InputLabel
                            for="constituency_id"
                            value="Constituency"
                            class="mt-4"
                        />
                        <VueSelect
                            v-model="form.constituency_id"
                            :options="constituencies"
                            placeholder="Select your constituency"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': form.errors.constituency_id,
                            }"
                            :disabled="!form.county_id"
                        />
                        <InputError
                            :message="form.errors.constituency_id"
                            class="mt-2"
                        />

                        <InputLabel for="ward_id" value="Ward" class="mt-4" />
                        <VueSelect
                            v-model="form.ward_id"
                            :options="wards"
                            placeholder="Select your ward"
                            class="mt-1 block w-full"
                            :class="{ 'border-red-500': form.errors.ward_id }"
                            :disabled="
                                !form.constituency_id && !form.sub_county_id
                            "
                        />
                        <InputError
                            :message="form.errors.ward_id"
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
                            <div class="flex items-center">
                                <InputLabel for="first_name" value="First Name"
                                    >First Name</InputLabel
                                ><i
                                    class="fas fa-star text-red-500 text-xs ml-1"
                                    aria-hidden="true"
                                ></i>
                            </div>
                            <TextInput
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                placeholder="Enter your first name"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
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
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            />
                            <InputError
                                :message="form.errors.middle_name"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center">
                                <InputLabel for="last_name" value="Last Name"
                                    >Last Name</InputLabel
                                ><i
                                    class="fas fa-star text-red-500 text-xs ml-1"
                                    aria-hidden="true"
                                ></i>
                            </div>
                            <TextInput
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                placeholder="Enter your last name"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                required
                            />
                            <InputError
                                :message="form.errors.last_name"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <div class="flex items-center">
                                <InputLabel for="gender" value="Gender"
                                    >Gender</InputLabel
                                ><i
                                    class="fas fa-star text-red-500 text-xs ml-1"
                                    aria-hidden="true"
                                ></i>
                            </div>
                            <VueSelect
                                v-model="form.gender"
                                :options="[
                                    { value: 'male', label: 'Male' },
                                    { value: 'female', label: 'Female' },
                                    { value: 'other', label: 'Other' },
                                    {
                                        value: 'prefer_not_to_say',
                                        label: 'Prefer not to say',
                                    },
                                ]"
                                placeholder="Select your gender"
                                label="label"
                                :reduce="(option) => option.value"
                                required
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="form.errors.gender"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center">
                            <InputLabel
                                for="date_of_birth"
                                value="Date of Birth"
                                >Date of Birth</InputLabel
                            ><i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <TextInput
                            id="date_of_birth"
                            v-model="form.date_of_birth"
                            type="date"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        />
                        <InputError
                            :message="form.errors.date_of_birth"
                            class="mt-2"
                        />
                    </div>
                    <!-- Disability Status -->
                    <div class="mt-4">
                        <div class="flex items-center">
                            <InputLabel for="disability_status" value="Disability Status" />
                            <i class="fas fa-star text-red-500 text-xs ml-1" aria-hidden="true"></i>
                        </div>
                        <VueSelect
                            v-model="form.disability_status"
                            :options="disabilityStatusOptions"
                            placeholder="Select your disability status"
                            label="label"
                            :reduce="option => option.value"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.disability_status" class="mt-2" />
                    </div>
                    <!-- Ethnicity -->
                    <div class="mt-4">
                        <div class="flex items-center">
                            <InputLabel for="ethnicity_id" value="Ethnicity" />
                            <i class="fas fa-star text-red-500 text-xs ml-1" aria-hidden="true"></i>
                        </div>
                        <VueSelect
                            v-model="form.ethnicity_id"
                            :options="ethnicityOptions"
                            placeholder="Select your ethnicity"
                            label="label"
                            :reduce="option => option.value"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.ethnicity_id" class="mt-2" />
                    </div>
                    <!-- Language -->
                    <div class="mt-4">
                        <div class="flex items-center">
                            <InputLabel for="language_id" value="Language" />
                            <i class="fas fa-star text-red-500 text-xs ml-1" aria-hidden="true"></i>
                        </div>
                        <VueSelect
                            v-model="form.language_id"
                            :options="languageOptions"
                            placeholder="Select your language"
                            label="label"
                            :reduce="option => option.value"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.language_id" class="mt-2" />
                    </div>
                    <!-- Religion -->
                    <div class="mt-4">
                        <div class="flex items-center">
                            <InputLabel for="religion_id" value="Religion" />
                            <i class="fas fa-star text-red-500 text-xs ml-1" aria-hidden="true"></i>
                        </div>
                        <VueSelect
                            v-model="form.religion_id"
                            :options="religionOptions"
                            placeholder="Select your religion"
                            label="label"
                            :reduce="option => option.value"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.religion_id" class="mt-2" />
                    </div>
                    <!-- Marital Status -->
                    <div class="mt-4">
                        <div class="flex items-center">
                            <InputLabel for="marital_status" value="Marital Status" />
                            <i class="fas fa-star text-red-500 text-xs ml-1" aria-hidden="true"></i>
                        </div>
                        <VueSelect
                            v-model="form.marital_status"
                            :options="maritalStatusOptions"
                            placeholder="Select your marital status"
                            label="label"
                            :reduce="option => option.value"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.marital_status" class="mt-2" />
                    </div>
                    <!-- Highest Level of Education -->
                    <div class="mt-4">
                        <div class="flex items-center">
                            <InputLabel for="highest_level_of_education" value="Highest Level of Education" />
                            <i class="fas fa-star text-red-500 text-xs ml-1" aria-hidden="true"></i>
                        </div>
                        <VueSelect
                            v-model="form.highest_level_of_education"
                            :options="educationLevelOptions"
                            placeholder="Select your highest education level"
                            label="label"
                            :reduce="option => option.value"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.highest_level_of_education" class="mt-2" />
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
                        <div class="flex items-center">
                            <InputLabel
                                for="address_line_1"
                                value="Address Line 1"
                            />
                            <i
                                class="fas fa-star text-red-500 text-xs ml-1"
                                aria-hidden="true"
                            ></i>
                        </div>
                        <TextInput
                            id="address_line_1"
                            v-model="form.address_line_1"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm placeholder-gray-400"
                            placeholder="Enter your address line 1"
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
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm placeholder-gray-400"
                            placeholder="Enter your address line 2"
                        />
                        <InputError
                            :message="form.errors.address_line_2"
                            class="mt-2"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center">
                                <InputLabel for="city" value="City/Town" />
                                <i
                                    class="fas fa-star text-red-500 text-xs ml-1"
                                    aria-hidden="true"
                                ></i>
                            </div>
                            <TextInput
                                id="city"
                                v-model="form.city"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm placeholder-gray-400"
                                placeholder="Enter your city"
                                required
                            />
                            <InputError
                                :message="form.errors.city"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <div class="flex items-center">
                                <InputLabel for="state" value="State/County" />
                                <i
                                    class="fas fa-star text-red-500 text-xs ml-1"
                                    aria-hidden="true"
                                ></i>
                            </div>
                            <TextInput
                                id="state"
                                v-model="form.state"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm placeholder-gray-400"
                                placeholder="Enter your state"
                                required
                            />
                            <InputError
                                :message="form.errors.state"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>

                <!-- Step 5: Education & Employment -->
                <div v-if="currentStep === 5" class="space-y-4">
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
                        <VueSelect
                            v-model="form.education_level"
                            :options="
                                educationLevels.map((level) => ({
                                    value: level,
                                    label: level,
                                }))
                            "
                            placeholder="Select your education level"
                            label="label"
                            :reduce="(option) => option.value"
                            required
                            class="mt-1 block w-full"
                        />
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
                            placeholder="e.g. Software Developer, Teacher, Business Owner"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Please enter your current job title or occupation
                        </p>
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

                <!-- Step 6: Security Information -->
                <div v-if="currentStep === 6" class="space-y-4">
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
                        <VueSelect
                            v-model="form.security_question"
                            :options="
                                securityQuestions.map((q) => ({
                                    value: q,
                                    label: q,
                                }))
                            "
                            placeholder="Select a security question"
                            label="label"
                            :reduce="(option) => option.value"
                            required
                            class="mt-1 block w-full"
                        />
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
                            placeholder="e.g. 12345678"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            required
                        />
                        <InputError
                            :message="form.errors.security_answer"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Step 7: Document Uploads -->
                <div v-if="currentStep === 7" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>
                    <div
                        class="border border-dashed border-gray-300 rounded-lg p-4"
                    >
                        <div>
                            <InputLabel
                                for="proof_of_identity"
                                value="Proof of Identity"
                                class="block text-sm font-medium text-gray-700"
                            />
                            <div class="mt-4 space-y-2">
                                <div class="flex space-x-2">
                                    <VueSelect
                                        v-model="form.proof_of_identity_type"
                                        :options="[
                                            {
                                                value: 'national_id',
                                                label: 'National ID',
                                            },
                                            {
                                                value: 'passport',
                                                label: 'Passport',
                                            },
                                            {
                                                value: 'driving_license',
                                                label: 'Driving License',
                                            },
                                            { value: 'other', label: 'Other' },
                                        ]"
                                        placeholder="Select document type"
                                        label="label"
                                        :reduce="(option) => option.value"
                                        class="w-1/3"
                                    />
                                    <input
                                        type="file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        @change="
                                            (e) =>
                                                handleFileUpload(
                                                    e,
                                                    'proof_of_identity'
                                                )
                                        "
                                        class="flex-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 placeholder-gray-400"
                                        placeholder="Choose ID document (PDF, JPG, PNG)"
                                    />
                                </div>
                                <!-- File Preview -->
                                <div
                                    v-if="form.proof_of_identity"
                                    class="mt-2 p-3 border rounded-md bg-gray-50"
                                >
                                    <div class="flex items-start space-x-3">
                                        <div
                                            v-if="
                                                form.proof_of_identity
                                                    .preview &&
                                                form.proof_of_identity.type.startsWith(
                                                    'image/'
                                                )
                                            "
                                            class="flex-shrink-0"
                                        >
                                            <img
                                                :src="
                                                    form.proof_of_identity
                                                        .preview
                                                "
                                                class="h-16 w-16 object-cover rounded"
                                                alt="ID Preview"
                                            />
                                        </div>
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
                                                {{
                                                    form.proof_of_identity.name
                                                }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{
                                                    formatFileSize(
                                                        form.proof_of_identity
                                                            .size
                                                    )
                                                }}
                                                •
                                                {{
                                                    form.proof_of_identity.type
                                                }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{
                                                    formatDocumentType(
                                                        form.proof_of_identity
                                                            .documentType
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="
                                                form.proof_of_identity = null
                                            "
                                            class="text-red-600 hover:text-red-800"
                                            aria-label="Remove file"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <p v-else class="text-sm text-gray-500">
                                    No file uploaded. Please upload a clear
                                    photo of your ID.
                                </p>
                            </div>
                            <p
                                v-if="form.errors.proof_of_identity"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.proof_of_identity }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- End of Proof of Identity Section -->

                <!-- Proof of Address Section -->
                <div v-if="currentStep === 7" class="mt-6">
                    <div
                        class="border border-dashed border-gray-300 rounded-lg p-4"
                    >
                        <div>
                            <InputLabel
                                for="proof_of_address"
                                value="Proof of Address"
                                class="block text-sm font-medium text-gray-700"
                            />
                            <div class="mt-4 space-y-2">
                                <div class="flex space-x-2">
                                    <VueSelect
                                        v-model="form.proof_of_address_type"
                                        :options="[
                                            {
                                                value: 'utility_bill',
                                                label: 'Utility Bill',
                                            },
                                            {
                                                value: 'bank_statement',
                                                label: 'Bank Statement',
                                            },
                                            {
                                                value: 'rental_agreement',
                                                label: 'Rental Agreement',
                                            },
                                            { value: 'other', label: 'Other' },
                                        ]"
                                        placeholder="Select address document type"
                                        label="label"
                                        :reduce="(option) => option.value"
                                        class="w-1/3"
                                    />
                                    <input
                                        type="file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        @change="
                                            (e) =>
                                                handleFileUpload(
                                                    e,
                                                    'proof_of_address'
                                                )
                                        "
                                        class="flex-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 placeholder-gray-400"
                                        placeholder="Choose proof of address (PDF, JPG, PNG)"
                                    />
                                </div>
                                <!-- File Preview -->
                                <div
                                    v-if="form.proof_of_address"
                                    class="mt-2 p-3 border rounded-md bg-gray-50"
                                >
                                    <div class="flex items-start space-x-3">
                                        <div
                                            v-if="
                                                form.proof_of_address.preview &&
                                                form.proof_of_address.type.startsWith(
                                                    'image/'
                                                )
                                            "
                                            class="flex-shrink-0"
                                        >
                                            <img
                                                :src="
                                                    form.proof_of_address
                                                        .preview
                                                "
                                                class="h-16 w-16 object-cover rounded"
                                                alt="Address Proof Preview"
                                            />
                                        </div>
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
                                                        form.proof_of_address
                                                            .size
                                                    )
                                                }}
                                                •
                                                {{ form.proof_of_address.type }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{
                                                    formatDocumentType(
                                                        form.proof_of_address
                                                            .documentType
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="
                                                form.proof_of_address = null
                                            "
                                            class="text-red-600 hover:text-red-800"
                                            aria-label="Remove file"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <p v-else class="text-sm text-gray-500">
                                    No file uploaded. Please upload a clear
                                    document showing your current address.
                                </p>
                            </div>
                            <p
                                v-if="form.errors.proof_of_address"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.proof_of_address }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- End of Proof of Address Section -->

                <!-- Confirmation Step -->
                <div v-if="currentStep === 8" class="space-y-4">
                    <h2
                        class="text-sm font-light text-gray-500 dark:text-gray-400"
                    >
                        Step {{ currentStep }}: {{ stepDescription }}
                    </h2>

                    <!-- Personal Information Card -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-300 ease-in-out mb-4"
                        :class="
                            expandedSections.personal
                                ? 'min-h-[120px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('personal')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-150 cursor-pointer"
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
                                class="fas fa-chevron-down text-gray-500 transition-transform duration-200"
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
                                <div class="w-full">
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
                                    <div
                                        v-if="form.date_of_birth"
                                        class="space-y-1"
                                    >
                                        <p class="text-sm text-gray-500">
                                            Date of Birth
                                        </p>
                                        <p class="font-medium text-gray-900">
                                            {{ form.date_of_birth }}
                                        </p>
                                    </div>
                                    <div v-if="form.gender" class="space-y-1">
                                        <p class="text-sm text-gray-500">
                                            Gender
                                        </p>
                                        <p class="font-medium text-gray-900">
                                            {{ form.gender }}
                                        </p>
                                    </div>
                                    <template v-if="form.role === 'citizen'">
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                County
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    counties.find(
                                                        (c) =>
                                                            c.value ===
                                                            form.county_id
                                                    )?.label || "Not specified"
                                                }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Sub-County
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    subCounties.find(
                                                        (s) =>
                                                            s.value ===
                                                            form.sub_county_id
                                                    )?.label || "Not specified"
                                                }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Constituency
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    constituencies.find(
                                                        (c) =>
                                                            c.value ===
                                                            form.constituency_id
                                                    )?.label || "Not specified"
                                                }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Ward
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    wards.find(
                                                        (w) =>
                                                            w.value ===
                                                            form.ward_id
                                                    )?.label || "Not specified"
                                                }}
                                            </p>
                                        </div>
                                    </template>
                                    <template
                                        v-else-if="form.role === 'resident'"
                                    >
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Reason for Residence
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    form.reason_for_residence ===
                                                    "Other"
                                                        ? form.reason_for_residence_other
                                                        : form.reason_for_residence ||
                                                          "Not specified"
                                                }}
                                            </p>
                                        </div>
                                    </template>
                                    <template
                                        v-else-if="form.role === 'refugee'"
                                    >
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Reason for Refugee Status
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    form.reason_for_refugee ===
                                                    "Other"
                                                        ? form.reason_for_refugee_other
                                                        : form.reason_for_refugee ||
                                                          "Not specified"
                                                }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Refugee Center
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    (refugeeCenters &&
                                                        refugeeCenters.find(
                                                            (rc) =>
                                                                rc.value ===
                                                                form.refugee_center_id
                                                        )?.label) ||
                                                    "Not specified"
                                                }}
                                            </p>
                                        </div>
                                    </template>
                                    <template
                                        v-else-if="form.role === 'diplomat'"
                                    >
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Consulate
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    (consulates &&
                                                        consulates.find(
                                                            (c) =>
                                                                c.value ===
                                                                form.consulate_id
                                                        )?.label) ||
                                                    "Not specified"
                                                }}
                                            </p>
                                        </div>
                                    </template>
                                    <template
                                        v-else-if="form.role === 'foreigner'"
                                    >
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-500">
                                                Purpose of Visit
                                            </p>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    purposeOfVisitOptions.find(
                                                        (opt) =>
                                                            opt.value ===
                                                            form.purpose_of_visit
                                                    )?.label || "Not specified"
                                                }}
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Uploads Card -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-300 ease-in-out mb-4"
                        :class="
                            expandedSections.documents
                                ? 'min-h-[180px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('documents')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-150 cursor-pointer"
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
                                class="fas fa-chevron-down text-gray-500 transition-transform duration-200"
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
                                <!-- Proof of Identity -->
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
                                        <!-- Image Preview Container -->
                                        <div
                                            class="bg-white p-3 rounded-lg border border-gray-200"
                                        >
                                            <div class="flex justify-center">
                                                <div
                                                    v-if="
                                                        form.proof_of_identity
                                                            .preview &&
                                                        form.proof_of_identity.type.startsWith(
                                                            'image/'
                                                        )
                                                    "
                                                    class="flex flex-col items-center"
                                                >
                                                    <img
                                                        :src="
                                                            form
                                                                .proof_of_identity
                                                                .preview
                                                        "
                                                        class="h-40 w-auto max-w-full object-contain rounded"
                                                        alt="ID Proof Preview"
                                                    />
                                                </div>
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

                                        <!-- Document Details -->
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
                                                    ></i>
                                                    <span
                                                        class="truncate max-w-[120px] sm:max-w-[180px]"
                                                    >
                                                        {{
                                                            form
                                                                .proof_of_identity
                                                                .name ||
                                                            "No file name"
                                                        }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center space-x-2"
                                                >
                                                    <i
                                                        class="fas fa-save text-gray-400"
                                                    ></i>
                                                    <span>
                                                        {{
                                                            formatFileSize(
                                                                form
                                                                    .proof_of_identity
                                                                    .size
                                                            ) || "N/A"
                                                        }}
                                                    </span>
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

                                <!-- Proof of Address -->
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
                                        <!-- Image Preview Container -->
                                        <div
                                            class="bg-white p-3 rounded-lg border border-gray-200"
                                        >
                                            <div class="flex justify-center">
                                                <div
                                                    v-if="
                                                        form.proof_of_address
                                                            .preview &&
                                                        form.proof_of_address.type.startsWith(
                                                            'image/'
                                                        )
                                                    "
                                                    class="flex flex-col items-center"
                                                >
                                                    <img
                                                        :src="
                                                            form
                                                                .proof_of_address
                                                                .preview
                                                        "
                                                        class="h-40 w-auto max-w-full object-contain rounded"
                                                        alt="Address Proof Preview"
                                                    />
                                                </div>
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

                                        <!-- Document Details -->
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
                                                    ></i>
                                                    <span
                                                        class="truncate max-w-[120px] sm:max-w-[180px]"
                                                    >
                                                        {{
                                                            form
                                                                .proof_of_address
                                                                .name ||
                                                            "No file name"
                                                        }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center space-x-2"
                                                >
                                                    <i
                                                        class="fas fa-save text-gray-400"
                                                    ></i>
                                                    <span>
                                                        {{
                                                            formatFileSize(
                                                                form
                                                                    .proof_of_address
                                                                    .size
                                                            ) || "N/A"
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-else class="text-sm text-gray-500">
                                        No file uploaded
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-300 ease-in-out"
                        :class="
                            expandedSections.contact
                                ? 'min-h-[180px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('contact')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-150 cursor-pointer"
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
                                class="fas fa-chevron-down text-gray-500 transition-transform duration-200"
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
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">
                                    {{ form.email }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium text-gray-900">
                                    {{ form.telephone }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Education & Employment Card -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-300 ease-in-out"
                        :class="
                            expandedSections.education
                                ? 'min-h-[180px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('education')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-150 cursor-pointer"
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
                                class="fas fa-chevron-down text-gray-500 transition-transform duration-200"
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

                                    @dragleave="dragOver = false"
                                    @drop.prevent="
                                        handleFileDrop(
                                            $event,
                                            'proof_of_identity'
                                        )
                                    "
                                    :class="[
                                        'border-2 border-dashed rounded-lg p-6 text-center transition-colors mt-1',
                                        dragOver
                                            ? 'border-green-500 bg-green-50'
                                            : 'border-gray-300',
                                    ]"
                                >
                                    <div v-if="!form.proof_of_identity">
                                        <i
                                            class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"
                                        ></i>
                                        <p class="text-sm text-gray-500">
                                            Drag and drop your file here, or
                                            <label
                                                class="text-green-600 hover:text-green-700 cursor-pointer"
                                            >
                                                click to browse
                                                <input
                                                    type="file"
                                                    class="hidden"
                                                    @change="
                                                        handleFileUpload(
                                                            $event,
                                                            'proof_of_identity'
                                                        )
                                                    "
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                />
                                            </label>
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Max file size: 5MB. Supported
                                            formats: PDF, JPG, PNG
                                        </p>
                                    </div>
                                    <div
                                        v-else
                                        class="flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-3"
                                        >
                                            <i
                                                class="fas fa-file-alt text-2xl text-gray-400"
                                            ></i>
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-900 truncate max-w-xs"
                                                >
                                                    {{
                                                        form.proof_of_identity
                                                            .name
                                                    }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        formatFileSize(
                                                            form
                                                                .proof_of_identity
                                                                .size
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="
                                                form.proof_of_identity = null
                                            "
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.proof_of_identity"
                                    class="mt-2"
                                />
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

                    <!-- Security Information Card -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-300 ease-in-out mt-4"
                        :class="
                            expandedSections.security
                                ? 'min-h-[200px]'
                                : 'h-[56px] overflow-hidden'
                        "
                    >
                        <div
                            @click="toggleSection('security')"
                            class="w-full px-4 py-3 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-150 cursor-pointer"
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
                                class="fas fa-chevron-down text-gray-500 transition-transform duration-200"
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
                                <div class="w-full">
                                    <p class="text-sm text-gray-500">
                                        Security Question
                                    </p>
                                    <p class="font-medium">
                                        {{
                                            form.security_question || "Not set"
                                        }}
                                    </p>
                                </div>
                                <div v-if="form.security_answer" class="w-full">
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
                            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400 transition-colors duration-200"
                        >
                            <i
                                class="fas fa-arrow-left mr-2 text-emerald-600"
                            ></i>
                            <span class="hidden sm:inline">Previous</span>
                        </button>
                        <div v-else class="w-24"></div>
                        <!-- Spacer for alignment -->

                        <div class="flex items-center space-x-4">
                            <button
                                v-if="currentStep < 8"
                                type="button"
                                @click="nextStep"
                                :disabled="form.processing"
                                :class="[
                                    'inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200',
                                    currentStep === 7
                                        ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 shadow-emerald-200'
                                        : 'bg-teal-500 hover:bg-teal-600 focus:ring-teal-400 shadow-teal-200',
                                    form.processing
                                        ? 'opacity-75 cursor-not-allowed'
                                        : '',
                                ]"
                            >
                                <i
                                    v-if="form.processing"
                                    class="fas fa-spinner fa-spin mr-2"
                                ></i>
                                <span class="hidden sm:inline">
                                    Next: {{ stepNames[currentStep] }}
                                </span>
                                <span class="sm:hidden">
                                    {{
                                        currentStep === 7
                                            ? "Review"
                                            : "Next: " + stepNames[currentStep]
                                    }}
                                </span>
                                <i
                                    v-if="!form.processing"
                                    class="fas fa-arrow-right ml-2"
                                ></i>
                            </button>

                            <button
                                v-else
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 shadow-emerald-200"
                                :class="{
                                    'opacity-75 cursor-not-allowed':
                                        form.processing,
                                }"
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
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mt-4">
                        <div
                            class="w-full bg-gray-200 rounded-full h-2 overflow-hidden"
                        >
                            <div
                                class="bg-gradient-to-r from-teal-400 via-emerald-500 to-green-600 h-2 rounded-full transition-all duration-500 ease-out"
                                :style="{
                                    width: `${(currentStep / 8) * 100}%`,
                                }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500 text-right mt-1">
                            Step {{ currentStep }} of 8
                        </p>
                    </div>
                </div>

                <!-- Terms and Privacy Policy -->
                <div v-if="currentStep === 8" class="mt-4">
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
                                    class="text-sm text-green-600 hover:text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium hover:underline hover:decoration-green-600 hover:dark:decoration-green-500 underline-offset-4 dark:text-green-500"
                                    >Terms of Service</a
                                >
                                and
                                <a
                                    :href="route('frontend.privacy-policy')"
                                    target="_blank"
                                    class="text-sm text-green-600 hover:text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium hover:underline hover:decoration-green-600 hover:dark:decoration-green-500 underline-offset-4 dark:text-green-500"
                                    >Privacy Policy</a
                                >.
                            </span>
                        </label>
                        <InputError :message="form.errors.terms" class="mt-2" />
                    </div>
                </div>

                <!-- Sign In Link (shown on all steps) -->
                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Already have an account?
                    <a
                        :href="route('login')"
                        class="font-medium text-green-600 hover:underline hover:decoration-green-600 hover:dark:decoration-green-500 underline-offset-4 dark:text-green-500"
                    >
                        Sign In
                    </a>
                </p>
            </form>
        </div>
    </AuthenticationCard>
</template>
