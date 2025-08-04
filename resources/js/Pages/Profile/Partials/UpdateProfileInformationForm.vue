<script setup>
import { ref, computed, watch, onMounted, defineProps } from "vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";

// Define props
defineProps({
    // No need for user prop since we're using usePage()
});
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Swal from "sweetalert2";

// Access page props
const { props: pageProps } = usePage();

// Access authenticated user data
const user = computed(() => pageProps?.auth?.user || null);

// Access the profile and citizen data
const profile = computed(() => user.value?.profile || {});
const citizen = computed(() => user.value?.citizen || {});

// Initialize form with default values and validation rules
const form = useForm({
    _method: "PUT",
    // User Profile Information
    profile: {
        // Personal Information
        surname: '',
        other_name: '',
        email: '',
        photo: null,
        date_of_birth: "",
        gender: "",
        ncpwd_number: "",
        ethnicity_id: "",
        religion_id: "",
        // Contact Information
        telephone: "",
        address_line_1: "",
        address_line_2: "",
        city: "",
        state: "",
        country: ""
    },
    // Citizen Information
    citizen: {
        // Identification Information
        national_identification_number: "",
        passport_number: "",
        driver_license_number: "",
        // Local Government Information
        county_id: "",
        sub_county_id: "",
        constituency_id: "",
        ward_id: "",
        location_id: "",
        village_id: "",
        polling_center_id: "",
        polling_station_id: "",
        polling_stream_id: ""
    }
}, {
    // Custom validation rules
    resetOnSuccess: false,
    rules: {
        'citizen.national_identification_number': [
            (value, form) => {
                if (!value && !form.citizen.passport_number) {
                    return "Either National Identification Number or Passport Number is required";
                }
                return true;
            }
        ],
        'citizen.passport_number': [
            (value, form) => {
                if (!value && !form.citizen.national_identification_number) {
                    return "Either Passport Number or National Identification Number is required";
                }
                return true;
            }
        ]
    }
});

// OTP Configuration
const otpConfig = {
    ttl: 300, // 5 minutes in seconds
    rate_limit: 60, // 60 seconds between requests
    attempts_limit: 5, // 5 attempts before OTP is invalidated
};

// OTP related state
const otpAttempts = ref(0);
const otpExpirationTime = ref(null);
let otpTimerInterval = null;
const otpVerificationInProgress = ref(false);
const showOtpModal = ref(false);
const otpForm = useForm({
    otp: '',
    field_to_verify: '',
    verification_value: ''
});

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

// Format phone number input
const formatPhoneNumber = (event) => {
    // Remove all non-digit characters
    let value = event.target.value.replace(/\D/g, '');
    
    // Limit to 10 digits for Kenyan numbers
    value = value.substring(0, 10);
    
    // Format as 0712 345 678
    if (value.length > 6) {
        value = value.replace(/^(\d{3})(\d{3})(\d+)$/, '$1 $2 $3');
    } else if (value.length > 3) {
        value = value.replace(/^(\d{3})(\d+)$/, '$1 $2');
    }
    
    // Update the form's telephone field
    form.telephone = value;
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
        expTime.setSeconds(expTime.getSeconds() + otpConfig.ttl);
        otpExpirationTime.value = expTime;
    }
    
    // Clean up any existing timer
    cleanupOtpTimer();

    Swal.fire({
        title: 'Verification Required',
        html: `
            <p class="mb-4">We've sent a 6-digit verification code to your ${otpForm.field_to_verify === 'email' ? 'email' : 'phone number'}: <strong>${otpForm.new_value}</strong></p>
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
                    Attempts remaining: <span class="font-medium">${otpConfig.attempts_limit - otpAttempts.value}</span> of ${otpConfig.attempts_limit}
                </div>
                ${otpAttempts.value > 0 ? `
                <div class="text-xs text-red-500 mt-1">
                    ${otpConfig.attempts_limit - otpAttempts.value === 0 ? 'No attempts left. ' : ''}${otpAttempts.value} failed attempt${otpAttempts.value > 1 ? 's' : ''}.
                </div>
                ` : ''}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Verify',
        confirmButtonColor: '#10b981',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
        preConfirm: () => {
            const otpInput = document.getElementById('swal-otp-input');
            const otp = otpInput.value;

            if (!otp || otp.length !== 6) {
                Swal.showValidationMessage('Please enter the 6-digit verification code');
                return false;
            }

            return otp;
        },
        didOpen: () => {
            const resendBtn = document.getElementById('resend-otp-btn');
            const resendCountdownEl = document.getElementById('resend-countdown');
            const otpTimerEl = document.getElementById('otp-timer');
            
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
                    resendBtn.classList.remove('text-green-600', 'hover:text-green-800');
                    resendBtn.classList.add('text-gray-400', 'cursor-not-allowed');
                    resendCountdownEl.classList.remove('hidden');
                    resendCountdownEl.textContent = `(${resendCountdown}s)`;
                } else {
                    resendBtn.disabled = false;
                    resendBtn.classList.add('text-green-600', 'hover:text-green-800');
                    resendBtn.classList.remove('text-gray-400', 'cursor-not-allowed');
                    resendCountdownEl.classList.add('hidden');
                }
            };

            // Add event listener for resend button
            resendBtn.addEventListener('click', async () => {
                if (!isResendDisabled) {
                    try {
                        await sendOtp(otpForm.field_to_verify, otpForm.new_value);
                    } catch (error) {
                        if (error.response?.data?.message?.includes('wait')) {
                            // Extract the wait time or use the rate limit from config
                            const timeMatch = error.response.data.message.match(/wait (\d+) (?:seconds|second)/i);
                            resendCountdown = timeMatch && timeMatch[1] 
                                ? parseInt(timeMatch[1]) 
                                : otpConfig.rate_limit;

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
            cleanupOtpTimer();
        },
    }).then(async (result) => {
        if (result.isConfirmed) {
            await verifyOtp(result.value);
        }
    });
};

// Send OTP to verify contact information
const sendOtp = async (field, value) => {
    try {
        // Validate the field and value
        if (!field || !value) {
            showToast('error', 'Validation Error', `${field === 'email' ? 'Email' : 'Phone number'} is required for verification`);
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Sending OTP',
            text: `Please wait while we send an OTP to your ${field === 'email' ? 'email' : 'phone number'}...`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Reset attempts counter and expiration time when requesting new OTP
        otpAttempts.value = 0;
        cleanupOtpTimer();
        const expTime = new Date();
        expTime.setSeconds(expTime.getSeconds() + otpConfig.ttl);
        otpExpirationTime.value = expTime;

        // Send OTP request
        // Prepare payload based on field type
        const payload = {};
        
        if (field === 'email') {
            payload.email = value;
        } else if (field === 'telephone') {
            payload.telephone = value;
        }
        
        const response = await axios.post(route('auth.request-otp'), payload);

        if (response.data.status === 'success') {
            // Update form with verification ID
            otpForm.verification_id = response.data.verification_id;
            otpForm.field_to_verify = field;
            otpForm.new_value = value;
            
            // Show OTP verification modal
            showOtpVerificationModal();
        } else {
            throw new Error(response.data.message || 'Failed to send OTP. Please try again.');
        }
    } catch (error) {
        console.error('OTP send error:', error);

        // Check if it's a rate limit error
        if (error.response?.data?.message && error.response.data.message.includes('wait')) {
            // Show countdown timer for rate limit
            let secondsLeft = otpConfig.rate_limit; // Use the rate limit from config

            // Try to extract the exact time from the message if available
            const timeMatch = error.response.data.message.match(/wait (\d+) (?:seconds|second)/i);
            if (timeMatch && timeMatch[1]) {
                secondsLeft = parseInt(timeMatch[1]);
            }

            const timerInterval = setInterval(() => {
                secondsLeft -= 1;

                // Update the timer text
                const timerElement = document.getElementById('otp-timer');
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
                icon: 'warning',
                title: 'Rate Limited',
                html: `
                    <p>You must wait before requesting another OTP.</p>
                    <div class="mt-4 text-center">
                        <p>Try again in <span id="otp-timer" class="font-bold text-lg">${secondsLeft}</span> seconds</p>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#10b981',
                willClose: () => {
                    clearInterval(timerInterval);
                },
            });
        } else {
            // Regular error handling
            Swal.fire({
                icon: 'error',
                title: 'OTP Error',
                text: error.response?.data?.message || 'Failed to send OTP. Please try again.',
            });
        }
    }
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
    if (otpAttempts.value >= otpConfig.attempts_limit) {
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
            title: 'Verifying OTP',
            text: 'Please wait while we verify your OTP...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Verify OTP request
        const payload = {
            otp: otp
        };
        
        // Add either email or telephone based on what's being verified
        if (otpForm.field_to_verify === 'email') {
            payload.email = otpForm.new_value;
        } else if (otpForm.field_to_verify === 'telephone') {
            payload.telephone = otpForm.new_value;
        }
        
        const response = await axios.post(route('auth.verify-otp'), payload);

        if (response.data.status === 'success') {
            // Reset attempts on successful verification
            otpAttempts.value = 0;
            
            // Update the form field with the verified value
            if (otpForm.field_to_verify === 'email') {
                form.email = otpForm.new_value;
                form.email_verified_at = new Date().toISOString();
                // Clear any previous email verification errors
                form.clearErrors('email');
            } else if (otpForm.field_to_verify === 'telephone') {
                form.telephone = otpForm.new_value;
                form.telephone_verified_at = new Date().toISOString();
                // Clear any previous telephone verification errors
                form.clearErrors('telephone');
            }
            
            // Close the modal and reset form
            showOtpModal.value = false;
            otpForm.reset();
            cleanupOtpTimer();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Verification Successful',
                text: `Your ${otpForm.field_to_verify === 'email' ? 'email address' : 'telephone number'} has been verified successfully.`,
                confirmButtonColor: '#10b981',
            });
            
            // If we have a token in the response, update the authentication state
            if (response.data.data?.accessToken) {
                // Store the new token
                window.localStorage.setItem('auth_token', response.data.data.accessToken);
                
                // Update axios default headers
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.accessToken}`;
                
                // Refresh the user data
                await router.reload({ only: ['user'] });
            }
            
            // Submit the form if this was part of a form submission
            if (shouldVerifyOnSubmit.value) {
                shouldVerifyOnSubmit.value = false;
                await updateProfileInformation();
            }
        } else {
            throw new Error(response.data.message || 'Invalid OTP. Please try again.');
        }
    } catch (error) {
        console.error('OTP verification error:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'Verification Failed',
            html: `
                <p>${error.response?.data?.message || 'Failed to verify OTP. Please try again.'}</p>
                ${error.response?.data?.message?.includes('attempts exceeded') 
                    ? `<p class="mt-2 text-sm">You have exceeded the maximum of ${otpConfig.attempts_limit} attempts. Please request a new OTP.</p>` 
                    : ''}
            `,
            confirmButtonColor: '#10b981',
        }).then(() => {
            // Show OTP verification modal again if not max attempts
            if (otpAttempts.value < otpConfig.attempts_limit) {
                showOtpVerificationModal();
            }
        });
    }
};

// Track if we're in the middle of verification flow
const shouldVerifyOnSubmit = ref(false);

// Helper function to show toast messages
const showToast = (type, title, message) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: title,
        text: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
};

// Watch for profile changes to update form data
watch(profile, (newProfile) => {
    if (newProfile) {
        // Update profile fields in form
        Object.keys(newProfile).forEach(key => {
            if (key in form.profile) {
                form.profile[key] = newProfile[key];
            }
        });
    }
}, { immediate: true, deep: true });

// Watch for citizen changes to update form data
watch(citizen, (newCitizen) => {
    if (newCitizen && Object.keys(newCitizen).length > 0) {
        // Update citizen fields in form
        Object.keys(newCitizen).forEach(key => {
            if (key in form.citizen) {
                form.citizen[key] = newCitizen[key];
            }
        });
    }
}, { immediate: true, deep: true });

// Watch for profile changes to update form
watch(profile, (newProfile) => {
    if (newProfile) {
        // Update profile fields
        form.profile.surname = newProfile.last_name || null;
        form.profile.other_name = (newProfile.first_name || '') + (newProfile.middle_name ? ` ${newProfile.middle_name}` : '');
        form.profile.email = newProfile.email || user.value.email;
        
        // Format date_of_birth for HTML date input (YYYY-MM-DD)
        const dob = newProfile.date_of_birth || null;
        if (dob) {
            const date = new Date(dob);
            form.profile.date_of_birth = !isNaN(date.getTime()) ? date.toISOString().split('T')[0] : '';
        } else {
            form.profile.date_of_birth = '';
        }

        // Update other profile fields
        form.profile.gender = newProfile.gender || null;
        form.profile.ncpwd_number = newProfile.ncpwd_number || null;
        form.profile.ethnicity_id = newProfile.ethnicity_id || null;
        form.profile.religion_id = newProfile.religion_id || null;
        form.profile.telephone = newProfile.telephone || null;
        form.profile.address_line_1 = newProfile.address_line_1 || null;
        form.profile.address_line_2 = newProfile.address_line_2 || null;
        form.profile.city = newProfile.city || null;
        form.profile.state = newProfile.state || null;
        form.profile.country = newProfile.country || null;
    }
}, { immediate: true, deep: true });

// Watch for citizen changes to update form
watch(citizen, (newCitizen) => {
    if (newCitizen && Object.keys(newCitizen).length > 0) {
        console.log('Updating citizen form data:', newCitizen);
        
        // Update citizen fields
        form.citizen.national_identification_number = newCitizen.national_identification_number || null;
        form.citizen.passport_number = newCitizen.passport_number || null;
        form.citizen.driver_license_number = newCitizen.driver_license_number || null;
        
        // Update location fields
        form.citizen.county_id = newCitizen.county_id || null;
        form.citizen.sub_county_id = newCitizen.sub_county_id || null;
        form.citizen.constituency_id = newCitizen.constituency_id || null;
        form.citizen.ward_id = newCitizen.ward_id || null;
        form.citizen.location_id = newCitizen.location_id || null;
        form.citizen.village_id = newCitizen.village_id || null;
        form.citizen.polling_center_id = newCitizen.polling_center_id || null;
        form.citizen.polling_station_id = newCitizen.polling_station_id || null;
        form.citizen.polling_stream_id = newCitizen.polling_stream_id || null;
        
        // Log the updated form data for debugging
        console.log('Updated form.citizen:', form.citizen);
    }
}, { immediate: true, deep: true });

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

// Create a function to prepare form data
const prepareFormData = () => {
    const formData = new FormData();
    
    // Add all profile fields to formData
    if (form.profile) {
        Object.keys(form.profile).forEach(key => {
            // Skip photo here, we'll add it separately
            if (key !== 'photo' && form.profile[key] !== null && form.profile[key] !== undefined) {
                formData.append(`profile[${key}]`, form.profile[key]);
            }
        });
    }
    
    // Add photo separately if it exists
    if (photoInput.value?.files?.[0]) {
        formData.append('profile[photo]', photoInput.value.files[0]);
    }
    
    // Add all citizen fields to formData
    if (form.citizen) {
        Object.keys(form.citizen).forEach(key => {
            if (form.citizen[key] !== null && form.citizen[key] !== undefined) {
                formData.append(`citizen[${key}]`, form.citizen[key]);
            }
        });
    }
    
    return formData;
};

const updateProfileInformation = async (skipVerification = false) => {
    // Check if we need to verify email or telephone
    const emailChanged = user?.email && form.profile.email !== user.email;
    const telephoneChanged = profile?.telephone && form.profile.telephone !== profile.telephone;
    
    // Only trigger OTP if:
    // 1. We're not skipping verification
    // 2. We're either in the middle of verification or there's a change in email/telephone
    // 3. The field being changed had a previous value (not empty)
    if (!skipVerification && (shouldVerifyOnSubmit.value || emailChanged || telephoneChanged)) {
        shouldVerifyOnSubmit.value = true;
        
        // Determine which OTP to send and to where
        let targetField = '';
        let targetValue = '';
        let message = '';
        
        if (telephoneChanged) {
            targetField = 'telephone';
            // Only verify if there was a previous telephone number
            if (profile?.telephone) {
                targetValue = profile.telephone; // Always use the old value for verification
                message = `We'll send a verification code to your current number <strong>${profile.telephone}</strong> to confirm this change.`;
            } else {
                // No previous telephone, no verification needed for new number
                shouldVerifyOnSubmit.value = false;
            }
        } else if (emailChanged && user?.email) {
            // Only verify if there was a previous email
            targetField = 'email';
            targetValue = user.email; // Always use the old value for verification
            message = `We'll send a verification code to <strong>${user.email}</strong> to confirm your email address change.`;
        }
        
        if (targetField) {
            try {
                const result = await Swal.fire({
                    title: 'Verify ' + (targetField === 'email' ? 'Email Address' : 'Phone Number'),
                    html: message,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Send Verification Code',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700',
                        cancelButton: 'px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300'
                    },
                    buttonsStyling: false
                });

                if (result.isConfirmed) {
                    await sendOtp(targetField, targetValue);
                } else if (result.isDismissed) {
                    // User cancelled the verification
                    shouldVerifyOnSubmit.value = false;
                }
                return;
            } catch (error) {
                console.error('Error in verification flow:', error);
                showToast('error', 'Verification Error', 'An error occurred while setting up verification. Please try again.');
                shouldVerifyOnSubmit.value = false;
                return;
            }
        }
    }

    // Log the form data being sent
    console.log('Form data being submitted:', JSON.stringify(form.data(), null, 2));
    
    // Use Inertia's form helper which handles file uploads and CSRF automatically
    form.transform((data) => {
        console.log('Transformed form data:', JSON.stringify(data, null, 2));
        return {
            ...data,
            _method: 'PUT' // Laravel's way to handle PUT/PATCH/DELETE via POST
        };
    }).post(route('user-profile-information.update'), {
        onStart: () => {
            console.log('Form submission started');
            form.processing = true;
        },
        onFinish: () => {
            console.log('Form submission finished');
            form.processing = false;
        },
        onSuccess: (page) => {
            console.log('Profile update successful', page);
            // Clear the photo input on success
            clearPhotoFileInput();
            
            // Show success message
            showToast('success', 'Success', 'Profile updated successfully!');
            
            // Refresh the page to show updated profile
            window.location.reload();
        },
        onError: (errors) => {
            console.error('Detailed error updating profile:', {
                errors,
                response: errors?.response,
                data: errors?.response?.data,
                status: errors?.response?.status,
                statusText: errors?.response?.statusText
            });
            
            // Extract the first error message if available
            let errorMessage = 'Failed to update profile. Please check the form for errors.';
            
            // Handle different error response formats
            if (errors && typeof errors === 'object') {
                // Check for Laravel validation errors
                if (errors.updateProfileInformation) {
                    const errorObj = errors.updateProfileInformation;
                    const firstErrorKey = Object.keys(errorObj)[0];
                    if (firstErrorKey) {
                        errorMessage = Array.isArray(errorObj[firstErrorKey]) 
                            ? errorObj[firstErrorKey][0] 
                            : String(errorObj[firstErrorKey]);
                    }
                } 
                // Handle Inertia form errors
                else if (Object.keys(errors).length > 0) {
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : String(firstError);
                }
            }
            
            showToast('error', 'Error', errorMessage);
        },
        preserveScroll: true,
        forceFormData: true, // Important for file uploads
        onCancelToken: (cancelToken) => {
            console.log('Request cancel token created');
        },
        onCancel: () => {
            console.log('Request was cancelled');
        }
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    
    if (!photo) return;
    
    // Validate file type
    if (!photo.type.match('image.*')) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            text: 'Please select an image file (JPEG, PNG, GIF, etc.)',
            confirmButtonColor: '#10b981',
        });
        clearPhotoFileInput();
        return;
    }
    
    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (photo.size > maxSize) {
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'Maximum file size is 5MB. Please choose a smaller file.',
            confirmButtonColor: '#10b981',
        });
        clearPhotoFileInput();
        return;
    }

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
        // Update the form with the new photo
        if (form.profile) {
            form.profile.photo = photo;
        }
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route("current-user-photo.destroy"), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};

const activeSection = ref("personal");

// Location data refs
const constituencies = ref([]);
const wards = ref([]);
const locations = ref([]);
const villages = ref([]);
const pollingCenters = ref([]);
const pollingStations = ref([]);
const pollingStreams = ref([]);

// Loading states for location dropdowns
const isLoadingSubCounties = ref(false);
const isLoadingConstituencies = ref(false);
const isLoadingWards = ref(false);
const isLoadingLocations = ref(false);
const isLoadingVillages = ref(false);
const isLoadingPollingCenters = ref(false);
const isLoadingPollingStations = ref(false);
const isLoadingPollingStreams = ref(false);

// Access location data from page props
const locationData = computed(() => {
    const data = pageProps.formData?.locations || {
        counties: [],
        sub_counties: [],
        constituencies: [],
        wards: [],
        locations: [],
        villages: [],
        polling_centers: [],
        polling_stations: [],
        polling_streams: []
    };
    
    return data;
});

// Reactive references for filtered location data
const filteredSubCounties = ref([]);
const filteredConstituencies = ref([]);
const filteredWards = ref([]);
const filteredLocations = ref([]);
const filteredVillages = ref([]);
const filteredPollingCenters = ref([]);
const filteredPollingStations = ref([]);
const filteredPollingStreams = ref([]);

// Watcher for county changes
watch(() => form.citizen.county_id, (newCountyId) => {
    if (!newCountyId) {
        filteredSubCounties.value = [];
        filteredConstituencies.value = [];
        // Reset only county-dependent fields
        form.citizen.sub_county_id = '';
        form.citizen.constituency_id = '';
        return;
    }
    
    // Filter sub-counties for the selected county
    filteredSubCounties.value = locationData.value.sub_counties?.filter(
        subCounty => subCounty.county_id == newCountyId
    ) || [];
    
    // Filter constituencies for the selected county
    filteredConstituencies.value = locationData.value.constituencies?.filter(
        constituency => constituency.county_id == newCountyId
    ) || [];
    
    // Reset only county-dependent fields
    form.citizen.sub_county_id = '';
    form.citizen.constituency_id = '';
});

// Watcher for constituency changes
watch(() => form.citizen.constituency_id, (newConstituencyId) => {
    if (!newConstituencyId) {
        filteredWards.value = [];
        // Reset only constituency-dependent fields
        form.citizen.ward_id = '';
        return;
    }
    
    filteredWards.value = locationData.value.wards?.filter(
        ward => ward.constituency_id == newConstituencyId
    ) || [];
    
    // Reset only constituency-dependent fields
    form.citizen.ward_id = '';
});

// Watcher for ward changes
watch(() => form.citizen.ward_id, (newWardId) => {
    if (!newWardId) {
        filteredLocations.value = [];
        filteredPollingCenters.value = [];
        // Reset only ward-dependent fields
        form.citizen.location_id = '';
        form.citizen.polling_center_id = '';
        return;
    }
    
    // Filter locations for the selected ward
    filteredLocations.value = locationData.value.locations?.filter(
        location => location.ward_id == newWardId
    ) || [];
    
    // Filter polling centers for the selected ward
    filteredPollingCenters.value = locationData.value.polling_centers?.filter(
        center => center.ward_id == newWardId
    ) || [];
    
    // Reset only ward-dependent fields
    form.citizen.location_id = '';
    form.citizen.polling_center_id = '';
});

// Watcher for location changes
watch(() => form.citizen.location_id, (newLocationId) => {
    if (!newLocationId) {
        filteredVillages.value = [];
        // Reset only location-dependent fields
        form.citizen.village_id = '';
        return;
    }
    
    filteredVillages.value = locationData.value.villages?.filter(
        village => village.location_id == newLocationId
    ) || [];
    
    // Reset only location-dependent fields
    form.citizen.village_id = '';
});

// Watcher for polling center changes
watch(() => form.citizen.polling_center_id, (newCenterId) => {
    if (!newCenterId) {
        filteredPollingStations.value = [];
        // Reset only polling-center-dependent fields
        form.citizen.polling_station_id = '';
        return;
    }
    
    filteredPollingStations.value = locationData.value.polling_stations?.filter(
        station => station.center_id == newCenterId
    ) || [];
    
    // Reset only polling-center-dependent fields
    form.citizen.polling_station_id = '';
});

// Watcher for polling station changes
watch(() => form.citizen.polling_station_id, (newStationId) => {
    if (!newStationId) {
        filteredPollingStreams.value = [];
        // Reset only polling-station-dependent fields
        form.citizen.polling_stream_id = '';
        return;
    }
    
    filteredPollingStreams.value = locationData.value.polling_streams?.filter(
        stream => stream.station_id == newStationId
    ) || [];
    
    // Reset only polling-station-dependent fields
    form.citizen.polling_stream_id = '';
});



// Watchers to clear child fields when parent changes
watch(() => form.citizen.county_id, () => {
    form.citizen.sub_county_id = '';
    form.citizen.constituency_id = '';
    form.citizen.ward_id = '';
    form.citizen.location_id = '';
    form.citizen.village_id = '';
    form.citizen.polling_center_id = '';
    form.citizen.polling_station_id = '';
    form.citizen.polling_stream_id = '';
});

watch(() => form.citizen.constituency_id, () => {
    form.citizen.ward_id = '';
    form.citizen.location_id = '';
    form.citizen.village_id = '';
    form.citizen.polling_center_id = '';
    form.citizen.polling_station_id = '';
    form.citizen.polling_stream_id = '';
});

watch(() => form.citizen.ward_id, () => {
    form.citizen.location_id = '';
    form.citizen.village_id = '';
    form.citizen.polling_center_id = '';
    form.citizen.polling_station_id = '';
    form.citizen.polling_stream_id = '';
});

watch(() => form.citizen.location_id, () => {
    form.citizen.village_id = '';
});

watch(() => form.citizen.polling_center_id, () => {
    form.citizen.polling_station_id = '';
    form.citizen.polling_stream_id = '';
});

watch(() => form.citizen.polling_station_id, () => {
    form.citizen.polling_stream_id = '';
});

// Watch for county changes to filter sub-counties and reset dependent fields
watch(
    () => form.citizen.county_id,
    (newCountyId) => {
        // Reset dependent fields
        form.citizen.sub_county_id = '';
        form.citizen.constituency_id = '';
        form.citizen.ward_id = '';
        form.citizen.location_id = '';
        form.citizen.village_id = '';
        form.citizen.polling_center_id = '';
        form.citizen.polling_station_id = '';
        form.polling_stream_id = '';
    }
);

// Watch for sub-county changes to reset dependent fields
watch(
    () => form.citizen.sub_county_id,
    (newSubCountyId) => {
        if (newSubCountyId) {
            // Reset dependent fields if needed
            form.citizen.ward_id = '';
            form.citizen.location_id = '';
            form.citizen.village_id = '';
            form.citizen.polling_center_id = '';
            form.citizen.polling_station_id = '';
            form.citizen.polling_stream_id = '';
        }
    }
);

// Watch for constituency changes to reset dependent fields
watch(
    () => form.citizen.constituency_id,
    (newConstituencyId) => {
        // Reset dependent fields
        form.citizen.ward_id = '';
        form.citizen.location_id = '';
        form.citizen.village_id = '';
        form.citizen.polling_center_id = '';
        form.citizen.polling_station_id = '';
        form.citizen.polling_stream_id = '';
    }
);

// Watch for ward changes to reset dependent fields
watch(
    () => form.citizen.ward_id,
    (newWardId) => {
        // Reset dependent fields
        form.citizen.location_id = '';
        form.citizen.village_id = '';
        form.citizen.polling_center_id = '';
        form.citizen.polling_station_id = '';
        form.citizen.polling_stream_id = '';
    }
);

// Watch for location changes to reset village field
watch(
    () => form.citizen.location_id,
    (newLocationId) => {
        if (newLocationId) {
            form.citizen.village_id = '';
        }
    }
);

// Watch for polling center changes to reset dependent fields
watch(
    () => form.citizen.polling_center_id,
    (newCenterId) => {
        form.citizen.polling_station_id = '';
        form.citizen.polling_stream_id = '';
    }
);

// Watch for polling station changes to reset polling stream
watch(
    () => form.citizen.polling_station_id,
    (newStationId) => {
        if (newStationId) {
            form.citizen.polling_stream_id = '';
        }
    }
);

const toggleSection = (section) => {
    activeSection.value = activeSection.value === section ? null : section;
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title> Profile Information </template>

        <template #description>
            Update your personal, contact, and identification information.
            Ensure all details are accurate and up-to-date.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="col-span-6 sm:col-span-4"
            >
                <!-- Profile Photo File Input -->
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <InputLabel for="photo" value="Profile Image" />

                <!-- Current Profile Photo -->
                <div v-show="!photoPreview" class="mt-2">
                    <img
                        :src="user.profile_photo_path"
                        :alt="user.name"
                        class="rounded-full size-20 object-cover"
                    />
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                        :style="
                            'background-image: url(\'' + photoPreview + '\');'
                        "
                    />
                </div>

                <SecondaryButton
                    class="mt-2 me-2"
                    type="button"
                    @click.prevent="selectNewPhoto"
                >
                    Select A New Photo
                </SecondaryButton>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    Remove Photo
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Collapsible Sections -->
            <div class="col-span-6 space-y-4">
                <!-- Personal Information Section -->
                <div
                    class="border rounded-lg overflow-hidden dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="toggleSection('personal')"
                        class="w-full px-6 py-4 text-left font-medium text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex justify-between items-center"
                    >
                        <span>Personal Information</span>
                        <svg
                            class="w-5 h-5 transition-transform duration-200"
                            :class="{
                                'rotate-180': activeSection === 'personal',
                            }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            ></path>
                        </svg>
                    </button>
                    <div
                        v-show="activeSection === 'personal'"
                        class="p-6 bg-white dark:bg-gray-900"
                    >
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="surname">
                                    Surname
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <TextInput
                                    id="surname"
                                    v-model="form.profile.surname"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Enter your surname"
                                    required
                                    autocomplete="family-name"
                                />
                                <InputError
                                    :message="form.errors.surname"
                                    class="mt-2"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="other_name">
                                    Other Name
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <TextInput
                                    id="other_name"
                                    v-model="form.profile.other_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Enter your other names"
                                    required
                                    autocomplete="given-name"
                                />
                                <InputError
                                    :message="form.errors['profile.other_name']"
                                    class="mt-2"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="date_of_birth">
                                    Date of Birth
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <TextInput
                                    id="date_of_birth"
                                    v-model="form.profile.date_of_birth"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :max="new Date().toISOString().split('T')[0]"
                                    required
                                />
                                <InputError
                                    :message="form.errors['profile.date_of_birth']"
                                    class="mt-2"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="gender">
                                    Gender
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <select
                                    id="gender"
                                    v-model="form.profile.gender"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Gender</option>
                                    <option
                                        v-for="gender in $page.props.formData
                                            .genders"
                                        :key="gender.id"
                                        :value="gender.id"
                                        :selected="user.profile?.gender === gender.id"
                                    >
                                        {{ gender.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.gender"
                                    class="mt-2"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="ethnicity_id">
                                    Ethnicity
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <select
                                    id="ethnicity_id"
                                    v-model="form.profile.ethnicity_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Ethnicity</option>
                                    <option
                                        v-for="ethnicity in $page.props.formData
                                            .ethnicities"
                                        :key="ethnicity.id"
                                        :value="ethnicity.id"
                                        :selected="user.profile?.ethnicity_id === ethnicity.id"
                                    >
                                        {{ ethnicity.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.ethnicity_id"
                                    class="mt-2"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="religion_id">
                                    Religion
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <select
                                    id="religion_id"
                                    v-model="form.profile.religion_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Religion</option>
                                    <option
                                        v-for="religion in $page.props.formData
                                            .religions"
                                        :key="religion.id"
                                        :value="religion.id"
                                        :selected="user.profile?.religion_id === religion.id"
                                    >
                                        {{ religion.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.religion_id"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Identification Information Section -->
                <div
                    class="border rounded-lg overflow-hidden dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="toggleSection('identification')"
                        class="w-full px-6 py-4 text-left font-medium text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex justify-between items-center"
                    >
                        <span>Identification Information</span>
                        <svg
                            class="w-5 h-5 transition-transform duration-200"
                            :class="{
                                'rotate-180':
                                    activeSection === 'identification',
                            }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            ></path>
                        </svg>
                    </button>
                    <div
                        v-show="activeSection === 'identification'"
                        class="p-6 bg-white dark:bg-gray-900"
                    >
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="national_identification_number"
                                >
                                    National Identification Number
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(or Passport Number)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="national_identification_number"
                                    v-model="
                                        form.citizen.national_identification_number
                                    "
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 12345678"
                                    :required="!form.citizen.passport_number"
                                    autocomplete="off"
                                />
                                <InputError
                                    :message="
                                        form.errors['citizen.national_identification_number']
                                    "
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="passport_number">
                                    Passport Number
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(or National Identification
                                        Number)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="passport_number"
                                    v-model="form.citizen.passport_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., A1234567"
                                    :required="!form.citizen.national_identification_number"
                                    autocomplete="off"
                                />
                                <InputError
                                    :message="form.errors['citizen.passport_number']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="driver_license_number">
                                    Driver's License Number
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(Optional)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="driver_license_number"
                                    v-model="form.citizen.driver_license_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., DL12345678"
                                />
                                <InputError
                                    :message="form.errors['citizen.driver_license_number']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="ncpwd_number">
                                    NCPWD Number
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(if Applicable)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="ncpwd_number"
                                    v-model="form.profile.ncpwd_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., NCPWD/1234/5678"
                                />
                                <InputError
                                    :message="form.errors.ncpwd_number"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div
                    class="border rounded-lg overflow-hidden dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="toggleSection('contact')"
                        class="w-full px-6 py-4 text-left font-medium text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex justify-between items-center"
                    >
                        <span>Contact Information</span>
                        <svg
                            class="w-5 h-5 transition-transform duration-200"
                            :class="{
                                'rotate-180': activeSection === 'contact',
                            }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            ></path>
                        </svg>
                    </button>
                    <div
                        v-show="activeSection === 'contact'"
                        class="p-6 bg-white dark:bg-gray-900"
                    >
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="email">
                                    Email Address
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(Optional)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="email"
                                    v-model="form.profile.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    placeholder="your.email@example.com"
                                    autocomplete="email"
                                />
                                <InputError
                                    :message="form.errors['profile.email']"
                                    class="mt-2"
                                />

                                <div
                                    v-if="
                                        $page.props.jetstream
                                            .hasEmailVerification &&
                                        user.email_verified_at === null
                                    "
                                    class="mt-2"
                                >
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-400"
                                    >
                                        Your email is unverified.
                                        <Link
                                            :href="route('verification.send')"
                                            method="post"
                                            as="button"
                                            class="underline text-xs hover:text-gray-900 dark:hover:text-gray-200"
                                            @click.prevent="
                                                sendEmailVerification
                                            "
                                        >
                                            Resend verification
                                        </Link>
                                    </p>
                                    <div
                                        v-show="verificationLinkSent"
                                        class="text-xs text-green-600 dark:text-green-400 mt-1"
                                    >
                                        Verification link sent!
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="telephone">
                                    Telephone Number
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <input
                                    id="telephone"
                                    name="telephone"
                                    v-model="form.profile.telephone"
                                    type="tel"
                                    class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1 block w-full"
                                    placeholder="e.g., 0712345678"
                                    required
                                    @input="formatPhoneNumber"
                                />
                                <InputError
                                    :message="form.errors['profile.telephone']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6">
                                <InputLabel for="address_line_1">
                                    Address Line 1
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(Optional)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="address_line_1"
                                    v-model="form.profile.address_line_1"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 123 Main Street"
                                />
                                <InputError
                                    :message="form.errors['profile.address_line_1']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6">
                                <InputLabel for="address_line_2">
                                    Address Line 2
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(Optional)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="address_line_2"
                                    v-model="form.profile.address_line_2"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., Apartment, suite, etc. (optional)"
                                />
                                <InputError
                                    :message="form.errors['profile.address_line_2']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <InputLabel for="city">
                                    City
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(Optional)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="city"
                                    v-model="form.profile.city"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., Nairobi"
                                />
                                <InputError
                                    :message="form.errors['profile.city']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <InputLabel for="state">
                                    State/Province
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(Optional)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="state"
                                    v-model="form.profile.state"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., Nairobi"
                                />
                                <InputError
                                    :message="form.errors['profile.state']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <InputLabel for="country">
                                    Country
                                    <span
                                        class="text-xs text-gray-500 font-normal"
                                        >(Optional)</span
                                    >
                                </InputLabel>
                                <TextInput
                                    id="country"
                                    v-model="form.profile.country"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-100 dark:bg-gray-800 cursor-not-allowed"
                                    placeholder="e.g., Kenya"
                                    readonly
                                />
                                <InputError
                                    :message="form.errors['profile.country']"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Information Section -->
                <div
                    class="border rounded-lg overflow-hidden dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="toggleSection('location')"
                        class="w-full px-6 py-4 text-left font-medium text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex justify-between items-center"
                    >
                        <span>Location Information</span>
                        <svg
                            class="w-5 h-5 transition-transform duration-200"
                            :class="{
                                'rotate-180': activeSection === 'location',
                            }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            ></path>
                        </svg>
                    </button>
                    <div
                        v-show="activeSection === 'location'"
                        class="p-6 bg-white dark:bg-gray-900"
                    >
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="county_id">
                                    County
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <select
                                    id="county_id"
                                    v-model="form.citizen.county_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select County</option>
                                    <option
                                        v-for="county in $page.props.formData.locations.counties"
                                        :key="county.id"
                                        :value="county.id"
                                    >
                                        {{ county.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors['citizen.county_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="sub_county_id">
                                    Sub County
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="sub_county_id"
                                        v-model="form.citizen.sub_county_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.county_id || isLoadingSubCounties"
                                    >
                                        <option value="">Select Sub County</option>
                                        <!-- Show current sub-county first if it exists -->
                                        <option 
                                            v-if="citizen?.sub_county && citizen.sub_county.county_id == form.citizen.county_id"
                                            :value="citizen.sub_county_id"
                                            :key="'current-subcounty-' + citizen.sub_county_id"
                                        >
                                            {{ citizen.sub_county.name }}
                                        </option>
                                        <!-- Then show other filtered sub-counties -->
                                        <option
                                            v-for="sub_county in filteredSubCounties"
                                            :key="sub_county.id"
                                            :value="sub_county.id"
                                            :disabled="citizen?.sub_county_id === sub_county.id"
                                        >
                                            {{ sub_county.name }}
                                        </option>
                                    </select>
                                    <div v-if="isLoadingSubCounties" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.sub_county_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="constituency_id">
                                    Constituency
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="constituency_id"
                                        v-model="form.citizen.constituency_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.county_id"
                                        required
                                    >
                                        <option value="">Select Constituency</option>
                                        <!-- Show current constituency first if it exists -->
                                        <option 
                                            v-if="citizen?.constituency && citizen.constituency.county_id == form.citizen.county_id"
                                            :value="citizen.constituency_id"
                                            :key="'current-constituency-' + citizen.constituency_id"
                                        >
                                            {{ citizen.constituency.name }}
                                        </option>
                                        <!-- Then show other filtered constituencies -->
                                        <option
                                            v-for="constituency in filteredConstituencies"
                                            :key="constituency.id"
                                            :value="constituency.id"
                                            :disabled="citizen?.constituency_id === constituency.id"
                                        >
                                            {{ constituency.name }}
                                        </option>
                                    </select>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.constituency_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="ward_id">
                                    Ward
                                    <i
                                        class="fas fa-star text-red-500 text-xs ml-1"
                                    ></i>
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="ward_id"
                                        v-model="form.citizen.ward_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.constituency_id || isLoadingWards"
                                        required
                                    >
                                        <option value="">Select Ward</option>
                                        <!-- Show current ward first if it exists -->
                                        <option 
                                            v-if="citizen?.ward && citizen.ward.constituency_id == form.citizen.constituency_id"
                                            :value="citizen.ward_id"
                                            :key="'current-ward-' + citizen.ward_id"
                                        >
                                            {{ citizen.ward.name }}
                                        </option>
                                        <!-- Then show other filtered wards -->
                                        <option
                                            v-for="ward in filteredWards"
                                            :key="ward.id"
                                            :value="ward.id"
                                            :disabled="citizen?.ward_id === ward.id"
                                        >
                                            {{ ward.name }}
                                        </option>
                                    </select>
                                    <div v-if="isLoadingWards" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.ward_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="location_id">
                                    Location
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="location_id"
                                        v-model="form.citizen.location_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.ward_id || isLoadingLocations"
                                    >
                                        <option value="">Select Location</option>
                                        <!-- Show current location first if it exists -->
                                        <option 
                                            v-if="citizen?.location && citizen.location.ward_id == form.citizen.ward_id"
                                            :value="citizen.location_id"
                                            :key="'current-location-' + citizen.location_id"
                                        >
                                            {{ citizen.location.name }}
                                        </option>
                                        <!-- Then show other filtered locations -->
                                        <option
                                            v-for="location in filteredLocations"
                                            :key="location.id"
                                            :value="location.id"
                                            :disabled="citizen?.location_id === location.id"
                                        >
                                            {{ location.name }}
                                        </option>
                                    </select>
                                    <div v-if="isLoadingLocations" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.location_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="village_id">
                                    Village
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="village_id"
                                        v-model="form.citizen.village_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.location_id || isLoadingVillages"
                                    >
                                        <option value="">Select Village</option>
                                        <!-- Show current village first if it exists -->
                                        <option 
                                            v-if="citizen?.village && citizen.village.location_id == form.citizen.location_id"
                                            :value="citizen.village_id"
                                            :key="'current-village-' + citizen.village_id"
                                        >
                                            {{ citizen.village.name }}
                                        </option>
                                        <!-- Then show other filtered villages -->
                                        <option
                                            v-for="village in filteredVillages"
                                            :key="village.id"
                                            :value="village.id"
                                            :disabled="citizen?.village_id === village.id"
                                        >
                                            {{ village.name }}
                                        </option>
                                    </select>
                                    <div v-if="isLoadingVillages" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.village_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="polling_center_id">
                                    Polling Center
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="polling_center_id"
                                        v-model="form.citizen.polling_center_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.ward_id || isLoadingPollingCenters"
    
                                    >
                                        <option value="">
                                            Select Polling Center
                                        </option>
                                        <!-- Show current polling center first if it exists -->
                                        <option 
                                            v-if="citizen?.polling_center && citizen.polling_center.ward_id == form.citizen.ward_id"
                                            :value="citizen.polling_center_id"
                                            :key="'current-center-' + citizen.polling_center_id"
                                        >
                                            {{ citizen.polling_center.name }}
                                        </option>
                                        <!-- Then show other filtered polling centers -->
                                        <option
                                            v-for="center in filteredPollingCenters"
                                            :key="center.id"
                                            :value="center.id"
                                            :disabled="citizen?.polling_center_id === center.id"
                                        >
                                            {{ center.name }}
                                        </option>
                                    </select>
                                    <div v-if="isLoadingPollingCenters" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.polling_center_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="polling_station_id">
                                    Polling Station
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="polling_station_id"
                                        v-model="form.citizen.polling_station_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.polling_center_id || isLoadingPollingStations"
    
                                    >
                                        <option value="">
                                            Select Polling Station
                                        </option>
                                        <!-- Show current polling station first if it exists -->
                                        <option 
                                            v-if="citizen?.polling_station && citizen.polling_station.polling_center_id == form.citizen.polling_center_id"
                                            :value="citizen.polling_station_id"
                                            :key="'current-station-' + citizen.polling_station_id"
                                        >
                                            {{ citizen.polling_station.name }}
                                        </option>
                                        <!-- Then show other filtered polling stations -->
                                        <option
                                            v-for="station in filteredPollingStations"
                                            :key="station.id"
                                            :value="station.id"
                                            :disabled="citizen?.polling_station_id === station.id"
                                        >
                                            {{ station.name }}
                                        </option>
                                    </select>
                                    <div v-if="isLoadingPollingStations" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.polling_station_id']"
                                    class="mt-2"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="polling_stream_id">
                                    Polling Stream
                                </InputLabel>
                                <div class="relative">
                                    <select
                                        id="polling_stream_id"
                                        v-model="form.citizen.polling_stream_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        :disabled="!form.citizen.polling_station_id || isLoadingPollingStreams"
    
                                    >
                                        <option value="">
                                            Select Polling Stream
                                        </option>
                                        <!-- Show current polling stream first if it exists -->
                                        <option 
                                            v-if="citizen?.polling_stream && citizen.polling_stream.polling_station_id == form.citizen.polling_station_id"
                                            :value="citizen.polling_stream_id"
                                            :key="'current-stream-' + citizen.polling_stream_id"
                                        >
                                            {{ citizen.polling_stream.name }}
                                        </option>
                                        <!-- Then show other filtered polling streams -->
                                        <option
                                            v-for="stream in filteredPollingStreams"
                                            :key="stream.id"
                                            :value="stream.id"
                                            :disabled="citizen?.polling_stream_id === stream.id"
                                        >
                                            {{ stream.name }}
                                        </option>
                                    </select>
                                    <div v-if="isLoadingPollingStreams" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors['citizen.polling_stream_id']"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </PrimaryButton>
        </template>
    </FormSection>

    <!-- OTP Verification Modal -->
    <div v-if="showOtpModal" class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="otp-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div 
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                aria-hidden="true" 
                @click.self="showOtpModal = false"
            ></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-8 relative">
                <!-- Close button -->
                <button 
                    type="button" 
                    @click="showOtpModal = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 focus:outline-none"
                    :disabled="otpVerificationInProgress"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="sm:flex sm:items-start">
                    <!-- Icon container -->
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    
                    <!-- Modal content -->
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="otp-modal-title">
                            Verify {{ otpForm.field_to_verify === 'email' ? 'Email Address' : 'Phone Number' }}
                        </h3>
                        
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                We've sent a 6-digit verification code to:
                                <span class="block font-medium text-gray-900 dark:text-white mt-1">
                                    {{ otpForm.field_to_verify === 'email' ? form.email : formatPhoneNumberForDisplay(otpForm.new_value) }}
                                </span>
                            </p>
                            
                            <!-- OTP Input -->
                            <div class="mt-6">
                                <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Enter verification code
                                </label>
                                <div class="flex justify-between space-x-3">
                                    <input
                                        v-for="i in 6"
                                        :key="i"
                                        v-model="otpDigits[i-1]"
                                        @input="onOtpInput($event, i-1)"
                                        @keydown.delete="onOtpKeyDown($event, i-1)"
                                        @paste.prevent="onOtpPaste($event)"
                                        type="tel"
                                        maxlength="1"
                                        pattern="[0-9]"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        :disabled="otpVerificationInProgress"
                                        class="w-full h-14 text-2xl text-center font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50"
                                        :class="{ 'border-red-500': otpError }"
                                    />
                                </div>
                                
                                <!-- Error message -->
                                <p v-if="otpError" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ otpError }}
                                </p>
                                
                                <!-- Resend code section -->
                                <div class="mt-4 text-center">
                                    <p v-if="!otpResendEnabled" class="text-sm text-gray-500 dark:text-gray-400">
                                        Didn't receive a code? Resend in 
                                        <span class="font-medium">{{ Math.floor(otpCountdown / 60) }}:{{ (otpCountdown % 60).toString().padStart(2, '0') }}</span>
                                    </p>
                                    <button
                                        v-else
                                        type="button"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 focus:outline-none disabled:opacity-50"
                                        @click="resendOtp"
                                        :disabled="!otpResendEnabled || otpVerificationInProgress"
                                    >
                                        Resend code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action buttons -->
                <div class="mt-6 sm:mt-8 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-3 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-150"
                        :class="{ 'opacity-75 cursor-not-allowed': otpDigits.join('').length !== 6 || otpVerificationInProgress }"
                        @click="verifyOtp(otpDigits.join(''))"
                        :disabled="otpDigits.join('').length !== 6 || otpVerificationInProgress"
                    >
                        <span v-if="!otpVerificationInProgress">Verify Now</span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verifying...
                        </span>
                    </button>
                    <button
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-3 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-150"
                        @click="showOtpModal = false"
                        :disabled="otpVerificationInProgress"
                    >
                        Cancel
                    </button>
                </div>
                
                <!-- Help text -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Having trouble? Contact support at 
                        <a href="mailto:support@forwardkenyaparty.com" class="text-indigo-600 dark:text-indigo-400 hover:underline">support@forwardkenyaparty.com</a>
                        or call +254 700 000 000
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
