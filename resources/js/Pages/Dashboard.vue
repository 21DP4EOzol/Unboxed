<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12 bg-cream-100">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                    <div class="p-6 bg-white border-b border-coffee-200">
                        <h2 class="text-2xl font-bold text-coffee-800 mb-6">Welcome to Unboxed!</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Discover Options Card -->
                            <div class="bg-gradient-to-br from-coffee-500 to-coffee-700 rounded-lg shadow-md overflow-hidden text-white">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-4">Discover Products & Categories</h3>
                                    <p class="mb-6">Start swiping to build personalized recommendations!</p>
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <Link :href="route('swipe.index', { type: 'product' })" class="inline-block px-4 py-2 bg-white text-coffee-600 rounded-lg font-medium hover:bg-cream-100 transition text-center">
                                            Swipe Products
                                        </Link>
                                        <Link :href="route('swipe.index', { type: 'category' })" class="inline-block px-4 py-2 bg-white text-coffee-600 rounded-lg font-medium hover:bg-cream-100 transition text-center">
                                            Swipe Categories
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recommendations Card -->
                            <div class="bg-gradient-to-br from-accent-500 to-accent-700 rounded-lg shadow-md overflow-hidden text-white">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">Your Recommendations</h3>
                                    <p class="mb-6">Check out products selected just for you based on your preferences!</p>
                                    <Link :href="route('recommendations.index')" class="inline-block px-4 py-2 bg-white text-accent-600 rounded-lg font-medium hover:bg-cream-100 transition">
                                        View Recommendations
                                    </Link>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <!-- Swipe History Card -->
                            <div class="bg-gradient-to-br from-cream-500 to-cream-700 rounded-lg shadow-md overflow-hidden text-coffee-900">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">Swipe History</h3>
                                    <p class="mb-6">Review the products and categories you've liked and disliked.</p>
                                    <Link :href="route('swipe.history')" class="inline-block px-4 py-2 bg-white text-cream-700 rounded-lg font-medium hover:bg-cream-100 transition">
                                        View History
                                    </Link>
                                </div>
                            </div>
                            
                            <!-- Orders Card -->
                            <div class="bg-gradient-to-br from-coffee-600 to-coffee-800 rounded-lg shadow-md overflow-hidden text-white">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">Your Orders</h3>
                                    <p class="mb-6">Track your orders and view your purchase history.</p>
                                    <Link :href="route('orders.index')" class="inline-block px-4 py-2 bg-white text-coffee-700 rounded-lg font-medium hover:bg-cream-100 transition">
                                        View Orders
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>