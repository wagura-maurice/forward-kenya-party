<!-- resources/js/Components/WhoWeAreSection.vue -->
<script setup>
import { onMounted, ref, onUnmounted, computed } from 'vue';

// Chart data refs
const membersCount = ref(0);
const countiesCount = ref(0);
const specialIntrestGroupsCount = ref(0);
const leadersCount = ref(0);

// Active value for accordion
const activeValue = ref('integrity');

// Toggle accordion
const toggleValue = (value) => {
    if (activeValue.value === value) {
        activeValue.value = null;
    } else {
        activeValue.value = value;
    }
};

// Chart options
const chartOptions = {
    chart: { 
        type: 'donut',
        sparkline: { enabled: true },
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800,
            dynamicAnimation: {
                speed: 800
            }
        },
        toolbar: {
            show: false
        }
    },
    stroke: { 
        show: false 
    },
    plotOptions: { 
        pie: { 
            donut: { 
                size: '75%',
                background: 'transparent'
            },
            expandOnClick: false
        } 
    },
    dataLabels: { 
        enabled: false 
    },
    legend: { 
        show: false 
    },
    tooltip: { 
        enabled: false 
    },
    states: {
        hover: {
            filter: { type: 'none' }
        },
        active: {
            filter: { type: 'none' }
        }
    }
};

// Chart options for each stat
const membersChartOptions = ref({
    ...chartOptions,
    colors: ['#10B981', '#D1FAE5'],
    labels: ['Members', 'Remaining']
});

const countiesChartOptions = ref({
    ...chartOptions,
    colors: ['#3B82F6', '#BFDBFE'],
    labels: ['Counties', 'Remaining']
});

const groupsChartOptions = ref({
    ...chartOptions,
    colors: ['#F59E0B', '#FEF3C7'],
    labels: ['Groups', 'Remaining']
});

const leadersChartOptions = ref({
    ...chartOptions,
    colors: ['#8B5CF6', '#EDE9FE'],
    labels: ['Leaders', 'Remaining']
});

// Counter animation function
const animateCounter = (target, start, end, duration) => {
    const range = end - start;
    const minFrameTime = 30; // 30ms per frame for smoother animation
    const totalFrames = Math.ceil(duration / minFrameTime);
    let frame = 0;
    
    const counter = setInterval(() => {
        frame++;
        const progress = frame / totalFrames;
        const current = Math.floor(start + (range * progress));
        target.value = current;
        
        if (frame >= totalFrames) {
            target.value = end;
            clearInterval(counter);
        }
    }, minFrameTime);
};

// Initialize counters when component mounts
onMounted(() => {
    // Start counter animations when the stats section comes into view
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
    }, { threshold: 0.1 });
    
    const statsSection = document.querySelector('#stats-section');
    if (statsSection) {
        observer.observe(statsSection);
    }
});

// Clean up on unmount
onUnmounted(() => {
    const statsSection = document.querySelector('#stats-section');
    if (statsSection && observer) {
        observer.unobserve(statsSection);
    }
});
</script>

<template>
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16 px-4 sm:px-6 lg:px-8">
        <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Who We Are</h3>
        <div class="grid md:grid-cols-2 gap-8 max-w-7xl mx-auto">
            <div class="px-4">
                <div class="flex items-center mb-4">
                    <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                </div>
                <p class="mb-8 lg:mb-12 font-light text-gray-500 dark:text-gray-400 sm:text-lg lg:text-xl leading-relaxed">
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
            <div id="stats-section" class="grid grid-cols-2 gap-6"> 
                <div class="bg-green-100 dark:bg-green-900/20 p-6 rounded-lg relative group">
                    <div class="h-40 mb-4">
                        <apexchart
                            type="donut"
                            :options="membersChartOptions"
                            :series="[membersCount, 1500000 - membersCount]"
                            width="100%"
                            height="100%"
                            ref="membersChart"
                        ></apexchart>
                    </div>
                    <h4 class="font-light text-4xl text-green-600 dark:text-green-400 mb-2 font-semibold text-center">{{ membersCount.toLocaleString('en-US') }}+</h4>
                    <p class="text-gray-600 dark:text-gray-300 font-light font-medium text-center">Members Nationwide</p>
                </div>
                
                <div class="bg-blue-100 dark:bg-blue-900/20 p-6 rounded-lg relative group">
                    <div class="h-40 mb-4">
                        <apexchart
                            type="donut"
                            :options="countiesChartOptions"
                            :series="[countiesCount, 47 - countiesCount]"
                            width="100%"
                            height="100%"
                            ref="countiesChart"
                        ></apexchart>
                    </div>
                    <h4 class="font-light text-4xl text-blue-600 dark:text-blue-400 mb-2 font-semibold text-center">{{ countiesCount.toLocaleString('en-US') }}/47</h4>
                    <p class="text-gray-600 dark:text-gray-300 font-light font-medium text-center">Counties Represented</p>
                </div>
                
                <div class="bg-yellow-100 dark:bg-yellow-900/20 p-6 rounded-lg relative group">
                    <div class="h-40 mb-4">
                        <apexchart
                            type="donut"
                            :options="groupsChartOptions"
                            :series="[specialIntrestGroupsCount, 10 - specialIntrestGroupsCount]"
                            width="100%"
                            height="100%"
                            ref="groupsChart"
                        ></apexchart>
                    </div>
                    <h4 class="font-light text-4xl text-yellow-600 dark:text-yellow-400 mb-2 font-semibold text-center">{{ specialIntrestGroupsCount.toLocaleString('en-US') }}</h4>
                    <p class="text-gray-600 dark:text-gray-300 font-light font-medium text-center">Special Interest Groups</p>
                </div>
                
                <div class="bg-purple-100 dark:bg-purple-900/20 p-6 rounded-lg relative group">
                    <div class="h-40 mb-4">
                        <apexchart
                            type="donut"
                            :options="leadersChartOptions"
                            :series="[leadersCount, 150 - leadersCount]"
                            width="100%"
                            height="100%"
                            ref="leadersChart"
                        ></apexchart>
                    </div>
                    <h4 class="font-light text-4xl text-purple-600 dark:text-purple-400 mb-2 font-semibold text-center">{{ leadersCount.toLocaleString('en-US') }}+</h4>
                    <p class="text-gray-600 dark:text-gray-300 font-light font-medium text-center">Elected Leaders</p>
                </div>
            </div>
        </div>
    </div>
</template>