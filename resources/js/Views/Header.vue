<!-- resources/js/Views/Header.vue -->
<template>
    <header
        class="fixed top-0 left-0 w-full bg-white dark:bg-gray-900 shadow z-50"
    >
        <div
            class="container mx-auto flex items-center justify-between py-4 px-6"
        >
            <!-- Logo -->
            <div class="text-lg font-bold text-black dark:text-white">
                <Link :href="route('home')">Forward Kenya Party KE</Link>
            </div>

            <!-- Navigation Links and Buttons -->
            <div class="flex items-center space-x-4 md:space-x-6">
                <!-- Navigation Links -->
                <nav
                    :class="['md:flex items-center', open ? 'block' : 'hidden']"
                    class="fixed top-14 left-0 w-full bg-white dark:bg-gray-900 z-50 md:relative md:top-0 md:w-auto md:bg-transparent"
                >
                    <ul
                        class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-8 p-4 md:p-0"
                    >
                        <li>
                            <a
                                href="#"
                                class="block py-2 px-4 text-gray-900 dark:text-white hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:hover:bg-gray-700"
                            >
                                Home
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="block py-2 px-4 text-gray-900 dark:text-white hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:hover:bg-gray-700"
                            >
                                About
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="block py-2 px-4 text-gray-900 dark:text-white hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:hover:bg-gray-700"
                            >
                                Services
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="block py-2 px-4 text-gray-900 dark:text-white hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:hover:bg-gray-700"
                            >
                                Contact
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Buttons -->
                <div class="hidden md:flex space-x-4">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="text-gray-700 dark:text-gray-200 hover:underline"
                    >
                        Sign In
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                    >
                        Sign Up
                    </Link>
                </div>

                <!-- Mobile Menu Toggle -->
                <button
                    @click="toggleMenu"
                    class="inline-flex items-center p-2 justify-center text-gray-500 rounded-md md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700"
                >
                    <span class="sr-only">Open menu</span>
                    <svg
                        class="w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </header>
</template>

<script>
export default {
    props: {
        canLogin: {
            type: Boolean,
            default: false,
        },
        canRegister: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            open: false, // Controls the mobile menu state
        };
    },
    methods: {
        toggleMenu() {
            this.open = !this.open;
        },
    },
};
</script>

<style scoped>
@media (min-width: 768px) {
    nav {
        display: flex !important;
    }
}
</style>
