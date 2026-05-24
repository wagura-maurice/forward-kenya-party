like th<!-- resources/js/Pages/Auth/Register.vue -->
<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import ReCaptcha from '@/Components/ReCaptcha.vue';

const props = defineProps({
    formData: Object,
});

const totalSteps = 4; // Updated to 4 steps to include party resignation validation
const currentStep = ref(1);
const activeAccordion = ref("personal");
const activeRegistrationSection = ref(null);
const acknowledged = ref(false);

const toggleSection = (section) => {
    activeRegistrationSection.value = activeRegistrationSection.value === section ? null : section;
};

// Handle USSD code interaction
const handleUssdClick = () => {
    // For mobile devices, try to initiate a call
    if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        window.location.href = 'tel:*509%23';
    } else {
        // For desktop, show a helpful message
        showToast('info', 'USSD Code', 'On your mobile device, dial *509#');
    }
};

// Handle IPPMS portal click
const handleIppmsClick = () => {
    window.open('https://ippms.orpp.or.ke', '_blank', 'noopener,noreferrer');
};

// Handle eCitizen portal click
const handleECitizenClick = () => {
    window.open('https://accounts.ecitizen.go.ke/en', '_blank', 'noopener,noreferrer');
};

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

const nextStepDescription = computed(() => {
    switch (currentStep.value) {
        case 1:
            return "Account and Personal Information";
        case 2:
            return "Location and Enlisting Information";
        case 3:
            return "Confirmation";
        case 4:
            return "Submit Registration";
        default:
            return "";
    }
});

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

const constituencies = ref([]);
const wards = ref([]);
const isLoadingConstituencies = ref(false);
const isLoadingWards = ref(false);

// Use the form data from props
const special_interest_groups = computed(() => props.formData?.special_interest_groups || []);
const genders = computed(() => props.formData?.genders || []);
const ethnicities = computed(() => props.formData?.ethnicities || []);
const religions = computed(() => props.formData?.religions || []);
const counties = computed(() => props.formData?.locations?.counties || []);

// Format today's date for the form
const today = new Date();
const formattedDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

const form = useForm({
    // Registration Instructions Confirmation
    understood_instructions: false,
    
    // Step 2: Account and Personal Information
    surname: "",
    other_name: "",
    telephone: "",
    identification_type: "national_identification_number", // 'national_identification_number' or 'passport_number'
    identification_number: "",
    party_membership_number: props.formData?.membership_number || "",
    date_of_birth: "",
    special_interest_groups: [],
    gender: "",
    ethnicity_id: null,
    religion_id: null,
    disability_status: false, // 'false', 'true'
    ncpwd_number: "",

    // Step 2: Location and Enlisting Information
    county_id: null,
    constituency_id: null,
    ward_id: null,
    enlisting_date: new Date().toISOString().split("T")[0], // Set to today (July 24, 2025), read-only

    // Step 3: Review & Submit
    terms: false,

    // ReCaptcha response
    'g-recaptcha-response': '',
});

const isLoading = ref(false);
const isSubmitting = ref(false);
const isCheckingMembershipStatus = ref(false);
const membershipStatusError = ref(null);
const membershipStatus = ref(null);
const ippmsRegistrationId = ref(null);
const memberId = ref(null);

const setCaptchaResponse = (response) => {
    form['g-recaptcha-response'] = response;
};

// Watch for county changes to filter constituencies
watch(
    () => form.county_id,
    (newCountyId) => {
        if (newCountyId) {
            const filtered =
                props.formData?.locations?.constituencies?.filter(
                    (c) => c.county_id == newCountyId
                ) || [];
            constituencies.value = filtered;
        } else {
            constituencies.value = [];
        }

        // Reset dependent fields
        form.constituency_id = "";
        form.ward_id = "";
        wards.value = [];
    }
);

// Watch for constituency changes to filter wards
watch(
    () => form.constituency_id,
    (newConstituencyId) => {
        if (newConstituencyId) {
            const filtered =
                props.formData?.locations?.wards?.filter(
                    (w) => w.constituency_id == newConstituencyId
                ) || [];
            wards.value = filtered;
        } else {
            wards.value = [];
        }

        // Reset dependent field
        form.ward_id = "";
    }
);

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

// Watch for identification_number changes to validate against IPPMS
watch(
    () => form.identification_number,
    async (newIdNumber) => {
        // Only trigger when 7, 8, or 9 characters and not already checking
        if ((newIdNumber.length === 7 || newIdNumber.length === 8 || newIdNumber.length === 9) && !isCheckingMembershipStatus.value) {
            // Clear previous errors
            membershipStatusError.value = null;
            membershipStatus.value = null;

            // Start loading state
            isCheckingMembershipStatus.value = true;

            try {
                const response = await axios.post(route('auth.check-membership-status'), {
                    identification_number: newIdNumber
                });

                if (response.data.status === 'success') {
                    const data = response.data.data || {};
                    const statusDescription = data.statusDescription || response.data.membership_status;
                    
                    membershipStatus.value = statusDescription;

                    if (statusDescription === 'Accepted') {
                        // Clear any error states and allow proceeding
                        membershipStatusError.value = null;
                    } else if (statusDescription === 'Rejected') {
                        // Prevent further progress and show error
                        membershipStatusError.value = response.data.message || 'Your ID has been rejected by the authority body.';
                        showToast('error', 'ID Rejected', membershipStatusError.value);
                    }
                } else {
                    membershipStatusError.value = response.data.message || 'Failed to verify membership status.';
                }
            } catch (error) {
                // Handle network failures gracefully
                membershipStatusError.value = error.response?.data?.message || 'Unable to verify membership status at this time. Please try again later.';
                console.error('Membership status check error:', error);
            } finally {
                // End loading state
                isCheckingMembershipStatus.value = false;
            }
        } else if (newIdNumber.length !== 8) {
            // Reset status when ID is not 8 characters
            membershipStatus.value = null;
            membershipStatusError.value = null;
        }
    }
);

const validateStep = (step) => {
    if (step === 1) {
        // Step 1: Party Registration Check - Only validate the acknowledgment
        if (!form.understood_instructions) {
            return { 
                isValid: false, 
                message: "You must acknowledge that you understand the registration process" 
            };
        }
        return { isValid: true };
    }

    if (step === 2) {
        // Step 2: Account and Personal Information
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
            
        // Validate identification number length (7, 8, or 9 characters)
        if (form.identification_number.length < 7 || form.identification_number.length > 9) {
            return {
                isValid: false,
                message: "Identification number must be 7, 8, or 9 characters long"
            };
        }

        // Check if membership status was rejected
        if (membershipStatusError.value) {
            return {
                isValid: false,
                message: membershipStatusError.value
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
        return { isValid: true };
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
        await nextStep();
    } else {
        // Submit form directly to local DB first, then trigger IPPMS verification
        await submit();
    }
};

const submit = async () => {
    isSubmitting.value = true;
    try {
        for (let i = 1; i <= totalSteps; i++) {
            const { isValid, message } = validateStep(i);
            if (!isValid) {
                showToast("error", "Validation Error", `Step ${i}: ${message}`);
                currentStep.value = i;
                window.scrollTo({ top: 0, behavior: "smooth" });
                return;
            }
        }

        // Additional validation for terms in Step 3
        if (!form.terms) {
            showToast(
                "error",
                "Validation Error",
                "You must agree to the terms and affirmation"
            );
            return;
        }

        // Submit form to custom registration endpoint (no auto-login)
        const response = await axios.post(route('auth.register-without-login'), form);

        if (response.data.status === 'success') {
            // Extract member_id and user_id from response
            memberId.value = response.data.data.member_id;
            const userId = response.data.data.user_id;
            
            // Store userId for later login after IPPMS verification
            localStorage.setItem('pending_user_id', userId);
            
            // Show success message for local registration (awaited to prevent SweetAlert conflict)
            await Swal.fire({
                icon: "success",
                title: "Local Registration Complete",
                text: "You have been registered in our local system. Now complete legal verification with IPPMS.",
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            
            // Trigger IPPMS verification flow (only after success modal is fully dismissed)
            showIppmsVerificationModal();
        } else {
            throw new Error(response.data.message || 'Registration failed');
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            // Handle validation errors
            const errors = error.response.data.errors;
            let errorMessage = "Validation failed:\n";
            for (const field in errors) {
                errorMessage += `${field}: ${errors[field].join(', ')}\n`;
            }
            showToast("error", "Registration Failed", errorMessage);
        } else {
            Swal.fire({
                icon: "error",
                title: "Registration Failed",
                text: error.response?.data?.message || error.message || "An error occurred during registration.",
                confirmButtonColor: "#10b981",
            });
        }
    } finally {
        isSubmitting.value = false;
    }
};

// Show IPPMS verification modal with legal consent messaging
const showIppmsVerificationModal = async () => {
    console.log('showIppmsVerificationModal called');
    
    if (!memberId.value) {
        showToast("error", "Error", "Member ID not found. Please contact support.");
        return;
    }

    try {
        console.log('Requesting IPPMS confirmation code for member:', memberId.value);
        
        // Request IPPMS confirmation code
        const response = await axios.post(route('auth.request-ippms-confirmation-code'), {
            member_id: memberId.value
        });

        console.log('IPPMS confirmation code response:', response.data);

        if (response.data.status !== 'success') {
            throw new Error(response.data.message || 'Failed to request IPPMS confirmation code');
        }

        // Extract registrationId from response
        ippmsRegistrationId.value = response.data.data?.registrationId || null;
        console.log('Registration ID:', ippmsRegistrationId.value);

        // Small delay to ensure page is stable before showing modal
        await new Promise(resolve => setTimeout(resolve, 200));

        // Show OTP verification modal using SweetAlert
        console.log('About to show SweetAlert modal');
        
        const result = await Swal.fire({
            title: "Legal Registration Verification",
            html: `
                <div class="text-left">
                    <p class="mb-4 text-sm text-gray-700">
                        <strong>Step 1: Local Registration Complete ✓</strong><br>
                        You have been successfully registered in our local system.
                    </p>
                    
                    <p class="mb-4 text-sm text-gray-700">
                        <strong>Step 2: Legal Consent Required</strong><br>
                        To become a legally registered party member, you must provide formal consent through the IPPMS authority system. This is a mandatory legal requirement.
                    </p>
                    
                    <p class="mb-4 text-sm text-gray-700">
                        <strong>Step 3: Complete Verification</strong><br>
                        Enter the 5-character alphanumeric OTP sent to your registered phone number. This OTP submission is the definitive step that legally synchronizes your identity with the authorities.
                    </p>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                        <p class="text-sm text-blue-700">
                            <strong>Important:</strong> By entering the OTP, you are providing legal consent to register as a party member with the Independent Electoral and Boundaries Commission (IEBC) through IPPMS.
                        </p>
                    </div>
                    
                    <div class="flex flex-col items-center mt-4">
                        <input id="ippms-otp-input" class="swal2-input w-full text-center mb-2" placeholder="Enter 5-character OTP (e.g., L97MT)" maxlength="5" type="text">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: "Complete Legal Registration",
            confirmButtonColor: "#10b981",
            cancelButtonText: "Cancel",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showLoaderOnConfirm: true,
            didOpen: () => {
                console.log('SweetAlert modal opened');
                const otpInput = document.getElementById("ippms-otp-input");
                if (otpInput) {
                    setTimeout(() => otpInput.focus(), 100);
                }
            },
            willClose: () => {
                console.log('SweetAlert modal will close');
            },
            preConfirm: async () => {
                const otpInput = document.getElementById("ippms-otp-input");
                const otp = otpInput.value;
                console.log('preConfirm called with OTP:', otp);

                if (!otp || otp.length !== 5) {
                    Swal.showValidationMessage(
                        "Please enter the 5-character alphanumeric OTP"
                    );
                    return false;
                }

                if (!/^[A-Za-z0-9]{5}$/.test(otp)) {
                    Swal.showValidationMessage(
                        "OTP must be exactly 5 alphanumeric characters (e.g., L97MT)"
                    );
                    return false;
                }

                return otp;
            }
        });

        console.log('SweetAlert result:', result);

        if (result.isConfirmed) {
            console.log('User confirmed OTP, calling completeIppmsRegistration');
            await completeIppmsRegistration(result.value);
        } else {
            console.log('User cancelled or dismissed modal');
        }

    } catch (error) {
        console.error('IPPMS verification error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Verification Failed',
            text: error.response?.data?.message || error.message || 'Unable to initiate IPPMS verification. Please try again later.',
            confirmButtonColor: '#10b981',
        });
    }
};

// Complete IPPMS registration with OTP
const completeIppmsRegistration = async (otp) => {
    try {
        Swal.fire({
            title: "Completing Legal Registration",
            text: "Please wait while we synchronize your registration with IPPMS...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const response = await axios.post(route('auth.complete-ippms-registration'), {
            member_id: memberId.value,
            otp: otp,
            registration_id: ippmsRegistrationId.value
        });

        if (response.data.status === 'success') {
            // Log the user in after successful IPPMS verification
            const userId = localStorage.getItem('pending_user_id');
            
            if (userId) {
                const loginResponse = await axios.post(route('auth.login-after-ippms-verification'), {
                    user_id: userId
                });

                if (loginResponse.data.status === 'success') {
                    // Clear the stored user_id
                    localStorage.removeItem('pending_user_id');
                    
                    // Store the Sanctum token
                    if (loginResponse.data.data.token) {
                        localStorage.setItem('sanctum_token', loginResponse.data.data.token);
                    }
                    
                    Swal.fire({
                        icon: "success",
                        title: "Legal Registration Complete",
                        html: `
                            <div class="text-left">
                                <p class="mb-2">Congratulations! You are now a legally registered party member.</p>
                                <p class="text-sm text-gray-600">Your registration has been successfully synchronized with the IPPMS authority system.</p>
                            </div>
                        `,
                        confirmButtonColor: "#10b981",
                    }).then(() => {
                        // Redirect to dashboard
                        window.location.href = route('dashboard');
                    });
                } else {
                    throw new Error(loginResponse.data.message || 'Failed to log in after IPPMS verification');
                }
            } else {
                throw new Error('User ID not found. Please try logging in manually.');
            }
        } else {
            throw new Error(response.data.message || 'Failed to complete IPPMS registration');
        }

    } catch (error) {
        console.error('IPPMS registration completion error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            html: `
                <div class="text-left">
                    <p class="mb-2">${error.response?.data?.message || error.message || 'Failed to complete IPPMS registration.'}</p>
                    <p class="text-sm text-gray-600">Your local registration is saved. You can retry the IPPMS verification later from your dashboard.</p>
                </div>
            `,
            confirmButtonColor: '#10b981',
        });
    }
};

const getLocationName = (id, locations) => {
    const location = locations.find((loc) => loc.id == id);
    return location ? toTitleCase(location.name) : "Not specified";
};

const getItemName = (id, items) => {
    const item = items.find((item) => item.id == id);
    return item ? toTitleCase(item.name) : "Not specified";
};

const getSpecialInterestGroupName = (id) => {
    if (!id) return '';
    const interest = special_interest_groups.value.find((item) => item.id == id);
    return interest ? interest.name : id;
};

const toTitleCase = (str) => {
    return str.replace(/\b\w/g, (match) => match.toUpperCase());
};

// Format date as YYYY-MM-DD for display
const formatDate = (date) => {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const genderDisplayName = computed(() => {
    if (form.gender === "XY") return "Male";
    if (form.gender === "XX") return "Female";
    return form.gender || "";
});

const canProceedToNextStep = computed(() => {
    // For step 1, only check the acknowledgment checkbox
    if (currentStep.value === 1) {
        return form.understood_instructions;
    } 
    // For step 2, check personal information fields
    else if (currentStep.value === 2) {
        return (
            form.surname &&
            form.other_name &&
            form.telephone &&
            form.identification_type &&
            form.identification_number &&
            form.date_of_birth &&
            form.gender &&
            form.ethnicity_id &&
            form.religion_id &&
            (form.disability_status === false || form.ncpwd_number)
        );
    } 
    // For step 3, check location fields
    else if (currentStep.value === 3) {
        return form.county_id && form.constituency_id && form.ward_id;
    }
    // For step 4 (confirmation), check terms acceptance
    return form.terms;
});
</script>

<template>
    <Head title="Sign up" />

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

                    <form
                        @submit.prevent="handleSubmit"
                        class="space-y-4"
                        :disabled="isSubmitting"
                    >
                        <!-- Step 1: Party Registration Check -->
                        <div v-if="currentStep === 1" class="space-y-6">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 1: Check Your Party Registration Status
                            </h2>

                            <div
                                class="bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 p-4 mb-6"
                            >
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i
                                            class="fas fa-info-circle text-blue-500"
                                        ></i>
                                    </div>
                                    <div class="ml-3">
                                        <p
                                            class="text-sm text-blue-700 dark:text-blue-300"
                                        >
                                            Before proceeding with your
                                            registration, you need to verify
                                            your current political party
                                            membership status with the Office of
                                            the Registrar of Political Parties
                                            (ORPP).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Check Registration Status - Collapsible -->
                                <div
                                    class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-4"
                                >
                                    <button
                                        type="button"
                                        @click="toggleSection('checkStatus')"
                                        class="w-full px-4 py-5 sm:px-6 text-left bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 rounded-t-lg"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <h3
                                                class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                                            >
                                                How to Check Your Party
                                                Registration Status?
                                            </h3>
                                            <i
                                                :class="[
                                                    activeRegistrationSection ===
                                                    'checkStatus'
                                                        ? 'fa-chevron-up'
                                                        : 'fa-chevron-down',
                                                    'fas',
                                                    'transition-transform',
                                                    'duration-200',
                                                    'text-gray-500',
                                                    'text-lg',
                                                ]"
                                            ></i>
                                        </div>
                                    </button>
                                    <div
                                        v-show="
                                            activeRegistrationSection ===
                                            'checkStatus'
                                        "
                                        class="border-t border-gray-200 dark:border-gray-600 px-4 py-5 sm:p-6"
                                    >
                                        <div class="space-y-4">
                                            <p
                                                class="text-sm text-gray-700 dark:text-gray-300"
                                            >
                                                To check your current party
                                                registration status, use one of
                                                the following official ORPP
                                                channels:
                                            </p>

                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4"
                                            >
                                                <!-- Mobile Check -->
                                                <div
                                                    class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800"
                                                >
                                                    <h4
                                                        class="font-medium text-purple-800 dark:text-purple-200 mb-3 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-laptop mr-2 text-purple-600 dark:text-purple-400"
                                                        ></i>
                                                        Mobile Check (USSD)
                                                    </h4>
                                                    <ol
                                                        class="list-decimal pl-5 space-y-1 text-sm text-blue-700 dark:text-blue-300"
                                                    >
                                                        <li>
                                                            Dial
                                                            <button
                                                                @click.stop="
                                                                    handleUssdClick
                                                                "
                                                                class="font-mono bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded text-sm hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors"
                                                            >
                                                                *509#
                                                            </button>
                                                            on your mobile phone
                                                        </li>
                                                        <li>
                                                            If first time,
                                                            register with your
                                                            ID and name
                                                        </li>
                                                        <li>
                                                            You'll receive an
                                                            ORPP PIN via SMS
                                                        </li>
                                                        <li>
                                                            Dial
                                                            <button
                                                                @click.stop="
                                                                    handleUssdClick
                                                                "
                                                                class="font-mono bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded text-sm hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors"
                                                            >
                                                                *509#
                                                            </button>
                                                            again and enter your
                                                            PIN
                                                        </li>
                                                        <li>
                                                            Select "Check
                                                            Membership Status"
                                                        </li>
                                                        <li>
                                                            View your current
                                                            party affiliation
                                                        </li>
                                                    </ol>
                                                </div>

                                                <!-- Online Check -->
                                                <div
                                                    class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-100 dark:border-purple-800"
                                                >
                                                    <h4
                                                        class="font-medium text-purple-800 dark:text-purple-200 mb-3 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-laptop mr-2 text-purple-600 dark:text-purple-400"
                                                        ></i>
                                                        Online Check (IPPMS)
                                                    </h4>
                                                    <ol
                                                        class="list-decimal pl-5 space-y-1 text-sm text-purple-700 dark:text-purple-300"
                                                    >
                                                        <li>
                                                            Visit
                                                            <button
                                                                @click.stop="
                                                                    handleIppmsClick
                                                                "
                                                                class="text-purple-700 dark:text-purple-300 hover:underline underline-offset-4 font-medium"
                                                            >
                                                                ippms.orpp.or.ke
                                                            </button>
                                                        </li>
                                                        <li>
                                                            Click "Check
                                                            Membership Status"
                                                        </li>
                                                        <li>
                                                            Enter ID number and
                                                            date of birth
                                                        </li>
                                                        <li>
                                                            Complete CAPTCHA
                                                            verification
                                                        </li>
                                                        <li>
                                                            View your current
                                                            party affiliation
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- If Registered with Another Party -->
                                <div
                                    class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4"
                                >
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i
                                                class="fas fa-exclamation-triangle text-yellow-400"
                                            ></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3
                                                class="text-sm font-medium text-yellow-800 dark:text-yellow-200"
                                            >
                                                If You're Registered with
                                                Another Party
                                            </h3>
                                            <div
                                                class="mt-2 text-sm text-yellow-700 dark:text-yellow-300"
                                            >
                                                <p>
                                                    You must first resign from
                                                    your current party before
                                                    joining Forward Kenya Party.
                                                    Here's how:
                                                </p>
                                                <ol
                                                    class="list-decimal pl-5 mt-2 space-y-1"
                                                >
                                                    <li>
                                                        Use the USSD code
                                                        <button
                                                            @click.stop="
                                                                handleUssdClick
                                                            "
                                                            class="font-mono bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded text-sm hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors"
                                                        >
                                                            *509#
                                                        </button>
                                                        or visit the
                                                        <button
                                                            @click.stop="
                                                                handleIppmsClick
                                                            "
                                                            class="text-purple-700 dark:text-purple-300 hover:underline underline-offset-4 font-medium"
                                                        >
                                                            IPPMS portal
                                                        </button>
                                                        to resign
                                                    </li>
                                                    <li>
                                                        Wait for confirmation of
                                                        your resignation
                                                        (usually within 24
                                                        hours)
                                                    </li>
                                                    <li>
                                                        Once confirmed, you can
                                                        proceed with your
                                                        Forward Kenya Party
                                                        registration
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Official Registration Options - Collapsible -->
                                <div
                                    class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg"
                                >
                                    <button
                                        type="button"
                                        @click="toggleSection('register')"
                                        class="w-full px-4 py-5 sm:px-6 text-left bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 rounded-t-lg"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <h3
                                                class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                                            >
                                                How to Register with Forward
                                                Kenya Party? (Official Channels)
                                            </h3>
                                            <i
                                                :class="[
                                                    activeRegistrationSection ===
                                                    'register'
                                                        ? 'fa-chevron-up'
                                                        : 'fa-chevron-down',
                                                    'fas',
                                                    'transition-transform',
                                                    'duration-200',
                                                    'text-gray-500',
                                                    'text-lg',
                                                ]"
                                            ></i>
                                        </div>
                                    </button>
                                    <div
                                        v-show="
                                            activeRegistrationSection ===
                                            'register'
                                        "
                                        class="border-t border-gray-200 dark:border-gray-600 px-4 py-5 sm:p-6"
                                    >
                                        <div class="space-y-4">
                                            <p
                                                class="text-sm text-gray-700 dark:text-gray-300"
                                            >
                                                To officially register with
                                                Forward Kenya Party, you must
                                                complete your registration
                                                through one of the following
                                                official ORPP channels:
                                            </p>

                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4"
                                            >
                                                <!-- Mobile Registration -->
                                                <div
                                                    class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-800"
                                                >
                                                    <h4
                                                        class="font-medium text-purple-800 dark:text-purple-200 mb-3 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-laptop mr-2 text-purple-600 dark:text-purple-400"
                                                        ></i>
                                                        Mobile Registration
                                                        (USSD)
                                                    </h4>
                                                    <ol
                                                        class="list-decimal pl-5 space-y-1 text-sm text-green-700 dark:text-green-300"
                                                    >
                                                        <li>
                                                            Dial
                                                            <button
                                                                @click.stop="
                                                                    handleUssdClick
                                                                "
                                                                class="font-mono bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded text-sm hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors"
                                                            >
                                                                *509#
                                                            </button>
                                                            on your mobile phone
                                                        </li>
                                                        <li>
                                                            Enter your ORPP PIN
                                                        </li>
                                                        <li>
                                                            Select "Join a
                                                            Party"
                                                        </li>
                                                        <li>
                                                            Enter party code:
                                                            <span
                                                                class="font-mono bg-green-100 dark:bg-green-800 px-1.5 py-0.5 rounded"
                                                                >FKP</span
                                                            >
                                                            (Forward Kenya
                                                            Party)
                                                        </li>
                                                        <li>
                                                            Follow the prompts
                                                            to complete your
                                                            registration
                                                        </li>
                                                    </ol>
                                                </div>

                                                <!-- Online Registration -->
                                                <div
                                                    class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-100 dark:border-purple-800"
                                                >
                                                    <h4
                                                        class="font-medium text-purple-800 dark:text-purple-200 mb-3 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-laptop mr-2 text-purple-600 dark:text-purple-400"
                                                        ></i>
                                                        Online Registration
                                                        (IPPMS)
                                                    </h4>
                                                    <ol
                                                        class="list-decimal pl-5 space-y-1 text-sm text-purple-700 dark:text-purple-300"
                                                    >
                                                        <li>
                                                            Visit
                                                            <button
                                                                @click.stop="
                                                                    handleIppmsClick
                                                                "
                                                                class="text-purple-700 dark:text-purple-300 hover:underline underline-offset-4 font-medium"
                                                            >
                                                                ippms.orpp.or.ke
                                                            </button>
                                                            or log in via
                                                            <button
                                                                @click.stop="
                                                                    handleECitizenClick
                                                                "
                                                                class="text-purple-700 dark:text-purple-300 hover:underline underline-offset-4 font-medium"
                                                            >
                                                                eCitizen
                                                            </button>
                                                        </li>
                                                        <li>
                                                            Navigate to "Join a
                                                            Party" section
                                                        </li>
                                                        <li>
                                                            Search for "Forward
                                                            Kenya Party"
                                                        </li>
                                                        <li>
                                                            Complete the online
                                                            registration form
                                                        </li>
                                                        <li>
                                                            Submit and wait for
                                                            confirmation
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmation Checkbox -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <Checkbox
                                            id="understood_instructions"
                                            v-model:checked="
                                                form.understood_instructions
                                            "
                                            required
                                            class="mt-0.5"
                                        />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label
                                            for="understood_instructions"
                                            class="font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            I understand that I must complete my
                                            registration through the official
                                            ORPP channels (<button
                                                @click.stop="handleUssdClick"
                                                class="font-mono bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded text-sm hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors"
                                            >
                                                *509#
                                            </button>
                                            or
                                            <button
                                                @click.stop="handleIppmsClick"
                                                class="text-purple-700 dark:text-purple-300 hover:underline underline-offset-4 font-medium"
                                            >
                                                IPPMS portal</button
                                            >) after submitting this form.
                                        </label>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                        >
                                            This form is for pre-registration
                                            only. Your membership will only be
                                            valid after completing the official
                                            ORPP registration process.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Account and Personal Information -->
                        <div v-if="currentStep === 2" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 2: Account and Personal Information
                            </h2>

                            <!-- Surname and Other Name (Responsive Grid) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="surname"
                                            value="Surname"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="surname"
                                        v-model="form.surname"
                                        type="text"
                                        :class="[
                                            'block w-full rounded-md shadow-sm sm:text-sm py-2 px-3 border transition duration-150 ease-in-out',
                                            form.errors.surname
                                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                                : 'border-gray-300 focus:border-green-500 focus:ring-green-500',
                                            isSubmitting
                                                ? 'opacity-75 cursor-not-allowed'
                                                : '',
                                        ]"
                                        placeholder="Enter your surname"
                                        required
                                        autocomplete="name"
                                        :disabled="isSubmitting"
                                        :aria-busy="isSubmitting"
                                        autofocus
                                    />
                                    <InputError
                                        :message="form.errors.surname"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="other_name"
                                            value="Other Name"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="other_name"
                                        v-model="form.other_name"
                                        type="text"
                                        :class="[
                                            'block w-full rounded-md shadow-sm sm:text-sm py-2 px-3 border transition duration-150 ease-in-out',
                                            form.errors.other_name
                                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                                : 'border-gray-300 focus:border-green-500 focus:ring-green-500',
                                            isSubmitting
                                                ? 'opacity-75 cursor-not-allowed'
                                                : '',
                                        ]"
                                        placeholder="Enter your other name"
                                        required
                                        autocomplete="name"
                                        :disabled="isSubmitting"
                                        :aria-busy="isSubmitting"
                                        autofocus
                                    />
                                    <InputError
                                        :message="form.errors.other_name"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- Member's Mobile No. and Identification Type (Responsive Grid) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="telephone"
                                            value="Member's Mobile No."
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="telephone"
                                        v-model="form.telephone"
                                        type="tel"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        placeholder="e.g. 0712345678"
                                        required
                                        autocomplete="tel"
                                        :disabled="isSubmitting"
                                        :aria-busy="isSubmitting"
                                        pattern="\d{10}"
                                    />
                                    <InputError
                                        :message="form.errors.telephone"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <!-- Hidden identification_type field -->
                                <input type="hidden" name="identification_type" v-model="form.identification_type" value="national_identification_number">

                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            value="Special Interest Group (optional)"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                    </div>
                                    <VueSelect
                                        id="special_interest_groups"
                                        v-model="form.special_interest_groups"
                                        :options="special_interest_groups"
                                        label="name"
                                        multiple
                                        :reduce="option => option.id"
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
                                        <template #option="{ name }">
                                            <div class="flex items-center">
                                                <span>{{ name }}</span>
                                            </div>
                                        </template>
                                        <template #selected-option="{ name }">
                                            <div class="bg-green-50 text-green-800 text-xs px-2 py-1 rounded mr-1">
                                                {{ name }}
                                            </div>
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.special_interest_groups"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- National ID and Party Membership No. (Half Width, Party Read-Only) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            :for="
                                                form.identification_type ===
                                                'national_identification_number'
                                                    ? 'national_identification_number'
                                                    : 'passport_number'
                                            "
                                            :value="
                                                form.identification_type ===
                                                'national_identification_number'
                                                    ? 'National Identification Number'
                                                    : 'Passport Number'
                                            "
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        :id="
                                            form.identification_type ===
                                            'national_identification_number'
                                                ? 'national_identification_number'
                                                : 'passport_number'
                                        "
                                        v-model="form.identification_number"
                                        type="number"
                                        :class="[
                                            'block w-full rounded-md shadow-sm sm:text-sm py-2 px-3 border transition duration-150 ease-in-out',
                                            membershipStatusError 
                                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
                                                : 'border-gray-300 focus:border-green-500 focus:ring-green-500'
                                        ]"
                                        :placeholder="
                                            form.identification_type ===
                                            'national_identification_number'
                                                ? 'Enter National Identification Number'
                                                : 'Enter Passport Number'
                                        "
                                        required
                                        maxlength="8"
                                    />
                                    <div v-if="isCheckingMembershipStatus" class="mt-1 text-sm text-gray-500 flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Verifying membership status...
                                    </div>
                                    <InputError
                                        :message="
                                            form.errors.identification_number || membershipStatusError
                                        "
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="party_membership_number"
                                            value="Party Membership Number"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="party_membership_number"
                                        v-model="form.party_membership_number"
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 bg-gray-200 text-gray-500 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        required
                                        readonly
                                    />
                                    <InputError
                                        :message="
                                            form.errors.party_membership_number
                                        "
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- Date of Birth and Sex (Responsive Grid) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="date_of_birth"
                                            value="Date of Birth (dd-mm-yyyy)"
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
                                        required
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
                                    />
                                    <InputError
                                        :message="form.errors.date_of_birth"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            value="Sex"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        id="gender"
                                        v-model="form.gender"
                                        :options="genders"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        placeholder="Select sex"
                                        class="w-full"
                                        :class="{
                                            'border-red-300':
                                                form.errors.gender,
                                        }"
                                        required
                                        :clearable="false"
                                    />
                                    <InputError
                                        :message="form.errors.gender"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- Ethnicity and Religion (Responsive Grid) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                        id="ethnicity_id"
                                        v-model="form.ethnicity_id"
                                        :options="ethnicities"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        placeholder="Select ethnicity"
                                        class="w-full"
                                        :class="{
                                            'border-red-300':
                                                form.errors.ethnicity_id,
                                        }"
                                        required
                                        :clearable="false"
                                    />
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
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        id="religion_id"
                                        v-model="form.religion_id"
                                        :options="religions"
                                        label="name"
                                        :reduce="(option) => option.id"
                                        placeholder="Select religion"
                                        class="w-full"
                                        :class="{
                                            'border-red-300':
                                                form.errors.religion_id,
                                        }"
                                        required
                                        :clearable="false"
                                    />
                                    <InputError
                                        :message="form.errors.religion_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- Disability Status and NCPWD Number (Responsive Grid) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="are you a pwd?"
                                            value="Are you a PWD?"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
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
                                                class="ml-1 text-sm text-gray-700"
                                                >No</span
                                            >
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input
                                                type="radio"
                                                v-model="form.disability_status"
                                                :value="true"
                                                class="text-green-600 border-gray-300 focus:ring-green-500"
                                            />
                                            <span
                                                class="ml-1 text-sm text-gray-700"
                                                >Yes</span
                                            >
                                        </label>
                                    </div>
                                    <InputError
                                        :message="form.errors.disability_status"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>

                                <div
                                    v-if="form.disability_status"
                                    class="space-y-2"
                                >
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="ncpwd_number"
                                            value="NCPWD Number"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            <i
                                                class="fas fa-star text-red-500 text-xs ml-1"
                                            ></i>
                                        </InputLabel>
                                    </div>
                                    <TextInput
                                        id="ncpwd_number"
                                        v-model="form.ncpwd_number"
                                        type="text"
                                        :class="[
                                            'block w-full rounded-md shadow-sm sm:text-sm py-2 px-3 border transition duration-150 ease-in-out',
                                            form.errors.ncpwd_number
                                                ? 'border-red-500'
                                                : 'border-gray-300 focus:border-green-500 focus:ring-green-500',
                                        ]"
                                        required
                                        placeholder="Enter your NCPWD number"
                                    />
                                    <InputError
                                        :message="form.errors.ncpwd_number"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Location and Enlisting Information -->
                        <div v-if="currentStep === 3" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 3: Location and Enlisting Information
                            </h2>
                            <!-- County, Constituency, and Ward (Responsive Grid) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="county_id"
                                            value="County of Member Registration"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.county_id"
                                        :options="counties"
                                        label="name"
                                        :reduce="(county) => county.id"
                                        placeholder="Select County"
                                        class="w-full capitalize-select"
                                        :class="{
                                            'border-red-500':
                                                form.errors.county_id,
                                        }"
                                        :clearable="false"
                                    >
                                        <template #option="{ name }">
                                            <span class="capitalize">{{ name }}</span>
                                        </template>
                                        <template #selected-option="{ name }">
                                            <span class="capitalize">{{ name }}</span>
                                        </template>
                                        <template #no-options>
                                            <div
                                                class="text-sm text-gray-500 py-2 px-3"
                                            >
                                                {{
                                                    counties.length === 0
                                                        ? "No counties available"
                                                        : "Type to search..."
                                                }}
                                            </div>
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
                                            for="constituency_id"
                                            value="Constituency of Member Registration"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.constituency_id"
                                        :options="constituencies"
                                        label="name"
                                        :reduce="
                                            (constituency) => constituency.id
                                        "
                                        placeholder="Select Constituency"
                                        class="w-full capitalize-select"
                                        :class="{
                                            'border-red-500':
                                                form.errors.constituency_id,
                                        }"
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
                                            <div
                                                class="text-sm text-gray-500 py-2 px-3"
                                            >
                                                {{
                                                    !form.county_id
                                                        ? "Please select a county first"
                                                        : "No constituencies found"
                                                }}
                                            </div>
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.constituency_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- Ward and Enlisting Date (Responsive Grid) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="ward_id"
                                            value="Ward of Member Registration"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <VueSelect
                                        v-model="form.ward_id"
                                        :options="wards"
                                        label="name"
                                        :reduce="(ward) => ward.id"
                                        placeholder="Select Ward"
                                        class="w-full capitalize-select"
                                        :class="{
                                            'border-red-500':
                                                form.errors.ward_id,
                                        }"
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
                                            <div
                                                class="text-sm text-gray-500 py-2 px-3"
                                            >
                                                {{
                                                    !form.constituency_id
                                                        ? "Please select a constituency first"
                                                        : "No wards found"
                                                }}
                                            </div>
                                        </template>
                                    </VueSelect>
                                    <InputError
                                        :message="form.errors.ward_id"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="enlistment_date"
                                            value="Membership Enlistment Date"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <input
                                        id="enlistment_date"
                                        type="text"
                                        :value="formatDate(new Date())"
                                        readonly
                                        class="block w-full rounded-md shadow-sm sm:text-sm py-2 px-3 border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                                    />
                                    <InputError
                                        :message="form.errors.enlistment_date"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Confirmation -->
                        <div v-if="currentStep === 4" class="space-y-6">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 4: Confirmation
                            </h2>

                            <!-- Account Information Card -->
                            <div
                                class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                            >
                                <div
                                    class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                    @click="
                                        toggleAccordion('account information')
                                    "
                                >
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        Account Information
                                    </h3>
                                    <i
                                        class="fas fa-chevron-down w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                activeAccordion ===
                                                'account information',
                                        }"
                                    ></i>
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
                                                - ({{
                                                    form.identification_type ===
                                                    "national_identification_number"
                                                        ? "National Identification Number"
                                                        : "Passport Number"
                                                }})
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
                                    </dl>
                                </div>
                            </div>

                            <!-- Personal Information Card -->
                            <div
                                class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
                            >
                                <div
                                    class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                                    @click="
                                        toggleAccordion('personal information')
                                    "
                                >
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        Personal Information
                                    </h3>
                                    <i
                                        class="fas fa-chevron-down w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                activeAccordion ===
                                                'personal information',
                                        }"
                                    ></i>
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
                                                Sex
                                            </dt>
                                            <dd
                                                class="mt-1 text-sm text-gray-900 sm:col-span-2"
                                            >
                                                {{
                                                    genderDisplayName ||
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
                                                    getItemName(
                                                        form.religion_id,
                                                        religions
                                                    )
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
                                        <div v-if="form.special_interest_groups && form.special_interest_groups.length > 0" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Special Interest Groups:
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                                <div class="flex flex-wrap gap-2">
                                                    <span 
                                                        v-for="(interest, index) in form.special_interest_groups" 
                                                        :key="index"
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                    >
                                                        {{ getSpecialInterestGroupName(interest) }}
                                                    </span>
                                                </div>
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
                                    @click="
                                        toggleAccordion(
                                            'location and enlisting information'
                                        )
                                    "
                                >
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        Location and Enlisting Information
                                    </h3>
                                    <i
                                        class="fas fa-chevron-down w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                activeAccordion ===
                                                'location and enlisting information',
                                        }"
                                    ></i>
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
                                            class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                        >
                                            <dt
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Constituency of Member
                                                Registration
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
                                            class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                        >
                                            <dt
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Ward of Member Registration
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

                            <!-- Terms and Affirmation -->
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
                                                    I, the undersigned, do
                                                    hereby
                                                    affirm/declare/confirm/verify
                                                    that I am not a registered
                                                    member of any other
                                                    registered political party
                                                    in Kenya. By signing up, I
                                                    agree to the
                                                    <a
                                                        :href="
                                                            route(
                                                                'frontend.terms-and-conditions'
                                                            )
                                                        "
                                                        target="_blank"
                                                        class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                    >
                                                        Terms of Service
                                                    </a>
                                                    <span class="mx-1"
                                                        >and</span
                                                    >
                                                    <a
                                                        :href="
                                                            route(
                                                                'frontend.privacy-policy'
                                                            )
                                                        "
                                                        target="_blank"
                                                        class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                    >
                                                        Privacy Policy
                                                    </a>
                                                    and affirm that all
                                                    information provided is true
                                                    and accurate.
                                                </span>
                                            </div>
                                        </InputLabel>
                                        <p
                                            class="text-xs text-gray-500 mt-2 sm:mt-1"
                                        >
                                            You must agree to the terms of
                                            service and our privacy policy to
                                            create an account.
                                        </p>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.terms"
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>

                            <!-- reCAPTCHA Component -->
                            <div class="mt-6">
                                <ReCaptcha
                                    action="register"
                                    :site-key="$page.props.recaptchaSiteKey"
                                    @captcha-response="setCaptchaResponse"
                                />
                                <InputError
                                    :message="
                                        form.errors['g-recaptcha-response']
                                    "
                                    class="mt-1 text-sm text-red-600"
                                />
                            </div>
                        </div>

                        <!-- Shared Navigation Buttons -->
                        <div class="flex items-center justify-between pt-6">
                            <button
                                v-if="currentStep > 1"
                                type="button"
                                @click="prevStep"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                :disabled="currentStep === 1 || isSubmitting"
                                :aria-busy="isSubmitting"
                            >
                                <i
                                    v-if="isSubmitting"
                                    class="fas fa-spinner fa-spin mr-2 text-gray-700"
                                ></i>
                                {{
                                    isSubmitting ? "Processing..." : "Previous"
                                }}
                            </button>
                            <div v-else class="w-24"></div>

                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-75 disabled:cursor-not-allowed"
                                :disabled="
                                    !canProceedToNextStep || isSubmitting
                                "
                                :aria-busy="isSubmitting"
                            >
                                <i
                                    v-if="form.processing"
                                    class="fas fa-spinner fa-spin mr-2"
                                ></i>
                                <template v-else>
                                    <span class="hidden sm:inline">
                                        {{
                                            currentStep === 4
                                                ? "Submit Application"
                                                : currentStep === 1
                                                ? "Start: With Personal Information"
                                                : "Next: " + nextStepDescription
                                        }}
                                    </span>
                                    <span class="sm:hidden">{{
                                        currentStep === 4 ? "Submit" : "Next"
                                    }}</span>
                                    <i
                                        :class="
                                            currentStep === 4
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
                                    {{ Math.round((currentStep / 4) * 100) }}%
                                    Complete
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div
                                    class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300"
                                    :style="{
                                        width: `${(currentStep / 4) * 100}%`,
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
