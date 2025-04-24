<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    cartItems: Array,
    cartTotal: Number
});

const billingAddressSameAsShipping = ref(true);
const isProcessing = ref(false);
const paymentStatus = ref('');
const paymentError = ref('');
const stripe = ref(null);
const elements = ref(null);
const card = ref(null);
const cardError = ref('');
const clientSecret = ref('');

const form = useForm({
    // Your existing form fields
    shipping_name: '',
    shipping_street: '',
    shipping_city: '',
    shipping_state: '',
    shipping_zip: '',
    shipping_country: '',
    
    billing_name: '',
    billing_street: '',
    billing_city: '',
    billing_state: '',
    billing_zip: '',
    billing_country: '',
    
    payment_method: 'credit_card',
    payment_intent_id: '', // Added for Stripe
    notes: ''
});

// Initialize Stripe on component mount
onMounted(() => {

    console.log("Component mounted");
    console.log("VITE_STRIPE_KEY value:", import.meta.env.VITE_STRIPE_KEY);
    console.log("Is Stripe available:", typeof window.Stripe);

    // Check if Stripe is available in the window object
    if (window.Stripe) {
        // Initialize Stripe with your publishable key
        stripe.value = window.Stripe(import.meta.env.VITE_STRIPE_KEY);
        
        // Create a payment intent when the page loads
        createPaymentIntent();
    } else {
        // If Stripe is not available, wait for it to load
        console.error("Stripe.js not loaded. Waiting for it to load...");
        
        // Try again after a short delay
        setTimeout(() => {
            if (window.Stripe) {
                stripe.value = window.Stripe(import.meta.env.VITE_STRIPE_KEY);
                createPaymentIntent();
            } else {
                paymentError.value = "Failed to load Stripe. Please refresh the page and try again.";
            }
        }, 1000);
    }
});

// Create a payment intent
const createPaymentIntent = async () => {

    console.log("Creating payment intent");
    console.log("Stripe initialized:", !!stripe.value);


    try {
        if (!stripe.value) {
            paymentError.value = 'Stripe is not initialized. Please refresh the page.';
            return;
        }
        
        const response = await axios.post(route('checkout.payment-intent'));
        clientSecret.value = response.data.clientSecret;
        
        // Initialize Stripe Elements
        elements.value = stripe.value.elements({
            clientSecret: clientSecret.value
        });
        
        // Create card element
        card.value = elements.value.create('card');
        
        // Mount the card element to the DOM
        setTimeout(() => {
            const cardElement = document.getElementById('card-element');
            if (cardElement) {
                try {
                    card.value.mount('#card-element');
                    
                    // Handle card validation errors
                    card.value.on('change', (event) => {
                        cardError.value = event.error ? event.error.message : '';
                    });
                } catch (mountError) {
                    console.error('Error mounting card element:', mountError);
                    paymentError.value = 'Failed to load payment form. Please refresh the page.';
                }
            } else {
                console.error('Card element not found in DOM');
                paymentError.value = 'Payment form not found. Please refresh the page.';
            }
        }, 300); // Increased timeout to ensure DOM is ready
    } catch (error) {
        paymentError.value = 'Failed to initialize payment system. Please try again later.';
        console.error('Payment intent creation error:', error);
    }
};

// Keep your existing address functions
const updateBillingAddress = () => {
    if (billingAddressSameAsShipping.value) {
        form.billing_name = form.shipping_name;
        form.billing_street = form.shipping_street;
        form.billing_city = form.shipping_city;
        form.billing_state = form.shipping_state;
        form.billing_zip = form.shipping_zip;
        form.billing_country = form.shipping_country;
    }
};

const toggleBillingAddress = () => {
    if (billingAddressSameAsShipping.value) {
        updateBillingAddress();
    } else {
        form.billing_name = '';
        form.billing_street = '';
        form.billing_city = '';
        form.billing_state = '';
        form.billing_zip = '';
        form.billing_country = '';
    }
};

// Format shipping address
const formatShippingAddress = () => {
    return `${form.shipping_name}
${form.shipping_street}
${form.shipping_city}, ${form.shipping_state} ${form.shipping_zip}
${form.shipping_country}`;
};

// Format billing address
const formatBillingAddress = () => {
    return `${form.billing_name}
${form.billing_street}
${form.billing_city}, ${form.billing_state} ${form.billing_zip}
${form.billing_country}`;
};

// Update the submitOrder function to process the payment with Stripe
const submitOrder = async () => {

    console.log("Submit order called");
    console.log("Stripe state:", {
        stripeInitialized: !!stripe.value,
        cardInitialized: !!card.value,
        hasClientSecret: !!clientSecret.value
    });

    
    if (isProcessing.value) return;
    
    // Basic validation
    if (!form.shipping_name || !form.shipping_street || !form.shipping_city || 
        !form.shipping_state || !form.shipping_zip || !form.shipping_country) {
        alert('Please fill out all shipping information fields');
        return;
    }
    
    if (!billingAddressSameAsShipping.value && 
        (!form.billing_name || !form.billing_street || !form.billing_city || 
         !form.billing_state || !form.billing_zip || !form.billing_country)) {
        alert('Please fill out all billing information fields');
        return;
    }
    
    // Check if Stripe and card are properly initialized
    if (!stripe.value || !card.value || !clientSecret.value) {
        paymentError.value = 'Payment system is not fully initialized. Please refresh the page and try again.';
        console.error('Stripe not initialized properly:', { 
            stripeInitialized: !!stripe.value, 
            cardInitialized: !!card.value, 
            hasClientSecret: !!clientSecret.value 
        });
        return;
    }
    
    isProcessing.value = true;
    paymentStatus.value = 'Processing payment...';
    paymentError.value = ''; // Clear any previous errors
    
    try {
        // Confirm card payment with Stripe
        const { paymentIntent, error } = await stripe.value.confirmCardPayment(
            clientSecret.value, {
                payment_method: {
                    card: card.value,
                    billing_details: {
                        name: form.billing_name || form.shipping_name,
                        address: {
                            line1: form.billing_street || form.shipping_street,
                            city: form.billing_city || form.shipping_city,
                            state: form.billing_state || form.shipping_state,
                            postal_code: form.billing_zip || form.shipping_zip,
                            country: form.billing_country || form.shipping_country,
                        }
                    }
                }
            }
        );
        
        if (error) {
            // Show error to your customer
            paymentError.value = error.message;
            isProcessing.value = false;
            return;
        }
        
        if (paymentIntent.status === 'succeeded') {
            // The payment was successful
            paymentStatus.value = 'Payment successful!';
            
            // Add the payment intent ID to the form
            form.payment_intent_id = paymentIntent.id;
            
            // Process the form and format the addresses for the backend
            const formData = {
                shipping_address: formatShippingAddress(),
                billing_address: formatBillingAddress(),
                payment_method: form.payment_method,
                payment_intent_id: form.payment_intent_id,
                notes: form.notes
            };
            
            // Post the formatted data
            form.post(route('checkout.process'), formData);
        } else {
            // Handle other payment intent statuses
            paymentError.value = `Payment status: ${paymentIntent.status}. Please try again.`;
            isProcessing.value = false;
        }
    } catch (error) {
        paymentError.value = 'An error occurred during payment processing. Please try again.';
        console.error('Payment processing error:', error);
        isProcessing.value = false;
    }
};

// Watch for shipping address changes
watch([
    () => form.shipping_name, 
    () => form.shipping_street, 
    () => form.shipping_city, 
    () => form.shipping_state, 
    () => form.shipping_zip, 
    () => form.shipping_country
], () => {
    if (billingAddressSameAsShipping.value) {
        updateBillingAddress();
    }
});
</script>

<template>
    <Head title="Checkout" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Checkout</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Checkout Form -->
                    <div class="md:col-span-2 space-y-6">
                        <!-- Shipping Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-coffee-800 mb-4">Shipping Information</h3>
                                
                                <!-- Full Name -->
                                <div class="mb-4">
                                    <InputLabel for="shipping_name" value="Full Name" />
                                    <TextInput
                                        id="shipping_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.shipping_name"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.shipping_name" />
                                </div>
                                
                                <!-- Street Address -->
                                <div class="mb-4">
                                    <InputLabel for="shipping_street" value="Street Address" />
                                    <TextInput
                                        id="shipping_street"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.shipping_street"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.shipping_street" />
                                </div>
                                
                                <!-- City, State, Zip Code (in a grid) -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <InputLabel for="shipping_city" value="City" />
                                        <TextInput
                                            id="shipping_city"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.shipping_city"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.shipping_city" />
                                    </div>
                                    
                                    <div>
                                        <InputLabel for="shipping_state" value="State/Province" />
                                        <TextInput
                                            id="shipping_state"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.shipping_state"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.shipping_state" />
                                    </div>
                                    
                                    <div>
                                        <InputLabel for="shipping_zip" value="Zip/Postal Code" />
                                        <TextInput
                                            id="shipping_zip"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.shipping_zip"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.shipping_zip" />
                                    </div>
                                </div>
                                
                                <!-- Country -->
                                <div class="mb-4">
                                    <InputLabel for="shipping_country" value="Country" />
                                    <TextInput
                                        id="shipping_country"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.shipping_country"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.shipping_country" />
                                </div>
                                
                                <div class="flex items-center mb-4">
                                    <input 
                                        type="checkbox" 
                                        id="same_address" 
                                        v-model="billingAddressSameAsShipping"
                                        @change="toggleBillingAddress"
                                        class="rounded border-gray-300 text-coffee-600 shadow-sm focus:ring-coffee-500"
                                    />
                                    <label for="same_address" class="ml-2 text-coffee-700">Billing address same as shipping</label>
                                </div>
                                
                                <!-- Billing Address Section (shown when checkbox is unchecked) -->
                                <div v-if="!billingAddressSameAsShipping">
                                    <h4 class="text-md font-semibold text-coffee-700 mt-4 mb-2">Billing Information</h4>
                                    
                                    <!-- Full Name -->
                                    <div class="mb-4">
                                        <InputLabel for="billing_name" value="Full Name" />
                                        <TextInput
                                            id="billing_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.billing_name"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.billing_name" />
                                    </div>
                                    
                                    <!-- Street Address -->
                                    <div class="mb-4">
                                        <InputLabel for="billing_street" value="Street Address" />
                                        <TextInput
                                            id="billing_street"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.billing_street"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.billing_street" />
                                    </div>
                                    
                                    <!-- City, State, Zip Code -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <InputLabel for="billing_city" value="City" />
                                            <TextInput
                                                id="billing_city"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.billing_city"
                                                required
                                            />
                                            <InputError class="mt-2" :message="form.errors.billing_city" />
                                        </div>
                                        
                                        <div>
                                            <InputLabel for="billing_state" value="State/Province" />
                                            <TextInput
                                                id="billing_state"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.billing_state"
                                                required
                                            />
                                            <InputError class="mt-2" :message="form.errors.billing_state" />
                                        </div>
                                        
                                        <div>
                                            <InputLabel for="billing_zip" value="Zip/Postal Code" />
                                            <TextInput
                                                id="billing_zip"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.billing_zip"
                                                required
                                            />
                                            <InputError class="mt-2" :message="form.errors.billing_zip" />
                                        </div>
                                    </div>
                                    
                                    <!-- Country -->
                                    <div class="mb-4">
                                        <InputLabel for="billing_country" value="Country" />
                                        <TextInput
                                            id="billing_country"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.billing_country"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.billing_country" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Information - Updated to use Stripe -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-coffee-800 mb-4">Payment Information</h3>
                                
                                <!-- Stripe Card Element -->
                                <div class="mt-4 p-4 border rounded-md border-coffee-200 bg-cream-50">
                                    <p class="text-sm text-coffee-600 mb-4">
                                        Enter your card details below. Your information is securely processed by Stripe.
                                    </p>
                                    <div id="card-element" class="p-3 border border-coffee-300 rounded-md bg-white"></div>
                                    
                                    <!-- Display card errors -->
                                    <div v-if="cardError" class="mt-2 text-red-600 text-sm">{{ cardError }}</div>
                                    
                                    <!-- Display payment processing status -->
                                    <div v-if="paymentStatus" class="mt-2 text-coffee-600 text-sm">{{ paymentStatus }}</div>
                                    
                                    <!-- Display payment errors -->
                                    <div v-if="paymentError" class="mt-2 text-red-600 text-sm">{{ paymentError }}</div>
                                    
                                    <div class="mt-4 text-sm text-coffee-500">
                                        <p>Test cards (use any future expiry date and any 3-digit CVC):</p>
                                        <ul class="ml-5 list-disc">
                                            <li>Success: 4242 4242 4242 4242</li>
                                            <li>Requires authentication: 4000 0025 0000 3155</li>
                                            <li>Declined: 4000 0000 0000 0002</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Notes (keep your existing implementation) -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-coffee-800 mb-4">Order Notes (Optional)</h3>
                                
                                <div>
                                    <textarea 
                                        id="notes" 
                                        v-model="form.notes" 
                                        class="mt-1 block w-full border-gray-300 focus:border-coffee-500 focus:ring-coffee-500 rounded-md shadow-sm" 
                                        rows="3"
                                        placeholder="Any special instructions for your order"
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.notes" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200 sticky top-6">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-coffee-800 mb-4">Order Summary</h3>
                                
                                <!-- Item List -->
                                <div class="divide-y divide-coffee-100 mb-4">
                                    <div v-for="item in cartItems" :key="item.id" class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-cream-100 rounded-md overflow-hidden mr-3">
                                                <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name" class="w-full h-full object-cover" />
                                            </div>
                                            <div>
                                                <p class="text-coffee-800 font-medium">{{ item.name }}</p>
                                                <p class="text-coffee-500 text-sm">Qty: {{ item.quantity }}</p>
                                                <div v-if="item.size || item.color" class="text-coffee-500 text-sm">
                                                    <span v-if="item.size">Size: {{ item.size }}</span>
                                                    <span v-if="item.size && item.color"> | </span>
                                                    <span v-if="item.color">Color: {{ item.color }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-coffee-800 font-medium">${{ item.subtotal.toFixed(2) }}</p>
                                    </div>
                                </div>
                                
                                <!-- Total -->
                                <div class="border-t border-coffee-200 pt-4">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-coffee-600">Subtotal:</span>
                                        <span class="text-coffee-800 font-medium">${{ cartTotal.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-coffee-600">Shipping:</span>
                                        <span class="text-coffee-800 font-medium">$0.00</span>
                                    </div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-coffee-600">Tax:</span>
                                        <span class="text-coffee-800 font-medium">$0.00</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-coffee-200">
                                        <span class="text-coffee-800 font-bold">Total:</span>
                                        <span class="text-coffee-800 font-bold text-xl">${{ cartTotal.toFixed(2) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Place Order Button -->
                                <div class="mt-6">
                                    <PrimaryButton
                                        class="w-full py-3 justify-center"
                                        :disabled="form.processing || isProcessing"
                                        @click="submitOrder"
                                    >
                                        {{ isProcessing ? 'Processing...' : 'Place Order' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>