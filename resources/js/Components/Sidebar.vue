<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    isOpen: Boolean
});

const emit = defineEmits(['close']);

// These would ideally come from your backend
const categories = [
    { id: 1, name: "Men's Clothing", slug: 'mens-clothing' },
    { id: 2, name: "Women's Clothing", slug: 'womens-clothing' },
    { id: 3, name: "Footwear", slug: 'footwear' },
    { id: 4, name: "Accessories", slug: 'accessories' },
    { id: 5, name: "Electronics", slug: 'electronics' },
    { id: 6, name: "Home & Kitchen", slug: 'home-kitchen' },
    { id: 7, name: "Beauty & Personal Care", slug: 'beauty-personal-care' }
];
</script>

<template>
    <div>
        <!-- Overlay -->
        <div 
            v-if="isOpen" 
            class="fixed inset-0 bg-coffee-900 bg-opacity-50 z-40 transition-opacity"
            @click="emit('close')"
        ></div>
        
        <!-- Sidebar -->
        <div 
            class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg z-50 transform transition-transform duration-300 ease-in-out"
            :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="p-5">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-coffee-800">Browse</h2>
                    <button 
                        @click="emit('close')" 
                        class="text-coffee-500 hover:text-coffee-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Categories Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-coffee-800 mb-3 border-b border-coffee-200 pb-2">Categories</h3>
                    <ul class="space-y-2">
                        <li v-for="category in categories" :key="category.id">
                            <Link 
                                :href="route('products.index', { category: category.slug })" 
                                class="block py-2 px-4 text-coffee-700 hover:bg-coffee-100 hover:text-coffee-900 rounded-md transition-colors"
                                @click="emit('close')"
                            >
                                {{ category.name }}
                            </Link>
                        </li>
                    </ul>
                </div>
                
                <!-- Additional Section -->
                <div>
                    <h3 class="text-lg font-medium text-coffee-800 mb-3 border-b border-coffee-200 pb-2">Quick Links</h3>
                    <ul class="space-y-2">
                        <li>
                            <Link 
                                :href="route('home')" 
                                class="block py-2 px-4 text-coffee-700 hover:bg-coffee-100 hover:text-coffee-900 rounded-md transition-colors"
                                @click="emit('close')"
                            >
                                Home
                            </Link>
                        </li>
                        <li>
                            <Link 
                                :href="route('products.index', { sort: 'price_low_high' })" 
                                class="block py-2 px-4 text-coffee-700 hover:bg-coffee-100 hover:text-coffee-900 rounded-md transition-colors"
                                @click="emit('close')"
                            >
                                Deals
                            </Link>
                        </li>
                        <li>
                            <Link 
                                :href="route('products.index', { sort: 'newest' })" 
                                class="block py-2 px-4 text-coffee-700 hover:bg-coffee-100 hover:text-coffee-900 rounded-md transition-colors"
                                @click="emit('close')"
                            >
                                New Arrivals
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>