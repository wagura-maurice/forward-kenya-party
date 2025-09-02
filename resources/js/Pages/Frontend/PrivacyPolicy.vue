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
                        <!-- Your Rights -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                                Your Rights
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                As a member, you have the following rights regarding your personal data, in accordance with Kenyan data protection laws:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Access</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may request a copy of your personal data held by the party (Article 4.3.2 of our Party Constitution).
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Rectification</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may request correction of inaccurate or incomplete data.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Withdraw Consent</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Where processing is based on consent, you may withdraw it at any time without affecting prior processing.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Object</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may object to certain processing activities, such as direct marketing, by contacting the Secretary General.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Right to Erasure</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            You may request deletion of your personal data upon termination of membership, as outlined in Article 4.5 of our Party Constitution, subject to legal retention requirements.
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

                        <!-- Personal Data We Collect -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                                Personal Data We Collect
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                In accordance with Article 4.2.5 of our Party Constitution, we collect and process the following categories of personal data:
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
                                We process your personal data based on the following legal grounds as outlined in our Party Constitution and in compliance with data protection regulations:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Performance of a Contract</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Processing necessary for membership administration as outlined in Articles 4.1 and 4.2 of our Party Constitution, including maintaining your membership account and facilitating your participation in party activities.
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Legitimate Interests</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Processing necessary for internal administration, member communication, and conducting internal nominations and elections as authorized by Articles 3, 21, and 31 of our Party Constitution.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm mb-4">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Legal Obligation</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Processing necessary to comply with legal requirements, including reporting to the Registrar of Political Parties as mandated by Article 4.5.4 of our Party Constitution.
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
                                Your personal data is used exclusively for purposes related to party operations and membership management, as authorized by our Party Constitution:
                            </p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Membership Management</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Processing membership applications and maintaining the membership register (Article 4.2 of our Party Constitution).
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Communication</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Sending updates about party activities, events, and policy developments (Article 4.3.1 of our Party Constitution).
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Internal Elections</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Facilitating nominations and voting processes (Article 21 of our Party Constitution).
                                        </p>
                                    </div>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-2">Legal Compliance</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            Fulfilling reporting requirements to regulatory authorities (Article 4.5.4 of our Party Constitution).
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Changes to This Policy -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                                Changes to This Policy
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                The National Executive Committee (NEC) may update this policy as needed (Article 8.2.8 of our Party Constitution). When we make changes, we will:
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

                        <!-- Data Sharing and Retention -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                                Data Sharing & Retention
                            </h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Data Sharing</h4>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                                        We may share your data in limited circumstances:
                                    </p>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                                        <li>With party branches and organs as required for internal operations (Article 5 of our Party Constitution)</li>
                                        <li>With regulatory authorities when legally required</li>
                                        <li>With service providers bound by confidentiality agreements</li>
                                    </ul>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-4">
                                        We do not sell or rent your personal data to third parties.
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Data Retention</h4>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                                        We retain your personal data only as long as necessary:
                                    </p>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300 text-sm mt-2">
                                        <li>Active membership data is retained throughout your membership period</li>
                                        <li>Financial records are kept for 2 years as required by law</li>
                                        <li>Terminated membership data is retained for 1 year or as required by law</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Data Security -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">7</span>
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

                        <!-- Data Controller -->
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">8</span>
                                Data Controller & Contact Information
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4 text-gray-600 dark:text-gray-300">
                                <div>
                                    <p class="font-semibold">Party Information:</p>
                                    <p><strong>Name:</strong> Forward Kenya Party (FKP)</p>
                                    <p><strong>Address:</strong> Utalii Lane, Viewpark Towers, 19th Floor, Suite 19<br>
                                    P.O. Box: 27999 – 00100, GPO NAIROBI</p>
                                    <p><strong>Email:</strong> forwardkenyaparty@gmail.com</p>
                                    <p><strong>Website:</strong> www.forwardkenyaparty.com</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Data Protection Officer:</p>
                                    <p>For privacy-related inquiries, please contact:</p>
                                    <p><strong>The Secretary General</strong><br>
                                    Email: forwardkenyaparty@gmail.com<br>
                                    Phone: +254 713 447820</p>
                                </div>
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