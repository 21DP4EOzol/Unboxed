<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    cartItems: Array,
    cartTotal: Number
});

const billingAddressSameAsShipping = ref(true);

const form = useForm({
    // Shipping information fields
    shipping_name: '',
    shipping_street: '',
    shipping_city: '',
    shipping_state: '',
    shipping_zip: '',
    shipping_country: '',
    
    // Billing information fields
    billing_name: '',
    billing_street: '',
    billing_city: '',
    billing_state: '',
    billing_zip: '',
    billing_country: '',
    
    payment_method: 'credit_card',
    notes: ''
});

// Update billing address when shipping address changes if the checkbox is checked
watch([
    form.shipping_name, 
    form.shipping_street, 
    form.shipping_city, 
    form.shipping_state, 
    form.shipping_zip, 
    form.shipping_country
], () => {
    if (billingAddressSameAsShipping.value) {
        updateBillingAddress();
    }
}, { deep: true });

// Update billing address fields to match shipping
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

// Toggle billing address fields
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

// Format shipping address for backend
const formatShippingAddress = () => {
    return `${form.shipping_name}
${form.shipping_street}
${form.shipping_city}, ${form.shipping_state} ${form.shipping_zip}
${form.shipping_country}`;
};

// Format billing address for backend
const formatBillingAddress = () => {
    return `${form.billing_name}
${form.billing_street}
${form.billing_city}, ${form.billing_state} ${form.billing_zip}
${form.billing_country}`;
};

const submitOrder = () => {
    // Process the form and format the addresses for the backend
    const formData = {
        shipping_address: formatShippingAddress(),
        billing_address: formatBillingAddress(),
        payment_method: form.payment_method,
        notes: form.notes
    };
    
    // Post the formatted data
    form.post(route('checkout.process'), formData);
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