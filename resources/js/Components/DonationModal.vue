<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>
            
            <!-- Modal panel - Using larger max-width (3xl) for donation form -->
            <div ref="modalRef" class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto sm:max-w-3xl">
                <div class="p-8 w-full">
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
                                    Select Your Payment Method <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <button 
                                        v-for="method in paymentMethods" 
                                        :key="method.value"
                                        type="button"
                                        @click="form.payment_method = method.value"
                                        :class="[
                                            'p-4 border rounded-lg flex flex-col items-start transition-colors text-left',
                                            form.payment_method === method.value 
                                                ? 'border-green-500 bg-green-50 dark:bg-green-900/20 ring-1 ring-green-500' 
                                                : 'border-gray-300 dark:border-gray-600 hover:border-green-500 hover:bg-gray-50 dark:hover:bg-gray-700'
                                        ]"
                                    >
                                        <div class="flex items-center w-full mb-1">
                                            <i :class="[method.icon, 'mr-2', form.payment_method === method.value ? 'text-green-600' : 'text-gray-500']"></i>
                                            <span class="font-medium text-sm">{{ method.label }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ method.description }}</p>
                                    </button>
                                </div>

                                <!-- Mobile Money Payment Details -->
                                <div v-if="form.payment_method === 'mobile_money'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Mobile Money Provider Selection -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-left">
                                                    Mobile Money Provider <span class="text-red-500">*</span>
                                                </label>
                                                <select 
                                                    v-model="form.mobile_money_provider"
                                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                    required
                                                >
                                                    <option value="mpesa">M-Pesa (Safaricom)</option>
                                                    <option value="airtel" disabled>Airtel Money</option>
                                                    <option value="tkash" disabled>T-Kash (Telkom)</option>
                                                    <option value="equity" disabled>Equity EazzyPay</option>
                                                </select>
                                            </div>

                                            <!-- Telephone Number Input -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-left">
                                                    Telephone Number <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative rounded-md shadow-sm flex">
                                                    <div class="relative flex-shrink-0">
                                                        <select 
                                                            v-model="form.telephone_prefix"
                                                            class="h-full py-2 pl-3 pr-8 border-r-0 border-gray-300 bg-transparent text-gray-500 sm:text-sm rounded-l-md focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-500 dark:text-gray-300"
                                                        >
                                                            <option value="+254">+254 (KE)</option>
                                                            <option value="+255" disabled>+255 (TZ)</option>
                                                            <option value="+256" disabled>+256 (UG)</option>
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                                            <i class="fas fa-chevron-down text-xs"></i>
                                                        </div>
                                                    </div>
                                                    <input 
                                                        type="tel" 
                                                        v-model="form.telephone_number"
                                                        :placeholder="form.mobile_money_provider === 'mpesa' ? '7XX XXX XXX' : 'Your mobile telephone number'"
                                                        class="focus:ring-green-500 focus:border-green-500 block w-full pl-3 pr-3 sm:text-sm border-l-0 border-gray-300 rounded-r-md dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                        required
                                                    >
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <template v-if="form.mobile_money_provider === 'mpesa'">
                                                        You'll receive an M-Pesa payment request on this number
                                                    </template>
                                                    <template v-else>
                                                        We'll send payment instructions to this number
                                                    </template>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Provider-specific instructions -->
                                        <div v-if="form.mobile_money_provider === 'mpesa'" class="p-3 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 text-sm rounded-md">
                                            <p>For Mpesa, please ensure your number is registered for Safaricom Mpesa services.</p>
                                        </div>
                                        <div v-if="form.mobile_money_provider === 'airtel'" class="p-3 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200 text-sm rounded-md">
                                            <p>For Airtel Money, please ensure your number is registered for Airtel Money services.</p>
                                        </div>
                                        <div v-else-if="form.mobile_money_provider === 'tkash'" class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 text-sm rounded-md">
                                            <p>For T-Kash, please ensure your Telkom line is registered for Telekom T-Kash services.</p>
                                        </div>
                                        <div v-else-if="form.mobile_money_provider === 'equity'" class="p-3 bg-purple-50 dark:bg-purple-900/20 text-purple-800 dark:text-purple-200 text-sm rounded-md">
                                            <p>For EazzyPay, please ensure your number is registered for Equity EazzyPay services.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Payment Details -->
                                <div v-else-if="form.payment_method === 'card'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-left">
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
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-left">
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
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-left">
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
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-left">
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
                                        
                                    </div>
                                </div>

                                <!-- Bank Transfer Details -->
                                <div v-else-if="form.payment_method === 'bank'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-left">
                                                Select Bank <span class="text-red-500">*</span>
                                            </label>
                                            <select 
                                                v-model="form.selected_bank"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            >
                                                <option v-for="bank in banks" :key="bank.id" :value="bank.id">
                                                    {{ bank.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Transaction Reference
                                                </label>
                                                <div class="flex rounded-md shadow-sm">
                                                    <input 
                                                        type="text" 
                                                        v-model="form.transaction_reference"
                                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                                        placeholder="Enter reference or click generate"
                                                    >
                                                    <button 
                                                        type="button"
                                                        @click="generateReference()"
                                                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-md bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                                    >
                                                        Generate
                                                    </button>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Please use this reference when making the transfer
                                                </p>
                                            </div>

                                            </div>
                                        
                                        <div class="space-y-3 bg-white dark:bg-gray-800 p-4 rounded-md border border-gray-200 dark:border-gray-700">
                                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Bank Transfer Details:</h4>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Bank Name</p>
                                                    <p class="text-sm font-medium">{{ selectedBank.name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Account Name</p>
                                                    <p class="text-sm font-medium">{{ selectedBank.accountName }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Account Number</p>
                                                    <p class="text-sm font-mono">{{ selectedBank.accountNumber }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Reference Code</p>
                                                    <p class="text-sm font-mono">
                                                        {{ selectedBank.referenceCode }}
                                                        <button 
                                                            type="button" 
                                                            @click="copyToClipboard(selectedBank.referenceCode)"
                                                            class="ml-2 p-1 text-gray-400 hover:text-green-600"
                                                            title="Copy to clipboard"
                                                        >
                                                            <i class="far fa-copy"></i>
                                                        </button>
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Branch</p>
                                                    <p class="text-sm">{{ selectedBank.branch }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">SWIFT Code</p>
                                                    <p class="text-sm font-mono">
                                                        {{ selectedBank.swiftCode }}
                                                        <button 
                                                            type="button" 
                                                            @click="copyToClipboard(selectedBank.swiftCode)"
                                                            class="ml-2 p-1 text-gray-400 hover:text-green-600"
                                                            title="Copy to clipboard"
                                                        >
                                                            <i class="far fa-copy"></i>
                                                        </button>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cryptocurrency Wallet Addresses -->
                                <div v-else-if="form.payment_method === 'crypto'" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Select Cryptocurrency <span class="text-red-500">*</span>
                                            </label>
                                            <select 
                                                v-model="form.selected_crypto"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            >
                                                <option v-for="crypto in cryptoWallets" :key="crypto.id" :value="crypto.id">
                                                    {{ crypto.name }}
                                                </option>
                                            </select>
                                        </div>
                                        
                                        <div class="bg-white dark:bg-gray-800 p-4 rounded-md border border-gray-200 dark:border-gray-700">
                                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Send to {{ selectedCrypto.name }} Address:</h4>
                                            <div class="flex flex-col items-center space-y-4">
                                                <div class="w-32 h-32 bg-white p-2 rounded border">
                                                    <img :src="selectedCrypto.qrCode" :alt="selectedCrypto.name + ' QR Code'" class="w-full h-full">
                                                </div>
                                                <div class="w-full">
                                                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 p-2 rounded">
                                                        <span class="font-mono text-sm break-all">{{ selectedCrypto.address }}</span>
                                                        <button 
                                                            type="button" 
                                                            @click="copyToClipboard(selectedCrypto.address)"
                                                            class="ml-2 p-1 text-gray-400 hover:text-green-600"
                                                            title="Copy to clipboard"
                                                        >
                                                            <i class="far fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="w-full">
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-gray-600 dark:text-gray-300">Network:</span>
                                                        <span class="font-medium">{{ selectedCrypto.network || 'ERC-20' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Transaction Reference (Optional)
                                                </label>
                                                <div class="flex rounded-md shadow-sm">
                                                    <input 
                                                        type="text" 
                                                        v-model="form.transaction_reference"
                                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                                        placeholder="Enter reference for tracking"
                                                    >
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Include this reference when sending the transaction
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                                
                        <!-- In-Kind Donation Fields -->
                        <div v-else-if="form.donation_type === 'in_kind'" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                            
    
                        </div>
                        
                        <!-- Volunteer Donation Fields -->
                        <div v-else-if="form.donation_type === 'volunteer'" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Monetary Donation Fields -->
                            <div v-if="form.donation_type === 'monetary'" class="space-y-4">
                                <!-- Donation Amount -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                            </div>

                            <!-- Donor Information -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
                                    Your Full Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.donor_name"
                                    class="focus:ring-green-500 focus:border-green-500 block w-full px-3 py-2 sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="John Doe"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">
                                    Your Telehone Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    v-model="form.phone"
                                    class="focus:ring-green-500 focus:border-green-500 block w-full px-3 py-2 sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="+254 700 000000"
                                >
                            </div>
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
                                    <label for="terms" class="font-medium text-gray-700 dark:text-gray-300 text-left">
                                        <span class="w-full text-left">
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
import Swal from 'sweetalert2';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

// Banks data
const banks = [
    {
        id: 'equity',
        name: 'Equity Bank Kenya Limited',
        accountName: 'Forward Kenya Party',
        accountNumber: '1234567890',
        branch: 'Nairobi CBD',
        swiftCode: 'EQBLKENAXXX',
        currency: 'KES'
    },
    {
        id: 'kcb',
        name: 'KCB Bank Kenya Limited',
        accountName: 'Forward Kenya Party',
        accountNumber: '0987654321',
        branch: 'Nairobi Main Branch',
        swiftCode: 'KCBLKENXXXX',
        currency: 'KES'
    }
];

// Cryptocurrency wallets
const cryptoWallets = [
    {
        id: 'bitcoin',
        name: 'Bitcoin (BTC)',
        address: '3FZbgi29cpjq2GjdwV8eyHuJJnkLtktZc5',
        qrCode: '/images/bitcoin-qr.png'
    },
    {
        id: 'ethereum',
        name: 'Ethereum (ETH)',
        address: '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
        qrCode: '/images/ethereum-qr.png'
    },
    {
        id: 'usdt',
        name: 'Tether (USDT-ERC20)',
        address: '0x1234567890abcdef1234567890abcdef12345678',
        qrCode: '/images/usdt-qr.png'
    }
];

// Donation types
const donationTypes = [
    { value: 'monetary', label: 'Monetary', icon: 'fas fa-money-bill-wave' },
    { value: 'in_kind', label: 'In-Kind', icon: 'fas fa-box' },
    { value: 'volunteer', label: 'Volunteer', icon: 'fas fa-hands-helping' }
];

// Payment methods
const paymentMethods = [
    { 
        value: 'mobile_money', 
        label: 'Mobile Money', 
        icon: 'fas fa-mobile-alt',
        description: 'Pay via M-Pesa, Airtel Money, T-Kash',
        subtext: 'Supports M-Pesa, Airtel Money, T-Kash and other mobile money services'
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
    payment_method: 'mobile_money',
    
    // Mobile Money Fields
    mobile_money_provider: 'mpesa',
    telephone_prefix: '+254',
    telephone_number: '',
    
    // Card Fields
    card_number: '',
    card_expiry: '',
    card_cvv: '',
    card_name: '',
    
    // Donor Information
    donor_name: '',
    email: '',
    phone: '',
    
    // In-Kind Donation Fields
    in_kind_type: '',
    in_kind_description: '',
    in_kind_value: '',
    
    // Volunteer Fields
    skills: '',
    availability: '',
    
    // Other Fields
    terms: false,
    notes: '',
    estimated_value: '',
    selected_bank: 'equity',
    selected_crypto: 'bitcoin',
    transaction_reference: ''
});

const processing = ref(false);
const modalRef = ref(null);

// Computed properties
const showMonetaryFields = computed(() => form.donation_type === 'monetary');

const selectedBank = computed(() => {
    return banks.find(bank => bank.id === form.selected_bank) || banks[0];
});

const selectedCrypto = computed(() => {
    return cryptoWallets.find(crypto => crypto.id === form.selected_crypto) || cryptoWallets[0];
});

const generateReference = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = 'FKP-';
    for (let i = 0; i < 8; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    form.transaction_reference = result;
    return result;
};

// Methods
const selectDonationType = (type) => {
    form.donation_type = type;
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        alert('Copied to clipboard!');
    } catch (err) {
        console.error('Failed to copy text: ', err);
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Copied to clipboard!');
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            alert('Failed to copy. Please copy it manually.');
        }
        document.body.removeChild(textArea);
    }
};

const close = () => {
    emit('close');
};

const handleClickOutside = (event) => {
    if (modalRef.value && !modalRef.value.contains(event.target)) {
        close();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

const showToast = (type, title, message) => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: type,
        title: title,
        text: message
    });
};

const submitDonation = async () => {
    // Validate form
    if (!form.terms) {
        showToast('warning', 'Terms & Conditions', 'Please accept the terms and conditions to proceed.');
        return;
    }
    
    if (form.donation_type === 'in_kind' && !form.in_kind_type) {
        showToast('warning', 'In-Kind Donation', 'Please select the type of in-kind donation.');
        return;
    }
    
    if (form.donation_type === 'volunteer' && !form.skills) {
        showToast('warning', 'Volunteer Information', 'Please provide your skills/expertise.');
        return;
    }
    
    // Validate mobile money phone number if that's the selected payment method
    if (form.donation_type === 'monetary' && form.payment_method === 'mobile_money') {
        if (!form.telephone_number) {
            showToast('warning', 'Phone Number Required', 'Please enter your mobile money phone number.');
            return;
        }
        
        // Basic phone number validation (Kenyan numbers)
        const phoneRegex = /^[0-9]{9,10}$/;
        if (!phoneRegex.test(form.telephone_number.replace(/\s+/g, ''))) {
            showToast('warning', 'Invalid Phone Number', 'Please enter a valid Kenyan phone number (e.g., 712345678).');
            return;
        }
    }
    
    // Show loading state
    const loadingSwal = Swal.fire({
        title: 'Processing Donation',
        html: 'Please wait while we process your donation...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        // Prepare the donation data
        const donationData = {
            type: form.donation_type,
            amount: parseFloat(form.amount) || 0,
            donor_name: form.donor_name,
            email: form.email,
            phone: form.phone,
            terms: form.terms,
            ...(form.donation_type === 'monetary' && {
                is_recurring: form.is_recurring,
                payment_method: form.payment_method,
                ...(form.payment_method === 'mobile_money' && {
                    mobile_money_provider: form.mobile_money_provider,
                    telephone_number: form.telephone_prefix + form.telephone_number,
                }),
                card_last_four: form.payment_method === 'card' ? form.card_number?.slice(-4) : null,
            }),
            ...(form.donation_type === 'in_kind' && {
                in_kind_type: form.in_kind_type,
                description: form.in_kind_description || form.notes,
                estimated_value: parseFloat(form.in_kind_value || form.estimated_value) || 0
            }),
            ...(form.donation_type === 'volunteer' && {
                skills: form.skills,
                availability: form.availability,
                notes: form.notes
            })
        };
        
        // Make the API call
        const response = await axios.post('/process-donation', donationData);
        
        // Close loading state
        await loadingSwal.close();
        
        // Generate reference if not already set
        if (!form.transaction_reference) {
            generateReference();
        }

        // Show success message based on donation type
        let successTitle = 'Thank You!';
        let successHtml = `
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">${successTitle}</h3>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
        `;
        
        if (form.donation_type === 'monetary') {
            successHtml += `
                <p>Thank you for your ${form.is_recurring ? 'monthly ' : ''}donation of <span class="font-semibold">KES ${form.amount ? form.amount.toLocaleString() : ''}</span>.</p>
            `;
            
            if (form.payment_method === 'mobile_money') {
                const providerName = {
                    'mpesa': 'M-Pesa',
                    'airtel': 'Airtel Money',
                    'tkash': 'T-Kash',
                    'equity': 'Equity EazzyPay'
                }[form.mobile_money_provider] || 'Mobile Money';
                
                successHtml += `
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md text-left">
                        <p class="font-medium text-blue-800 dark:text-blue-200 mb-2">Next Steps:</p>
                        <ul class="list-disc pl-5 space-y-1 text-sm text-blue-700 dark:text-blue-300">
                            <li>Check your phone to complete the ${providerName} payment</li>
                            <li>Keep the ${providerName} confirmation message for your records</li>
                            ${form.mobile_money_provider === 'mpesa' ? '<li>Enter your M-Pesa PIN when prompted</li>' : ''}
                        </ul>
                    </div>`;
            } else if (form.payment_method === 'bank') {
                const bank = selectedBank.value;
                successHtml += `
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md text-left">
                        <p class="font-medium text-blue-800 dark:text-blue-200 mb-2">Bank Transfer Details:</p>
                        <div class="grid grid-cols-2 gap-2 text-sm text-blue-700 dark:text-blue-300">
                            <span class="font-medium">Bank:</span><span>${bank.name}</span>
                            <span class="font-medium">Account Name:</span><span>${bank.accountName}</span>
                            <span class="font-medium">Account No:</span><span>${bank.accountNumber}</span>
                            <span class="font-medium">Branch:</span><span>${bank.branch}</span>
                            <span class="font-medium">SWIFT Code:</span><span>${bank.swiftCode}</span>
                            <span class="font-medium">Reference:</span><span class="font-mono">${form.transaction_reference}</span>
                        </div>
                    </div>`;
            } else if (form.payment_method === 'crypto') {
                const crypto = selectedCrypto.value;
                successHtml += `
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md text-left">
                        <p class="font-medium text-blue-800 dark:text-blue-200 mb-2">Crypto Payment Details:</p>
                        <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                            <div><span class="font-medium">Currency:</span> ${crypto.name}</div>
                            <div class="flex items-center">
                                <span class="font-medium mr-2">Address:</span>
                                <span class="font-mono text-xs break-all">${crypto.address}</span>
                                <button 
                                    type="button" 
                                    @click="copyToClipboard('${crypto.address}')"
                                    class="ml-2 p-1 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Copy to clipboard"
                                >
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            <div><span class="font-medium">Reference:</span> <span class="font-mono">${form.transaction_reference}</span></div>
                        </div>
                    </div>`;
            }
        } else if (form.donation_type === 'in_kind') {
            successTitle = 'Donation Received!';
            successHtml = `
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${successTitle}</h3>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Thank you for your in-kind donation of <span class="font-semibold">${form.in_kind_type}</span>.</p>
                        <p class="mt-2">We'll review your donation and contact you shortly.</p>
                    </div>
                </div>
            `;
        } else if (form.donation_type === 'volunteer') {
            successTitle = 'Welcome Aboard!';
            successHtml = `
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${successTitle}</h3>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Thank you for volunteering with us!</p>
                        <p class="mt-2">Our team will review your application and get in touch soon.</p>
                    </div>
                </div>
            `;
        }
        
        successHtml += `
                </div>
                <div class="mt-6 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        <i class="fas fa-envelope mr-1"></i>
                        Please email your payment confirmation to 
                        <a href="mailto:donations@forwardkenyaparty.com?subject=Donation%20Confirmation%20${encodeURIComponent(form.transaction_reference)}" 
                           class="text-green-600 hover:underline font-medium">
                            donations@forwardkenyaparty.com
                        </a>
                        with the reference: <span class="font-mono">${form.transaction_reference}</span>
                    </p>
                </div>
            </div>
        `;
        
        // Show success message
        await Swal.fire({
            html: successHtml,
            showConfirmButton: true,
            confirmButtonText: 'Done',
            confirmButtonColor: '#10B981',
            allowOutsideClick: false,
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'px-4 py-2 text-sm font-medium rounded-md'
            }
        });
        
        // Reset form and close modal
        form.reset();
        form.donation_type = 'monetary';
        close();
        
    } catch (error) {
        console.error('Donation submission failed:', error);
        
        // Close loading state if still open
        if (Swal.isVisible()) {
            Swal.close();
        }
        
        // Show error message
        let errorMessage = 'There was an error processing your request. Please try again.';
        
        if (error.response) {
            // The request was made and the server responded with a status code
            // that falls out of the range of 2xx
            if (error.response.status === 422) {
                // Handle validation errors
                const errors = error.response.data.errors || {};
                const errorMessages = Object.values(errors).flat();
                
                // Show first error in a toast
                if (errorMessages.length > 0) {
                    showToast('error', 'Validation Error', errorMessages[0]);
                    // Don't show the error in the alert if we've shown it in a toast
                    return;
                }
                errorMessage = 'Please check your input and try again.';
            } else if (error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            } else if (error.response.status === 429) {
                // Handle rate limiting
                errorMessage = 'Too many attempts. Please try again later.';
            }
        } else if (error.request) {
            // The request was made but no response was received
            errorMessage = 'Unable to connect to the server. Please check your internet connection.';
        }
        
        await Swal.fire({
            icon: 'error',
            title: 'Donation Failed',
            text: errorMessage,
            confirmButtonText: 'Try Again',
            confirmButtonColor: '#EF4444',
            allowOutsideClick: false
        });
    }
};

// Event listeners
onMounted(() => {
    if (props.show) {
        document.addEventListener('keydown', onKeydown);
        document.addEventListener('mousedown', handleClickOutside);
    }
});

onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown);
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>