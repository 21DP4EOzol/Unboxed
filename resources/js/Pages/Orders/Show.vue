<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    order: Object
});

// Function to determine status badge color
const getStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        case 'shipped':
            return 'bg-indigo-100 text-indigo-800';
        case 'delivered':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head :title="`Order #${order.order_number}`" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-coffee-800 leading-tight">
                    Order #{{ order.order_number }}
                </h2>
                <Link :href="route('orders.index')" class="px-4 py-2 bg-coffee-200 text-coffee-800 rounded hover:bg-coffee-300 transition">
                    Back to Orders
                </Link>
            </div>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Order Status Banner -->
                <div class="mb-6 p-4 rounded-lg" :class="getStatusClass(order.status)">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">
                            Order Status: {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                        </span>
                    </div>
                </div>
                
                <!-- Order Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200 mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Order Information</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Order Number:</span>
                                        <span class="text-coffee-800 font-medium">{{ order.order_number }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Date:</span>
                                        <span class="text-coffee-800">{{ new Date(order.created_at).toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Status:</span>
                                        <span class="px-2 py-1 rounded text-xs" :class="getStatusClass(order.status)">
                                            {{ order.status }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Payment Method:</span>
                                        <span class="text-coffee-800">{{ order.payment_method === 'credit_card' ? 'Credit Card' : 'PayPal' }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Payment Status:</span>
                                        <span class="px-2 py-1 rounded text-xs" :class="{
                                            'bg-green-100 text-green-800': order.payment_status === 'paid',
                                            'bg-yellow-100 text-yellow-800': order.payment_status === 'pending',
                                            'bg-red-100 text-red-800': order.payment_status === 'failed'
                                        }">
                                            {{ order.payment_status }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Total Amount:</span>
                                        <span class="text-coffee-800 font-bold">€{{ order.total_amount }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Shipping Information</h3>
                                <div class="p-3 bg-cream-50 rounded border border-coffee-100 mb-4">
                                    <div v-html="order.shipping_address.replace(/\n/g, '<br>')"></div>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Billing Information</h3>
                                <div class="p-3 bg-cream-50 rounded border border-coffee-100">
                                    <div v-html="order.billing_address.replace(/\n/g, '<br>')"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200 mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-coffee-800 mb-4">Order Items</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Product</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-center text-coffee-700">Price</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-center text-coffee-700">Quantity</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-right text-coffee-700">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in order.items" :key="item.id">
                                        <td class="py-4 px-4 border-b border-coffee-100">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 mr-4 bg-cream-100 rounded overflow-hidden">
                                                    <img 
                                                        v-if="item.product && item.product.images && item.product.images.length > 0" 
                                                        :src="`/storage/${item.product.images[0]}`" 
                                                        :alt="item.product ? item.product.name : 'Product'" 
                                                        class="w-full h-full object-cover"
                                                    />
                                                </div>
                                                <div>
                                                    <div class="font-medium text-coffee-800">
                                                        {{ item.product ? item.product.name : 'Product no longer available' }}
                                                    </div>
                                                    <div v-if="item.product" class="text-xs text-coffee-500">
                                                        SKU: {{ item.product.sku }}
                                                    </div>
                                                    <div v-if="item.size || item.color" class="text-xs text-coffee-500 mt-1">
                                                        <span v-if="item.size">Size: {{ item.size }}</span>
                                                        <span v-if="item.size && item.color"> | </span>
                                                        <span v-if="item.color">Color: {{ item.color }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 border-b border-coffee-100 text-center">€{{ item.price }}</td>
                                        <td class="py-4 px-4 border-b border-coffee-100 text-center">{{ item.quantity }}</td>
                                        <td class="py-4 px-4 border-b border-coffee-100 text-right font-medium">€{{ item.subtotal }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="py-4 px-4 text-right font-semibold text-coffee-700">Total:</td>
                                        <td class="py-4 px-4 text-right font-bold text-coffee-800">€{{ order.total_amount }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Order Notes -->
                <div v-if="order.notes" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200 mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-coffee-800 mb-4">Order Notes</h3>
                        <div class="p-4 bg-cream-50 rounded border border-coffee-100">
                            <p class="text-coffee-800">{{ order.notes }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-center mt-6">
                    <Link :href="route('products.index')" class="px-6 py-2 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition mx-2">
                        Continue Shopping
                    </Link>
                    <Link :href="route('orders.index')" class="px-6 py-2 border border-coffee-300 text-coffee-700 rounded-md hover:bg-coffee-50 transition mx-2">
                        Back to Orders
                    </Link>
                    <!-- MODIFIED: Changed from Link to regular anchor tag with target="_blank" -->
                    <a :href="route('orders.receipt', order.id)" class="px-6 py-2 bg-coffee-800 text-white rounded-md hover:bg-coffee-900 transition mx-2" target="_blank">
                        Download Receipt
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>