<script setup>
defineProps({
    type: {
        type: String,
        default: "submit",
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white uppercase tracking-wider bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 active:from-emerald-700 active:to-green-700 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-md hover:shadow-lg rounded-lg"
    >
        <slot />
    </button>
</template>
