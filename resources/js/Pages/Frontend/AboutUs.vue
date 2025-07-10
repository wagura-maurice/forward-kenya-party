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
                Our e-government platform is designed to empower citizens by
                providing seamless access to essential government services with
                a mission to create a more efficient, inclusive, and accessible
                government experience for all.
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
                            Powering E-Government Innovation for
                            <span class="font-extrabold"
                                >millions of citizens and government
                                entities</span
                            >
                        </h2>
                        <p class="mb-4 font-light">
                            Our e-government platform is transforming how
                            citizens, refugees, diplomats, foreigners, and
                            residents interact with government agencies. By
                            offering specialized access to services based on
                            roles, we enable each user group to seamlessly
                            engage with government processes. Through advanced
                            technology, we ensure that everyone—regardless of
                            their status—has easy and secure access to essential
                            public services.
                        </p>
                        <p class="mb-4 font-medium">
                            The platform is designed with flexibility in mind,
                            catering to various user roles including citizens,
                            refugees, diplomats, foreigners, and residents. Each
                            group will have access to tailored services,
                            ensuring that their unique needs are met by
                            government agencies on the platform. This allows for
                            efficient service delivery, regardless of the
                            individual's status.
                        </p>
                        <p class="mb-4 font-light">
                            Citizens and residents can easily access a wide
                            range of services, from requesting local government
                            services to making payments for taxes and utilities.
                            Refugees and diplomats will find dedicated channels
                            for their specific needs, such as registering with
                            the government or accessing diplomatic services.
                            Foreigners will be supported with visa applications,
                            residency permits, and other services essential to
                            their stay in the country.
                        </p>

                        <!-- E-Government Principles -->
                        <div class="mt-8">
                            <h3
                                class="text-3xl font-bold text-gray-900 dark:text-white mb-4"
                            >
                                Core Principles of E-Government
                            </h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Accessibility:
                                    </div>
                                    <div class="col-span-10">
                                        Ensuring all citizens, refugees,
                                        diplomats, foreigners, and residents can
                                        easily access government services that
                                        match their role and status.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Transparency:
                                    </div>
                                    <div class="col-span-10">
                                        Government processes are open and clear
                                        to all users, with tailored information
                                        available based on user type, ensuring
                                        fairness and understanding.
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-2 font-semibold">
                                        Efficiency:
                                    </div>
                                    <div class="col-span-10">
                                        The platform streamlines service
                                        delivery based on role-specific needs,
                                        reducing delays and providing efficient
                                        access to services for all user groups.
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
