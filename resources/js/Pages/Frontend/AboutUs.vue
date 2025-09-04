<!-- resources/js/Pages/Frontend/AboutUs.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import TeamSection from '@/Components/TeamSection.vue';
import WhoWeAreSection from '@/Components/WhoWeAreSection.vue';
import PartyHistorySection from '@/Components/PartyHistorySection.vue';
import CallToActionSection from '@/Components/CallToActionSection.vue';
import CombinedIdeologyAndVisionMissionSection from '@/Components/CombinedIdeologyAndVisionMissionSection.vue';

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
    <Head :title="title" />
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
                The Forward Kenya Party is a national political movement established to transform Kenya's governance through progressive policies and ethical leadership. As a beacon of hope and change, we unite Kenyans across all communities with a shared commitment to national development, social equity, and economic transformation. Our foundation is built on the principles of social democracy, ensuring that every policy decision prioritizes the welfare of ordinary citizens while fostering sustainable growth and national unity.
            </p>

            <!-- Combined Ideology and Vision/Mission Card Section -->
            <CombinedIdeologyAndVisionMissionSection />

            <!-- Who We Are Section -->
            <WhoWeAreSection />

            <!-- Team Section -->
            <TeamSection />

            <!-- Party History Section -->
            <PartyHistorySection />

            <!-- Call to Action Section -->
            <CallToActionSection />
        </section>
    </GuestLayout>
</template>
