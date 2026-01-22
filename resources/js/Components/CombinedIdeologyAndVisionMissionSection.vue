<!-- resources/js/Components/CombinedIdeologyAndVisionMissionSection.vue -->
 <script setup>
 import { onMounted, ref, onUnmounted, computed } from 'vue';
 // Image carousel for ideology section
const currentImageIndex = ref(0);
const images = [
    '/assets/FKP COLLATERALS/FKP PNG/Primary Logo/Asset 6FKP.png',
    '/assets/FKP COLLATERALS/FKP PNG/Primary Logo/Asset 7FKP.png',
    '/assets/FKP COLLATERALS/FKP PNG/Primary Logo/Asset 8FKP.png',
    '/assets/FKP COLLATERALS/FKP PNG/Primary Logo/Asset 9FKP.png',
    '/assets/FKP COLLATERALS/FKP PNG/Primary Logo/Asset 10FKP.png'
];

let carouselInterval;

const nextImage = () => {
    currentImageIndex.value = (currentImageIndex.value + 1) % images.length;
};

const prevImage = () => {
    currentImageIndex.value = (currentImageIndex.value - 1 + images.length) % images.length;
};

onMounted(() => {
    // Auto-rotate images every 3 seconds
    carouselInterval = setInterval(nextImage, 3000);
});

onUnmounted(() => {
    if (carouselInterval) {
        clearInterval(carouselInterval);
    }
});
 </script>

 <template>
    <div class="rounded-2xl overflow-hidden shadow-lg mb-16">
                <!-- Ideology Section -->
                <div class="bg-gradient-to-r from-emerald-500 to-yellow-500 p-8 transform transition-all duration-500 group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/90 to-yellow-500/90 group-hover:opacity-0 transition-opacity duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col lg:flex-row gap-12 items-center">
                            <div class="lg:w-1/2 text-white">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="bg-purple-600 dark:bg-purple-500 p-4 rounded-full w-16 h-16 flex-shrink-0 flex items-center justify-center transition-all duration-300">
                                        <i class="fas fa-lightbulb text-2xl text-white"></i>
                                    </div>
                                    <h3 class="text-2xl md:text-3xl font-semibold font-light text-gray-800 dark:text-gray-200">Our Ideology</h3>
                                </div>
                                <p class="mb-8 lg:mb-16 font-light text-white sm:text-xl leading-relaxed pl-20">
                                    Forward Kenya Party is grounded in civic egalitarianism, emphasizing equal rights, opportunities, and civic participation for all citizens regardless of background. We promote Pan-Africanism for continental solidarity, regional integration in East Africa for economic and political unity, and global engagement to advance Kenya's interests. Our ideology bridges center-left values of democratic participation, social equality, civic empowerment, and decentralization, ensuring inclusivity, transparency, and sustainable progress.
                                </p>
                                <ul class="list-disc pl-24 space-y-3 mb-8 text-white">
                                    <li>Democratic Participation: Equal access to decision-making for all citizens</li>
                                    <li>Social Equality: Equitable access to education, healthcare, and opportunities</li>
                                    <li>Civic Education & Empowerment: Informing citizens for active engagement</li>
                                    <li>Decentralization and Localism: Devolving power for grassroots governance</li>
                                    <li>Pan-Africanism & Integration: Fostering African solidarity and regional unity</li>
                                </ul>
                            </div>
                            <div class="lg:w-1/2 flex items-center justify-center">
                                <div class="w-full max-w-md mx-auto p-2 md:p-4 bg-white/10 backdrop-blur-sm rounded-2xl">
                                    <div class="relative overflow-hidden rounded-xl">
                                        <transition 
                                            v-for="(image, index) in images" 
                                            :key="index"
                                            enter-active-class="transition-opacity duration-1000"
                                            leave-active-class="absolute opacity-0 transition-opacity duration-1000"
                                            mode="out-in"
                                        >
                                            <img 
                                                v-show="currentImageIndex === index"
                                                :src="image" 
                                                :alt="'Forward Kenya Party Ideology ' + (index + 1)" 
                                                class="rounded-lg w-full h-auto max-h-[400px] object-contain"
                                            >
                                        </transition>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vision & Mission Section -->
                <div class="bg-white dark:bg-gray-700 p-8 transform transition-all duration-500 group relative">
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Vision -->
                        <div class="relative">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="bg-blue-600 dark:bg-blue-500 p-4 rounded-full w-16 h-16 flex-shrink-0 flex items-center justify-center transition-all duration-300">
                                    <i class="fas fa-eye text-2xl text-white"></i>
                                </div>
                                <h3 class="text-2xl md:text-3xl font-semibold font-light text-gray-800 dark:text-gray-200">Our Vision</h3>
                            </div>
                            <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl leading-relaxed pl-20">
                                A Kenya where prosperity is shared, opportunities are accessible to all, and sustainable development is achieved through innovative and inclusive governance.
                            </p>
                            <ul class="list-disc pl-24 space-y-3 mb-8 text-gray-500 dark:text-gray-400">
                                <li>Shared prosperity through equitable economic growth</li>
                                <li>Universal access to quality education and healthcare</li>
                                <li>Sustainable development that preserves our environment</li>
                                <li>Inclusive governance that leaves no one behind</li>
                            </ul>
                        </div>
                        
                        <!-- Mission -->
                        <div class="relative">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="bg-green-600 dark:bg-green-500 p-4 rounded-full w-16 h-16 flex-shrink-0 flex items-center justify-center transition-all duration-300">
                                    <i class="fas fa-bullseye text-2xl text-white"></i>
                                </div>
                                <h3 class="text-2xl md:text-3xl font-semibold font-light text-gray-800 dark:text-gray-200">Our Mission</h3>
                            </div>
                            <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl leading-relaxed pl-20">
                                To implement practical solutions that address Kenya's most pressing challenges through evidence-based policies, ethical leadership, and active citizen participation at all levels of governance.
                            </p>
                            <ul class="list-disc pl-24 space-y-3 mb-8 text-gray-500 dark:text-gray-400">
                                <li>Develop and implement evidence-based policy solutions</li>
                                <li>Foster transparent and accountable leadership</li>
                                <li>Empower citizens through active participation</li>
                                <li>Promote national unity and social cohesion</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
 </template>