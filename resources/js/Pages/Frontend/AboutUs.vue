<!-- resources/js/Pages/Frontend/AboutUs.vue -->
<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, onUnmounted } from 'vue';
import DonationModal from '@/Components/DonationModal.vue';
import TeamSection from '@/Components/TeamSection.vue';

// Show/hide donation modal
const showDonationModal = ref(false);

// Toggle donation modal
const toggleDonationModal = () => {
    showDonationModal.value = !showDonationModal.value;
};

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

// Counter animation function
const animateCounter = (target, start, end, duration) => {
    const range = end - start;
    const minFrameTime = 50; // 50ms per frame
    const totalFrames = Math.ceil(duration / minFrameTime);
    let frame = 0;
    
    const counter = setInterval(() => {
        frame++;
        const progress = frame / totalFrames;
        const current = Math.floor(start + (range * progress));
        
        if (frame >= totalFrames) {
            target.value = end.toLocaleString();
            clearInterval(counter);
        } else {
            target.value = current.toLocaleString();
        }
    }, minFrameTime);
};

const membersCount = ref(0);
const countiesCount = ref(0);
const pillarsCount = ref(0);
const leadersCount = ref(0);

onMounted(() => {
    // Start counter animations when the component is mounted
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(membersCount, 0, 1000000, 2000);
                animateCounter(countiesCount, 0, 47, 1500);
                animateCounter(pillarsCount, 0, 5, 1000);
                animateCounter(leadersCount, 0, 100, 1800);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.querySelector('#stats-section');
    if (statsSection) {
        observer.observe(statsSection);
    }
});

// Define props passed from the backend
defineProps({
    title: {
        type: String,
        required: true,
    },
    logoUrl: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head :title="title" />
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
            <section class="container mx-auto px-4 py-8 pt-36 flex-1">
            <h2
                class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"
            >
                {{ title }}
            </h2>
            <div
                class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mb-4"
            ></div>
            <p
                class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl"
            >
                The Forward Kenya Party is a national political movement established to transform Kenya's governance through progressive policies and ethical leadership. As a beacon of hope and change, we unite Kenyans across all communities with a shared commitment to national development, social equity, and economic transformation. Our foundation is built on the principles of social democracy, ensuring that every policy decision prioritizes the welfare of ordinary citizens while fostering sustainable growth and national unity.
            </p>

            <!-- Ideology Section -->
            <div class="bg-gradient-to-r from-emerald-500 to-yellow-500 rounded-2xl p-8 mb-10 transform transition-all duration-500 hover:scale-[1.005] hover:shadow-2xl group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/90 to-yellow-500/90 group-hover:opacity-0 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row gap-12 items-center">
                        <div class="lg:w-1/2 text-white">
                            <h2 class="text-2xl md:text-3xl font-bold mb-4">Our Ideology</h2>
                            <p class="text-white/90 mb-8 text-lg leading-relaxed">
                                Forward Kenya Party is grounded in civic egalitarianism, emphasizing equal rights, opportunities, and civic participation for all citizens regardless of background. We promote Pan-Africanism for continental solidarity, regional integration in East Africa for economic and political unity, and global engagement to advance Kenya's interests. Our ideology bridges center-left values of democratic participation, social equality, civic empowerment, and decentralization, ensuring inclusivity, transparency, and sustainable progress.
                            </p>
                            <ul class="list-disc pl-6 space-y-3 mb-8 text-white/90">
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

            <!-- Vision & Mission -->
            <div class="grid md:grid-cols-2 gap-8 mb-16">
                <!-- Vision -->
                <div class="bg-gradient-to-br from-blue-600 to-green-600 rounded-2xl p-8 text-white transform transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/90 to-green-600/90 group-hover:opacity-0 transition-opacity duration-500"></div>
                    <div class="relative z-10">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6 group-hover:bg-white/30 transition-all duration-500">
                            <i class="fas fa-eye text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold mb-4">Our Vision</h3>
                        <p class="text-lg leading-relaxed">
                            A Kenya where prosperity is shared, opportunities are accessible to all, and sustainable development is achieved through innovative and inclusive governance.
                        </p>
                    </div>
                </div>
                
                <!-- Mission -->
                <div class="bg-gradient-to-br from-green-600 to-blue-600 rounded-2xl p-8 text-white transform transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/90 to-blue-600/90 group-hover:opacity-0 transition-opacity duration-500"></div>
                    <div class="relative z-10">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6 group-hover:bg-white/30 transition-all duration-500">
                            <i class="fas fa-bullseye text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold mb-4">Our Mission</h3>
                        <p class="text-lg leading-relaxed">
                            To implement practical solutions that address Kenya's most pressing challenges through evidence-based policies, ethical leadership, and active citizen participation at all levels of governance.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Core Values -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16">
                <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Our Core Values</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                        <div class="flex items-start mb-4">
                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 mr-4">
                                <i class="fas fa-handshake text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <h4 class="font-bold text-lg text-gray-900 dark:text-white mt-1">Integrity</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 ml-16 -mt-2">
                            We uphold the highest standards of honesty, transparency, and accountability in all our dealings.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                        <div class="flex items-start mb-4">
                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 mr-4">
                                <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <h4 class="font-bold text-lg text-gray-900 dark:text-white mt-1">Inclusivity</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 ml-16 -mt-2">
                            We celebrate diversity and ensure equal participation and representation for all Kenyans.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                        <div class="flex items-start mb-4">
                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 mr-4">
                                <i class="fas fa-lightbulb text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <h4 class="font-bold text-lg text-gray-900 dark:text-white mt-1">Innovation</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 ml-16 -mt-2">
                            We embrace creative solutions and modern approaches to address Kenya's challenges.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                        <div class="flex items-start mb-4">
                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 mr-4">
                                <i class="fas fa-hands-helping text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <h4 class="font-bold text-lg text-gray-900 dark:text-white mt-1">Service</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 ml-16 -mt-2">
                            We are committed to selfless service and putting the needs of Kenyans first in all our actions.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Who We Are -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16">
                <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Who We Are</h3>
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">
Established in 2020, the Forward Kenya Party emerged from a collective desire for a new political direction. We are a diverse coalition of professionals, grassroots leaders, and ordinary citizens united by our commitment to ethical leadership and national transformation.
                        </p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-l-4 border-blue-500">
                            <p class="text-blue-700 dark:text-blue-300 italic mb-3">
                                "The Forward Kenya Party is a dynamic movement rooted in Unity in Diversity, dedicated to building a prosperous, inclusive Kenya where every voice is heard and every individual is empowered."
                            </p>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-800 flex-shrink-0 overflow-hidden mr-3">
                                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=6366f1&color=fff&rounded=true&size=40" alt="Party Chairman" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <!-- <p class="text-blue-600 dark:text-blue-200 text-sm font-medium">John Doe</p> -->
                                    <p class="text-blue-500 dark:text-blue-300 text-xs">Party Chairman, 2020</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="stats-section" class="grid grid-cols-2 gap-4">
                        <div class="bg-green-100 dark:bg-green-900/20 p-6 rounded-lg">
                            <h4 class="font-bold text-4xl text-green-600 dark:text-green-400 mb-2">{{ membersCount.toLocaleString() }}+</h4>
                            <p class="text-gray-600 dark:text-gray-300">Members Nationwide</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h4 class="font-bold text-4xl text-blue-600 dark:text-blue-400 mb-2">{{ countiesCount }}</h4>
                            <p class="text-gray-600 dark:text-gray-300">Counties Represented</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900/20 p-6 rounded-lg">
                            <h4 class="font-bold text-4xl text-yellow-600 dark:text-yellow-400 mb-2">{{ pillarsCount }}</h4>
                            <p class="text-gray-600 dark:text-gray-300">Core Pillars</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/20 p-6 rounded-lg">
                            <h4 class="font-bold text-4xl text-purple-600 dark:text-purple-400 mb-2">{{ leadersCount.toLocaleString() }}+</h4>
                            <p class="text-gray-600 dark:text-gray-300">Elected Leaders</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Party History -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16">
                <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Our History</h3>
                <div class="relative">
                    <!-- Timeline -->
                    <div class="border-l-2 border-green-500 absolute h-full left-1/2 transform -translate-x-1/2"></div>
                    
                    <!-- Timeline Items -->
                    <div class="relative">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-500 rounded-full w-6 h-6 absolute left-1/2 transform -translate-x-1/2"></div>
                        </div>
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div class="md:w-5/12 text-right pr-8 mb-4 md:mb-0">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">05</h3>
                                <p class="text-gray-600 dark:text-gray-300">National Expansion</p>
                            </div>
                            <div class="md:w-5/12 pl-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Expanded our presence nationwide, establishing offices in all 47 counties 
                                        and growing our membership base across diverse communities.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-500 rounded-full w-6 h-6 absolute left-1/2 transform -translate-x-1/2"></div>
                        </div>
                        <div class="flex flex-col md:flex-row-reverse justify-between items-center">
                            <div class="md:w-5/12 text-left pl-8 mb-4 md:mb-0">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">04</h3>
                                <p class="text-gray-600 dark:text-gray-300">Grassroots Mobilization</p>
                            </div>
                            <div class="md:w-5/12 pr-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Launched nationwide grassroots mobilization efforts, engaging with communities 
                                        to understand their needs and build a people-centered development agenda.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-500 rounded-full w-6 h-6 absolute left-1/2 transform -translate-x-1/2"></div>
                        </div>
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div class="md:w-5/12 text-right pr-8 mb-4 md:mb-0">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">03</h3>
                                <p class="text-gray-600 dark:text-gray-300">First Elections</p>
                            </div>
                            <div class="md:w-5/12 pl-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Participated in our first general elections, fielding candidates 
                                        across various elective positions nationwide.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-500 rounded-full w-6 h-6 absolute left-1/2 transform -translate-x-1/2"></div>
                        </div>
                        <div class="flex flex-col md:flex-row-reverse justify-between items-center">
                            <div class="md:w-5/12 text-left pl-8 mb-4 md:mb-0">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">02</h3>
                                <p class="text-gray-600 dark:text-gray-300">Official Registration</p>
                            </div>
                            <div class="md:w-5/12 pr-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        The party was officially registered with the Office of the Registrar 
                                        of Political Parties, marking the beginning of our formal political journey.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-500 rounded-full w-6 h-6 absolute left-1/2 transform -translate-x-1/2"></div>
                        </div>
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div class="md:w-5/12 text-right pr-8 mb-4 md:mb-0">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">01</h3>
                                <p class="text-gray-600 dark:text-gray-300">Party Conception</p>
                            </div>
                            <div class="md:w-5/12 pl-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Forward Kenya Party was conceived by a group of visionary leaders 
                                        committed to transformative leadership and national development.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-xl p-12 text-center text-white mb-16">
                <h2 class="text-3xl font-bold mb-6">Join Our Movement</h2>
                <p class="text-xl mb-8 max-w-3xl mx-auto">
                    Join Forward Kenya Party today to shape a united, prosperous, and inclusive Kenya. Engage with our platform, contribute to our vision, and be part of the change.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a :href="route('register')" class="bg-white text-green-600 hover:bg-gray-100 font-semibold px-8 py-3 rounded-full transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Membership</span>
                    </a>
                    <button @click="toggleDonationModal" class="bg-transparent border-2 border-white hover:bg-white/10 font-semibold px-8 py-3 rounded-full transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Donation</span>
                    </button>
                </div>
            </div>

            <!-- Team Section -->
            <TeamSection />
            
            <!-- Donation Modal -->
            <DonationModal 
                :show="showDonationModal" 
                @close="toggleDonationModal" 
            />
        </section>
    </GuestLayout>
</template>
