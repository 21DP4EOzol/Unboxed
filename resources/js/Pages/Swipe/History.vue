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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Swipe History</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Liked Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Products You Liked</h3>
                    
                    <div v-if="likedProducts.length === 0" class="text-gray-500">
                        You haven't liked any products yet.
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="product in likedProducts" :key="product.id" class="border rounded-lg overflow-hidden">
                            <!-- Product Image -->
                            <div class="h-48 bg-gray-200">
                                <img v-if="product.images && product.images.length > 0" 
                                     :src="`/storage/${product.images[0]}`" 
                                     alt="Product Image" 
                                     class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-gray-400">No image available</span>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold">{{ product.name }}</h4>
                                <p class="text-gray-600 text-sm mt-1">{{ product.description.substring(0, 80) }}{{ product.description.length > 80 ? '...' : '' }}</p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="font-bold text-blue-600">${{ product.price }}</span>
                                    <Link :href="`/products/${product.id}`" class="text-blue-500 hover:underline">View Details</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Disliked Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Products You Disliked</h3>
                    
                    <div v-if="dislikedProducts.length === 0" class="text-gray-500">
                        You haven't disliked any products yet.
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="product in dislikedProducts" :key="product.id" class="border rounded-lg overflow-hidden opacity-70">
                            <!-- Product Image -->
                            <div class="h-48 bg-gray-200">
                                <img v-if="product.images && product.images.length > 0" 
                                     :src="`/storage/${product.images[0]}`" 
                                     alt="Product Image" 
                                     class="w-full h-full object-cover grayscale" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-gray-400">No image available</span>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold">{{ product.name }}</h4>
                                <p class="text-gray-600 text-sm mt-1">{{ product.description.substring(0, 80) }}{{ product.description.length > 80 ? '...' : '' }}</p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="font-bold text-gray-600">${{ product.price }}</span>
                                    <Link :href="`/products/${product.id}`" class="text-gray-500 hover:underline">View Details</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>