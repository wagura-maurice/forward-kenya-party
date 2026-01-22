<!-- resources/js/Components/TeamSection.vue -->
<template>
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16">
      <div class="container mx-auto">
        <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Our Leadership</h3>
        <div class="flex items-center mb-4">
                    <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                </div>
        <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
            <span class="font-bold">Our Leadership, founded in 2021,</span><br>
            is dedicated to shaping the future of Kenya through our shared values of unity, equity, and sustainable development.
            Our team is committed to putting our vision into action and creating a brighter future for all Kenyans.
            Learn about their backgrounds, engage with them, and be part of the change.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div 
            v-for="(member, index) in teamMembers" 
            :key="index"
            class="relative group overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300"
          >
            <div class="relative h-80 w-full">
              <img 
                :src="member.image" 
                :alt="member.name"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                <div>
                  <h3 class="text-white text-xl font-semibold">{{ member.name }}</h3>
                  <p class="text-gray-200">{{ member.role }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>

<script setup>
const teamMembers = [
  {
    name: 'Benson',
    role: 'Party Leader and Core Founder',
    image: `https://ui-avatars.com/api/?name=${encodeURIComponent('Benson')}&background=4F46E5&color=fff&size=256`
  },
  {
    name: 'Faith',
    role: 'Party Deputy Leader and Core Founder',
    image: `https://ui-avatars.com/api/?name=${encodeURIComponent('Faith')}&background=7C3AED&color=fff&size=256`
  },
  {
    name: 'Justus',
    role: 'Party Secretariate and Core Founder',
    image: `https://ui-avatars.com/api/?name=${encodeURIComponent('Justus')}&background=2563EB&color=fff&size=256`
  },
  {
    name: 'Bran',
    role: 'Party Secretariate and Core Founder',
    image: `https://ui-avatars.com/api/?name=${encodeURIComponent('Bran')}&background=1E40AF&color=fff&size=256`
  },
  {
    name: 'Unknown',
    role: 'Party Secretariate and Core Founder',
    image: `https://ui-avatars.com/api/?name=${encodeURIComponent('Unknown')}&background=6B7280&color=fff&size=256`
  },
  {
    name: 'Maurice',
    role: 'Party Secretariate and Lead Tech',
    image: `https://ui-avatars.com/api/?name=${encodeURIComponent('Maurice')}&background=1F2937&color=fff&size=256`
  }
];
</script>