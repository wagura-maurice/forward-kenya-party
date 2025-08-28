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
                Welcome to the Forward Kenya Party's official platform. These terms and conditions outline our commitment to transparency and accountability in all our digital interactions. By engaging with our platform, you're joining our movement towards a more inclusive and progressive Kenya, built on the principles of integrity and public service.
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
                                <p>These Terms and Conditions ("Terms") govern your access to and use of the Forward Kenya Party's digital platforms, including our website, mobile applications, and any related services (collectively, the "Services").</p>
                                <p>By accessing or using our Services, you agree to be bound by these Terms and our Privacy Policy. If you disagree with any part of these Terms, you must not use our Services.</p>
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
                                        <li>Must be a Kenyan citizen or resident</li>
                                        <li>Must be at least 18 years of age</li>
                                        <li>Must not be a member of any other political party</li>
                                        <li>Must subscribe to the party's ideology and constitution</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Membership Obligations:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Adhere to the party's code of conduct</li>
                                        <li>Pay prescribed membership fees</li>
                                        <li>Participate in party activities and meetings</li>
                                        <li>Uphold the party's values and principles</li>
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
                                <p>All members and users of our Services must adhere to the following principles:</p>
                                <div class="grid md:grid-cols-2 gap-6 mt-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Prohibited Conduct:</h4>
                                        <ul class="list-disc pl-6 space-y-2">
                                            <li>Hate speech or discrimination</li>
                                            <li>Sharing of false or misleading information</li>
                                            <li>Unauthorized use of party materials</li>
                                            <li>Violation of intellectual property rights</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Expected Behavior:</h4>
                                        <ul class="list-disc pl-6 space-y-2">
                                            <li>Respect for all members and officials</li>
                                            <li>Constructive participation in discussions</li>
                                            <li>Compliance with party decisions</li>
                                            <li>Maintenance of confidentiality when required</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acceptance of Terms -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                                Acceptance of Terms
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                By applying for membership with Forward Kenya Party (FKP), you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions, our Constitution, and any applicable laws and regulations.
                            </p>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mt-4">
                                <p class="text-yellow-800 dark:text-yellow-200 text-sm flex items-start">
                                    <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                                    <span>Membership application constitutes acceptance of these terms. If you do not agree with any part of these terms, you must not proceed with membership registration.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Membership Eligibility -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                                Membership Eligibility
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                Membership to Forward Kenya Party is open to all Kenyan citizens who meet the following criteria:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 mb-4">
                                <li>Must be at least 18 years of age</li>
                                <li>Must be a registered voter in Kenya</li>
                                <li>Must accept and abide by the Party's Constitution and Code of Conduct</li>
                                <li>Must not be a member of any other political party concurrently</li>
                                <li>Must pay the prescribed membership fees</li>
                            </ul>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-blue-800 dark:text-blue-200 text-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    The National Executive Committee reserves the right to accept or reject any membership application based on the rules and regulations established in the Party Constitution (Article 4.2.2).
                                </p>
                            </div>
                        </div>

                        <!-- Membership Obligations -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                                Membership Rights & Obligations
                            </h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Member Rights:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Participate in Party activities and meetings (Article 4.3.1.1)</li>
                                        <li>Contribute to policy formulation and implementation (Article 4.3.1.2)</li>
                                        <li>Access Party documents upon formal request (Article 4.3.1.3)</li>
                                        <li>Participate in elections and be eligible for Party positions (Article 4.3.1.5)</li>
                                        <li>Seek Party nomination for elective positions (Article 4.3.1.6.6)</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Member Obligations:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Abide by the Party Constitution and Code of Conduct</li>
                                        <li>Promote the Party's objectives and values</li>
                                        <li>Pay prescribed membership fees promptly</li>
                                        <li>Participate in Party activities and electoral processes</li>
                                        <li>Maintain discipline and uphold the Party's reputation</li>
                                        <li>Not to support candidates of other political parties (Article 4.4.1.13)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Code of Conduct -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                                Code of Conduct
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                All members are required to adhere to the Party's Code of Conduct, which includes but is not limited to:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 mb-4">
                                <li>Maintaining high ethical standards in all Party activities</li>
                                <li>Respecting decisions of Party organs and leadership</li>
                                <li>Promoting unity and cohesion within the Party</li>
                                <li>Avoiding actions that may bring the Party into disrepute</li>
                                <li>Resolving disputes through proper Party mechanisms</li>
                                <li>Refraining from publishing unauthorized Party information (Article 4.4.1.6)</li>
                            </ul>
                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                <p class="text-red-800 dark:text-red-200 text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    Violation of the Code of Conduct may result in disciplinary action, including suspension or termination of membership, as outlined in Article 29 of the Party Constitution.
                                </p>
                            </div>
                        </div>

                        <!-- Fees and Payments -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                                Membership Fees & Financial Obligations
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                The Party offers different membership categories with associated fees:
                            </p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Joining Fee</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Annual Subscription</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">Life Member</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 10,000</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 5,000</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">Ordinary Member</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 10</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 5</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">Corporate Member</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 60,000</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 10,000</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">Group Member</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 30,000</td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">KES 5,000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-blue-800 dark:text-blue-200 text-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Fees are subject to review by the National Executive Committee and ratification by the National Delegates Conference (Schedule IV). Membership fees are non-refundable except as required by law.
                                </p>
                            </div>
                        </div>

                        <!-- Intellectual Property -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                                Intellectual Property
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                All Party symbols, logos, names, slogans, and materials are the intellectual property of Forward Kenya Party and protected under applicable laws:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                <li>Party Symbol: Infinity arrows denoting synergy (Article 1.4)</li>
                                <li>Party Colors: Green, Black, and White (Article 1.5)</li>
                                <li>Party Slogan: "Our lives, Our Heritage" (Article 1.6)</li>
                                <li>All publications, policy documents, and campaign materials</li>
                            </ul>
                            <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Unauthorized use of Party intellectual property is strictly prohibited and may result in legal action and disciplinary measures.
                                </p>
                            </div>
                        </div>

                        <!-- Termination of Membership -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">7</span>
                                Termination of Membership
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                Membership may be terminated under the following circumstances (Article 4.5):
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 mb-4">
                                <li>By resignation in writing to the Party office</li>
                                <li>By resolution passed by the National Executive Committee</li>
                                <li>By accepting office, subscribing to, or promoting activities of another political party</li>
                                <li>By death</li>
                                <li>By operation of any law</li>
                            </ul>
                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                <p class="text-red-800 dark:text-red-200 text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    Upon termination, all rights and privileges cease immediately. Terminated members are not entitled to refunds of fees or subscriptions and must return all Party property in their possession.
                                </p>
                            </div>
                        </div>

                        <!-- Dispute Resolution -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">8</span>
                                Dispute Resolution
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                All disputes arising from membership or Party activities shall be resolved through the following mechanisms:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 mb-4">
                                <li><strong>Internal Dispute Resolution Committee (IDRC):</strong> First point of contact for all disputes except electoral matters (Article 41)</li>
                                <li><strong>Disciplinary Committee (DC):</strong> Handles matters of discipline and Code of Conduct violations (Article 29)</li>
                                <li><strong>Electoral and Nomination Dispute Resolution Committee:</strong> Handles disputes arising from internal Party elections (Article 32)</li>
                                <li><strong>National Executive Committee (NEC):</strong> Serves as the final appellate body for most disputes</li>
                            </ul>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-blue-800 dark:text-blue-200 text-sm">
                                    <i class="fas fa-handshake mr-2"></i>
                                    Members are encouraged to exhaust all internal dispute resolution mechanisms before seeking external legal recourse, in line with the Party's commitment to internal cohesion and conflict resolution.
                                </p>
                            </div>
                        </div>

                        <!-- Amendments -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">9</span>
                                Amendments to Terms
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                The National Executive Committee reserves the right to amend these Terms and Conditions at any time:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                <li>Amendments must be approved by at least two-thirds of the NEC members (Article 42.1)</li>
                                <li>Significant changes will be communicated to all members through appropriate channels</li>
                                <li>Continued membership after amendments constitutes acceptance of the revised terms</li>
                                <li>Members who do not agree with amendments may resign their membership</li>
                            </ul>
                        </div>

                        <!-- Governing Law -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">10</span>
                                Governing Law & Jurisdiction
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                These Terms and Conditions are governed by the laws of Kenya, particularly:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 mb-4">
                                <li>The Constitution of Kenya</li>
                                <li>The Political Parties Act</li>
                                <li>The Data Protection Act</li>
                                <li>Other applicable legislation</li>
                            </ul>
                            <p class="text-gray-600 dark:text-gray-300">
                                Any legal proceedings arising from these terms shall be subject to the jurisdiction of Kenyan courts, after exhaustion of all internal dispute resolution mechanisms.
                            </p>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg section-container">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">11</span>
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
                                        View Park Towers<br>
                                        P.O. Box 27999-00100<br>
                                        Nairobi, Kenya<br>
                                        Email: forwardkenyaparty@gmail.com<br>
                                        Phone: [Party Contact Number]
                                    </address>
                                </div>
                                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Legal Affairs Secretary</h4>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        For specialized legal inquiries regarding these terms, you may also contact our Legal Affairs Secretary at the same address.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center pt-8 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="far fa-clock mr-1"></i> Last updated: August 28, 2024
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                These Terms and Conditions are based on the Forward Kenya Party Constitution and comply with the laws of Kenya.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
