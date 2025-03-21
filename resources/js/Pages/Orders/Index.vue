<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    orders: Object
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
    <Head title="My Orders" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">My Orders</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-coffee-800 mb-6">Order History</h3>
                        
                        <div v-if="orders.data.length === 0" class="text-center py-8">
                            <p class="text-coffee-600 mb-4">You haven't placed any orders yet.</p>
                            <Link :href="route('products.index')" class="px-6 py-2 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition">
                                Start Shopping
                            </Link>
                        </div>
                        
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 border-b border-coffee-200 text-left text-coffee-700">Order #</th>
                                        <th class="py-3 px-4 border-b border-coffee-200 text-left text-coffee-700">Date</th>
                                        <th class="py-3 px-4 border-b border-coffee-200 text-left text-coffee-700">Total</th>
                                        <th class="py-3 px-4 border-b border-coffee-200 text-left text-coffee-700">Status</th>
                                        <th class="py-3 px-4 border-b border-coffee-200 text-left text-coffee-700">Payment</th>
                                        <th class="py-3 px-4 border-b border-coffee-200 text-left text-coffee-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in orders.data" :key="order.id">
                                        <td class="py-3 px-4 border-b border-coffee-100">{{ order.order_number }}</td>
                                        <td class="py-3 px-4 border-b border-coffee-100">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                                        <td class="py-3 px-4 border-b border-coffee-100">${{ order.total_amount }}</td>
                                        <td class="py-3 px-4 border-b border-coffee-100">
                                            <span class="px-2 py-1 rounded text-xs" :class="getStatusClass(order.status)">
                                                {{ order.status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b border-coffee-100">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-green-100 text-green-800': order.payment_status === 'paid',
                                                'bg-yellow-100 text-yellow-800': order.payment_status === 'pending',
                                                'bg-red-100 text-red-800': order.payment_status === 'failed'
                                            }">
                                                {{ order.payment_status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b border-coffee-100">
                                            <Link :href="route('orders.show', order.id)" class="text-coffee-600 hover:text-coffee-800 hover:underline">
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <Pagination class="mt-6" :links="orders.links" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>