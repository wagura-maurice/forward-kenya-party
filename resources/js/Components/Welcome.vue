<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const page = usePage();
const user = computed(() => page.props.auth.user);

const fullName = computed(() => {
    if (!user.value) return "Not available";
    const profile = user.value.profile || {};
    return (
        [profile.first_name, profile.middle_name, profile.last_name]
            .filter(Boolean)
            .join(" ") || "Not provided"
    );
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
    if (!user.value) return "Not provided";
    const profile = user.value.profile || {};
    const address = [
        profile.address_line_1,
        profile.address_line_2,
        profile.city,
        profile.county?.name || profile.county,
        profile.sub_county?.name || profile.sub_county,
        profile.constituency?.name || profile.constituency,
        profile.ward?.name || profile.ward,
    ].filter(Boolean);

    return address.length > 0 ? address.join(", ") : "Not provided";
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <section class="bg-white py-6 sm:py-8 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="mb-4 sm:mb-6" aria-label="Breadcrumb">
                    <ol
                        class="flex flex-wrap items-center space-x-1 sm:space-x-2 rtl:space-x-reverse"
                    >
                        <li class="inline-flex items-center">
                            <a
                                href="#"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white"
                            >
                                <svg
                                    class="w-4 h-4 me-2 rtl:me-0 rtl:ms-2"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"
                                    />
                                </svg>
                                <span class="hidden sm:inline">Home</span>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg
                                class="w-4 h-4 text-gray-400 rtl:rotate-180 mx-1 sm:mx-2"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m9 5 7 7-7 7"
                                />
                            </svg>
                            <a
                                href="#"
                                class="text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white whitespace-nowrap"
                            >
                                My account
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg
                                class="w-4 h-4 text-gray-400 rtl:rotate-180 mx-1 sm:mx-2"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m9 5 7 7-7 7"
                                />
                            </svg>
                            <span
                                class="text-sm font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap"
                            >
                                Account
                            </span>
                        </li>
                    </ol>
                </nav>

                <!-- Page Title -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8"
                >
                    <h2
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        General Overview
                    </h2>
                    <div class="flex-shrink-0">
                        <Link
                            :href="route('profile.show')"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition ease-in-out duration-150"
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
                <!-- Content Section -->
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 dark:bg-gray-800 overflow-hidden"
                >
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
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
                                            {{ user?.email || "Not available" }}
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
                                            {{ user?.name || "Not available" }}
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
                                                user?.gender
                                                    ? String(
                                                          user.profile
                                                              ?.gender ||
                                                              user.gender
                                                      )
                                                          .charAt(0)
                                                          .toUpperCase() +
                                                      String(
                                                          user.profile
                                                              ?.gender ||
                                                              user.gender
                                                      ).slice(1)
                                                    : "Not specified"
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
                                                user?.profile?.date_of_birth ||
                                                user?.date_of_birth
                                                    ? formatDate(
                                                          user.profile
                                                              ?.date_of_birth ||
                                                              user.date_of_birth
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
                                                user?.telephone ||
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
            </div>
        </section>
    </div>
</template>
