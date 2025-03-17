<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    recommendations: Array
});
</script>

<template>
    <Head title="Recommended Products" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Recommended For You</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-6">Based on your preferences</h3>
                    
                    <div v-if="recommendations.length === 0" class="text-center py-8">
                        <p class="text-gray-500 mb-4">We don't have any recommendations for you yet.</p>
                        <p class="text-gray-500">
                            <Link :href="route('swipe.index')" class="text-blue-500 hover:underline">Start swiping</Link>
                            to help us learn your preferences!
                        </p>
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div v-for="product in recommendations" :key="product.id" class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <!-- Product Image -->
                            <div class="h-48 bg-gray-200 relative">
                                <img v-if="product.images && product.images.length > 0" 
                                     :src="`/storage/${product.images[0]}`" 
                                     alt="Product Image" 
                                     class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-gray-400">No image available</span>
                                </div>
                                
                                <!-- Featured badge -->
                                <div v-if="product.featured" class="absolute top-2 right-2">
                                    <span class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-bold">Featured</span>
                                </div>
                            </div>
                            
                            <!-- Categories -->
                            <div class="px-4 pt-3 flex flex-wrap gap-1">
                                <span v-for="category in product.categories" 
                                      :key="category.id" 
                                      class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs">
                                    {{ category.name }}
                                </span>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold">{{ product.name }}</h4>
                                <p class="text-gray-600 text-sm mt-1">{{ product.description.substring(0, 60) }}{{ product.description.length > 60 ? '...' : '' }}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="font-bold text-blue-600">${{ product.price }}</span>
                                    <Link :href="`/products/${product.id}`" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                        View Details
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