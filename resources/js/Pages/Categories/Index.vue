<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    categories: Array
});
</script>

<template>
    <Head title="Categories" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Categories</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-coffee-800 mb-6">Browse Categories</h2>
                        
                        <div v-if="categories.length === 0" class="text-center py-8">
                            <p class="text-coffee-600">No categories available.</p>
                        </div>
                        
                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <Link 
                                v-for="category in categories" 
                                :key="category.id" 
                                :href="route('products.index', { category: category.slug })"
                                class="bg-coffee-100 rounded-lg p-6 border border-coffee-200 hover:bg-coffee-200 transition-colors duration-300 flex flex-col items-center text-center"
                            >
                                <div v-if="category.image" class="w-32 h-32 mb-4 rounded-lg overflow-hidden bg-white flex items-center justify-center">
                                    <img :src="`/storage/${category.image}`" :alt="category.name" class="w-full h-full object-cover" />
                                </div>
                                <div v-else class="w-32 h-32 mb-4 rounded-lg bg-white flex items-center justify-center text-4xl">
                                    <!-- Default icons based on category name -->
                                    <span v-if="category.name.toLowerCase().includes('men')">👔</span>
                                    <span v-else-if="category.name.toLowerCase().includes('women')">👗</span>
                                    <span v-else-if="category.name.toLowerCase().includes('foot') || category.name.toLowerCase().includes('shoe')">👟</span>
                                    <span v-else-if="category.name.toLowerCase().includes('access')">🧣</span>
                                    <span v-else-if="category.name.toLowerCase().includes('electron')">📱</span>
                                    <span v-else>🛍️</span>
                                </div>
                                
                                <h3 class="text-xl font-bold text-coffee-800 mb-2">{{ category.name }}</h3>
                                <p v-if="category.description" class="text-coffee-600 text-sm">{{ category.description }}</p>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>