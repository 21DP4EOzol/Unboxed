<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    cartItems: Array,
    cartTotal: Number
});

const billingAddressSameAsShipping = ref(true);

const form = useForm({
    shipping_address: '',
    billing_address: '',
    payment_method: 'credit_card',
    notes: ''
});

// Update billing address when shipping address changes if the checkbox is checked
const updateBillingAddress = () => {
    if (billingAddressSameAsShipping.value) {
        form.billing_address = form.shipping_address;
    }
};

// Toggle billing address field
const toggleBillingAddress = () => {
    if (billingAddressSameAsShipping.value) {
        form.billing_address = form.shipping_address;
    } else {
        form.billing_address = '';
    }
};

const submitOrder = () => {
    form.post(route('checkout.process'));
};
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
                                
                                <div class="mb-4">
                                    <InputLabel for="shipping_address" value="Shipping Address" />
                                    <textarea 
                                        id="shipping_address" 
                                        v-model="form.shipping_address" 
                                        class="mt-1 block w-full border-gray-300 focus:border-coffee-500 focus:ring-coffee-500 rounded-md shadow-sm" 
                                        rows="4"
                                        @input="updateBillingAddress"
                                        required
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.shipping_address" />
                                    <p class="mt-1 text-sm text-coffee-500">Please enter your full shipping address including name, street, city, state, zip, and country.</p>
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
                                
                                <div v-if="!billingAddressSameAsShipping" class="mb-4">
                                    <InputLabel for="billing_address" value="Billing Address" />
                                    <textarea 
                                        id="billing_address" 
                                        v-model="form.billing_address" 
                                        class="mt-1 block w-full border-gray-300 focus:border-coffee-500 focus:ring-coffee-500 rounded-md shadow-sm" 
                                        rows="4"
                                        required
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.billing_address" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-coffee-800 mb-4">Payment Information</h3>
                                
                                <div class="mb-4">
                                    <InputLabel for="payment_method" value="Payment Method" />
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center">
                                            <input 
                                                type="radio" 
                                                id="credit_card" 
                                                value="credit_card" 
                                                v-model="form.payment_method"
                                                class="rounded-full border-gray-300 text-coffee-600 shadow-sm focus:ring-coffee-500"
                                            />
                                            <label for="credit_card" class="ml-2 text-coffee-700">Credit Card</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input 
                                                type="radio" 
                                                id="paypal" 
                                                value="paypal" 
                                                v-model="form.payment_method"
                                                class="rounded-full border-gray-300 text-coffee-600 shadow-sm focus:ring-coffee-500"
                                            />
                                            <label for="paypal" class="ml-2 text-coffee-700">PayPal</label>
                                        </div>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.payment_method" />
                                </div>
                                
                                <!-- Credit Card Fields (simplified for demo) -->
                                <div v-if="form.payment_method === 'credit_card'" class="border p-4 rounded-md border-coffee-200 bg-cream-50">
                                    <p class="text-sm text-center text-coffee-500 italic mb-3">
                                        Note: This is a demo application. No actual payment will be processed.
                                    </p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <InputLabel for="card_number" value="Card Number" />
                                            <TextInput 
                                                id="card_number" 
                                                type="text" 
                                                class="mt-1 block w-full" 
                                                placeholder="1234 5678 9012 3456"
                                                disabled
                                            />
                                        </div>
                                        <div>
                                            <InputLabel for="card_name" value="Name on Card" />
                                            <TextInput 
                                                id="card_name" 
                                                type="text" 
                                                class="mt-1 block w-full" 
                                                placeholder="John Doe"
                                                disabled
                                            />
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="expiry" value="Expiry Date" />
                                            <TextInput 
                                                id="expiry" 
                                                type="text" 
                                                class="mt-1 block w-full" 
                                                placeholder="MM/YY"
                                                disabled
                                            />
                                        </div>
                                        <div>
                                            <InputLabel for="cvv" value="CVV" />
                                            <TextInput 
                                                id="cvv" 
                                                type="text" 
                                                class="mt-1 block w-full" 
                                                placeholder="123"
                                                disabled
                                            />
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- PayPal info (simplified for demo) -->
                                <div v-if="form.payment_method === 'paypal'" class="border p-4 rounded-md border-coffee-200 bg-cream-50">
                                    <p class="text-sm text-center text-coffee-500 italic">
                                        Note: This is a demo application. You will be redirected to PayPal in a real application.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Notes -->
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
                                        :disabled="form.processing"
                                        @click="submitOrder"
                                    >
                                        Place Order
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