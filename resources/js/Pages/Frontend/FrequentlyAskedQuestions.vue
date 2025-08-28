<!-- resources/js/Pages/Frontend/FrequentlyAskedQuestions.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import GuestLayout from "@/Layouts/GuestLayout.vue";

// Define props passed from the backend
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    logoUrl: {
        type: String,
        required: true,
    },
});

// Reactive state for FAQ items
const faqItems = ref([
    { id: 'faq-0', isOpen: false },
    { id: 'faq-1', isOpen: false },
    { id: 'faq-2', isOpen: false },
    { id: 'faq-3', isOpen: false },
    { id: 'faq-4', isOpen: false },
    { id: 'faq-5', isOpen: false },
    { id: 'faq-6', isOpen: false },
    { id: 'faq-7', isOpen: false },
    { id: 'faq-8', isOpen: false },
]);

// Toggle FAQ item
const toggleFaq = (index) => {
    if (faqItems.value[index]) {
        faqItems.value[index].isOpen = !faqItems.value[index].isOpen;
    }
};
</script>

<template>
    <!-- Pass dynamic props to GuestLayout -->
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
        <section class="container mx-auto px-4 py-8 pt-36 flex-1">
            <h2
                class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"
            >
                {{ title }}
            </h2>
            <p
                class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl"
            >
                Discover how Forward Kenya Party is working towards a more united and prosperous Kenya. Here, we address your most pressing questions about our policies, initiatives, and how you can join us in building a better future for all Kenyans, guided by our core values of unity, justice, and sustainable development.
            </p>

            <!-- Showcase Section -->
            <div class="bg-white dark:bg-gray-900 mt-8">
                <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                    <div class="space-y-8">
                        <!-- Search and Filter -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
                                Find Answers
                            </h3>
                            <div class="relative max-w-2xl mx-auto">
                                <input 
                                    type="text" 
                                    placeholder="Search questions..." 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                >
                                <div class="absolute right-3 top-3 text-gray-400">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                            <div class="mt-6 flex flex-wrap justify-center gap-3">
                                <button class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800/50 transition-colors">
                                    <i class="fas fa-users mr-2"></i> Membership
                                </button>
                                <button class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors">
                                    <i class="fas fa-vote-yea mr-2"></i> Elections
                                </button>
                                <button class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200 hover:bg-purple-200 dark:hover:bg-purple-800/50 transition-colors">
                                    <i class="fas fa-handshake mr-2"></i> Activities
                                </button>
                                <button class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-800/50 transition-colors">
                                    <i class="fas fa-info-circle mr-2"></i> General
                                </button>
                            </div>
                        </div>

                        <!-- Membership Questions -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                                Membership Questions
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- FAQ Item 1 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(0)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[0]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-0"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-user-plus text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">How do I become a member of Forward Kenya Party?</h4>
                                                <div 
                                                    id="faq-answer-0"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[0]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>To become a member, you need to fill out a membership application form available at our county offices or through authorized party officials. You must be at least 18 years old, a Kenyan citizen, and agree to abide by the party's constitution and values. After submitting your application and paying the membership fee, your application will be reviewed by the National Executive Committee.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[0]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>

                                <!-- FAQ Item 2 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(1)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[1]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-1"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-money-bill-wave text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">What are the membership fees?</h4>
                                                <div 
                                                    id="faq-answer-1"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[1]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>We have different membership categories with varying fees:</p>
                                                        <ul class="list-disc pl-5 mt-2 space-y-1">
                                                            <li>Ordinary Membership: KES 10 registration, KES 5 annual subscription</li>
                                                            <li>Life Membership: KES 10,000 registration, KES 5,000 annual subscription</li>
                                                            <li>Corporate Membership: KES 60,000 registration, KES 10,000 annual subscription</li>
                                                            <li>Group Membership: KES 30,000 registration, KES 5,000 annual subscription</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[1]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>

                                <!-- FAQ Item 3 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(2)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[2]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-2"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-user-shield text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Can I be a member of multiple political parties?</h4>
                                                <div 
                                                    id="faq-answer-2"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[2]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>No, according to our constitution (Article 4.5.1.3), membership in Forward Kenya Party is incompatible with membership in any other political party. If you join another party, your membership with Forward Kenya Party will be automatically terminated.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[2]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Election Questions -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                                Election & Nomination Questions
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- FAQ Item 4 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(3)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[3]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-3"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-clipboard-check text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">How does the party nomination process work?</h4>
                                                <div 
                                                    id="faq-answer-3"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[3]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>The nomination process is managed by the National Elections Board (Article 21). The process includes:</p>
                                                        <ol class="list-decimal pl-5 mt-2 space-y-1">
                                                            <li>Submission of application by aspiring candidates</li>
                                                            <li>Vetting by the National Elections Board</li>
                                                            <li>Party primaries conducted through universal suffrage or electoral college</li>
                                                            <li>Issuance of nomination certificate to successful candidates</li>
                                                        </ol>
                                                        <p class="mt-2">Specific rules and timelines are published before each election cycle.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[3]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>

                                <!-- FAQ Item 5 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(4)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[4]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-4"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-vote-yea text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Who is eligible to vote in party primaries?</h4>
                                                <div 
                                                    id="faq-answer-4"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[4]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>All registered, fully paid-up members of the party are eligible to vote in primaries for positions within their electoral areas. You must have been a member for at least 3 months before the primaries to be eligible to vote, unless otherwise specified by the National Elections Board.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[4]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>

                                <!-- FAQ Item 6 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(5)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[5]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-5"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-yellow-100 dark:bg-yellow-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-gavel text-yellow-600 dark:text-yellow-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">How can I appeal a nomination decision?</h4>
                                                <div 
                                                    id="faq-answer-5"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[5]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>Appeals against nomination decisions should be filed with the Electoral and Nomination Dispute Resolution Committee (Article 32) within 48 hours of the announcement of results. The committee will hear and determine the dispute within 7 days, and its decision is final.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[5]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Party Activities Questions -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                                Party Activities & Structure
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- FAQ Item 7 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(6)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[6]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-6"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-handshake text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">How are party leaders elected?</h4>
                                                <div 
                                                    id="faq-answer-6"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[6]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>Party leaders are elected at the National Delegates Congress (NDC) which is the supreme organ of the party (Article 6). The process involves:</p>
                                                        <ol class="list-decimal pl-5 mt-2 space-y-1">
                                                            <li>Nomination of candidates by delegates</li>
                                                            <li>Campaign period as regulated by the National Elections Board</li>
                                                            <li>Voting by secret ballot during the NDC</li>
                                                            <li>Announcement of results by the National Elections Board</li>
                                                        </ol>
                                                        <p class="mt-2">Elected officials serve for a term of five years, renewable once.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[6]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>
                                
                                <!-- FAQ Item 8 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(7)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[7]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-7"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-file-contract text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">How can I participate in party policy formulation?</h4>
                                                <div 
                                                    id="faq-answer-7"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[7]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>Members can participate in policy formulation through various channels:</p>
                                                        <ul class="list-disc pl-5 mt-2 space-y-1">
                                                            <li>Attending ward, constituency, and county meetings where policies are discussed</li>
                                                            <li>Submitting policy proposals through the appropriate party structures</li>
                                                            <li>Participating in the National Policy Committee forums</li>
                                                            <li>Engaging with your elected representatives in party organs</li>
                                                        </ul>
                                                        <p class="mt-2">Article 4.3.1.2 of our constitution guarantees every member the right to participate in discussion, formulation, and implementation of party policies.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[7]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>
                                
                                <!-- FAQ Item 9 -->
                                <div class="faq-item bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200">
                                    <button 
                                        @click="toggleFaq(8)"
                                        class="flex justify-between items-start w-full text-left focus:outline-none"
                                        :aria-expanded="faqItems[8]?.isOpen ? 'true' : 'false'"
                                        aria-controls="faq-answer-8"
                                    >
                                        <div class="flex items-start">
                                            <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg mr-4 mt-0.5">
                                                <i class="fas fa-sitemap text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">What are the different organs of the party?</h4>
                                                <div 
                                                    id="faq-answer-8"
                                                    class="faq-answer overflow-hidden transition-all duration-300 ease-in-out"
                                                    :class="faqItems[8]?.isOpen ? 'max-h-96 opacity-100 mt-2' : 'max-h-0 opacity-0 mt-0'"
                                                >
                                                    <div class="text-gray-600 dark:text-gray-300">
                                                        <p>Forward Kenya Party has a well-structured organization with various organs (Article 5):</p>
                                                        <ul class="list-disc pl-5 mt-2 space-y-1">
                                                            <li>National Delegates Congress (supreme organ)</li>
                                                            <li>National Governing Council</li>
                                                            <li>National Executive Committee</li>
                                                            <li>National Steering Committee</li>
                                                            <li>National Policy Committee</li>
                                                            <li>County, Constituency, and Ward Congresses</li>
                                                            <li>Specialized caucuses and committees</li>
                                                        </ul>
                                                        <p class="mt-2">Each organ has specific functions and responsibilities as outlined in the party constitution.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <i 
                                            class="fas ml-4 mt-1 transition-transform transform"
                                            :class="faqItems[8]?.isOpen ? 'fa-chevron-up text-green-600 dark:text-green-400' : 'fa-chevron-down text-green-600 dark:text-green-400'"
                                        ></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Support -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg text-center">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Still have questions?</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">Can't find the answer you're looking for? Please reach out to our support team.</p>
                            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                                <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-envelope mr-2"></i> Contact Us
                                </a>
                                <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <i class="fas fa-file-alt mr-2"></i> View Constitution
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
