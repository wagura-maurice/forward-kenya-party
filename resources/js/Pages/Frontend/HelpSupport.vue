<!-- resources/js/Pages/Frontend/HelpSupport.vue -->
<script setup>
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
</script>

<template>
    <!-- Pass dynamic props to GuestLayout -->
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
        <section class="container mx-auto px-4 py-8 pt-36 flex-1">
            <h2
                class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"
            >
                {{ title }}
            </h2>
            <p
                class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl"
            >
                The Forward Kenya Party is committed to serving all Kenyans with dedication and transparency. Our support team is here to assist you with any inquiries, whether you're looking to join our movement, volunteer, or learn more about our policies and initiatives for a better Kenya. Your voice matters to us.
            </p>

            <!-- Showcase Section -->
            <div class="bg-white dark:bg-gray-900 mt-8">
                <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                    here add the content and html resource etc relevant to
                    achiving a practical a Help and Support page
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
