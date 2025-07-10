<!-- resources/js/Components/AuthenticationCard.vue -->
<template>
    <div
        class="min-h-screen flex flex-col justify-center items-center bg-gray-100 px-4 py-8 md:h-screen"
    >
        <div
            class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white"
        >
            <slot name="logo" />
        </div>
        <div class="flex-1 w-full flex flex-col items-center justify-center">
            <div
                class="relative flex flex-col w-full max-w-2xl px-8 py-6 bg-white shadow-md overflow-y-auto sm:rounded-lg rounded-lg dark:border dark:bg-gray-800 dark:border-gray-700 h-[90vh]"
            >
                <slot />
            </div>
        </div>
    </div>
</template>
