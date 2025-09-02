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

// Form data refs
const form = ref(null);
const name = ref('');
const email = ref('');
const message = ref('');
const agreeToTerms = ref(false);

// Form submission handler
const handleSubmit = (e) => {
    e.preventDefault();
    
    if (!agreeToTerms.value) {
        alert('Please agree to the Terms of Service and Privacy Policy before submitting.');
        return;
    }
    
    if (name.value && email.value && message.value) {
        // Here you would typically send the form data to your backend
        console.log('Form submitted:', {
            name: name.value,
            email: email.value,
            message: message.value
        });
        
        alert('Thank you for your feedback! We will respond within 48 hours.');
        form.value.reset();
        // Clear form data
        name.value = '';
        email.value = '';
        message.value = '';
        agreeToTerms.value = false;
    } else {
        alert('Please fill in all required fields.');
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
                The Forward Kenya Party is committed to serving all Kenyans with dedication and transparency. Our support team is here to assist you with any inquiries, whether you're looking to join our movement, volunteer, or learn more about our policies and initiatives for a better Kenya. Your voice matters to us.
            </p>

            <!-- Showcase Section -->
            <div class="bg-white dark:bg-gray-900 mt-8">
                <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">                    
                    <div class="grid md:grid-cols-2 gap-8 mb-12">
                        <!-- Contact Information Card -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 section-container">
                            <div class="flex items-center mb-4">
                                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full mr-4">
                                    <i class="fas fa-phone-alt text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Contact Information</h3>
                            </div>
                            <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                                <li class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-green-600 mt-1 mr-3"></i>
                                    <span>Utalii Lane, Viewpark Towers, 19th Floor, Suite 19<br>
                                    P.O. Box 27999–00100, GPO Nairobi</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-envelope text-green-600 mt-1 mr-3"></i>
                                    <span>forwardkenyaparty@gmail.com</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-globe text-green-600 mt-1 mr-3"></i>
                                    <span>www.forwardkenyaparty.com</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-phone text-green-600 mt-1 mr-3"></i>
                                    <span>+254 713 447 820</span>
                                </li>
                            </ul>
                            <div class="mt-6">
                                <a href="#" class="inline-flex items-center text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    <i class="fas fa-list mr-2"></i> View County Office Contacts
                                </a>
                            </div>
                        </div>
                        
                        <!-- Support Options Card -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 section-container">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full mr-4">
                                    <i class="fas fa-headset text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Support Options</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="#" class="p-4 bg-white dark:bg-gray-700 rounded-lg text-center hover:shadow-md transition-shadow">
                                    <div class="bg-green-100 dark:bg-green-900/20 p-3 rounded-full inline-flex mb-2">
                                        <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Membership Support</p>
                                </a>
                                <a href="#" class="p-4 bg-white dark:bg-gray-700 rounded-lg text-center hover:shadow-md transition-shadow">
                                    <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full inline-flex mb-2">
                                        <i class="fas fa-vote-yea text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Election Support</p>
                                </a>
                                <a href="#" class="p-4 bg-white dark:bg-gray-700 rounded-lg text-center hover:shadow-md transition-shadow">
                                    <div class="bg-purple-100 dark:bg-purple-900/20 p-3 rounded-full inline-flex mb-2">
                                        <i class="fas fa-exclamation-triangle text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Report Issues</p>
                                </a>
                                <a href="#" class="p-4 bg-white dark:bg-gray-700 rounded-lg text-center hover:shadow-md transition-shadow">
                                    <div class="bg-yellow-100 dark:bg-yellow-900/20 p-3 rounded-full inline-flex mb-2">
                                        <i class="fas fa-file-download text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Resources</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Membership Support -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white border-b pb-2 border-gray-200 dark:border-gray-700 flex items-center">
                            <i class="fas fa-users mr-3 text-green-600"></i>
                            Membership Support
                        </h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-5">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Becoming a Member</h4>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">Learn how to join Forward Kenya Party and participate in our democratic processes.</p>
                                <ul class="list-disc pl-5 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li>Eligibility requirements</li>
                                    <li>Membership application process</li>
                                    <li>Membership fee structure</li>
                                    <li>Membership card information</li>
                                </ul>
                                <a href="#" class="inline-flex items-center mt-4 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    Learn more <i class="fas fa-arrow-right ml-1 text-sm"></i>
                                </a>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-5">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Member Resources</h4>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">Access important documents and resources for party members.</p>
                                <ul class="list-disc pl-5 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li>Party Constitution</li>
                                    <li>Code of Conduct</li>
                                    <li>Election guidelines</li>
                                    <li>Membership forms</li>
                                </ul>
                                <a href="#" class="inline-flex items-center mt-4 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    Download resources <i class="fas fa-download ml-1 text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dispute Resolution -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white border-b pb-2 border-gray-200 dark:border-gray-700 flex items-center">
                            <i class="fas fa-gavel mr-3 text-blue-600"></i>
                            Dispute Resolution
                        </h3>
                        
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
                            <p class="text-gray-600 dark:text-gray-300 mb-4">Forward Kenya Party has established procedures for resolving disputes in accordance with our Constitution (Articles 29, 32, 41).</p>
                            
                            <div class="grid md:grid-cols-3 gap-4 mt-6">
                                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="bg-blue-100 dark:bg-blue-900/20 p-2 rounded-full inline-flex mb-3">
                                        <i class="fas fa-balance-scale text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Disciplinary Issues</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Report violations of the Code of Conduct</p>
                                </div>
                                
                                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="bg-green-100 dark:bg-green-900/20 p-2 rounded-full inline-flex mb-3">
                                        <i class="fas fa-vote-yea text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Election Disputes</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Challenge nomination or election results</p>
                                </div>
                                
                                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="bg-purple-100 dark:bg-purple-900/20 p-2 rounded-full inline-flex mb-3">
                                        <i class="fas fa-handshake text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Internal Disputes</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Resolve conflicts between members</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-file-alt mr-2"></i> File a Complaint
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Technical Support -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white border-b pb-2 border-gray-200 dark:border-gray-700 flex items-center">
                            <i class="fas fa-laptop mr-3 text-purple-600"></i>
                            Technical Support
                        </h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-5">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Website Assistance</h4>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">Get help with our online platforms and digital services.</p>
                                <ul class="list-disc pl-5 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li>Account access issues</li>
                                    <li>Password reset</li>
                                    <li>Online registration help</li>
                                    <li>Website navigation</li>
                                </ul>
                                <a href="#" class="inline-flex items-center mt-4 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    Get technical support <i class="fas fa-arrow-right ml-1 text-sm"></i>
                                </a>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-5">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Event Registration</h4>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">Support for registering and managing event participation.</p>
                                <ul class="list-disc pl-5 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li>Event registration issues</li>
                                    <li>Virtual meeting access</li>
                                    <li>Payment processing</li>
                                    <li>Confirmation and tickets</li>
                                </ul>
                                <a href="#" class="inline-flex items-center mt-4 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    Event support <i class="fas fa-calendar-check ml-1 text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Emergency Contact -->
                    <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg mb-8">
                        <div class="flex items-start">
                            <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full mr-4">
                                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Emergency Contact</h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">For urgent matters that require immediate attention outside regular business hours.</p>
                                <div class="flex items-center">
                                    <i class="fas fa-phone-alt text-red-600 dark:text-red-400 mr-2"></i>
                                    <span class="font-semibold text-gray-900 dark:text-white">[Emergency Contact Number]</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Available for critical issues only</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Feedback Section -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Submit Feedback</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">We value your input. Share your suggestions, comments, or concerns to help us improve our services.</p>
                        
                        <form @submit.prevent="handleSubmit" class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <input v-model="name" type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Your name" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input v-model="email" type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Your email" required>
                            </div>
                            
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <select id="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option>General Feedback</option>
                                    <option>Complaint</option>
                                    <option>Suggestion</option>
                                    <option>Member Support</option>
                                    <option>Technical Issue</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                                <textarea v-model="message" id="message" name="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Your message..."></textarea>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        type="checkbox"
                                        id="agree_to_terms"
                                        v-model="agreeToTerms"
                                        class="w-4 h-4 text-green-600 bg-gray-100 rounded border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        required
                                    />
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="agree_to_terms" class="font-medium text-gray-700 dark:text-gray-300">
                                        <div class="flex flex-wrap">
                                            <span class="mr-1">
                                                I agree to the
                                                <a
                                                    :href="route('frontend.terms-and-conditions')"
                                                    target="_blank"
                                                    class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                >
                                                    Terms of Service
                                                </a>
                                                <span class="mx-1">and</span>
                                                <a
                                                    :href="route('frontend.privacy-policy')"
                                                    target="_blank"
                                                    class="text-emerald-600 hover:text-emerald-500 hover:underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 rounded"
                                                >
                                                    Privacy Policy
                                                </a>
                                                and confirm that all information provided is accurate.
                                            </span>
                                        </div>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        You must agree to the terms of service and our privacy policy to submit feedback.
                                    </p>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                                    :disabled="!agreeToTerms"
                                >
                                    <i class="fas fa-paper-plane mr-2"></i> Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </section>
    </GuestLayout>
</template>
