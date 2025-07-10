<!-- resources/js/Pages/Frontend/AboutUs.vue -->
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
            <div
                class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mb-4"
            ></div>
            <p
                class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl"
            >
                Forward Kenya Party is a new, inclusive platform dedicated to
                addressing the needs and aspirations of all people of Kenya. We
                serve citizens, refugees, diplomats, foreigners, residents, and
                work in partnership with communities and stakeholders to
                build a better, more just society. The party's mission is to empower
                every individual, regardless of status, to have a voice and
                access to opportunities for positive change.
            </p>

            <!-- Showcase Section -->
            <div
                class="bg-white dark:bg-gray-900 mt-8 rounded-lg shadow-lg mb-16"
            >
                <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                    <div
                        class="max-w-screen-lg text-gray-500 sm:text-lg dark:text-gray-400"
                    >
                        <h2
                            class="mb-4 text-4xl tracking-tight font-bold text-gray-900 dark:text-white"
                        >
                            Powering Forward Kenya Party for
                            <span class="font-extrabold"
                                >all people of Kenya</span
                            >
                        </h2>
                        <p class="mb-4 font-light">
                            Our platform is transforming how people engage with
                            Forward Kenya Party and community advocacy. By
                            offering tailored engagement and resources based on
                            roles—including citizens, refugees, diplomats,
                            foreigners, residents, and party supporters—we ensure that
                            every voice is heard and every member can
                            participate meaningfully in shaping Kenya's future.
                            Through innovative outreach and technology, we
                            connect, inform, and mobilize for collective
                            progress.
                        </p>
                        <p class="mb-4 font-medium">
                            Forward Kenya Party is built on a foundation of
                            flexibility and inclusivity. We recognize the
                            diverse backgrounds and needs of our members and
                            supporters—whether you are a citizen, refugee,
                            diplomat, foreigner, resident, or party
                            supporter. Each group is provided with dedicated
                            channels for engagement, advocacy, and support,
                            ensuring that everyone can contribute to and benefit
                            from our shared vision for Kenya.
                        </p>
                        <p class="mb-4 font-light">
                            Whether you are looking to join the party,
                            participate in events, access member resources, or
                            collaborate on community initiatives, Forward Kenya
                            Party provides the tools and support you need. Our
                            approach ensures that all groups—citizens, refugees,
                            diplomats, foreigners, residents, and party
                            supporters—can engage with the party in meaningful ways and help
                            drive positive change for Kenya.
                        </p>

                        <!-- Forward Kenya Party Principles -->
                        <div class="mt-8">
                            <h3
                                class="text-3xl font-bold text-gray-900 dark:text-white mb-4"
                            >
                                Core Principles of Forward Kenya Party
                            </h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Accessibility:
                                    </div>
                                    <div class="col-span-10">
                                        Ensuring all people—regardless of
                                        background or status—can easily engage
                                        with the party, access resources, and
                                        participate in shaping our agenda.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Transparency:
                                    </div>
                                    <div class="col-span-10">
                                        Party processes and decision-making are
                                        open, clear, and inclusive. We provide
                                        tailored information and encourage open
                                        dialogue to foster trust and
                                        accountability.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Participation:
                                    </div>
                                    <div class="col-span-10">
                                        We empower members and supporters to
                                        actively participate in party
                                        activities, discussions, and leadership,
                                        ensuring every voice is valued in our
                                        movement.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Advocacy:
                                    </div>
                                    <div class="col-span-10">
                                        We champion the needs and rights of all
                                        groups in Kenya, working collaboratively
                                        with partners and communities to create
                                        lasting positive change.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Innovation:
                                    </div>
                                    <div class="col-span-10">
                                        We leverage technology and new ideas to
                                        connect, inform, and mobilize our
                                        members, making Forward Kenya Party a
                                        leader in modern political engagement.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Collaboration:
                                    </div>
                                    <div class="col-span-10">
                                        Facilitating communication and
                                        cooperation between various government
                                        agencies and users, ensuring that
                                        different user roles have a clear,
                                        responsive line of communication.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Accountability:
                                    </div>
                                    <div class="col-span-10">
                                        Users can track their interactions with
                                        government agencies based on their
                                        specific roles, promoting responsibility
                                        and engagement.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Security:
                                    </div>
                                    <div class="col-span-10">
                                        Protecting personal and role-specific
                                        data for each user type, ensuring
                                        confidentiality and trust in the
                                        platform.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Citizen-Focused Features -->
                        <div class="mt-8">
                            <h3
                                class="text-3xl font-bold text-gray-900 dark:text-white mb-4"
                            >
                                Role-Based Government Services
                            </h3>
                            <p class="mb-4 font-light">
                                Our platform provides customized service
                                pathways based on user roles, ensuring that
                                citizens, refugees, diplomats, foreigners, and
                                residents have tailored access to the services
                                they need. Here's how the platform supports each
                                group:
                            </p>
                            <div class="space-y-4">
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Citizens:
                                    </div>
                                    <div class="col-span-10">
                                        Access to essential services such as
                                        healthcare, education, taxation, and
                                        local government services. Citizens can
                                        track their requests, make payments, and
                                        stay updated on public services.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Refugees:
                                    </div>
                                    <div class="col-span-10">
                                        Special support for refugee
                                        registration, settlement services, and
                                        humanitarian assistance. The platform
                                        provides access to necessary resources
                                        and government support tailored for
                                        refugees.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Diplomats:
                                    </div>
                                    <div class="col-span-10">
                                        Diplomats can register with the
                                        government for specific diplomatic
                                        services, request consular assistance,
                                        and manage their stay in the country
                                        through a dedicated portal.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Foreigners:
                                    </div>
                                    <div class="col-span-10">
                                        Foreign nationals can apply for visas,
                                        permits, and other required
                                        documentation while managing their stay
                                        with efficient digital services.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Residents:
                                    </div>
                                    <div class="col-span-10">
                                        Residents, both local and foreign, can
                                        access services like municipal
                                        applications, public health programs,
                                        and tax registration, ensuring they have
                                        everything they need during their
                                        residency.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
