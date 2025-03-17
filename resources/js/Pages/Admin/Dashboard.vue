<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recentOrders: Array
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-400 text-sm mb-2">Total Users</div>
                        <div class="text-3xl font-bold">{{ stats.users }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-400 text-sm mb-2">Total Products</div>
                        <div class="text-3xl font-bold">{{ stats.products }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-400 text-sm mb-2">Total Orders</div>
                        <div class="text-3xl font-bold">{{ stats.orders }}</div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Recent Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Order #</th>
                                    <th class="py-2 px-4 border-b text-left">Customer</th>
                                    <th class="py-2 px-4 border-b text-left">Amount</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in recentOrders" :key="order.id">
                                    <td class="py-2 px-4 border-b">{{ order.order_number }}</td>
                                    <td class="py-2 px-4 border-b">{{ order.user.name }}</td>
                                    <td class="py-2 px-4 border-b">${{ order.total_amount }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <span class="px-2 py-1 rounded text-xs" :class="{
                                            'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                            'bg-blue-100 text-blue-800': order.status === 'processing',
                                            'bg-indigo-100 text-indigo-800': order.status === 'shipped',
                                            'bg-green-100 text-green-800': order.status === 'delivered',
                                            'bg-red-100 text-red-800': order.status === 'cancelled',
                                        }">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td class="py-4 px-4 border-b text-center" colspan="5">No orders found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>