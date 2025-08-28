<template>
  <div class="bakong-payment max-w-2xl mx-auto">
    <!-- Progress Indicator -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300', paymentStep === 'form' ? 'bg-blue-600 text-white' : 'bg-green-500 text-white']">
            <svg v-if="paymentStep !== 'form'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span v-else>1</span>
          </div>
          <span :class="['text-sm font-medium transition-colors', paymentStep === 'form' ? 'text-blue-600' : 'text-green-600']">Payment Details</span>
        </div>
        
        <div class="flex-1 mx-4">
          <div :class="['h-1 rounded-full transition-all duration-500', paymentStep === 'form' ? 'bg-gray-200' : 'bg-green-500']"></div>
        </div>
        
        <div class="flex items-center space-x-4">
          <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300', paymentStep === 'qr' ? 'bg-blue-600 text-white' : paymentStep === 'success' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500']">
            <svg v-if="paymentStep === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span v-else>2</span>
          </div>
          <span :class="['text-sm font-medium transition-colors', paymentStep === 'qr' ? 'text-blue-600' : paymentStep === 'success' ? 'text-green-600' : 'text-gray-500']">Scan & Pay</span>
        </div>
        
        <div class="flex-1 mx-4">
          <div :class="['h-1 rounded-full transition-all duration-500', paymentStep === 'success' ? 'bg-green-500' : 'bg-gray-200']"></div>
        </div>
        
        <div class="flex items-center space-x-4">
          <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300', paymentStep === 'success' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500']">
            <svg v-if="paymentStep === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span v-else>3</span>
          </div>
          <span :class="['text-sm font-medium transition-colors', paymentStep === 'success' ? 'text-green-600' : 'text-gray-500']">Complete</span>
        </div>
      </div>
    </div>

    <!-- Step 1: Payment Form -->
    <div v-if="paymentStep === 'form'" class="payment-form">
      <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 px-8 py-12 text-white">
          <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-6">
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <h2 class="text-3xl font-bold mb-3">Secure Bakong Payment</h2>
            <p class="text-blue-100 text-lg">Complete your ticket purchase safely</p>
          </div>
        </div>

        <!-- Form Content -->
        <div class="px-8 py-8">
          <form @submit.prevent="initiatePayment" class="space-y-6">
            <!-- Event Summary -->
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-6 mb-6">
              <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                Ticket Summary
              </h3>
              <div class="flex justify-between items-center">
                <div>
                  <p class="font-semibold text-gray-900">{{ event?.title }}</p>
                  <p class="text-sm text-gray-600">{{ event?.category }} • {{ event?.location }}</p>
                </div>
                <div class="text-right">
                  <p class="text-3xl font-bold text-blue-600">${{ event?.price }}</p>
                  <p class="text-sm text-gray-500">Total Amount</p>
                </div>
              </div>
            </div>

            <!-- Payment Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="merchant_name" class="block text-sm font-semibold text-gray-700 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  Merchant Name
                </label>
                <input
                  id="merchant_name"
                  v-model="paymentForm.merchant_name"
                  type="text"
                  required
                  class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 text-gray-900 placeholder-gray-400"
                  placeholder="Your business name"
                />
              </div>

              <div class="space-y-2">
                <label for="merchant_city" class="block text-sm font-semibold text-gray-700 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  City
                </label>
                <input
                  id="merchant_city"
                  v-model="paymentForm.merchant_city"
                  type="text"
                  required
                  class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 text-gray-900 placeholder-gray-400"
                  placeholder="Phnom Penh"
                />
              </div>

              <div class="space-y-2">
                <label for="bakong_account" class="block text-sm font-semibold text-gray-700 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                  </svg>
                  Bakong Account
                </label>
                <input
                  id="bakong_account"
                  v-model="paymentForm.bakong_account"
                  type="text"
                  required
                  class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 text-gray-900 placeholder-gray-400"
                  placeholder="yourname@wing"
                />
              </div>

              <div class="space-y-2">
                <label for="phone_number" class="block text-sm font-semibold text-gray-700 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                  </svg>
                  Phone Number
                </label>
                <input
                  id="phone_number"
                  v-model="paymentForm.phone_number"
                  type="tel"
                  required
                  class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 text-gray-900 placeholder-gray-400"
                  placeholder="+855123456789"
                />
              </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-xl">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-700">
                  <span class="font-semibold">Secure Payment:</span> Your transaction is protected by advanced encryption
                </p>
              </div>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-4 px-8 rounded-2xl font-bold text-lg shadow-xl hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed transform transition-all duration-200 hover:scale-105"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating Payment...
              </span>
              <span v-else class="flex items-center justify-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-2.99M12 12V9m0 0h2m0 0h2"></path>
                </svg>
                Generate QR Code
              </span>
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Step 2: QR Code Payment -->
    <div v-else-if="paymentStep === 'qr'" class="payment-qr">
      <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Mobile-First Header -->
        <div class="bg-gradient-to-br from-purple-600 via-blue-600 to-indigo-700 px-6 py-8 text-white relative overflow-hidden">
          <!-- Background Pattern -->
          <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 50%, white 1px, transparent 1px); background-size: 50px 50px;"></div>
          </div>
          
          <div class="relative text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-4 backdrop-blur-sm">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-2.99M12 12V9m0 0h2m0 0h2"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold mb-2">Scan to Pay</h2>
            <p class="text-purple-100">Use your Bakong app to complete payment</p>
            
            <!-- Timer Badge -->
            <div class="inline-flex items-center bg-red-500/20 backdrop-blur-sm rounded-full px-4 py-2 mt-4">
              <svg class="w-4 h-4 mr-2 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-sm font-bold text-red-200">{{ formatTime(timeRemaining) }} remaining</span>
            </div>
          </div>
        </div>

        <div class="px-6 py-8">
          <!-- QR Code Section -->
          <div class="text-center mb-8">
            <!-- QR Code with Phone Frame -->
            <div class="relative inline-block">
              <!-- Phone Frame -->
              <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-[2.5rem] p-2 shadow-2xl phone-frame">
                <div class="bg-black rounded-[2rem] p-6 relative">
                  <!-- QR Code -->
                  <div class="bg-white rounded-2xl p-6 shadow-inner">
                    <div id="qr-code" class="mx-auto"></div>
                  </div>
                  
                  <!-- Phone Details -->
                  <div class="mt-4 text-center">
                    <div class="bg-gray-800 rounded-xl p-3">
                      <p class="text-xs text-gray-300 mb-1">Transaction ID</p>
                      <p class="text-sm font-mono text-white">{{ transactionId }}</p>
                    </div>
                  </div>
                </div>
                
                <!-- Phone Notch -->
                <div class="absolute top-4 left-1/2 transform -translate-x-1/2 w-20 h-6 bg-black rounded-full"></div>
              </div>
              
              <!-- Floating Amount Badge -->
              <div class="absolute -top-4 -right-4 bg-green-500 text-white rounded-2xl px-4 py-2 shadow-xl transform rotate-12 floating-badge">
                <div class="text-center">
                  <p class="text-xs font-medium">Amount</p>
                  <p class="text-lg font-bold">${{ event?.price }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Steps -->
          <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="text-center">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2 step-icon">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Open App</p>
              </div>
              
              <div class="text-center">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2 step-icon">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-2.99M12 12V9m0 0h2m0 0h2"></path>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Scan QR</p>
              </div>
              
              <div class="text-center">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2 step-icon">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Confirm</p>
              </div>
              
              <div class="text-center">
                <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-2 step-icon">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Get Ticket</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="space-y-3">
            <button
              @click="checkPaymentStatus"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-4 px-6 rounded-2xl font-bold text-lg shadow-lg hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-4 focus:ring-green-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <div class="w-6 h-6 border-3 border-white border-t-transparent rounded-full animate-spin mr-3"></div>
                Checking Payment...
              </span>
              <span v-else class="flex items-center justify-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Payment Completed ✨
              </span>
            </button>
            
            <button
              @click="cancelPayment"
              class="w-full bg-gray-50 text-gray-600 py-3 px-6 rounded-2xl font-semibold border-2 border-gray-200 hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-200 transition-all duration-200"
            >
              <span class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel Payment
              </span>
            </button>
          </div>

          <!-- Trust Indicators -->
          <div class="mt-8 flex items-center justify-center space-x-6 text-xs text-gray-500">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              Secure
            </div>
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              Fast
            </div>
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
              Protected
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 3: Success State -->
    <div v-else-if="paymentStep === 'success'" class="payment-success">
      <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Success Header -->
        <div class="bg-gradient-to-br from-green-500 via-emerald-500 to-teal-600 px-6 py-12 text-white relative overflow-hidden">
          <!-- Confetti Animation Background -->
          <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px), radial-gradient(circle at 75% 75%, white 2px, transparent 2px); background-size: 60px 60px; animation: float 6s ease-in-out infinite;"></div>
          </div>
          
          <div class="relative text-center">
            <!-- Success Icon with Animation -->
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white/20 rounded-full mb-6 backdrop-blur-sm animate-bounce">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <h2 class="text-3xl font-bold mb-3">Payment Successful! 🎉</h2>
            <p class="text-green-100 text-lg">Your ticket has been generated</p>
          </div>
        </div>

        <div class="px-6 py-8">
          <!-- Success Details -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 mb-6">
            <div class="text-center">
              <div class="inline-flex items-center bg-green-100 rounded-full px-4 py-2 mb-4">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <span class="text-sm font-semibold text-green-800">Ticket Generated</span>
              </div>
              
              <h3 class="text-xl font-bold text-gray-900 mb-2">{{ event?.title }}</h3>
              <p class="text-gray-600 mb-4">{{ event?.category }} • {{ event?.location }}</p>
              
              <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Amount Paid</p>
                <p class="text-2xl font-bold text-green-600">${{ event?.price }}</p>
              </div>
            </div>
          </div>

          <!-- Action Button -->
          <button
            @click="$emit('payment-success', ticket)"
            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-2xl font-bold text-lg shadow-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105"
          >
            <span class="flex items-center justify-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
              </svg>
              View My Ticket
            </span>
          </button>

          <!-- Success Features -->
          <div class="mt-6 grid grid-cols-3 gap-4 text-center">
            <div>
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <p class="text-xs font-semibold text-gray-600">Verified</p>
            </div>
            
            <div>
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-2.99M12 12V9m0 0h2m0 0h2"></path>
                </svg>
              </div>
              <p class="text-xs font-semibold text-gray-600">QR Ready</p>
            </div>
            
            <div>
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 6h6m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v4m-6 0a2 2 0 002 2h2a2 2 0 002-2"></path>
                </svg>
              </div>
              <p class="text-xs font-semibold text-gray-600">Secured</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="paymentStep === 'error'" class="payment-error">
      <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Error Header -->
        <div class="bg-gradient-to-br from-red-500 via-pink-500 to-rose-600 px-6 py-12 text-white relative overflow-hidden">
          <div class="relative text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-6 backdrop-blur-sm">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold mb-3">Payment Failed</h2>
            <p class="text-red-100">Don't worry, let's try again</p>
          </div>
        </div>

        <div class="px-6 py-8">
          <!-- Error Message -->
          <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
            <div class="text-center">
              <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-red-800 mb-2">What happened?</h3>
              <p class="text-red-700">{{ errorMessage }}</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="space-y-3">
            <button
              @click="resetPayment"
              class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-4 px-6 rounded-2xl font-bold text-lg shadow-xl hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105"
            >
              <span class="flex items-center justify-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Try Again
              </span>
            </button>
            
            <button
              @click="$emit('payment-cancelled')"
              class="w-full bg-gray-50 text-gray-600 py-3 px-6 rounded-2xl font-semibold border-2 border-gray-200 hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-200 transition-all duration-200"
            >
              Cancel & Go Back
            </button>
          </div>

          <!-- Help Section -->
          <div class="mt-6 text-center">
            <p class="text-sm text-gray-500 mb-2">Need help?</p>
            <div class="flex items-center justify-center space-x-4 text-xs text-gray-400">
              <span>📧 Support</span>
              <span>💬 Live Chat</span>
              <span>📞 Call Us</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { BakongKHQR, MerchantInfo, khqrData } from 'bakong-khqr'
import QRCode from 'qrcode'
import axios from 'axios'

interface Event {
  id: number
  title: string
  price: number
  [key: string]: any
}

interface Ticket {
  id: number
  ticket_number: string
  [key: string]: any
}

const props = defineProps<{
  event: Event
}>()

const emit = defineEmits<{
  'payment-success': [ticket: Ticket]
  'payment-cancelled': []
}>()

const paymentStep = ref<'form' | 'qr' | 'success' | 'error'>('form')
const loading = ref(false)
const errorMessage = ref('')
const transactionId = ref('')
const timeRemaining = ref(900) // 15 minutes in seconds
const ticket = ref<Ticket | null>(null)

let countdownInterval: NodeJS.Timeout | null = null
let statusCheckInterval: NodeJS.Timeout | null = null

const paymentForm = ref({
  merchant_name: '',
  merchant_city: 'Phnom Penh',
  bakong_account: '',
  phone_number: ''
})

const initiatePayment = async () => {
  loading.value = true
  errorMessage.value = ''

  console.log('Initiating payment with data:', {
    event_id: props.event.id,
    ...paymentForm.value
  })

  try {
    const response = await axios.post('/api/payments/initiate', {
      event_id: props.event.id,
      ...paymentForm.value
    })

    console.log('Payment response:', response.data)

    if (response.data.status === 'success') {
      // Use the transaction_id from payment_data, not purchase_id
      transactionId.value = response.data.data.payment_data.transaction_id
      await generateQRCode(response.data.data.payment_data)
      paymentStep.value = 'qr'
      startCountdown()
      startStatusChecking()
    }
  } catch (error: any) {
    console.error('Payment initiation error:', error)
    console.error('Error response:', error.response?.data)
    errorMessage.value = error.response?.data?.message || error.message || 'Failed to initiate payment'
    paymentStep.value = 'error'
  } finally {
    loading.value = false
  }
}

const generateQRCode = async (paymentData: any) => {
  try {
    const merchantInfo = new MerchantInfo(
      paymentData.bakong_account,
      paymentData.merchant_name,
      paymentData.merchant_city,
      Date.now(),
      'BAKONGKHPPXXX',
      {
        currency: khqrData.currency.usd,
        amount: Math.round(paymentData.amount * 100), // Convert to cents
        billNumber: paymentData.bill_number,
        mobileNumber: paymentData.phone_number,
        storeLabel: paymentData.store_label,
        terminalLabel: paymentData.terminal_label,
        expirationTimestamp: paymentData.expiration_time * 1000
      }
    )

    const khqr = new BakongKHQR()
    const qrString = khqr.generateMerchant(merchantInfo)

    await nextTick()
    const qrElement = document.getElementById('qr-code')
    if (qrElement) {
      qrElement.innerHTML = ''
      await QRCode.toCanvas(qrElement, qrString, {
        width: 256,
        margin: 2
      })
    }
  } catch (error) {
    console.error('QR Code generation failed:', error)
    errorMessage.value = 'Failed to generate QR code'
    paymentStep.value = 'error'
  }
}

const checkPaymentStatus = async () => {
  if (!transactionId.value) return

  console.log('Checking payment status for transaction:', transactionId.value)
  
  loading.value = true
  try {
    const response = await axios.post('/api/payments/status', {
      transaction_id: transactionId.value
    })
    
    if (response.data.status === 'success') {
      const paymentStatus = response.data.data.payment_status
      
      if (paymentStatus === 'completed') {
        ticket.value = response.data.data.ticket
        paymentStep.value = 'success'
        stopCountdown()
        stopStatusChecking()
      } else if (paymentStatus === 'expired' || paymentStatus === 'cancelled') {
        errorMessage.value = 'Payment has expired or been cancelled'
        paymentStep.value = 'error'
        stopCountdown()
        stopStatusChecking()
      }
    }
  } catch (error: any) {
    console.error('Status check failed:', error)
  } finally {
    loading.value = false
  }
}

const cancelPayment = async () => {
  if (!transactionId.value) return

  try {
    await axios.post('/api/payments/cancel', {
      transaction_id: transactionId.value
    })
    
    paymentStep.value = 'form'
    transactionId.value = ''
    stopCountdown()
    stopStatusChecking()
    emit('payment-cancelled')
  } catch (error) {
    console.error('Cancel payment failed:', error)
  }
}

const resetPayment = () => {
  paymentStep.value = 'form'
  errorMessage.value = ''
  transactionId.value = ''
  timeRemaining.value = 900
  stopCountdown()
  stopStatusChecking()
}

const startCountdown = () => {
  countdownInterval = setInterval(() => {
    timeRemaining.value--
    if (timeRemaining.value <= 0) {
      errorMessage.value = 'Payment has expired'
      paymentStep.value = 'error'
      stopCountdown()
      stopStatusChecking()
    }
  }, 1000)
}

const stopCountdown = () => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
    countdownInterval = null
  }
}

const startStatusChecking = () => {
  statusCheckInterval = setInterval(checkPaymentStatus, 10000) // Check every 10 seconds
}

const stopStatusChecking = () => {
  if (statusCheckInterval) {
    clearInterval(statusCheckInterval)
    statusCheckInterval = null
  }
}

const formatTime = (seconds: number): string => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

onUnmounted(() => {
  stopCountdown()
  stopStatusChecking()
})
</script>

<style scoped>
.bakong-payment {
  min-height: 400px;
}

.payment-form, .payment-qr, .payment-success, .payment-error {
  animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(40px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-10px) rotate(2deg);
  }
}

@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
  }
  50% {
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.6);
  }
}

/* QR Code Container Glow */
#qr-code {
  animation: pulse-glow 3s ease-in-out infinite;
}

/* Progress Bar Animation */
.progress-bar {
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Button Hover Effects */
button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Phone Frame Realistic Shadow */
.phone-frame {
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.05);
}

/* Floating Animation for Amount Badge */
.floating-badge {
  animation: float 4s ease-in-out infinite;
}

/* Success Confetti Animation */
@keyframes confetti {
  0% {
    transform: translateY(0) rotateZ(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(-100px) rotateZ(720deg);
    opacity: 0;
  }
}

/* Loading Spinner Enhancement */
.custom-spinner {
  border-width: 3px;
  border-style: solid;
  border-color: transparent;
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Card Entrance Animation */
.card-entrance {
  animation: cardSlide 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes cardSlide {
  0% {
    opacity: 0;
    transform: translateY(50px) rotateX(10deg);
  }
  100% {
    opacity: 1;
    transform: translateY(0) rotateX(0deg);
  }
}

/* Step Icon Animation */
.step-icon {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.step-icon:hover {
  transform: scale(1.1) rotate(5deg);
}

/* Countdown Timer */
.countdown-timer {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

/* Responsive Adjustments */
@media (max-width: 640px) {
  .phone-frame {
    transform: scale(0.9);
  }
  
  .floating-badge {
    transform: scale(0.8) rotate(8deg);
  }
}
</style>
