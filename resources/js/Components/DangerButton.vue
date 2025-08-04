<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white uppercase tracking-wider bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 active:from-red-700 active:to-rose-700 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-md hover:shadow-lg rounded-lg">
        <slot />
    </button>
</template>
