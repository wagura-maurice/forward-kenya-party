<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const page = usePage();
const user = computed(() => page.props.auth.user);

const fullName = computed(() => {
    if (!user.value?.profile) return "Not available";
    return [
        user.value.profile.first_name,
        user.value.profile.middle_name,
        user.value.profile.last_name,
    ]
        .filter(Boolean)
        .join(" ");
});

const formatDate = (dateString) => {
    if (!dateString) return "Not specified";
    try {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString(undefined, options);
    } catch (e) {
        return "Invalid date";
    }
};

const getAddress = computed(() => {
    if (!user.value?.profile) return "Not provided";
    const { profile } = user.value;
    return (
        [
            profile.address_line_1,
            profile.address_line_2,
            profile.city,
            profile.county?.name,
            profile.sub_county?.name,
            profile.constituency?.name,
            profile.ward?.name,
        ]
            .filter(Boolean)
            .join(", ") || "Not provided"
    );
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <header class="bg-white shadow dark:bg-gray-800">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Welcome to Your Dashboard
                </h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Loading State -->
            <div
                v-if="!$page.props.auth"
                class="flex justify-center items-center py-12"
            >
                <div
                    class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500"
                ></div>
            </div>

            <!-- Unauthenticated State -->
            <div v-else-if="!$page.props.auth.user" class="text-center py-12">
                <h2
                    class="text-2xl font-bold text-gray-900 dark:text-white mb-4"
                >
                    Please log in to view your profile
                </h2>
                <Link
                    :href="route('login')"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Go to Login
                </Link>
            </div>

            <!-- Authenticated Content -->
            <template v-else>
                <!-- Welcome Section -->
                <section class="mb-8">
                    <div
                        class="relative px-6 py-12 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div class="mb-4">
                                <ApplicationLogo class="w-20 h-20" />
                            </div>
                            <h2
                                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                            >
                                Welcome back, {{ user?.name || "User" }}!
                            </h2>
                            <p class="mb-4 text-gray-500 dark:text-gray-400">
                                You're logged in to your account dashboard. Here
                                you can manage your profile and settings.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Account Information Section -->
                <section>
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-hidden"
                    >
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700"
                        >
                            <h3
                                class="text-lg font-medium leading-6 text-gray-900 dark:text-white"
                            >
                                Account Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Personal Information -->
                                <div class="space-y-4">
                                    <h4
                                        class="text-md font-semibold text-gray-900 dark:text-white"
                                    >
                                        Personal Information
                                    </h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Full Name
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ fullName }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Email
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    user?.email ||
                                                    "Not available"
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Username
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    user?.name ||
                                                    "Not available"
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Gender
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white capitalize"
                                            >
                                                {{
                                                    user?.profile?.gender ||
                                                    "Not specified"
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Date of Birth
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    user?.profile?.date_of_birth
                                                        ? formatDate(
                                                              user.profile
                                                                  .date_of_birth
                                                          )
                                                        : "Not specified"
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="space-y-4">
                                    <h4
                                        class="text-md font-semibold text-gray-900 dark:text-white"
                                    >
                                        Contact Information
                                    </h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Phone Number
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    user?.profile?.telephone ||
                                                    "Not provided"
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Address
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ getAddress }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="pt-4">
                                        <Link
                                            :href="route('profile.show')"
                                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        >
                                            <svg
                                                class="w-4 h-4 me-2"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    d="m13.835 7.578-.005.007-7.137 7.137 2.139 2.138 7.143-7.142-2.14-2.14Zm-10.696 3.59 2.139 2.14 7.18-7.18-2.14-2.137-7.179 7.178v-.001Zm1.433 4.261v2.139l2.14-2.14-2.14-2.14v4.28h4.28l-2.14-2.14h-.001l2.14-2.14H4.442a2.154 2.154 0 0 0-2.142 2.14l.01-7.053 7.043-7.043c.362-.473.9-.73 1.468-.73.005 0 .658.01 1.318.143.49.1.96.35 1.334.725l.002.002c.37.38.6.866.66 1.39.07.56.07 1.2.07 1.794v.022h-1.5v-.213c0-.408 0-.867-.043-1.249a1.71 1.71 0 0 0-.12-.5 1.1 1.1 0 0 0-.3-.42 1.1 1.1 0 0 0-.35-.23c-.16-.07-.298-.1-.5-.12-.4-.04-.86-.04-1.27-.04-.4 0-.87 0-1.27.04a1.8 1.8 0 0 0-.5.12 1.1 1.1 0 0 0-.35.23c-.11.1-.21.25-.26.4-.07.16-.1.33-.12.5-.04.39-.04.85-.04 1.25v.21c0 .4 0 .86.04 1.25.02.17.05.34.12.5.1.24.23.41.42.53.16.1.33.14.5.16.39.04.86.04 1.27.04h.21c.4 0 .87 0 1.27-.04.17-.02.34-.06.5-.16.08-.05.16-.1.23-.17l7.18-7.18v7.18h-1.5V8.518l-7.18 7.18a2.25 2.25 0 0 1-.53.42c-.24.14-.51.21-.78.24-.4.04-.86.04-1.27.04h-.21c-.4 0-.87 0-1.27-.04a2.25 2.25 0 0 1-.78-.24 2.25 2.25 0 0 1-.77-.64 2.25 2.25 0 0 1-.5-1.39c-.04-.39-.04-.85-.04-1.25v-.21c0-.4 0-.86.04-1.25.02-.27.1-.54.24-.77.14-.24.33-.44.57-.6.23-.14.5-.23.78-.26.39-.04.86-.04 1.27-.04h.21c.4 0 .87 0 1.27.04"
                                                />
                                            </svg>
                                            Edit Profile
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>
        </main>
    </div>
</template>
