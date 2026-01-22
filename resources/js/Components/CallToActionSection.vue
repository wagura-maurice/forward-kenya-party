<!-- resources/js/Components/CallToActionSection.vue -->
 <script setup>

import { onMounted, ref, onUnmounted, computed } from 'vue';
import DonationModal from '@/Components/DonationModal.vue';
import ExportMembersModal from '@/Components/ExportMembersModal.vue';

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
 </script>

 <template>
    <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-xl p-8 md:p-12 text-center text-white mb-16">
                <h3 class="text-3xl font-bold text-center mb-12">Join Our Movement</h3>
                <p class="text-xl mb-8 max-w-3xl mx-auto font-medium"> Join Forward Kenya Party today to shape a united, prosperous, and inclusive Kenya. Engage with our platform, contribute to our vision, and be part of the change. </p>
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
                                            <i class="fas fa-quote-left text-blue-100 absolute -top-6 -left-2 opacity-20 w-8 h-8 pl-1"></i>
                                            <p class="font-light text-lg md:text-xl italic mb-4 px-4 relative z-10 pr-10">{{ testimonial.quote }}</p>
                                            <i class="fas fa-quote-right text-blue-100 absolute -bottom-6 -right-2 opacity-20 w-8 h-8 pr-1"></i>
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
                        <div class="text-sm text-blue-100">Regions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">1.2M+</div>
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
 </template>