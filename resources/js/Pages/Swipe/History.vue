<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    likedProducts: Array,
    dislikedProducts: Array,
});
</script>

<template>
    <Head title="Swipe History" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Swipe History</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Liked Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border border-coffee-200">
                    <h3 class="text-lg font-semibold text-coffee-800 mb-4">Products You Liked</h3>
                    
                    <div v-if="likedProducts.length === 0" class="text-coffee-600">
                        You haven't liked any products yet.
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="product in likedProducts" :key="product.id" class="border border-coffee-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Product Image -->
                            <div class="h-48 bg-cream-100">
                                <img v-if="product.images && product.images.length > 0" 
                                     :src="`/storage/${product.images[0]}`" 
                                     alt="Product Image" 
                                     class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-coffee-400">No image available</span>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold text-coffee-800">{{ product.name }}</h4>
                                <p class="text-coffee-600 text-sm mt-1">{{ product.description.substring(0, 80) }}{{ product.description.length > 80 ? '...' : '' }}</p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="font-bold text-coffee-700">${{ product.price }}</span>
                                    <Link :href="`/products/${product.id}`" class="text-coffee-600 hover:text-coffee-800 hover:underline">View Details</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Disliked Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                    <h3 class="text-lg font-semibold text-coffee-800 mb-4">Products You Disliked</h3>
                    
                    <div v-if="dislikedProducts.length === 0" class="text-coffee-600">
                        You haven't disliked any products yet.
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="product in dislikedProducts" :key="product.id" class="border border-coffee-100 rounded-lg overflow-hidden opacity-70">
                            <!-- Product Image -->
                            <div class="h-48 bg-cream-100">
                                <img v-if="product.images && product.images.length > 0" 
                                     :src="`/storage/${product.images[0]}`" 
                                     alt="Product Image" 
                                     class="w-full h-full object-cover grayscale" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-coffee-400">No image available</span>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold text-coffee-700">{{ product.name }}</h4>
                                <p class="text-coffee-500 text-sm mt-1">{{ product.description.substring(0, 80) }}{{ product.description.length > 80 ? '...' : '' }}</p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="font-bold text-coffee-600">${{ product.price }}</span>
                                    <Link :href="`/products/${product.id}`" class="text-coffee-500 hover:text-coffee-700 hover:underline">View Details</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>