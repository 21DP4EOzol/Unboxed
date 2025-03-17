<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Welcome to Unboxed!</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Discover Products Card -->
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md overflow-hidden text-white">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Discover Products</h3>
                                <p class="mb-6">Swipe through our catalog to find products you love!</p>
                                <Link :href="route('swipe.index')" class="inline-block px-4 py-2 bg-white text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition">
                                    Start Swiping
                                </Link>
                            </div>
                        </div>
                        
                        <!-- Recommendations Card -->
                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg shadow-md overflow-hidden text-white">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Your Recommendations</h3>
                                <p class="mb-6">Check out products selected just for you!</p>
                                <Link :href="route('recommendations.index')" class="inline-block px-4 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-purple-50 transition">
                                    View Recommendations
                                </Link>
                            </div>
                        </div>
                        
                        <!-- Swipe History Card -->
                        <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-md overflow-hidden text-white">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Swipe History</h3>
                                <p class="mb-6">Review the products you've liked and disliked.</p>
                                <Link :href="route('swipe.history')" class="inline-block px-4 py-2 bg-white text-green-600 rounded-lg font-medium hover:bg-green-50 transition">
                                    View History
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>