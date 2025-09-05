<script setup>
import { useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

// Define props passed from the backend
defineProps({
    title: {
        type: String,
        required: true,
    },
    logoUrl: {
        type: String,
        required: true,
    },
});

// Reactive state for form fields
const form = useForm({
    email: "",
    phone: "",
    subject: "",
    message: "",
    agree_to_privacy_policy: false,
});

// Form submission handler
const submitForm = () => {
    form.post(route("feedback.stream.store"), {
        preserveScroll: true,
        onSuccess: (page) => {
            // Access flash messages from backend
            const successMessage = page.props.flash?.success;
            const errorMessage = page.props.flash?.error;

            // Show success alert if success message exists
            if (successMessage) {
                form.reset();

                window.Toast.fire({
                    icon: "success",
                    title: successMessage,
                });
            }

            // Show error alert if error message exists
            if (errorMessage) {
                window.Toast.fire({
                    icon: "warning",
                    title: errorMessage,
                });
            }
        },
        onError: (errors) => {
            // Handle validation errors
            if (errors) {
                window.Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    html: Object.values(errors)
                        .map((msg) => `<p>${msg}</p>`)
                        .join(""), // Render errors
                    confirmButtonColor: "#ef4444", // Tailwind red-500
                    showClass: {
                        popup: "animate__animated animate__fadeInDown",
                    },
                    hideClass: {
                        popup: "animate__animated animate__fadeOutUp",
                    },
                });
            }
        },
    });
};
</script>

<style scoped>
@keyframes bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}
.animate-bounce {
    animation: bounce 1s infinite;
}
</style>

<template>
    <!-- Pass dynamic props to GuestLayout -->
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
        <section class="container mx-auto px-4 py-8 pt-36 flex-1">
            <h2
                class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"
            >
                {{ title }}
            </h2>
            <div
                class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mb-4"
            ></div>
            <p
                class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl"
            >
                Join us in shaping the future of Kenya through our shared values of unity, equity, and sustainable development. Whether you're interested in party membership, want to learn about our vision for Kenya, seek information about our candidates, or wish to contribute your ideas, we welcome your engagement. Your voice matters in our collective journey towards building a more inclusive and prosperous nation. Together, we can make a difference.
            </p>

            <!-- Contact Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <!-- Call Us -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                    <div class="flex items-center mb-4">
                        <div
                            class="bg-green-100 p-3 rounded-full w-12 h-12 flex items-center justify-center"
                        >
                            <i
                                class="fas fa-phone-alt text-green-600 text-xl"
                            ></i>
                        </div>
                        <h3
                            class="ml-4 text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Call Us
                        </h3>
                    </div>
                    <p class="ml-16 text-gray-600 dark:text-gray-400">
                        Telephone:
                    </p>
                    <a
                        href="tel:+254713447820"
                        class="ml-16 text-green-600 dark:text-green-400 font-semibold"
                    >
                        +254 713 447820
                    </a>
                </div>

                <!-- Write Us -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                    <div class="flex items-center mb-4">
                        <div
                            class="bg-green-100 p-3 rounded-full w-12 h-12 flex items-center justify-center"
                        >
                            <i
                                class="fas fa-envelope text-green-600 text-xl"
                            ></i>
                        </div>
                        <h3
                            class="ml-4 text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Write Us
                        </h3>
                    </div>
                    <p class="ml-16 text-gray-600 dark:text-gray-400">Email:</p>
                    <a
                        href="mailto:forwardkenyaparty@gmail.com"
                        class="ml-16 text-green-600 dark:text-green-400 font-semibold"
                    >
                        forwardkenyaparty@gmail.com
                    </a>
                </div>

                <!-- Visit Us -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                    <div class="flex items-center mb-4">
                        <div
                            class="bg-green-100 p-3 rounded-full w-12 h-12 flex items-center justify-center"
                        >
                            <i
                                class="fas fa-map-marker-alt text-green-600 text-xl"
                            ></i>
                        </div>
                        <h3
                            class="ml-4 text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Visit Us
                        </h3>
                    </div>
                    <p class="ml-16 text-gray-600 dark:text-gray-400">
                        Find us at:
                    </p>
                    <a
                        href="/contact-us"
                        class="ml-16 text-green-600 dark:text-green-400 font-semibold"
                    >
                        View Park Towers, Nairobi, Kenya
                    </a>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg rounded-xl p-8 md:p-12 text-white mb-16">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Left Column: Contact Form -->
                    <div>
                        <h3
                            class="text-2xl font-bold text-gray-900 dark:text-white mb-6"
                        >
                            Send us a message
                        </h3>
                        <form
                            @submit.prevent="submitForm"
                            class="space-y-6"
                        >
                            <!-- Email Input -->
                            <div>
                                <label
                                    for="email"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                                >
                                    Your email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    v-model="form.email"
                                    required
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500 dark:shadow-sm-light"
                                    placeholder="name@forwardkenyaparty.com"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Telephone Input -->
                            <div>
                                <label
                                    for="phone"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                                >
                                    Your telephone number
                                </label>
                                <input
                                    type="tel"
                                    id="phone"
                                    v-model="form.phone"
                                    required
                                    pattern="[0-9+\-\s()]{10,20}"
                                    title="Please enter a valid phone number"
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500 dark:shadow-sm-light"
                                    placeholder="+254 700 000 000"
                                />
                                <p
                                    v-if="form.errors.phone"
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ form.errors.phone }}
                                </p>
                            </div>

                            <!-- Subject Input -->
                            <div>
                                <label
                                    for="subject"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                                >
                                    Subject
                                </label>
                                <input
                                    type="text"
                                    id="subject"
                                    v-model="form.subject"
                                    class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500 dark:shadow-sm-light"
                                    placeholder="Let us know how we can help you"
                                    required
                                />
                                <p
                                    v-if="form.errors.subject"
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ form.errors.subject }}
                                </p>
                            </div>

                            <!-- Message Textarea -->
                            <div class="sm:col-span-2">
                                <label
                                    for="message"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                                >
                                    Your message
                                </label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    rows="6"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                    placeholder="Leave a comment..."
                                    required
                                ></textarea>
                                <p
                                    v-if="form.errors.message"
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ form.errors.message }}
                                </p>
                            </div>

                            <!-- Privacy Policy Checkbox -->
                            <div class="flex items-center">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            type="checkbox"
                                            id="agree_to_privacy_policy"
                                            v-model="form.agree_to_privacy_policy"
                                            class="w-4 h-4 text-green-600 bg-gray-100 rounded border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                            required
                                        />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="agree_to_privacy_policy" class="font-medium text-gray-700 dark:text-gray-300">
                                            <div class="flex flex-wrap">
                                                <span class="mr-1">
                                                    By contacting us, you agree to the
                                                    <a
                                                        :href="route('frontend.terms-and-conditions')"
                                                        target="_blank"
                                                        class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                    >
                                                        Terms of Service
                                                    </a>
                                                    <span class="mx-1">and</span>
                                                    <a
                                                        :href="route('frontend.privacy-policy')"
                                                        target="_blank"
                                                        class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                    >
                                                        Privacy Policy
                                                    </a>
                                                    and affirm that all information provided is true and accurate.
                                                </span>
                                            </div>
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            You must agree to the terms of service and our privacy policy to submit a contact form.
                                        </p>
                                    </div>
                                </div>
                                <p
                                    v-if="
                                        form.errors.agree_to_privacy_policy
                                    "
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    {{
                                        form.errors.agree_to_privacy_policy
                                    }}
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                :disabled="!form.agree_to_privacy_policy || form.processing || !form.email || !form.phone || !form.subject || !form.message"
                                :class="[
                                    'font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2',
                                    form.agree_to_privacy_policy && form.email && form.phone && form.subject && form.message && !form.processing
                                        ? 'text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 cursor-pointer'
                                        : 'bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed'
                                ]"
                            >
                                <span v-if="form.processing">Sending...</span>
                                <span v-else>Send message</span>
                            </button>
                        </form>
                    </div>

                    <!-- Right Column: Map -->
                    <div
                        class="rounded-lg overflow-hidden"
                    >
                        <h3
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Find us on map
                        </h3>
                        <div class="h-full w-full pt-2">
                            <div
                                class="relative h-96 w-full overflow-hidden"
                            >
                                <!-- Google Maps Embed -->
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.800778530791!2d36.81814931475399!3d-1.284638999999982!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f10d7b2f1e9f3%3A0x1d5e6c1c9b6b1f1f!2sView%20Park%20Towers%2C%20Nairobi!5e0!3m2!1sen!2ske!4v1620000000000!5m2!1sen!2ske"
                                    width="100%"
                                    height="100%"
                                    style="border: 0"
                                    allowfullscreen=""
                                    loading="lazy"
                                    class="relative z-0"
                                ></iframe>
                                <!-- Animated Map Pin -->
                                <div
                                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10 text-center"
                                >
                                    <div class="relative">
                                        <div
                                            class="animate-ping absolute inline-flex h-12 w-12 rounded-full bg-red-500 opacity-75 -top-6 -left-6"
                                        ></div>
                                        <i
                                            class="fas fa-map-marker-alt text-4xl text-red-600 hover:animate-bounce cursor-pointer"
                                            style="
                                                filter: drop-shadow(
                                                    0 0 2px
                                                        rgba(0, 0, 0, 0.5)
                                                );
                                            "
                                        ></i>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="p-4 bg-white/50 dark:bg-gray-800/50 rounded-b-lg shadow-sm border border-gray-100 dark:border-gray-700"
                            >
                                <div
                                    class="flex items-start space-x-3 mb-3"
                                >
                                    <div
                                        class="p-2 bg-green-100 dark:bg-green-900/30 rounded-full w-10 h-10 flex-shrink-0 flex items-center justify-center mt-0.5"
                                    >
                                        <i
                                            class="fas fa-map-marker-alt text-green-600 dark:text-green-400 text-base"
                                        ></i>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            Forward Kenya Party
                                        </p>
                                        <p
                                            class="text-xs text-gray-600 dark:text-gray-400"
                                        >
                                            Utalii Lane, Viewpark Towers, 19th Floor, Suite 19<br>
                                            P.O. Box: 27999 – 00100, GPO NAIROBI
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="border-t border-gray-200 dark:border-gray-700 pt-3"
                                >
                                    <div
                                        class="flex items-center justify-center mb-2"
                                    >
                                        <i
                                            class="far fa-clock text-green-600 dark:text-green-400 text-sm mr-1.5"
                                        ></i>
                                        <h4
                                            class="text-sm font-semibold text-gray-800 dark:text-white"
                                        >
                                            Opening Hours
                                        </h4>
                                    </div>
                                    <ul class="space-y-1.5 text-xs">
                                        <li
                                            class="flex items-center justify-between py-1 px-2 bg-gray-50 dark:bg-gray-700/30 rounded"
                                        >
                                            <span
                                                class="text-gray-600 dark:text-gray-400"
                                                >Monday - Friday</span
                                            >
                                            <span
                                                class="font-medium text-gray-800 dark:text-gray-200"
                                                >8:00 AM - 5:00 PM</span
                                            >
                                        </li>
                                        <li
                                            class="flex items-center justify-between py-1 px-2 bg-gray-50 dark:bg-gray-700/30 rounded"
                                        >
                                            <span
                                                class="text-gray-600 dark:text-gray-400"
                                                >Saturday</span
                                            >
                                            <span
                                                class="font-medium text-gray-800 dark:text-gray-200"
                                                >8:00 AM - 1:00 PM</span
                                            >
                                        </li>
                                        <li
                                            class="flex items-center justify-between py-1 px-2 bg-gray-50 dark:bg-gray-700/30 rounded"
                                        >
                                            <span
                                                class="text-gray-600 dark:text-gray-400"
                                                >Sunday</span
                                            >
                                            <span
                                                class="font-medium text-red-500 dark:text-red-400"
                                                >Closed</span
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Font Awesome for the map pin -->
                    <link
                        rel="stylesheet"
                        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
                    />
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
