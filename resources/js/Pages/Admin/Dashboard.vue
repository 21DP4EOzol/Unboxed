<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    topSellingProducts: Array,
    leastSellingProducts: Array
});

// Helper function to format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

// Helper function to calculate percentage
const calculatePercentage = (part, total) => {
    if (total === 0) return 0;
    return Math.round((part / total) * 100);
};

// Get order status counts
const getStatusCount = (status) => {
    return props.stats.orders.statusBreakdown?.[status] || 0;
};
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Admin Dashboard</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Revenue Banner -->
                <div class="bg-gradient-to-r from-coffee-700 to-coffee-600 text-white rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-coffee-200 text-sm mb-1">Total Revenue</div>
                            <div class="text-3xl font-bold text-white">{{ stats.revenue ? formatCurrency(stats.revenue) : '$0.00' }}</div>
                        </div>
                        <div>
                            <div class="text-coffee-200 text-sm mb-1">Total Orders</div>
                            <div class="text-3xl font-bold text-white">{{ stats.orders.total || stats.orders }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- User Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                        <div class="text-coffee-500 text-sm mb-2">User Statistics</div>
                        <div class="text-3xl font-bold text-coffee-800 mb-4">
                            {{ stats.users.total || stats.users }}
                        </div>
                        
                        <!-- User breakdown (if available) -->
                        <div v-if="stats.users.active !== undefined" class="mt-4 space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-coffee-600">Active Users</span>
                                    <span>{{ stats.users.active }} ({{ calculatePercentage(stats.users.active, stats.users.total) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" 
                                         :style="`width: ${calculatePercentage(stats.users.active, stats.users.total)}%`"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-coffee-600">Inactive Users</span>
                                    <span>{{ stats.users.inactive }} ({{ calculatePercentage(stats.users.inactive, stats.users.total) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" 
                                         :style="`width: ${calculatePercentage(stats.users.inactive, stats.users.total)}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                        <div class="text-coffee-500 text-sm mb-2">Product Statistics</div>
                        <div class="text-3xl font-bold text-coffee-800 mb-4">
                            {{ stats.products.total || stats.products }}
                        </div>
                        
                        <!-- Product breakdown (if available) -->
                        <div v-if="stats.products.inStock !== undefined" class="mt-4 space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-coffee-600">In Stock</span>
                                    <span>{{ stats.products.inStock }} ({{ calculatePercentage(stats.products.inStock, stats.products.total) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" 
                                         :style="`width: ${calculatePercentage(stats.products.inStock, stats.products.total)}%`"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-coffee-600">Out of Stock</span>
                                    <span>{{ stats.products.outOfStock }} ({{ calculatePercentage(stats.products.outOfStock, stats.products.total) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" 
                                         :style="`width: ${calculatePercentage(stats.products.outOfStock, stats.products.total)}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                        <div class="text-coffee-500 text-sm mb-2">Order Statistics</div>
                        <div class="text-3xl font-bold text-coffee-800 mb-3">
                            {{ stats.orders.total || stats.orders }}
                        </div>
                        
                        <!-- Order breakdown (if available) -->
                        <div v-if="stats.orders.statusBreakdown" class="mt-4 space-y-1.5 text-sm">
                            <div class="flex justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-yellow-400 mr-2"></div>
                                    <span>Pending</span>
                                </div>
                                <span>{{ getStatusCount('pending') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-blue-400 mr-2"></div>
                                    <span>Processing</span>
                                </div>
                                <span>{{ getStatusCount('processing') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-indigo-400 mr-2"></div>
                                    <span>Shipped</span>
                                </div>
                                <span>{{ getStatusCount('shipped') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-400 mr-2"></div>
                                    <span>Delivered</span>
                                </div>
                                <span>{{ getStatusCount('delivered') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-red-400 mr-2"></div>
                                    <span>Cancelled</span>
                                </div>
                                <span>{{ getStatusCount('cancelled') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Performance Section -->
                <div v-if="topSellingProducts || leastSellingProducts" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Top Selling Products -->
                    <div v-if="topSellingProducts" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                        <div class="p-4 border-b border-coffee-200 bg-coffee-50">
                            <h3 class="font-medium text-coffee-800">Top Selling Products</h3>
                        </div>
                        <div class="p-4">
                            <div v-if="topSellingProducts.length === 0" class="text-center py-2 text-coffee-500">
                                No sales data available
                            </div>
                            <ul v-else class="divide-y divide-coffee-100">
                                <li v-for="product in topSellingProducts" :key="product.id" class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-cream-50 rounded overflow-hidden mr-3">
                                            <img v-if="product.images && product.images.length > 0" 
                                                 :src="`/storage/${product.images[0]}`" 
                                                 :alt="product.name"
                                                 class="w-full h-full object-cover" />
                                        </div>
                                        <div>
                                            <div class="text-coffee-800 font-medium">{{ product.name }}</div>
                                            <div class="text-coffee-500 text-xs">{{ product.total_sold }} units sold</div>
                                        </div>
                                    </div>
                                    <Link :href="`/admin/products/${product.id}/edit`" class="text-coffee-600 hover:text-coffee-800 text-xs">
                                        View
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Least Selling Products -->
                    <div v-if="leastSellingProducts" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                        <div class="p-4 border-b border-coffee-200 bg-coffee-50">
                            <h3 class="font-medium text-coffee-800">Least Selling Products</h3>
                        </div>
                        <div class="p-4">
                            <div v-if="leastSellingProducts.length === 0" class="text-center py-2 text-coffee-500">
                                No product data available
                            </div>
                            <ul v-else class="divide-y divide-coffee-100">
                                <li v-for="product in leastSellingProducts" :key="product.id" class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-cream-50 rounded overflow-hidden mr-3">
                                            <img v-if="product.images && product.images.length > 0" 
                                                 :src="`/storage/${product.images[0]}`" 
                                                 :alt="product.name"
                                                 class="w-full h-full object-cover" />
                                        </div>
                                        <div>
                                            <div class="text-coffee-800 font-medium">{{ product.name }}</div>
                                            <div class="text-coffee-500 text-xs">{{ product.total_sold }} units sold</div>
                                        </div>
                                    </div>
                                    <Link :href="`/admin/products/${product.id}/edit`" class="text-coffee-600 hover:text-coffee-800 text-xs">
                                        View
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                    <h3 class="text-lg font-medium text-coffee-800 mb-4">Recent Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-600">Order #</th>
                                    <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-600">Customer</th>
                                    <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-600">Amount</th>
                                    <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-600">Status</th>
                                    <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-600">Date</th>
                                    <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in recentOrders" :key="order.id">
                                    <td class="py-2 px-4 border-b border-coffee-100">{{ order.order_number }}</td>
                                    <td class="py-2 px-4 border-b border-coffee-100">{{ order.user.name }}</td>
                                    <td class="py-2 px-4 border-b border-coffee-100">${{ order.total_amount }}</td>
                                    <td class="py-2 px-4 border-b border-coffee-100">
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
                                    <td class="py-2 px-4 border-b border-coffee-100">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                                    <td class="py-2 px-4 border-b border-coffee-100">
                                        <Link :href="route('admin.orders.show', order.id)" class="text-coffee-600 hover:text-coffee-800 text-sm">
                                            View
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td class="py-4 px-4 border-b text-center text-coffee-500" colspan="6">No orders found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>