<!-- resources/js/Components/AuthenticationCard.vue -->
<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 justify-center px-6 py-8 mx-auto md:h-screen lg:py-0"
    >
        <div
            class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white"
        >
            <slot name="logo" />
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg rounded-lg dark:border md:mt-0 xl:p-0 dark:bg-gray-800 dark:border-gray-700"
        >
            <slot />
        </div>
    </div>
</template>
