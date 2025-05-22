<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    recommendations: Array,
    preferredCategories: Array
});
</script>

<template>
    <Head title="Recommended Products" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Recommended For You</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Preferred Categories Section -->
                <div v-if="preferredCategories && preferredCategories.length > 0" class="bg-coffee-100 p-6 rounded-lg mb-6 border border-coffee-200">
                    <h3 class="text-lg font-semibold text-coffee-800 mb-4">Based on categories you like</h3>
                    <div class="flex flex-wrap gap-3">
                        <Link 
                            v-for="category in preferredCategories" 
                            :key="category.id"
                            :href="route('products.index', { category: category.slug })"
                            class="px-4 py-2 bg-white border border-coffee-300 rounded-full text-coffee-700 hover:bg-coffee-50 transition flex items-center gap-2"
                        >
                            <div v-if="category.image" class="w-6 h-6 rounded-full overflow-hidden bg-white">
                                <img :src="`/storage/${category.image}`" :alt="category.name" class="w-full h-full object-cover" />
                            </div>
                            <span v-else class="text-lg">
                                <!-- Default icons based on category name -->
                                <span v-if="category.name.toLowerCase().includes('men')">👔</span>
                                <span v-else-if="category.name.toLowerCase().includes('women')">👗</span>
                                <span v-else-if="category.name.toLowerCase().includes('foot') || category.name.toLowerCase().includes('shoe')">👟</span>
                                <span v-else-if="category.name.toLowerCase().includes('access')">🧣</span>
                                <span v-else-if="category.name.toLowerCase().includes('electron')">📱</span>
                                <span v-else>🛍️</span>
                            </span>
                            {{ category.name }}
                        </Link>
                    </div>
                    
                    <div class="mt-4 px-4 py-2 bg-coffee-50 border border-coffee-200 rounded-md text-coffee-600 text-sm">
                        <p>These recommendations are based on categories you've liked during swiping. Click on any category to browse more products.</p>
                    </div>
                </div>
                
                <!-- Product Recommendations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                    <h3 class="text-lg font-semibold text-coffee-800 mb-6">Products You Might Like</h3>
                    
                    <div v-if="recommendations.length === 0" class="text-center py-8">
                        <p class="text-coffee-600 mb-4">We don't have any recommendations for you yet.</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link :href="route('swipe.index', { type: 'product' })" class="px-4 py-2 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition text-center">
                                Swipe Products
                            </Link>
                            <Link :href="route('swipe.index', { type: 'category' })" class="px-4 py-2 bg-coffee-700 text-white rounded-md hover:bg-coffee-800 transition text-center">
                                Swipe Categories
                            </Link>
                        </div>
                        <p class="text-coffee-600 mt-4">
                            Swipe on products and categories to help us learn your preferences!
                        </p>
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div v-for="product in recommendations" :key="product.id" class="border border-coffee-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <!-- Product Image -->
                            <div class="h-48 bg-cream-100 relative">
                                <img v-if="product.images && product.images.length > 0" 
                                     :src="`/storage/${product.images[0]}`" 
                                     alt="Product Image" 
                                     class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-coffee-400">No image available</span>
                                </div>
                                
                                <!-- Featured badge -->
                                <div v-if="product.featured" class="absolute top-2 right-2">
                                    <span class="bg-coffee-200 text-coffee-800 px-2 py-1 rounded-full text-xs font-bold">Featured</span>
                                </div>
                            </div>
                            
                            <!-- Categories -->
                            <div class="px-4 pt-3 flex flex-wrap gap-1">
                                <span v-for="category in product.categories" 
                                      :key="category.id" 
                                      class="bg-coffee-100 text-coffee-800 px-2 py-0.5 rounded-full text-xs">
                                    {{ category.name }}
                                </span>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold text-coffee-800">{{ product.name }}</h4>
                                <p class="text-coffee-600 text-sm mt-1">{{ product.description.substring(0, 60) }}{{ product.description.length > 60 ? '...' : '' }}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="font-bold text-coffee-700">€{{ product.price }}</span>
                                    <Link :href="`/products/${product.id}`" class="px-3 py-1 bg-coffee-600 text-white rounded hover:bg-coffee-700 transition">
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Need More Recommendations section -->
                    <div v-if="recommendations.length > 0" class="mt-8 p-4 bg-cream-50 rounded-md border border-coffee-200">
                        <h4 class="text-md font-semibold text-coffee-800 mb-2">Want more recommendations?</h4>
                        <p class="text-coffee-600 mb-3">Continue swiping to help us improve your recommendations.</p>
                        <div class="flex flex-wrap gap-3">
                            <Link :href="route('swipe.index', { type: 'product' })" class="px-4 py-2 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition">
                                Swipe More Products
                            </Link>
                            <Link :href="route('swipe.index', { type: 'category' })" class="px-4 py-2 bg-coffee-700 text-white rounded-md hover:bg-coffee-800 transition">
                                Swipe Categories
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>