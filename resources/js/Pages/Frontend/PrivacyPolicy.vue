<!-- resources/js/Pages/Frontend/PrivacyPolicy.vue -->
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
                At Forward Kenya Party, we uphold the highest standards of data protection and privacy. This policy reflects our unwavering commitment to transparency and trust, ensuring that your personal information is handled with the utmost care and in accordance with both Kenyan law and our party's principles of integrity and accountability.
            </p>

            <!-- Showcase Section -->
            <div class="bg-white dark:bg-gray-900 mt-8">
                <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">    
                    <div class="space-y-8">
                        <!-- Data Controller -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                                Data Controller & Contact Information
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4 text-gray-600 dark:text-gray-300">
                                <div>
                                    <p class="font-semibold">Party Information:</p>
                                    <p><strong>Name:</strong> Forward Kenya Party (FKP)</p>
                                    <p><strong>Address:</strong> View Park Towers, P.O. Box 27999-00100, Nairobi</p>
                                    <p><strong>Email:</strong> forwardkenyaparty@gmail.com</p>
                                    <p><strong>Website:</strong> www.forwardkenyaparty.com</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Data Protection Officer:</p>
                                    <p>For privacy-related inquiries, please contact:</p>
                                    <p><strong>The Secretary General</strong><br>
                                    Email: forwardkenyaparty@gmail.com<br>
                                    Phone: [Party Contact Number]</p>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Data We Collect -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                                Personal Data We Collect
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                In accordance with Article 4.2.5 of our Constitution, we collect and process the following categories of personal data:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Required Membership Data:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Full name and identification details</li>
                                        <li>Contact information (phone, email, address)</li>
                                        <li>Demographic information (region, county, ethnicity)</li>
                                        <li>Gender and date of birth</li>
                                        <li>Disability status (if applicable)</li>
                                        <li>Payment information for membership fees</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Additional Data We May Collect:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Records of participation in party activities and meetings</li>
                                        <li>Photographs and video from party events</li>
                                        <li>Opinions and feedback provided through surveys</li>
                                        <li>Communication preferences</li>
                                        <li>Candidate application information (if applicable)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Legal Basis for Processing -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                                Legal Basis for Processing
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                We process your personal data based on the following legal grounds as outlined in our Constitution and in compliance with data protection regulations:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Performance of a Contract</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Processing necessary for membership administration as outlined in Articles 4.1 and 4.2 of our Constitution, including maintaining your membership account and facilitating your participation in party activities.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Legitimate Interests</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Processing necessary for internal administration, member communication, and conducting internal nominations and elections as authorized by Articles 3, 21, and 31 of our Constitution.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Legal Obligation</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Processing necessary to comply with legal requirements, including reporting to the Registrar of Political Parties as mandated by Article 4.5.4 of our Constitution.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Consent</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            For specific purposes not covered by the above bases, we will seek your explicit consent, which you may withdraw at any time by contacting us.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- How We Use Your Data -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                                How We Use Your Data
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                We use your personal data for the following purposes in accordance with our Constitution:
                            </p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li>Create and manage your membership account (Article 4)</li>
                                    <li>Maintain an accurate and continuously updated Membership Register (Articles 4.2.5, 4.2.6)</li>
                                    <li>Communicate about party activities, meetings, and events</li>
                                    <li>Administer internal party elections and nominations (Articles 21, 31)</li>
                                </ul>
                                <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li>Facilitate your participation in party discussions and policy formulation (Article 4.3)</li>
                                    <li>Manage party finances, including membership fees (Schedule IV, Article 35)</li>
                                    <li>Comply with legal obligations, including reporting to the Registrar of Political Parties (Article 4.5.4)</li>
                                    <li>Analyze member demographics to improve party outreach and representation</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Data Sharing -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                                Data Sharing and Disclosure
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                We may share your personal data with the following parties under strict confidentiality agreements:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Internal Sharing:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>National Executive Committee for party administration</li>
                                        <li>County Congress for regional coordination</li>
                                        <li>National Elections Board for election processes</li>
                                        <li>Other internal party organs as necessary for legitimate party functions</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">External Sharing:</h4>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                        <li>Regulatory bodies (e.g., Registrar of Political Parties - Article 4.5.4)</li>
                                        <li>Service providers under strict contractual agreements (IT, communication platforms)</li>
                                        <li>Law enforcement agencies when required by law or to protect our legal rights</li>
                                        <li>Other political parties in case of coalitions, with appropriate safeguards</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <p class="text-blue-700 dark:text-blue-300 text-sm flex items-start">
                                    <i class="fas fa-info-circle mt-1 mr-2"></i>
                                    <span>We never sell your personal data to third parties. Any sharing is done with appropriate safeguards and only for specific, legitimate purposes aligned with our Constitution.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Data Retention -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                                Data Retention
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                We retain your personal data only for as long as necessary to fulfill the purposes outlined in this policy:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Active Membership:</h4>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Your data will be retained for the duration of your membership with Forward Kenya Party.
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">After Membership Ends:</h4>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Upon termination of membership (Article 4.5), your name will be removed from the active register, but we may retain minimal data for historical, legal, or audit purposes for a period not exceeding 7 years, unless a longer retention period is required by law.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Your Data Rights -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">7</span>
                                Your Data Rights
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                As a member of Forward Kenya Party, you have the following rights regarding your personal data:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right of Access</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You have the right to inspect your personal data in the Party Register as provided in Article 4.3.1.6.5 of our Constitution.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Rectification</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may request correction of inaccurate or incomplete personal data, in line with our continuous update obligation under Article 4.2.6.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Object</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may object to certain processing activities, such as direct marketing, by contacting the Secretary General.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Erasure</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may request deletion of your personal data upon termination of membership, as outlined in Article 4.5 of our Constitution, subject to legal retention requirements.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Restriction</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may request temporary restriction of processing in certain circumstances, such as when accuracy is contested.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Data Portability</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Where applicable, you may request a copy of your data in a structured, machine-readable format.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                <p class="text-green-700 dark:text-green-300 text-sm flex items-start">
                                    <i class="fas fa-lightbulb mt-1 mr-2"></i>
                                    <span>To exercise any of these rights, please contact the Secretary General with your membership details and specific request. We will respond within 30 days as required by law.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Data Security -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">8</span>
                                Data Security
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction. These measures include:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                <li>Secure storage systems with access controls</li>
                                <li>Encryption of sensitive data during transmission</li>
                                <li>Regular security assessments and updates</li>
                                <li>Confidentiality agreements with staff and volunteers</li>
                                <li>Limited access to personal data on a need-to-know basis</li>
                            </ul>
                        </div>

                        <!-- Changes to This Policy -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">9</span>
                                Changes to This Policy
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                The National Executive Committee (NEC) may update this policy as needed (Article 8.2.8). When we make changes, we will:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                                <li>Update the "Last updated" date at the bottom of this policy</li>
                                <li>Notify members through appropriate channels (email, website notice, or official communications)</li>
                                <li>Provide a summary of significant changes when appropriate</li>
                            </ul>
                            <p class="text-gray-600 dark:text-gray-300 mt-4">
                                Continued use of your membership after changes constitutes acceptance of the updated policy.
                            </p>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">10</span>
                                Contact Information
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                For any questions, concerns, or requests regarding this Privacy Policy or your personal data, please contact:
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
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Data Protection Officer</h4>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        For specialized data protection inquiries, you may also contact our designated Data Protection Officer at the same address or email.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <p class="text-blue-700 dark:text-blue-300 text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    If you are not satisfied with our response, you have the right to lodge a complaint with the Office of the Data Protection Commissioner in Kenya.
                                </p>
                            </div>
                        </div>

                        <div class="text-center pt-8 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="far fa-clock mr-1"></i> Last updated: August 28, 2024
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                This Privacy Policy is based on the Forward Kenya Party Constitution and complies with the Data Protection Act, 2019 of Kenya.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
