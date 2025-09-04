<!-- resources/js/Pages/Frontend/AboutUs.vue -->
<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, onUnmounted, computed } from 'vue';
import DonationModal from '@/Components/DonationModal.vue';
import TeamSection from '@/Components/TeamSection.vue';

// Testimonial data
const currentTestimonial = ref(0);
const testimonials = ref([
    {
        name: "Sarah Wanjiku",
        role: "Youth Representative, Nairobi Chapter",
        quote: "Being part of Forward Kenya Party has given me a platform to contribute to real change in our community. The leadership's commitment to transparency and inclusivity is truly inspiring.",
        rating: 4.5,
        image: "https://randomuser.me/api/portraits/women/44.jpg"
    },
    {
        name: "James Mwangi",
        role: "Business Leader, Mombasa",
        quote: "The Forward Kenya Party's economic policies are exactly what our country needs. Their focus on sustainable development and job creation aligns with our business community's goals.",
        rating: 5,
        image: "https://randomuser.me/api/portraits/men/32.jpg"
    },
    {
        name: "Amina Omondi",
        role: "Community Organizer, Kisumu",
        quote: "I've never seen a political party so committed to grassroots development. Their programs have directly impacted our community, especially in education and healthcare.",
        rating: 4,
        image: "https://randomuser.me/api/portraits/women/63.jpg"
    }
]);

// Auto-rotate testimonials
const autoRotate = ref(null);

const nextTestimonial = () => {
    if (currentTestimonial.value < testimonials.value.length - 1) {
        currentTestimonial.value++;
    } else {
        currentTestimonial.value = 0;
    }
    resetAutoRotate();
};

const prevTestimonial = () => {
    if (currentTestimonial.value > 0) {
        currentTestimonial.value--;
    } else {
        currentTestimonial.value = testimonials.value.length - 1;
    }
    resetAutoRotate();
};

const resetAutoRotate = () => {
    if (autoRotate.value) {
        clearInterval(autoRotate.value);
    }
    autoRotate.value = setInterval(() => {
        nextTestimonial();
    }, 8000);
};

// Start auto-rotation when component mounts
onMounted(() => {
    resetAutoRotate();
});

// Clean up interval on component unmount
onUnmounted(() => {
    if (autoRotate.value) {
        clearInterval(autoRotate.value);
    }
});

// Start auto-rotation when component is mounted
onMounted(() => {
    resetAutoRotate();
});

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
const specialIntrestGroupsCount = ref(0);
const leadersCount = ref(0);

onMounted(() => {
    // Start counter animations when the component is mounted
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(membersCount, 0, 1000000, 2000);
                animateCounter(countiesCount, 0, 47, 1500);
                animateCounter(specialIntrestGroupsCount, 0, 5, 1000);
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

            <!-- Combined Ideology and Vision/Mission Card -->
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

            <!-- Who We Are -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16">
                <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Who We Are</h3>
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                    <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                </div>
                        <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                            Established in 2021, the Forward Kenya Party emerged from a collective desire for a new political direction. 
                            We are a diverse coalition of professionals, grassroots leaders, and ordinary citizens united by our 
                            commitment to ethical leadership and national transformation.
                        </p>

                        <!-- Collapsible Core Values -->
                        <div class="space-y-4">
                            <h4 class="text-xl font-semibold font-light text-gray-800 dark:text-gray-200 mb-4">Our Core Values</h4>
                            
                            <!-- Integrity -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden transition-all duration-300"
                                :class="{'ring-2 ring-green-500': activeValue === 'integrity'}">
                                <button 
                                    @click="toggleValue('integrity')"
                                    class="w-full px-6 py-4 text-left focus:outline-none"
                                    :aria-expanded="activeValue === 'integrity'"
                                    aria-controls="integrity-content">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0 mr-4">
                                                <i class="fas fa-handshake text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <span class="font-light font-medium text-gray-900 dark:text-white">Integrity</span>
                                        </div>
                                        <i 
                                            class="fas transition-transform duration-300"
                                            :class="activeValue === 'integrity' ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-gray-400'"
                                        ></i>
                                    </div>
                                </button>
                                <div 
                                    id="integrity-content"
                                    class="px-6 pb-4 pt-2 transition-all duration-300 ease-in-out overflow-hidden"
                                    :class="activeValue === 'integrity' ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0'"
                                    style="transition-property: max-height, opacity;">
                                    <p class="text-gray-600 dark:text-gray-300 font-light pl-14">
                                        We uphold the highest standards of honesty, transparency, and accountability in all our dealings.
                                    </p>
                                </div>
                            </div>

                            <!-- Inclusivity -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden transition-all duration-300"
                                :class="{'ring-2 ring-green-500': activeValue === 'inclusivity'}">
                                <button 
                                    @click="toggleValue('inclusivity')"
                                    class="w-full px-6 py-4 text-left focus:outline-none"
                                    :aria-expanded="activeValue === 'inclusivity'"
                                    aria-controls="inclusivity-content">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0 mr-4">
                                                <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <span class="font-light font-medium text-gray-900 dark:text-white">Inclusivity</span>
                                        </div>
                                        <i 
                                            class="fas transition-transform duration-300"
                                            :class="activeValue === 'inclusivity' ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-gray-400'"
                                        ></i>
                                    </div>
                                </button>
                                <div 
                                    id="inclusivity-content"
                                    class="px-6 pb-4 pt-2 transition-all duration-300 ease-in-out overflow-hidden"
                                    :class="activeValue === 'inclusivity' ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0'"
                                    style="transition-property: max-height, opacity;">
                                    <p class="text-gray-600 dark:text-gray-300 font-light pl-14">
                                        We celebrate diversity and ensure equal participation and representation for all Kenyans.
                                    </p>
                                </div>
                            </div>

                            <!-- Innovation -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden transition-all duration-300"
                                :class="{'ring-2 ring-green-500': activeValue === 'innovation'}">
                                <button 
                                    @click="toggleValue('innovation')"
                                    class="w-full px-6 py-4 text-left focus:outline-none"
                                    :aria-expanded="activeValue === 'innovation'"
                                    aria-controls="innovation-content">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0 mr-4">
                                                <i class="fas fa-lightbulb text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <span class="font-light font-medium text-gray-900 dark:text-white">Innovation</span>
                                        </div>
                                        <i 
                                            class="fas transition-transform duration-300"
                                            :class="activeValue === 'innovation' ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-gray-400'"
                                        ></i>
                                    </div>
                                </button>
                                <div 
                                    id="innovation-content"
                                    class="px-6 pb-4 pt-2 transition-all duration-300 ease-in-out overflow-hidden"
                                    :class="activeValue === 'innovation' ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0'"
                                    style="transition-property: max-height, opacity;">
                                    <p class="text-gray-600 dark:text-gray-300 font-light pl-14">
                                        We embrace creative solutions and modern approaches to address Kenya's challenges.
                                    </p>
                                </div>
                            </div>

                            <!-- Service -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden transition-all duration-300"
                                :class="{'ring-2 ring-green-500': activeValue === 'service'}">
                                <button 
                                    @click="toggleValue('service')"
                                    class="w-full px-6 py-4 text-left focus:outline-none"
                                    :aria-expanded="activeValue === 'service'"
                                    aria-controls="service-content">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0 mr-4">
                                                <i class="fas fa-hands-helping text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <span class="font-light font-medium text-gray-900 dark:text-white">Service</span>
                                        </div>
                                        <i 
                                            class="fas transition-transform duration-300"
                                            :class="activeValue === 'service' ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-gray-400'"
                                        ></i>
                                    </div>
                                </button>
                                <div 
                                    id="service-content"
                                    class="px-6 pb-4 pt-2 transition-all duration-300 ease-in-out overflow-hidden"
                                    :class="activeValue === 'service' ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0'"
                                    style="transition-property: max-height, opacity;">
                                    <p class="text-gray-600 dark:text-gray-300 font-light pl-14">
                                        We are committed to selfless service and putting the needs of Kenyans first in all our actions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="stats-section" class="grid grid-cols-2 gap-4"> 
                        <div class="bg-green-100 dark:bg-green-900/20 p-6 rounded-lg">
                            <h4 class="font-light text-4xl text-green-600 dark:text-green-400 mb-2 font-semibold">{{ membersCount.toLocaleString('en-US') }}+</h4>
                            <p class="text-gray-600 dark:text-gray-300 font-light font-medium">Members Nationwide</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h4 class="font-light text-4xl text-blue-600 dark:text-blue-400 mb-2 font-semibold">{{ countiesCount.toLocaleString('en-US') }}</h4>
                            <p class="text-gray-600 dark:text-gray-300 font-light font-medium">Counties Represented</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900/20 p-6 rounded-lg">
                            <h4 class="font-light text-4xl text-yellow-600 dark:text-yellow-400 mb-2 font-semibold">{{ specialIntrestGroupsCount.toLocaleString('en-US') }}</h4>
                            <p class="text-gray-600 dark:text-gray-300 font-light font-medium">Special Interest Groups</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/20 p-6 rounded-lg">
                            <h4 class="font-light text-4xl text-purple-600 dark:text-purple-400 mb-2 font-semibold">{{ leadersCount.toLocaleString('en-US') }}+</h4>
                            <p class="text-gray-600 dark:text-gray-300 font-light font-medium">Elected Leaders</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <TeamSection />

            <!-- Party History -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16">
                <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Our History</h3>
                <div class="flex items-center mb-4">
                    <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                </div>
                <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                    <span class="font-bold">Forward Kenya, founded in 2021,</span><br>
                    is dedicated to shaping the future of Kenya through our shared values of unity, equity, and sustainable development.
                    Our leadership team, committed to putting our vision into action, is creating a brighter future for all Kenyans.
                    Engage with our team by learning about their backgrounds and be part of the change.
                </p>
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
                                <h3 class="font-bold text-xl text-gray-900 dark:text-white">05</h3>
                                <p class="text-gray-600 dark:text-gray-300 font-light font-semibold">National Expansion</p>
                            </div>
                            <div class="md:w-5/12 pl-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300 font-light">
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
                                <h3 class="font-bold text-xl text-gray-900 dark:text-white">04</h3>
                                <p class="text-gray-600 dark:text-gray-300 font-light font-semibold">Grassroots Mobilization</p>
                            </div>
                            <div class="md:w-5/12 pr-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300 font-light">
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
                                <h3 class="font-bold text-xl text-gray-900 dark:text-white">03</h3>
                                <p class="text-gray-600 dark:text-gray-300 font-light font-semibold">First Elections</p>
                            </div>
                            <div class="md:w-5/12 pl-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300 font-light">
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
                                <h3 class="font-bold text-xl text-gray-900 dark:text-white">02</h3>
                                <p class="text-gray-600 dark:text-gray-300 font-light font-semibold">Official Registration</p>
                            </div>
                            <div class="md:w-5/12 pr-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300 font-light">
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
                                <h3 class="font-bold text-xl text-gray-900 dark:text-white">01</h3>
                                <p class="text-gray-600 dark:text-gray-300 font-light font-semibold">Party Conception</p>
                            </div>
                            <div class="md:w-5/12 pl-8">
                                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300 font-light">
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
            <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-xl p-8 md:p-12 text-center text-white mb-16">
                <h3 class="text-3xl font-bold text-center mb-12">Join Our Movement</h3>
                <p class="text-xl mb-8 max-w-3xl mx-auto font-light"> Join Forward Kenya Party today to shape a united, prosperous, and inclusive Kenya. Engage with our platform, contribute to our vision, and be part of the change. </p>
                <!-- Testimonials Carousel -->
                <div class="relative w-full max-w-4xl mx-auto mb-10 px-4 sm:px-6 md:px-8">
                    <div class="relative flex items-center">
                        <!-- Left Arrow -->
                        <button 
                            @click="prevTestimonial"
                            class="absolute left-0 sm:-left-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/40 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg z-20 transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600"
                            :disabled="currentTestimonial === 0" 
                            :class="{'opacity-50 cursor-not-allowed': currentTestimonial === 0}"
                            aria-label="Previous testimonial"
                        >
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <!-- Testimonial Items Container -->
                        <div class="w-full h-full px-8">
                            <div 
                                v-for="(testimonial, index) in testimonials" 
                                :key="index"
                                class="w-full transition-all duration-300 ease-in-out z-10"
                                :class="{
                                    'opacity-100 block': currentTestimonial === index,
                                    'opacity-0 hidden': currentTestimonial !== index
                                }"
                            >
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 w-full transform transition-all duration-300 hover:shadow-2xl relative z-10">
                                <div class="flex flex-col md:flex-row items-center text-center md:text-left gap-6 h-full">
                                    <!-- Member Photo -->
                                    <div class="flex-shrink-0 w-24 h-24 md:w-28 md:h-28 rounded-full overflow-hidden border-4 border-white/20 shadow-lg">
                                        <img 
                                            :src="testimonial.image" 
                                            :alt="testimonial.name" 
                                            class="w-full h-full object-cover"
                                        >
                                    </div>
                                    
                                    <!-- Testimonial Content -->
                                    <div class="flex-1">
                                        <div class="relative flex items-center h-full">
                                            <i class="fas fa-quote-left text-blue-100 absolute -top-6 -left-2 opacity-20 w-8 h-8"></i>
                                            <p class="text-lg md:text-xl italic mb-4 px-4 relative z-10">
                                                {{ testimonial.quote }}
                                            </p>
                                            <i class="fas fa-quote-right text-blue-100 absolute -bottom-6 -right-2 opacity-20 w-8 h-8"></i>
                                        </div>
                                        <div class="flex flex-col items-center md:items-start mt-4">
                                            <p class="font-semibold text-lg">{{ testimonial.name }}</p>
                                            <p class="text-blue-100 text-sm">{{ testimonial.role }}</p>
                                            <div class="flex mt-2">
                                                <i v-for="star in 5" :key="star" 
                                                    :class="[
                                                        'fas',
                                                        star <= testimonial.rating ? 'fa-star' : 'fa-star',
                                                        star <= Math.floor(testimonial.rating) ? 'text-yellow-300' : 'text-gray-400',
                                                        star > Math.floor(testimonial.rating) && star - 0.5 <= testimonial.rating ? 'fa-star-half-alt text-yellow-300' : ''
                                                    ]"
                                                ></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right Arrow -->
                        <button 
                            @click="nextTestimonial"
                            class="absolute right-0 sm:-right-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/40 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg z-20 transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600"
                            :disabled="currentTestimonial === testimonials.length - 1"
                            :class="{'opacity-50 cursor-not-allowed': currentTestimonial === testimonials.length - 1}"
                            aria-label="Next testimonial"
                        >
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    </div>

                    <!-- Pagination Dots -->
                    <div class="flex justify-center mt-8 space-x-2 relative z-10">
                        <button 
                            v-for="(testimonial, index) in testimonials" 
                            :key="'dot-'+index"
                            @click="currentTestimonial = index; resetAutoRotate();"
                            class="w-3 h-3 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600"
                            :class="currentTestimonial === index ? 'bg-white w-8 scale-125' : 'bg-white/50 w-3 hover:bg-white/75'"
                            :aria-label="`View testimonial ${index + 1} from ${testimonial.name}`"
                        ></button>
                    </div>

                    <!-- Counter -->
                    <div class="text-center mt-4 text-sm text-blue-100 relative z-0">
                        Showing {{ currentTestimonial + 1 }} of {{ testimonials.length }} testimonials
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-6 mt-12">
                    <a 
                        :href="route('register')" 
                        class="group bg-white text-green-600 hover:bg-gray-50 font-semibold px-8 py-4 rounded-full transition-all duration-300 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl hover:-translate-y-1"
                    >
                        <i class="fas fa-user-plus text-lg"></i>
                        <span class="text-lg">Become a Member</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-300"></i>
                    </a>
                    <button 
                        @click="toggleDonationModal" 
                        class="group bg-transparent border-2 border-white hover:bg-white/10 font-semibold px-8 py-4 rounded-full transition-all duration-300 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl hover:-translate-y-1"
                    >
                        <i class="fas fa-hand-holding-heart text-lg"></i>
                        <span class="text-lg">Support Our Cause</span>
                        <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
                    </button>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">24/7</div>
                        <div class="text-sm text-blue-100">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">50+</div>
                        <div class="text-sm text-blue-100">Counties</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">1M+</div>
                        <div class="text-sm text-blue-100">Members</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">100%</div>
                        <div class="text-sm text-blue-100">Engagement</div>
                    </div>
                </div>
            </div>

            <!-- Donation Modal -->
            <DonationModal 
                :show="showDonationModal" 
                @close="toggleDonationModal" 
            />
        </section>
    </GuestLayout>
</template>

<script>
// Toggle for core values accordion
const activeValue = ref('integrity');

const toggleValue = (value) => {
    if (activeValue.value === value) {
        activeValue.value = null;
    } else {
        activeValue.value = value;
    }
};
</script>
