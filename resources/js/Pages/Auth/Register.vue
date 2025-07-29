<!-- resources/js/Pages/Auth/Register.vue -->
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

const props = defineProps({
    formData: Object,
});

// Get OTP configuration from props
const otpConfig = computed(
    () =>
        props.formData?.otp_config || {
            ttl: 300, // Default: 5 minutes in seconds
            rate_limit: 60, // Default: 60 seconds between requests
            attempts_limit: 5, // Default: 5 attempts before OTP is invalidated
        }
);

const totalSteps = 3; // Updated to 3 steps to include confirmation
const currentStep = ref(1);
const activeAccordion = ref("personal");

const stepDescription = computed(() => {
    switch (currentStep.value) {
        case 1:
            return "Account and Personal Information";
        case 2:
            return "Location and Enlisting Information";
        case 3:
            return "Confirmation";
        default:
            return "";
    }
});

const nextStepDescription = computed(() => {
    switch (currentStep.value) {
        case 1:
            return "Location and Enlisting Information";
        case 2:
            return "Confirmation";
        case 3:
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
const genders = computed(() => props.formData?.genders || []);
const ethnicities = computed(() => props.formData?.ethnicities || []);
const religions = computed(() => props.formData?.religions || []);
const counties = computed(() => {
    console.log("Counties data:", props.formData?.locations?.counties);
    return props.formData?.locations?.counties || [];
});

const form = useForm({
    // Step 1: Account and Personal Information
    surname: "",
    other_name: "",
    telephone: "",
    identification_type: "national_identification_number", // 'national_identification_number' or 'passport_number'
    identification_number: "",
    party_membership_number: props.formData?.membership_number || "",
    date_of_birth: "",
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

    // OTP verification
    otpVerified: false,
});

const isLoading = ref(false);
const isSubmitting = ref(false);

// Watch for county changes to filter constituencies
watch(
    () => form.county_id,
    (newCountyId) => {
        console.log("County changed:", newCountyId);
        if (newCountyId) {
            const filtered =
                props.formData?.locations?.constituencies?.filter(
                    (c) => c.county_id == newCountyId
                ) || [];
            console.log("Filtered constituencies:", filtered);
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
        console.log("Constituency changed:", newConstituencyId);
        if (newConstituencyId) {
            const filtered =
                props.formData?.locations?.wards?.filter(
                    (w) => w.constituency_id == newConstituencyId
                ) || [];
            console.log("Filtered wards:", filtered);
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

const validateStep = (step) => {
    if (step === 1) {
        // Step 1: Account and Personal Information
        if (!form.surname?.trim())
            return { isValid: false, message: "Surname is required" };
        if (!form.other_name?.trim())
            return { isValid: false, message: "Other name is required" };
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
        if (!form.identification_number?.trim())
            return {
                isValid: false,
                message: `${
                    form.identification_type ===
                    "national_identification_number"
                        ? "National Identification Number"
                        : "Passport Number"
                } is required`,
            };
        if (!form.date_of_birth)
            return { isValid: false, message: "Date of birth is required" };
        if (!form.gender) return { isValid: false, message: "Sex is required" };
        if (!form.ethnicity_id)
            return { isValid: false, message: "Ethnicity is required" };
        if (!form.religion_id)
            return { isValid: false, message: "Religion is required" };
        if (form.disability_status === true && !form.ncpwd_number?.trim())
            return {
                isValid: false,
                message:
                    "NCPWD number is required when a disability is selected",
            };
        return { isValid: true };
    }

    if (step === 2) {
        // Step 2: Location and Enlisting Information
        if (!form.county_id)
            return {
                isValid: false,
                message: "County of voter registration is required",
            };
        if (!form.constituency_id)
            return {
                isValid: false,
                message: "Constituency of voter registration is required",
            };
        if (!form.ward_id)
            return {
                isValid: false,
                message: "Ward of voter registration is required",
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
        // If OTP is not verified, show OTP verification modal
        if (!form.otpVerified) {
            await sendOtp();
        } else {
            await submit();
        }
    }
};

// Send OTP to the user's phone number
const sendOtp = async () => {
    try {
        // Validate the phone number
        if (!form.telephone) {
            showToast(
                "error",
                "Validation Error",
                "Phone number is required for OTP verification"
            );
            return;
        }

        // Show loading state
        Swal.fire({
            title: "Sending OTP",
            text: "Please wait while we send an OTP to your phone...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Reset attempts counter and expiration time when requesting new OTP
        otpAttempts.value = 0;
        cleanupOtpTimer();
        const expTime = new Date();
        expTime.setSeconds(expTime.getSeconds() + otpConfig.value.ttl);
        otpExpirationTime.value = expTime;

        // Send OTP request
        const response = await axios.post(route("otp.send"), {
            telephone: form.telephone,
            critical: true, // Mark as critical for registration
        });

        if (response.data.status === "success") {
            // Show OTP verification modal
            showOtpVerificationModal();
        } else {
            throw new Error(response.data.message || "Failed to send OTP. Please try again.");
        }
    } catch (error) {
        console.error("OTP send error:", error);

        // Check if it's a rate limit error
        if (
            error.response?.data?.message &&
            error.response.data.message.includes("wait")
        ) {
            // Show countdown timer for rate limit
            let secondsLeft = otpConfig.value.rate_limit; // Use the rate limit from config

            // Try to extract the exact time from the message if available
            const timeMatch = error.response.data.message.match(
                /wait (\d+) (?:seconds|second)/i
            );
            if (timeMatch && timeMatch[1]) {
                secondsLeft = parseInt(timeMatch[1]);
            }

            const timerInterval = setInterval(() => {
                secondsLeft -= 1;

                // Update the timer text
                const timerElement = document.getElementById("otp-timer");
                if (timerElement) {
                    timerElement.textContent = `${secondsLeft}`;
                }

                // When timer reaches zero
                if (secondsLeft <= 0) {
                    clearInterval(timerInterval);
                    Swal.close();
                }
            }, 1000);

            Swal.fire({
                icon: "warning",
                title: "Rate Limited",
                html: `
                    <p>You must wait before requesting another OTP.</p>
                    <div class="mt-4 text-center">
                        <p>Try again in <span id="otp-timer" class="font-bold text-lg">${secondsLeft}</span> seconds</p>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#10b981",
                willClose: () => {
                    clearInterval(timerInterval);
                },
            });
        } else {
            // Regular error handling
            Swal.fire({
                icon: "error",
                title: "OTP Error",
                text:
                    error.response?.data?.message ||
                    "Failed to send OTP. Please try again.",
            });
        }
    }
};

// Track OTP verification attempts and timer state
const otpAttempts = ref(0);
const maxOtpAttempts = computed(() => otpConfig.value.attempts_limit || 5);
const otpExpirationTime = ref(null);
let otpTimerInterval = null;

// Format time remaining
const formatTimeRemaining = (endTime) => {
    const now = new Date();
    const diffInSeconds = Math.max(0, Math.floor((endTime - now) / 1000));
    const minutes = Math.floor(diffInSeconds / 60);
    const seconds = diffInSeconds % 60;
    return {
        formatted: `${minutes}:${seconds.toString().padStart(2, '0')}`,
        isExpired: diffInSeconds <= 0
    };
};

// Clean up timer
const cleanupOtpTimer = () => {
    if (otpTimerInterval) {
        clearInterval(otpTimerInterval);
        otpTimerInterval = null;
    }
};

// Show OTP verification modal
const showOtpVerificationModal = () => {
    // Track if resend is on cooldown
    let isResendDisabled = false;
    let resendCountdown = 0;
    let countdownInterval = null;
    
    // Initialize or reuse existing expiration time
    if (!otpExpirationTime.value) {
        const expTime = new Date();
        expTime.setSeconds(expTime.getSeconds() + otpConfig.value.ttl);
        otpExpirationTime.value = expTime;
    }
    
    // Clean up any existing timer
    cleanupOtpTimer();

    Swal.fire({
        title: "Phone Verification",
        html: `
            <p class="mb-4">We've sent a 6-digit verification code to your phone number: <strong>${
                form.telephone
            }</strong></p>
            <p class="mb-4 text-sm text-gray-600">
                This code will expire in 
                <span id="otp-timer" class="font-medium">${formatTimeRemaining(otpExpirationTime.value).formatted}</span>
            </p>
            <div class="flex flex-col items-center">
                <input id="swal-otp-input" class="swal2-input w-full text-center" placeholder="Enter 6-digit code" maxlength="6" type="text">
                <div class="text-sm text-gray-500 mt-2">
                    Didn't receive the code?
                    <button type="button" id="resend-otp-btn" class="text-green-600 hover:text-green-800">Resend</button>
                    <span id="resend-countdown" class="hidden ml-1 text-gray-500"></span>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Attempts remaining: <span class="font-medium">${maxOtpAttempts.value - otpAttempts.value}</span> of ${maxOtpAttempts.value}
                </div>
                ${otpAttempts.value > 0 ? `
                <div class="text-xs text-red-500 mt-1">
                    ${maxOtpAttempts.value - otpAttempts.value === 0 ? 'No attempts left. ' : ''}${otpAttempts.value} failed attempt${otpAttempts.value > 1 ? 's' : ''}.
                </div>
                ` : ''}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Verify",
        confirmButtonColor: "#10b981",
        cancelButtonText: "Cancel",
        allowOutsideClick: false,
        preConfirm: () => {
            const otpInput = document.getElementById("swal-otp-input");
            const otp = otpInput.value;

            if (!otp || otp.length !== 6) {
                Swal.showValidationMessage(
                    "Please enter the 6-digit verification code"
                );
                return false;
            }

            return otp;
        },
        didOpen: () => {
            const resendBtn = document.getElementById("resend-otp-btn");
            const resendCountdownEl = document.getElementById("resend-countdown");
            const otpTimerEl = document.getElementById("otp-timer");
            
            // Update OTP timer every second
            if (otpTimerEl) {
                const updateTimerDisplay = () => {
                    const { formatted: timeLeft, isExpired } = formatTimeRemaining(otpExpirationTime.value);
                    otpTimerEl.textContent = timeLeft;
                    
                    // Change color to red when less than 1 minute remaining or expired
                    if (timeLeft.startsWith('0:') || isExpired) {
                        otpTimerEl.classList.add('text-red-500');
                        otpTimerEl.classList.remove('text-gray-600');
                    } else {
                        otpTimerEl.classList.remove('text-red-500');
                        otpTimerEl.classList.add('text-gray-600');
                    }
                    
                    // If time's up, update display but keep the interval running
                    if (isExpired) {
                        otpTimerEl.textContent = 'expired';
                        otpTimerEl.classList.add('font-bold');
                    }
                };
                
                // Initial update
                updateTimerDisplay();
                
                // Set up interval for updates
                otpTimerInterval = setInterval(updateTimerDisplay, 1000);
            }

            // Function to update the resend button state
            const updateResendButton = () => {
                if (isResendDisabled) {
                    resendBtn.disabled = true;
                    resendBtn.classList.remove(
                        "text-green-600",
                        "hover:text-green-800"
                    );
                    resendBtn.classList.add(
                        "text-gray-400",
                        "cursor-not-allowed"
                    );
                    resendCountdownEl.classList.remove("hidden");
                    resendCountdownEl.textContent = `(${resendCountdown}s)`;
                } else {
                    resendBtn.disabled = false;
                    resendBtn.classList.add(
                        "text-green-600",
                        "hover:text-green-800"
                    );
                    resendBtn.classList.remove(
                        "text-gray-400",
                        "cursor-not-allowed"
                    );
                    resendCountdownEl.classList.add("hidden");
                }
            };

            // Add event listener for resend button
            resendBtn.addEventListener("click", async () => {
                if (!isResendDisabled) {
                    try {
                        await sendOtp();
                    } catch (error) {
                        // Check if it's a rate limit error
                        if (
                            error.response?.data?.message &&
                            error.response.data.message.includes("wait")
                        ) {
                            // Extract the wait time or use the rate limit from config
                            const timeMatch = error.response.data.message.match(
                                /wait (\d+) (?:seconds|second)/i
                            );
                            resendCountdown =
                                timeMatch && timeMatch[1]
                                    ? parseInt(timeMatch[1])
                                    : otpConfig.value.rate_limit;

                            isResendDisabled = true;
                            updateResendButton();

                            // Start countdown
                            countdownInterval = setInterval(() => {
                                resendCountdown -= 1;
                                updateResendButton();

                                if (resendCountdown <= 0) {
                                    clearInterval(countdownInterval);
                                    isResendDisabled = false;
                                    updateResendButton();
                                }
                            }, 1000);
                        }
                    }
                }
            });
        },
        willClose: () => {
            // Clear any running intervals when the modal is closed
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            // Don't clean up the timer when modal closes
            // We want to maintain the countdown state
        },
    }).then(async (result) => {
        if (result.isConfirmed) {
            await verifyOtp(result.value);
        }
    });
};

// Verify OTP
const verifyOtp = async (otp) => {
    // If OTP is expired, don't proceed with verification
    if (otpExpirationTime.value && new Date() > otpExpirationTime.value) {
        Swal.fire({
            icon: 'error',
            title: 'OTP Expired',
            text: 'The verification code has expired. Please request a new one.',
            confirmButtonColor: '#10b981',
        });
        return;
    }
    // Check if max attempts reached
    if (otpAttempts.value >= maxOtpAttempts.value) {
        Swal.fire({
            icon: 'error',
            title: 'Maximum Attempts Reached',
            text: `You've exceeded the maximum number of verification attempts. Please request a new OTP.`,
            confirmButtonColor: '#10b981',
        });
        return;
    }

    // Increment attempts counter
    otpAttempts.value += 1;

    try {
        // Show loading state
        Swal.fire({
            title: "Verifying OTP",
            text: "Please wait while we verify your OTP...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Verify OTP request
        const response = await axios.post(route("otp.verify"), {
            telephone: form.telephone,
            otp: otp,
        });

        if (response.data.status === "success" && response.data.verified) {
            // Reset attempts on successful verification
            otpAttempts.value = 0;
            // Mark OTP as verified
            form.otpVerified = true;

            // Show success message
            Swal.fire({
                icon: "success",
                title: "Phone Verified",
                text: "Your phone number has been verified successfully.",
                confirmButtonColor: "#10b981",
            }).then(() => {
                // Submit the form after OTP verification
                submit();
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Verification Failed",
                text: response.data.message || "Invalid OTP. Please try again.",
                confirmButtonColor: "#10b981",
            }).then(() => {
                // Show OTP verification modal again
                showOtpVerificationModal();
            });
        }
    } catch (error) {
        console.error("OTP verification error:", error);
        Swal.fire({
            icon: "error",
            title: "Verification Failed",
            html: `
                <p>${
                    error.response?.data?.message ||
                    "Failed to verify OTP. Please try again."
                }</p>
                ${
                    error.response?.data?.message?.includes("attempts exceeded")
                        ? `<p class="mt-2 text-sm">You have exceeded the maximum of ${maxAttempts} attempts. Please request a new OTP.</p>`
                        : ""
                }
            `,
            confirmButtonColor: "#10b981",
        }).then(() => {
            // Show OTP verification modal again
            showOtpVerificationModal();
        });
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

        // Ensure OTP is verified before submitting
        if (!form.otpVerified) {
            await sendOtp();
            return;
        }

        // Use Inertia's form helper to submit the form
        form.post(route("register"), {
            forceFormData: true,
            onSuccess: () => {
                // Handle successful registration
                showToast(
                    "success",
                    "Registration Successful",
                    "Your registration was successful! Redirecting..."
                );
                // The server should handle the redirect in the response
            },
            onError: (errors) => {
                // Handle form errors
                console.error("Registration errors:", errors);
                let errorMessage = "An error occurred during registration.";

                if (errors && typeof errors === "object") {
                    errorMessage = Object.values(errors).flat().join(" ");
                } else if (typeof errors === "string") {
                    errorMessage = errors;
                }

                showToast("error", "Registration Failed", errorMessage);
            },
            onFinish: () => {
                form.processing = false;
            },
        });
    } catch (error) {
        console.error("Form submission error:", error);
    } finally {
        isSubmitting.value = false;
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

const toTitleCase = (str) => {
    return str.replace(/\b\w/g, (match) => match.toUpperCase());
};

const genderDisplayName = computed(() => {
    if (form.gender === "XY") return "Male";
    if (form.gender === "XX") return "Female";
    return form.gender || "";
});

const canProceedToNextStep = computed(() => {
    if (currentStep.value === 1) {
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
    } else if (currentStep.value === 2) {
        return form.county_id && form.constituency_id && form.ward_id;
    } else {
        return form.terms;
    }
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
                        <!-- Step 1: Account and Personal Information -->
                        <div v-if="currentStep === 1" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 1: Account and Personal Information
                            </h2>

                            <!-- Surname and Other Name (Half Width) -->
                            <div class="grid grid-cols-2 gap-4">
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

                            <!-- Member's Mobile No. and Identification Type (Half Width) -->
                            <div class="grid grid-cols-2 gap-4">
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
                                    />
                                    <InputError
                                        :message="form.errors.telephone"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="identification_type"
                                            value="Identification Type"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <select
                                        id="identification_type"
                                        v-model="form.identification_type"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        required
                                        @change="
                                            form.identification_number = ''
                                        "
                                    >
                                        <option value="">
                                            Select Identification Type
                                        </option>
                                        <option
                                            value="national_identification_number"
                                        >
                                            National Identification Number
                                        </option>
                                        <option value="passport_number">
                                            Passport Number
                                        </option>
                                    </select>
                                    <InputError
                                        :message="
                                            form.errors.identification_type
                                        "
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>

                            <!-- National ID and Party Membership No. (Half Width, Party Read-Only) -->
                            <div class="grid grid-cols-2 gap-4">
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
                                        type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        :placeholder="
                                            form.identification_type ===
                                            'national_identification_number'
                                                ? 'Enter National Identification Number'
                                                : 'Enter Passport Number'
                                        "
                                        required
                                    />
                                    <InputError
                                        :message="
                                            form.errors
                                                .national_identification_number
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

                            <!-- Date of Birth and Sex (Half Width) -->
                            <div class="grid grid-cols-2 gap-4">
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
                                            new Date()
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

                            <!-- Ethnicity and Religion (Half Width) -->
                            <div class="grid grid-cols-2 gap-4">
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

                            <!-- Disability Status and NCPWD Number (Half Width) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
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

                        <!-- Step 2: Location and Enlisting Information -->
                        <div v-if="currentStep === 2" class="space-y-4">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 2: Location and Enlisting Information
                            </h2>
                            <!-- County, Constituency, and Ward (Half Width) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="county_id"
                                            value="County of Voter Registration"
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
                                        class="w-full"
                                        :class="{
                                            'border-red-500':
                                                form.errors.county_id,
                                        }"
                                        :clearable="false"
                                    >
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
                                            value="Constituency of Voter Registration"
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
                                        class="w-full"
                                        :class="{
                                            'border-red-500':
                                                form.errors.constituency_id,
                                        }"
                                        :disabled="!form.county_id"
                                        :clearable="false"
                                    >
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

                            <!-- Ward and Enlisting Date (Half Width) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <InputLabel
                                            for="ward_id"
                                            value="Ward of Voter Registration"
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
                                        class="w-full"
                                        :class="{
                                            'border-red-500':
                                                form.errors.ward_id,
                                        }"
                                        :disabled="!form.constituency_id"
                                        :clearable="false"
                                    >
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
                                            for="enlisting_date"
                                            value="Enlisting Date (dd-mm-yyyy)"
                                            class="block text-sm font-medium text-gray-700"
                                        />
                                        <i
                                            class="fas fa-star text-red-500 text-xs ml-1"
                                        ></i>
                                    </div>
                                    <TextInput
                                        id="enlisting_date"
                                        v-model="form.enlisting_date"
                                        type="date"
                                        class="block w-full rounded-md border-gray-300 bg-gray-200 text-gray-500 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border transition duration-150 ease-in-out"
                                        required
                                        readonly
                                    />
                                    <InputError
                                        :message="form.errors.enlisting_date"
                                        class="mt-1 text-sm text-red-600"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div v-if="currentStep === 3" class="space-y-6">
                            <h2 class="text-base font-medium text-gray-700">
                                Step 3: Confirmation
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
                                            class="sm:grid sm:grid-cols-3 sm:gap-4"
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
                                            class="sm:grid sm:grid-cols-3 sm:gap-4"
                                        >
                                            <dt
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                ID/Passport No.
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
                                            class="sm:grid sm:grid-cols-3 sm:gap-4"
                                        >
                                            <dt
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Party Membership Number
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
                                            class="sm:grid sm:grid-cols-3 sm:gap-4"
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
                                            class="sm:grid sm:grid-cols-3 sm:gap-4"
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
                                            'address location information'
                                        )
                                    "
                                >
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        Address Location Information
                                    </h3>
                                    <i
                                        class="fas fa-chevron-down w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                activeAccordion ===
                                                'address location information',
                                        }"
                                    ></i>
                                </div>
                                <div
                                    v-if="
                                        activeAccordion ===
                                        'address location information'
                                    "
                                    class="p-6"
                                >
                                    <dl class="space-y-4">
                                        <div
                                            class="sm:grid sm:grid-cols-3 sm:gap-4"
                                        >
                                            <dt
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                County of Voter Registration
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
                                                Constituency of Voter
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
                                            class="sm:grid sm:grid-cols-3 sm:gap-4"
                                        >
                                            <dt
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Ward of Voter Registration
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
                                <svg
                                    v-if="isSubmitting"
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
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
                                            currentStep === 3
                                                ? "Submit Application"
                                                : "Next: " + nextStepDescription
                                        }}
                                    </span>
                                    <span class="sm:hidden">{{
                                        currentStep === 3 ? "Submit" : "Next"
                                    }}</span>
                                    <i
                                        :class="
                                            currentStep === 3
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
                                    Step {{ currentStep }} of 3:
                                    {{ stepDescription }}
                                </span>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ Math.round((currentStep / 3) * 100) }}%
                                    Complete
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div
                                    class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300"
                                    :style="{
                                        width: `${(currentStep / 3) * 100}%`,
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
