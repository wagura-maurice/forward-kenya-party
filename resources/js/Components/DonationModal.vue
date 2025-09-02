<!-- resources/js/Components/DonationModal.vue -->
<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div ref="modalRef" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8 relative">
                <div class="w-full">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Support Our Cause
                        </h3>
                        <button 
                            type="button" 
                            @click="close"
                            class="text-gray-400 hover:text-green-500 focus:outline-none focus:text-green-500"
                        >
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times text-xl hover:text-green-500 cursor-pointer"></i>
                        </button>
                    </div>

                    <!-- Donation Form -->
                    <div class="space-y-6">
                        <div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">
                                Your generous donation will help us continue our mission. All contributions are greatly appreciated.
                            </p>
                        </div>
                        
                        <!-- Donation Type -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Donation Type <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-2">
                                <button 
                                    v-for="type in donationTypes" 
                                    :key="type.value"
                                    type="button"
                                    @click="selectDonationType(type.value)"
                                    :class="[
                                        'p-4 border rounded-lg flex flex-col items-center justify-center transition-colors',
                                        form.donation_type === type.value 
                                            ? 'border-green-500 bg-green-50 dark:bg-green-900/20' 
                                            : 'border-gray-300 dark:border-gray-600 hover:border-green-500'
                                    ]"
                                >
                                    <i :class="[type.icon, 'text-2xl mb-2', form.donation_type === type.value ? 'text-green-600' : 'text-gray-500']"></i>
                                    <span class="text-sm font-medium">{{ type.label }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Monetary Donation Fields -->
                        <div v-if="form.donation_type === 'monetary'" class="space-y-4">
                            <!-- Donation Amount -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Donation Amount (KES)
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">KES</span>
                                    </div>
                                    <input 
                                        type="number" 
                                        v-model="form.amount"
                                        class="focus:ring-green-500 focus:border-green-500 block w-full pl-14 pr-12 sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0.00"
                                        min="1"
                                        step="1"
                                        required
                                    >
                                </div>
                            </div>
                            
                            <!-- Recurring Donation Option -->
                            <div v-if="form.donation_type === 'monetary'" class="flex items-center">
                                <input 
                                    id="recurring" 
                                    type="checkbox" 
                                    v-model="form.is_recurring"
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                >
                                <label for="recurring" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Make this a monthly recurring donation
                                </label>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Select Your Payment Method <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button 
                                        v-for="method in paymentMethods" 
                                        :key="method.value"
                                        type="button"
                                        @click="form.payment_method = method.value"
                                        :class="[
                                            'p-3 border rounded-lg flex items-center justify-center transition-colors',
                                            form.payment_method === method.value 
                                                ? 'border-green-500 bg-green-50 dark:bg-green-900/20' 
                                                : 'border-gray-300 dark:border-gray-600 hover:border-green-500'
                                        ]"
                                    >
                                        <i :class="[method.icon, 'mr-2', form.payment_method === method.value ? 'text-green-600' : 'text-gray-500']"></i>
                                        <span class="font-medium">{{ method.label }}</span>
                                    </button>
                                </div>

                                <!-- M-Pesa Payment Details -->
                                <div v-if="form.payment_method === 'mpesa'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                M-Pesa Phone Number <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 flex items-center">
                                                    <select 
                                                        v-model="form.mpesa_prefix"
                                                        class="h-full py-0 pl-3 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-l-md focus:outline-none dark:bg-gray-700 dark:text-gray-300"
                                                    >
                                                        <option value="+254">+254</option>
                                                        <option value="+255">+255</option>
                                                        <option value="+256">+256</option>
                                                    </select>
                                                </div>
                                                <input 
                                                    type="tel" 
                                                    v-model="form.mpesa_phone"
                                                    placeholder="7XX XXX XXX"
                                                    class="focus:ring-green-500 focus:border-green-500 block w-full pl-20 pr-12 sm:text-sm border-gray-300 rounded-md dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                    required
                                                >
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                You'll receive an M-Pesa payment request on this number
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Payment Details -->
                                <div v-else-if="form.payment_method === 'card'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Card Number <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                v-model="form.card_number"
                                                placeholder="1234 5678 9012 3456"
                                                class="focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                required
                                            >
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Expiry Date <span class="text-red-500">*</span>
                                                </label>
                                                <input 
                                                    type="text" 
                                                    v-model="form.card_expiry"
                                                    placeholder="MM/YY"
                                                    class="focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    CVV <span class="text-red-500">*</span>
                                                </label>
                                                <input 
                                                    type="text" 
                                                    v-model="form.card_cvv"
                                                    placeholder="123"
                                                    class="focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                    required
                                                >
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Name on Card <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                v-model="form.card_name"
                                                placeholder="Full name as on card"
                                                class="focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                required
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Details -->
                                <div v-else-if="form.payment_method === 'bank'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Bank Transfer Details:</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Equity Bank Kenya Limited</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Account Name</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Forward Kenya Party</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Account Number</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">1234567890</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Branch</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Nairobi CBD</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">SWIFT Code</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">EQBLKENAXXX</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                                        Please use your name as the payment reference. Email the transaction details to 
                                        <a href="mailto:donations@forwardkenyaparty.com" class="text-green-600 hover:underline">donations@forwardkenyaparty.com</a> 
                                        after transfer.
                                    </p>
                                </div>

                                <!-- Cryptocurrency Wallet Addresses -->
                                <div v-else-if="form.payment_method === 'crypto'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Send to Wallet Address:</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Bitcoin (BTC)</span>
                                                <button 
                                                    @click="copyToClipboard('bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh')"
                                                    class="text-green-600 hover:text-green-700 text-xs flex items-center"
                                                >
                                                    <i class="far fa-copy mr-1"></i> Copy
                                                </button>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-2 rounded text-xs font-mono text-gray-600 dark:text-gray-300 break-all flex items-center justify-between">
                                                <span>bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh</span>
                                                <i class="fab fa-bitcoin text-orange-500 ml-2"></i>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Ethereum (ETH)</span>
                                                <button 
                                                    @click="copyToClipboard('0x71C7656EC7ab88b098defB751B7401B5f6d8976F')"
                                                    class="text-green-600 hover:text-green-700 text-xs flex items-center"
                                                >
                                                    <i class="far fa-copy mr-1"></i> Copy
                                                </button>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-2 rounded text-xs font-mono text-gray-600 dark:text-gray-300 break-all flex items-center justify-between">
                                                <span>0x71C7656EC7ab88b098defB751B7401B5f6d8976F</span>
                                                <i class="fab fa-ethereum text-purple-500 ml-2"></i>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">USDT (ERC20)</span>
                                                <button 
                                                    @click="copyToClipboard('0x71C7656EC7ab88b098defB751B7401B5f6d8976F')"
                                                    class="text-green-600 hover:text-green-700 text-xs flex items-center"
                                                >
                                                    <i class="far fa-copy mr-1"></i> Copy
                                                </button>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-2 rounded text-xs font-mono text-gray-600 dark:text-gray-300 break-all flex items-center justify-between">
                                                <span>0x71C7656EC7ab88b098defB751B7401B5f6d8976F</span>
                                                <i class="fas fa-coins text-blue-500 ml-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            After sending your donation, please email the transaction details to 
                                            <a href="mailto:donations@forwardkenyaparty.com" class="text-green-600 hover:underline font-medium">
                                                donations@forwardkenyaparty.com
                                            </a> 
                                            for confirmation.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- In-Kind Donation Fields -->
                        <div v-else-if="form.donation_type === 'in_kind'" class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Type of Donation <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.in_kind_type"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                >
                                    <option value="">Select donation type</option>
                                    <option v-for="type in inKindTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    v-model="form.description"
                                    rows="3"
                                    class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Please describe the item or service you're donating"
                                    required
                                ></textarea>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Estimated Value (KES)
                                </label>
                                <input 
                                    type="number" 
                                    v-model="form.estimated_value"
                                    class="focus:ring-green-500 focus:border-green-500 block w-full px-3 py-2 sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="0.00"
                                    min="0"
                                    step="1"
                                >
                            </div>
                        </div>
                        
                        <!-- Volunteer Donation Fields -->
                        <div v-else-if="form.donation_type === 'volunteer'" class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Skills/Expertise <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.skills"
                                    class="focus:ring-green-500 focus:border-green-500 block w-full px-3 py-2 sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="e.g., Graphic Design, Event Planning, Legal Advice"
                                    required
                                >
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Availability <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.availability"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                >
                                    <option value="">Select your availability</option>
                                    <option value="weekdays">Weekdays</option>
                                    <option value="weekends">Weekends</option>
                                    <option value="evenings">Evenings</option>
                                    <option value="flexible">Flexible</option>
                                    <option value="one_time">One-time event</option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Additional Notes
                                </label>
                                <textarea 
                                    v-model="form.notes"
                                    rows="3"
                                    class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Any additional information about how you'd like to volunteer"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Donor Information -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Your Full Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                v-model="form.donor_name"
                                class="focus:ring-green-500 focus:border-green-500 block w-full px-3 py-2 sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="John Doe"
                            >
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Your Email Address <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                v-model="form.email"
                                class="focus:ring-green-500 focus:border-green-500 block w-full px-3 py-2 sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="you@example.com"
                            >
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Your Telehone Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                v-model="form.phone"
                                class="focus:ring-green-500 focus:border-green-500 block w-full px-3 py-2 sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="+254 700 000000"
                            >
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input 
                                        id="terms" 
                                        type="checkbox" 
                                        v-model="form.terms"
                                        class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-gray-700 dark:text-gray-300">
                                        <span class="w-full">
                                            By donating, I agree with the
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
                                                Privacy Policy. 
                                            </a>

                                                Furthermore, I understand that donations made to this cause are
                                                tax deductible as per the provisions of the Income Tax
                                                Ordinance Chapter 186 of the Laws of Kenya, 2015.

                                        </span>
                                    </label>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 ml-7 -mt-1">
                                You must agree to the terms of
                                service and our privacy policy to make a donation.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button
                                type="button"
                                @click="submitDonation"
                                :disabled="!form.terms || processing"
                                :class="[
                                    'w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500',
                                    (!form.terms || processing) ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                            >
                                <span v-if="!processing">Donate Now</span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

// Donation types
const donationTypes = [
    { value: 'monetary', label: 'Monetary', icon: 'fas fa-money-bill-wave' },
    { value: 'in_kind', label: 'In-Kind', icon: 'fas fa-box' },
    { value: 'volunteer', label: 'Volunteer', icon: 'fas fa-hands-helping' }
];

// Payment methods
const paymentMethods = [
    { 
        value: 'mpesa', 
        label: 'M-Pesa', 
        icon: 'fas fa-mobile-alt',
        description: 'Pay via M-Pesa (Kenya)'
    },
    { 
        value: 'card', 
        label: 'Credit/Debit Card', 
        icon: 'fas fa-credit-card',
        description: 'Pay with Visa, Mastercard, etc.'
    },
    { 
        value: 'bank', 
        label: 'Bank Transfer', 
        icon: 'fas fa-university',
        description: 'Direct bank transfer'
    },
    { 
        value: 'crypto', 
        label: 'Cryptocurrency', 
        icon: 'fab fa-bitcoin',
        description: 'Pay with Bitcoin, Ethereum, etc.'
    }
];
// In-kind donation types
const inKindTypes = [
    { value: 'office_supplies', label: 'Office Supplies' },
    { value: 'equipment', label: 'Equipment' },
    { value: 'services', label: 'Services' },
    { value: 'venue', label: 'Venue' },
    { value: 'other', label: 'Other' }
];

const form = useForm({
    donation_type: 'monetary',
    amount: '',
    is_recurring: false,
    payment_method: 'mpesa',
    mpesa_phone: '',
    mpesa_prefix: '+254',
    card_number: '',
    card_expiry: '',
    card_cvv: '',
    card_name: '',
    donor_name: '',
    email: '',
    phone: '',
    in_kind_type: '',
    in_kind_description: '',
    in_kind_value: '',
    skills: '',
    availability: '',
    terms: false
});

const processing = ref(false);

// Computed properties
const showMonetaryFields = computed(() => form.donation_type === 'monetary');

// Methods
const selectDonationType = (type) => {
    form.donation_type = type;
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        // You could add a toast notification here if you have one
        alert('Wallet address copied to clipboard!');
    } catch (err) {
        console.error('Failed to copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Wallet address copied to clipboard!');
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            alert('Failed to copy address. Please copy it manually.');
        }
        document.body.removeChild(textArea);
    }
};

const submitDonation = async () => {
    if (!form.terms) {
        alert('Please accept the terms and conditions to proceed.');
        return;
    }
    
    // Validate required fields based on donation type
    if (form.donation_type === 'in_kind' && !form.in_kind_type) {
        alert('Please select the type of in-kind donation.');
        return;
    }
    
    if (form.donation_type === 'volunteer' && !form.skills) {
        alert('Please provide your skills/expertise.');
        return;
    }
    
    processing.value = true;
    
    try {
        // Prepare the donation data
        const donationData = {
            type: form.donation_type,
            amount: parseFloat(form.amount) || 0,
            donor_name: form.donor_name,
            email: form.email,
            phone: form.phone,
            terms: form.terms,
            created_at: new Date().toISOString(),
            // Type-specific fields
            ...(form.donation_type === 'monetary' && {
                is_recurring: form.is_recurring,
                payment_method: form.payment_method
            }),
            ...(form.donation_type === 'in_kind' && {
                in_kind_type: form.in_kind_type,
                in_kind_description: form.in_kind_description,
                in_kind_value: parseFloat(form.in_kind_value) || 0
            }),
            ...(form.donation_type === 'volunteer' && {
                skills: form.skills,
                availability: form.availability
            })
        };
        
        // Log the donation data (replace with actual API call)
        console.log('Submitting donation:', donationData);
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // Show success message based on donation type
        let successMessage = 'Thank you for your ';
        switch(form.donation_type) {
            case 'monetary':
                successMessage += 'generous monetary donation';
                if (form.is_recurring) successMessage += ' (recurring monthly)';
                break;
            case 'in_kind':
                successMessage += 'in-kind donation';
                break;
            case 'volunteer':
                successMessage = 'Thank you for volunteering your time and skills';
                break;
        }
        successMessage += '! We appreciate your support.';
        
        alert(successMessage);
        
        // Reset form and close modal
        form.reset();
        form.donation_type = 'monetary'; // Reset to default donation type
        close();
        
    } catch (error) {
        console.error('Donation submission failed:', error);
        alert('There was an error processing your request. Please try again.');
    } finally {
        processing.value = false;
    }
};

const modalRef = ref(null);

const handleClickOutside = (event) => {
    if (modalRef.value && !modalRef.value.contains(event.target)) {
        close();
    }
};

// Close on escape key
const onKeydown = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

// Add event listeners when component is mounted
onMounted(() => {
    if (props.show) {
        document.addEventListener('keydown', onKeydown);
        document.addEventListener('mousedown', handleClickOutside);
    }
});

// Clean up event listeners when component is unmounted
onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown);
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>