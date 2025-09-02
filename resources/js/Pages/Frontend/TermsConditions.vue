<!-- resources/js/Pages/Frontend/TermsConditions.vue -->
<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";

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
                Welcome to the Forward Kenya Party's official platform. These Terms and Conditions, grounded in the Forward Kenya Party Constitution (March 2024), outline our commitment to transparency, accountability, and inclusivity in all digital interactions. By engaging with our platform, you join our movement toward a progressive Kenya, built on integrity and public service, in alignment with the Constitution of Kenya (2010).
            </p>

            <!-- Showcase Section -->
            <div class="bg-white dark:bg-gray-900 mt-8">
                <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                    <div class="space-y-8">
                        <!-- Introduction -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                                Introduction
                            </h3>
                            <div class="text-gray-600 dark:text-gray-300 space-y-4">
                                <p>These Terms and Conditions ("Terms") govern your access to and use of the Forward Kenya Party's digital platforms, including our website, mobile applications, and related services (collectively, the "Services"), as authorized by Article 2.6 of the Forward Kenya Party Constitution.</p>
                                <p>By accessing or using our Services, you agree to be bound by these Terms, our Privacy Policy, and the Forward Kenya Party Constitution. If you disagree with any part of these Terms, you must not use our Services.</p>
                            </div>
                        </div>

                        <!-- Membership Terms -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                                Membership Terms
                            </h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Eligibility:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Must be a Kenyan citizen or resident, as per Article 4.1 of the Forward Kenya Party Constitution</li>
                                        <li>Must be at least 18 years of age, in accordance with Article 4.1 of the Forward Kenya Party Constitution</li>
                                        <li>Must not be a member of any other political party, as stipulated in Article 4.2.2 of the Forward Kenya Party Constitution</li>
                                        <li>Must subscribe to the party's ideology and constitution, as required by Article 2.6 of the Forward Kenya Party Constitution</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Membership Obligations:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Adhere to the party's code of conduct, as outlined in Article 4.4 of the Forward Kenya Party Constitution</li>
                                        <li>Pay prescribed membership fees, as per Article 4.2.5 of the Forward Kenya Party Constitution</li>
                                        <li>Participate in party activities and meetings, as encouraged by Article 4.3.1 of the Forward Kenya Party Constitution</li>
                                        <li>Uphold the party's values and principles, as defined in Article 2.6 of the Forward Kenya Party Constitution</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Code of Conduct -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                                Code of Conduct
                            </h3>
                            <div class="space-y-4 text-gray-600 dark:text-gray-300">
                                <p>All members and users of our Services must adhere to the principles outlined in Article 4.4 of the Forward Kenya Party Constitution:</p>
                                <div class="grid md:grid-cols-2 gap-6 mt-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Prohibited Conduct:</h4>
                                        <ul class="list-disc pl-6 space-y-2">
                                            <li>Hate speech or discrimination, prohibited under Article 4.4 of the Forward Kenya Party Constitution and Article 27 of the Constitution of Kenya (2010)</li>
                                            <li>Sharing of false or misleading information, violating Article 4.4 of the Forward Kenya Party Constitution</li>
                                            <li>Unauthorized use of party materials, contrary to Article 2.6 of the Forward Kenya Party Constitution</li>
                                            <li>Violation of intellectual property rights, as protected under Kenyan law</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Expected Behavior:</h4>
                                        <ul class="list-disc pl-6 space-y-2">
                                            <li>Respect for all members and officials, as required by Article 4.4 of the Forward Kenya Party Constitution</li>
                                            <li>Constructive participation in discussions, in line with Article 4.3.1 of the Forward Kenya Party Constitution</li>
                                            <li>Compliance with party decisions, as mandated by Article 4.4 of the Forward Kenya Party Constitution</li>
                                            <li>Maintenance of confidentiality when required, per Article 4.4 of the Forward Kenya Party Constitution</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acceptance of Terms -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                                Acceptance of Terms
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                By applying for membership with Forward Kenya Party (FKP), you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions, the Forward Kenya Party Constitution (March 2024), and applicable laws, including the Constitution of Kenya (2010).
                            </p>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mt-4">
                                <p class="text-yellow-800 dark:text-yellow-200 text-sm flex items-start">
                                    <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                                    <span>Membership application constitutes acceptance of these Terms and the Forward Kenya Party Constitution. If you do not agree with any part of these Terms, you must not proceed with membership registration.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Dispute Resolution -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                                Dispute Resolution
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                All disputes arising from membership or Party activities shall be resolved through mechanisms outlined in the Forward Kenya Party Constitution:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 mb-4">
                                <li><strong>Internal Dispute Resolution Committee (IDRC):</strong> First point of contact for all disputes except electoral matters (Article 41 of the Forward Kenya Party Constitution)</li>
                                <li><strong>Disciplinary Committee (DC):</strong> Handles matters of discipline and Code of Conduct violations (Article 29 of the Forward Kenya Party Constitution)</li>
                                <li><strong>Electoral and Nomination Dispute Resolution Committee:</strong> Handles disputes arising from internal Party elections (Article 32 of the Forward Kenya Party Constitution)</li>
                                <li><strong>National Executive Committee (NEC):</strong> Serves as the final appellate body for most disputes (Article 8 of the Forward Kenya Party Constitution)</li>
                            </ul>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-blue-800 dark:text-blue-200 text-sm">
                                    <i class="fas fa-handshake mr-2"></i>
                                    Members are encouraged to exhaust all internal dispute resolution mechanisms before seeking external legal recourse, in line with the Party's commitment to internal cohesion and conflict resolution, as per Article 41 of the Forward Kenya Party Constitution.
                                </p>
                            </div>
                        </div>

                        <!-- Amendments -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                                Amendments to Terms
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                The National Executive Committee reserves the right to amend these Terms and Conditions, as authorized by Article 8.2.8 of the Forward Kenya Party Constitution:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                <li>Amendments must be approved by at least two-thirds of the NEC members (Article 42.1 of the Forward Kenya Party Constitution)</li>
                                <li>Significant changes will be communicated to all members through appropriate channels</li>
                                <li>Continued membership after amendments constitutes acceptance of the revised terms</li>
                                <li>Members who do not agree with amendments may resign their membership, as per Article 4.5 of the Forward Kenya Party Constitution</li>
                            </ul>
                        </div>

                        <!-- Governing Law -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">7</span>
                                Governing Law & Jurisdiction
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                These Terms and Conditions are governed by the laws of Kenya, specifically:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 mb-4">
                                <li>The Constitution of Kenya (2010)</li>
                                <li>The Political Parties Act (2011)</li>
                                <li>The Data Protection Act (2019)</li>
                                <li>Other applicable legislation</li>
                            </ul>
                            <p class="text-gray-600 dark:text-gray-300">
                                Any legal proceedings arising from these Terms shall be subject to the jurisdiction of Kenyan courts, after exhaustion of all internal dispute resolution mechanisms as outlined in Article 41 of the Forward Kenya Party Constitution.
                            </p>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">8</span>
                                Contact Information
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                For questions regarding these Terms and Conditions, please contact:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">The Secretary General</h4>
                                    <address class="not-italic text-gray-600 dark:text-gray-300">
                                        Forward Kenya Party<br>
                                        Utalii Lane, Viewpark Towers, 19th Floor, Suite 19<br>
                                        P.O. Box: 27999 – 00100, GPO NAIROBI<br>
                                        Nairobi, Kenya<br>
                                        Email: forwardkenyaparty@gmail.com<br>
                                        Phone: +254 713 447820
                                    </address>
                                </div>
                                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Legal Affairs Secretary</h4>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        For specialized legal inquiries regarding these Terms, you may also contact our Legal Affairs Secretary at the same address.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center pt-8 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="far fa-clock mr-1"></i> Last updated: August 28, 2024
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                These Terms and Conditions are based on the Forward Kenya Party Constitution (March 2024) and comply with the Constitution of Kenya (2010) and other applicable laws.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>