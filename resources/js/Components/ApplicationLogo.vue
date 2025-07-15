<template>
    <img
        :src="currentLogo"
        alt="Logo"
        class="h-10 w-auto rounded-md transition-transform duration-300 group-hover:scale-105"
        loading="lazy"
    />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const logos = [
    "/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 1FKP.png",
    "/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 2FKP.png",
    "/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 3FKP.png",
    "/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 4FKP.png",
    "/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png",
];

const idx = ref(0);
const currentLogo = ref(logos[0]);
let interval = null;

onMounted(() => {
    interval = setInterval(() => {
        idx.value = (idx.value + 1) % logos.length;
        currentLogo.value = logos[idx.value];
    }, 800);
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});
</script>
