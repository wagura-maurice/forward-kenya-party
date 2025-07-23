<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";

// Props for dynamic logo, title, and navigation links
defineProps({
    title: { type: String, required: true },
    menuLogo: { type: String, required: true },
    footerLogo: { type: String, required: true },
    menuLinks: {
        type: Array,
        default: () => [
            { name: "Home", href: "/" },
            { name: "About Us", href: "/about-us" },
            { name: "Contact Us", href: "/contact-us" },
        ],
    },
    otherLinks: {
        type: Array,
        default: () => [
            { name: "Help & Support", href: "/help-and-support" },
            { name: "FAQ", href: "/frequently-asked-questions" },
            { name: "Terms & Conditions", href: "/terms-and-conditions" },
            { name: "Privacy Policy", href: "/privacy-policy" },
        ],
    },
});

// State for mobile menu toggle
const isMobileMenuOpen = ref(false);

// Get the current year dynamically
const currentYear = new Date().getFullYear();

// Get current page and auth status
const { url, props } = usePage();
const { app } = usePage().props;
const isAuthenticated = props.auth?.user !== null;

// Check if the link href matches the current URL
const isActivePage = (linkHref) => url === linkHref;

// State for scroll visibility
const isScrolled = ref(false);

// Scroll to top function
const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
};

// Handle scroll event
const handleScroll = () => {
    isScrolled.value = window.scrollY > 100; // Show button after scrolling 100px
};

// Logout function
const logout = () => {
    router.post(route('logout'), {}, {
        onSuccess: () => {
            // Force a hard reload after logout to ensure all state is reset
            window.location.href = route('frontend.welcome');
        }
    });
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
    // Initialize scroll state
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <div
        class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen flex flex-col"
    >
        <Head :title="title" />
        <!-- Navbar -->
        <header
            class="fixed top-0 left-0 w-full z-50 bg-white dark:bg-gray-800 shadow"
        >
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <!-- Navbar Logo -->
                    <a href="/" class="flex items-center space-x-3 group">
                        <img
                            :src="menuLogo"
                            :alt="`${title} Menu Logo`"
                            class="h-10 w-auto rounded-md transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                        />
                        <span
                            class="hidden text-lg font-bold text-gray-800 dark:text-gray-200 group-hover:text-green-600"
                        >
                            {{ title }}
                        </span>
                    </a>

                    <div class="flex items-center space-x-8">
                        <!-- Desktop Navigation -->
                        <nav class="hidden md:flex items-center space-x-6">
                            <div
                                v-for="(link, index) in menuLinks"
                                :key="index"
                                class="relative group"
                            >
                                <a
                                    :href="link.href"
                                    class="text-gray-700 dark:text-gray-200 hover:text-green-600 transition relative"
                                    :class="{
                                        'text-green-600 font-semibold':
                                            isActivePage(link.href),
                                    }"
                                >
                                    {{ link.name }}
                                    <span
                                        class="absolute left-0 -bottom-1 w-full h-0.5 bg-green-600 transform transition-all duration-300"
                                        :class="{
                                            'scale-100': isActivePage(
                                                link.href
                                            ),
                                            'scale-0 group-hover:scale-100':
                                                !isActivePage(link.href),
                                        }"
                                    ></span>
                                </a>
                            </div>
                        </nav>

                        <!-- Authentication Buttons -->
                        <div class="hidden md:flex items-center justify-center">
                            <template v-if="isAuthenticated">
                                <div class="flex flex-row">
                                    <a
                                        href="/dashboard"
                                        class="flex items-center justify-center text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:bg-gradient-to-l hover:from-blue-600 hover:to-indigo-600 focus:ring-4 focus:outline-none focus:ring-indigo-200 dark:focus:ring-blue-700 font-medium text-sm px-5 py-2.5 text-center rounded-l-full rounded-r-none"
                                        aria-label="Dashboard"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8v-10h-8v10zm0-18v6h8V3h-8z"
                                            />
                                        </svg>
                                    </a>
                                    <button
                                        @click="logout"
                                        class="flex items-center justify-center text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-l hover:from-red-600 hover:to-pink-600 focus:ring-4 focus:outline-none focus:ring-pink-200 dark:focus:ring-red-700 font-medium text-sm px-5 py-2.5 text-center rounded-r-full rounded-l-none"
                                        aria-label="Sign Out"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <template v-else>
                                <a
                                    :href="route('register')"
                                    class="text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                >
                                    Join the Movement
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="md:hidden text-gray-700 dark:text-gray-300 absolute right-4 top-4"
                >
                    <i
                        class="fas"
                        :class="
                            isMobileMenuOpen
                                ? 'fa-times text-2xl'
                                : 'fa-bars text-2xl'
                        "
                    ></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav
                v-if="isMobileMenuOpen"
                class="bg-gray-100 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-700 md:hidden"
            >
                <ul class="flex flex-col space-y-2 py-4 px-4">
                    <li v-for="(link, index) in menuLinks" :key="index">
                        <a
                            :href="link.href"
                            :class="{
                                'bg-gray-200 dark:bg-gray-700': isActivePage(
                                    link.href
                                ),
                                'hover:bg-gray-200 dark:hover:bg-gray-700':
                                    !isActivePage(link.href),
                            }"
                            class="block py-2 px-4 rounded"
                        >
                            {{ link.name }}
                        </a>
                    </li>
                </ul>
                <div class="py-4 px-4">
                    <template v-if="isAuthenticated">
                        <div class="flex flex-row justify-center w-full">
                            <a
                                href="/dashboard"
                                class="flex items-center justify-center py-2.5 px-5 text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:bg-gradient-to-l hover:from-blue-600 hover:to-indigo-600 focus:ring-4 focus:outline-none focus:ring-indigo-200 dark:focus:ring-blue-700 font-medium text-sm text-center rounded-l-full rounded-r-none"
                                aria-label="Dashboard"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8v-10h-8v10zm0-18v6h8V3h-8z"
                                    />
                                </svg>
                            </a>
                            <form method="POST" action="/logout" class="w-full">
                                <button
                                    type="submit"
                                    class="flex items-center justify-center w-full py-2.5 px-5 text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-l hover:from-red-600 hover:to-pink-600 focus:ring-4 focus:outline-none focus:ring-pink-200 dark:focus:ring-red-700 font-medium text-sm text-center rounded-r-full rounded-l-none"
                                    aria-label="Sign Out"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"
                                        />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </template>
                    <template v-else>
                        <a
                            :href="route('register')"
                            class="block text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                        >
                            Join the Movement
                        </a>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Back to top button -->
        <button
            @click="scrollToTop"
            class="fixed bottom-6 right-6 bg-green-600 hover:bg-green-700 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110 z-50"
            :class="{
                'opacity-0 invisible': !isScrolled,
                'opacity-100 visible': isScrolled,
            }"
            aria-label="Back to top"
        >
            <i class="fas fa-arrow-up"></i>
        </button>

        <!-- Footer -->
        <footer
            class="bg-gray-200 dark:bg-gray-800 w-full relative overflow-hidden"
        >
            <!-- Decorative elements -->
            <div class="absolute inset-0 opacity-20">
                <div
                    class="absolute top-0 left-0 w-64 h-64 bg-green-400 rounded-full filter blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2"
                ></div>
                <div
                    class="absolute bottom-0 right-0 w-64 h-64 bg-blue-400 rounded-full filter blur-3xl opacity-20 translate-x-1/2 translate-y-1/2"
                ></div>
            </div>

            <div class="container mx-auto px-4 py-8 relative z-10">
                <!-- Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Logo and Description -->
                    <div class="col-span-1">
                        <a href="/">
                            <img
                                :src="footerLogo"
                                :alt="`${title} Logo`"
                                class="h-12 w-auto mb-4"
                            />
                        </a>
                        <p
                            class="text-gray-600 dark:text-gray-300 mb-4 text-sm leading-relaxed"
                        >
                            Empowering Kenyans through Forward Kenya Party's
                            digital platform for political engagement, community
                            action, and inclusive participation.
                        </p>
                        <div
                            class="flex items-center space-x-2 bg-white dark:bg-gray-700 px-3 py-1.5 rounded-full w-fit"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-green-500 animate-pulse"
                            ></span>
                            <span
                                class="text-xs font-medium text-gray-700 dark:text-gray-200"
                                >24 / 7 Available</span
                            >
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3
                            class="text-base font-semibold text-gray-800 dark:text-white mb-4 relative pb-2"
                        >
                            Quick Links
                            <span
                                class="absolute bottom-0 left-0 w-8 h-0.5 bg-gradient-to-r from-green-500 to-blue-500"
                            ></span>
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-chevron-right text-green-500 text-xs"
                                        ></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <a
                                        href="/help-and-support"
                                        class="text-gray-700 hover:text-green-600 dark:text-gray-200 dark:hover:text-green-400 text-sm"
                                    >
                                        Help & Support
                                    </a>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-question-circle text-green-500 text-xs"
                                        ></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <a
                                        href="/frequently-asked-questions"
                                        class="text-gray-700 hover:text-green-600 dark:text-gray-200 dark:hover:text-green-400 text-sm"
                                    >
                                        FAQ
                                    </a>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-file-contract text-green-500 text-xs"
                                        ></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <a
                                        href="/terms-and-conditions"
                                        class="text-gray-700 hover:text-green-600 dark:text-gray-200 dark:hover:text-green-400 text-sm"
                                    >
                                        Terms & Conditions
                                    </a>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-shield-alt text-green-500 text-xs"
                                        ></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <a
                                        href="/privacy-policy"
                                        class="text-gray-700 hover:text-green-600 dark:text-gray-200 dark:hover:text-green-400 text-sm"
                                    >
                                        Privacy Policy
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <h3
                            class="text-base font-semibold text-gray-800 dark:text-white mb-4 relative pb-2"
                        >
                            Contact Us
                            <span
                                class="absolute bottom-0 left-0 w-8 h-0.5 bg-gradient-to-r from-green-500 to-blue-500"
                            ></span>
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-map-marker-alt text-green-500 text-xs"
                                        ></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Our Location
                                    </h4>
                                    <a
                                        href="/contact-us"
                                        class="text-gray-700 dark:text-gray-200 text-sm hover:text-green-600 dark:hover:text-green-400"
                                    >
                                        Nairobi, Kenya
                                    </a>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-phone-alt text-green-500 text-xs"
                                        ></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Call Us
                                    </h4>
                                    <a
                                        href="tel:+254000000000"
                                        class="text-gray-700 dark:text-gray-200 text-sm hover:text-green-600 dark:hover:text-green-400"
                                    >
                                        +254 000 000 000
                                    </a>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-envelope text-green-500 text-xs"
                                        ></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Email Us
                                    </h4>
                                    <a
                                        href="mailto:forwardkenyaparty@gmail.com"
                                        class="text-gray-700 dark:text-gray-200 text-sm hover:text-green-600 dark:hover:text-green-400"
                                    >
                                        forwardkenyaparty@gmail.com
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Social Media -->
                    <div>
                        <h3
                            class="text-base font-semibold text-gray-800 dark:text-white mb-4 relative pb-2 inline-block"
                        >
                            Follow Us
                            <span
                                class="absolute bottom-0 left-0 w-8 h-0.5 bg-gradient-to-r from-green-500 to-blue-500"
                            ></span>
                        </h3>

                        <div class="mb-6">
                            <p
                                class="text-gray-600 dark:text-gray-300 mb-4 text-sm"
                            >
                                Connect with us on social media for the latest
                                updates and news.
                            </p>
                            <div class="flex flex-wrap gap-2 mb-6">
                                <a
                                    v-for="(social, index) in [
                                        {
                                            icon: 'fab fa-facebook-f',
                                            url: 'https://facebook.com',
                                            color: 'bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white',
                                        },
                                        {
                                            icon: 'fab fa-twitter',
                                            url: 'https://twitter.com',
                                            color: 'bg-sky-100 text-sky-500 hover:bg-sky-500 hover:text-white',
                                        },
                                        {
                                            icon: 'fab fa-linkedin-in',
                                            url: 'https://linkedin.com',
                                            color: 'bg-blue-100 text-blue-700 hover:bg-blue-700 hover:text-white',
                                        },
                                        {
                                            icon: 'fab fa-youtube',
                                            url: 'https://youtube.com',
                                            color: 'bg-red-100 text-red-600 hover:bg-red-600 hover:text-white',
                                        },
                                        {
                                            icon: 'fab fa-whatsapp',
                                            url: 'https://api.whatsapp.com/send?phone=+254700000000',
                                            color: 'bg-green-100 text-green-600 hover:bg-green-600 hover:text-white',
                                        },
                                        {
                                            icon: 'fab fa-telegram-plane',
                                            url: 'https://t.me/EgovKe',
                                            color: 'bg-blue-100 text-blue-500 hover:bg-blue-500 hover:text-white',
                                        },
                                    ]"
                                    :key="index"
                                    :href="social.url"
                                    target="_blank"
                                    class="w-8 h-8 rounded-full flex items-center justify-center transform transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
                                    :class="social.color"
                                >
                                    <i :class="social.icon + ' text-sm'"></i>
                                </a>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm"
                        >
                            <h4
                                class="text-gray-800 dark:text-white font-medium mb-2 text-sm"
                            >
                                Subscribe to our newsletter
                            </h4>
                            <p
                                class="text-gray-600 dark:text-gray-300 text-xs mb-3"
                            >
                                Get the latest news and updates.
                            </p>
                            <div class="flex">
                                <input
                                    type="email"
                                    placeholder="Your email"
                                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white placeholder-gray-400 rounded-l-md text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent w-full transition-all duration-200 focus:outline-none"
                                />
                                <button
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 rounded-r-md transition-all duration-200 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
                                >
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                            <p
                                class="text-xs text-gray-400 dark:text-gray-300 mt-2"
                            >
                                We respect your privacy.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Copyright and Links -->
                <div
                    class="border-t border-gray-300 dark:border-gray-700 pt-6 mt-8"
                >
                    <div
                        class="flex flex-col md:flex-row justify-between items-center"
                    >
                        <p
                            class="text-sm text-gray-400 mb-4 md:mb-0 text-center md:text-left"
                        >
                            © {{ currentYear }}
                            <a
                                href="/"
                                class="text-gray-400 hover:text-green-500 dark:hover:text-green-400 transition-colors relative inline-block after:content-[''] after:absolute after:w-0 after:h-px after:bg-green-400 after:left-0 after:-bottom-0.5 after:transition-all hover:after:w-full"
                                >{{ app?.name }}</a
                            >. All rights reserved. <s>Developed and Maintained by
                            <a
                                href="https://www.waguramaurice.com"
                                target="_blank"
                                class="text-gray-400 hover:text-green-500 dark:hover:text-green-400 transition-colors relative inline-block after:content-[''] after:absolute after:w-0 after:h-px after:bg-green-400 after:left-0 after:-bottom-0.5 after:transition-all hover:after:w-full"
                                >Wagura Maurice</a
                            ></s>
                        </p>    
                        <div class="flex flex-wrap justify-center gap-4">
                            <a
                                v-for="(link, index) in otherLinks"
                                :key="`footer-link-${index}`"
                                class="text-gray-400 hover:text-green-500 dark:hover:text-green-400 transition-colors text-sm relative after:content-[''] after:absolute after:w-0 after:h-px after:bg-green-400 after:left-0 after:-bottom-1 after:transition-all hover:after:w-full"
                                :href="link.href"
                            >
                                {{ link.name }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
