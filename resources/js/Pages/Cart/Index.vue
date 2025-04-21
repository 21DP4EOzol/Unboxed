<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import BackToTopButton from '@/Components/BackToTopButton.vue';

const props = defineProps({
    cartItems: Array,
    cartTotal: Number
});

const updateQuantityForm = useForm({
    cart_item_id: null,
    quantity: 1,
});

const removeItemForm = useForm({
    cart_item_id: null,
});

const clearCartForm = useForm({});

function updateQuantity(cartItemId, currentQuantity) {
    updateQuantityForm.cart_item_id = cartItemId;
    updateQuantityForm.quantity = currentQuantity;
    
    updateQuantityForm.post(route('cart.update'), {
        preserveScroll: true
    });
}

function removeItem(cartItemId) {
    if (confirm('Are you sure you want to remove this item?')) {
        removeItemForm.cart_item_id = cartItemId;
        
        removeItemForm.delete(route('cart.remove'), {
            preserveScroll: true
        });
    }
}

function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        clearCartForm.delete(route('cart.clear'), {
            preserveScroll: true
        });
    }
}
</script>

<template>
    <Head title="Shopping Cart" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Shopping Cart</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-coffee-800 mb-6">Your Cart</h3>
                        
                        <!-- Empty Cart State -->
                        <div v-if="cartItems.length === 0" class="text-center py-12">
                            <div class="text-coffee-600 mb-4">Your cart is empty</div>
                            <Link :href="route('home')" class="px-4 py-2 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition">
                                Continue Shopping
                            </Link>
                        </div>
                        
                        <!-- Cart Items -->
                        <div v-else>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-coffee-200">
                                            <th class="py-3 px-4 text-left text-coffee-700">Product</th>
                                            <th class="py-3 px-4 text-right text-coffee-700">Price</th>
                                            <th class="py-3 px-4 text-center text-coffee-700">Quantity</th>
                                            <th class="py-3 px-4 text-right text-coffee-700">Subtotal</th>
                                            <th class="py-3 px-4 text-center text-coffee-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in cartItems" :key="item.id" class="border-b border-coffee-100">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center">
                                                    <div class="w-16 h-16 mr-4 bg-cream-100 rounded overflow-hidden">
                                                        <img v-if="item.image" 
                                                             :src="`/storage/${item.image}`" 
                                                             alt="Product Image" 
                                                             class="w-full h-full object-cover" />
                                                    </div>
                                                    <div>
                                                        <div class="text-coffee-800 font-medium">{{ item.name }}</div>
                                                        <div v-if="item.size || item.color" class="text-sm text-coffee-600 mt-1">
                                                            <span v-if="item.size">Size: {{ item.size }}</span>
                                                            <span v-if="item.size && item.color"> | </span>
                                                            <span v-if="item.color">Color: {{ item.color }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-right text-coffee-700">${{ item.price }}</td>
                                            <td class="py-4 px-4">
                                                <div class="flex items-center justify-center">
                                                    <button 
                                                        @click="updateQuantity(item.id, Math.max(1, item.quantity - 1))"
                                                        class="w-8 h-8 flex items-center justify-center border border-coffee-300 rounded-l text-coffee-600 hover:bg-coffee-50"
                                                    >
                                                        -
                                                    </button>
                                                    <input 
                                                        type="number" 
                                                        :value="item.quantity" 
                                                        min="1" 
                                                        class="w-12 h-8 border-t border-b border-coffee-300 text-center text-coffee-700"
                                                        @change="updateQuantity(item.id, parseInt($event.target.value) || 1)"
                                                    />
                                                    <button 
                                                        @click="updateQuantity(item.id, item.quantity + 1)"
                                                        class="w-8 h-8 flex items-center justify-center border border-coffee-300 rounded-r text-coffee-600 hover:bg-coffee-50"
                                                    >
                                                        +
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-right text-coffee-800 font-bold">${{ item.subtotal.toFixed(2) }}</td>
                                            <td class="py-4 px-4 text-center">
                                                <button 
                                                    @click="removeItem(item.id)"
                                                    class="text-coffee-600 hover:text-coffee-800"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Cart Summary -->
                            <div class="mt-8 flex flex-col md:flex-row md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <button 
                                        @click="clearCart"
                                        class="px-4 py-2 border border-coffee-300 rounded-md text-coffee-700 hover:bg-coffee-50"
                                    >
                                        Clear Cart
                                    </button>
                                </div>
                                <div class="bg-cream-50 p-6 rounded-md">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-coffee-700">Subtotal:</span>
                                        <span class="text-coffee-800 font-bold">${{ cartTotal.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between mb-4">
                                        <span class="text-coffee-700">Shipping:</span>
                                        <span class="text-coffee-800">Calculated at checkout</span>
                                    </div>
                                    <div class="border-t border-coffee-200 pt-4 mb-4">
                                        <div class="flex justify-between">
                                            <span class="text-coffee-800 font-bold">Total:</span>
                                            <span class="text-coffee-800 font-bold text-xl">${{ cartTotal.toFixed(2) }}</span>
                                        </div>
                                    </div>
                                    
                                    <Link 
                                        :href="route('checkout.index')" 
                                        class="block w-full px-4 py-2 bg-coffee-600 text-white text-center rounded-md hover:bg-coffee-700 transition"
                                    >
                                        Proceed to Checkout
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <BackToTopButton />
    </AppLayout>
</template>