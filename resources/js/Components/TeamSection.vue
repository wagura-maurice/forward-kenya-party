<!-- resources/js/Components/TeamSection.vue -->
<template>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl py-16 px-4">
    <div class="container mx-auto px-4">
        <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Our Leadership</h3>
      
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