<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    auth: Object,
    featuredProducts: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth?.user);
</script>

<template>
    <Head title="Unboxed - Clothing & Fashion" />
    
    <AppLayout>
        <div class="bg-cream-50 py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Login prompt for guests -->
                <div v-if="!isLoggedIn" class="bg-coffee-100 rounded-lg p-6 mb-6 border border-coffee-200">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-coffee-800">Create an account to unlock all features!</h3>
                            <p class="text-coffee-600 mt-2">Sign up to swipe through products, get personalized recommendations, and more.</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-4">
                            <Link :href="route('login')" class="px-4 py-2 bg-coffee-600 text-white rounded-lg font-medium hover:bg-coffee-700 transition">
                                Log In
                            </Link>
                            <Link :href="route('register')" class="px-4 py-2 bg-coffee-800 text-white rounded-lg font-medium hover:bg-coffee-900 transition">
                                Register
                            </Link>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-coffee-200">
                    <div class="p-6 bg-white border-b border-coffee-200">
                        <h2 class="text-2xl font-bold text-coffee-800 mb-6">Welcome to Unboxed!</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Discover Products Card -->
                            <div class="bg-gradient-to-br from-amber-500 to-coffee-700 rounded-lg shadow-md overflow-hidden text-white transform transition-transform hover:scale-105 duration-300">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">Discover Products</h3>
                                    <p class="mb-6">Swipe through our catalog to find products you love!</p>
                                    <div v-if="isLoggedIn">
                                        <Link :href="route('swipe.index')" class="inline-block px-4 py-2 bg-white text-coffee-600 rounded-lg font-medium hover:bg-cream-100 transition">
                                            Start Swiping
                                        </Link>
                                    </div>
                                    <div v-else>
                                        <Link :href="route('login')" class="inline-block px-4 py-2 bg-white text-coffee-600 rounded-lg font-medium hover:bg-cream-100 transition">
                                            Login to Swipe
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recommendations Card -->
                            <div class="bg-gradient-to-br from-coffee-600 to-amber-800 rounded-lg shadow-md overflow-hidden text-white transform transition-transform hover:scale-105 duration-300">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">Your Recommendations</h3>
                                    <p class="mb-6">Check out products selected just for you!</p>
                                    <div v-if="isLoggedIn">
                                        <Link :href="route('recommendations.index')" class="inline-block px-4 py-2 bg-white text-coffee-700 rounded-lg font-medium hover:bg-cream-100 transition">
                                            View Recommendations
                                        </Link>
                                    </div>
                                    <div v-else>
                                        <Link :href="route('login')" class="inline-block px-4 py-2 bg-white text-coffee-700 rounded-lg font-medium hover:bg-cream-100 transition">
                                            Login for Recommendations
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Swipe History Card -->
                            <div class="bg-gradient-to-br from-amber-600 to-coffee-600 rounded-lg shadow-md overflow-hidden text-white transform transition-transform hover:scale-105 duration-300">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">Swipe History</h3>
                                    <p class="mb-6">Review the products you've liked and disliked.</p>
                                    <div v-if="isLoggedIn">
                                        <Link :href="route('swipe.history')" class="inline-block px-4 py-2 bg-white text-coffee-600 rounded-lg font-medium hover:bg-cream-100 transition">
                                            View History
                                        </Link>
                                    </div>
                                    <div v-else>
                                        <Link :href="route('login')" class="inline-block px-4 py-2 bg-white text-coffee-600 rounded-lg font-medium hover:bg-cream-100 transition">
                                            Login to View History
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Featured Categories Section -->
                        <div class="mt-12">
                            <h3 class="text-xl font-bold text-coffee-800 mb-6">Featured Categories</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <Link :href="route('products.index', { category: 'mens-clothing' })" class="bg-coffee-100 rounded-lg p-4 text-center hover:bg-coffee-200 transition group">
                                    <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center">
                                        <span class="text-3xl">👔</span>
                                    </div>
                                    <h4 class="font-medium text-coffee-800 group-hover:text-coffee-900">Men's Clothing</h4>
                                </Link>
                                <Link :href="route('products.index', { category: 'womens-clothing' })" class="bg-coffee-100 rounded-lg p-4 text-center hover:bg-coffee-200 transition group">
                                    <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center">
                                        <span class="text-3xl">👗</span>
                                    </div>
                                    <h4 class="font-medium text-coffee-800 group-hover:text-coffee-900">Women's Clothing</h4>
                                </Link>
                                <Link :href="route('products.index', { category: 'footwear' })" class="bg-coffee-100 rounded-lg p-4 text-center hover:bg-coffee-200 transition group">
                                    <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center">
                                        <span class="text-3xl">👟</span>
                                    </div>
                                    <h4 class="font-medium text-coffee-800 group-hover:text-coffee-900">Footwear</h4>
                                </Link>
                                <Link :href="route('products.index', { category: 'accessories' })" class="bg-coffee-100 rounded-lg p-4 text-center hover:bg-coffee-200 transition group">
                                    <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center">
                                        <span class="text-3xl">🧣</span>
                                    </div>
                                    <h4 class="font-medium text-coffee-800 group-hover:text-coffee-900">Accessories</h4>
                                </Link>
                            </div>
                        </div>
                        
                        <!-- Featured Products Section -->
                        <div class="mt-12">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-coffee-800">Featured Products</h3>
                                <Link :href="route('products.index')" class="text-coffee-600 hover:text-coffee-800 hover:underline">View All</Link>
                            </div>
                            
                            <div v-if="featuredProducts && featuredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div v-for="product in featuredProducts" :key="product.id" class="bg-white rounded-lg shadow overflow-hidden border border-coffee-200 hover:shadow-lg transition">
                                    <Link :href="route('products.show', product.id)">
                                        <div class="h-48 bg-cream-50 relative">
                                            <img v-if="product.images && product.images.length > 0" 
                                                :src="`/storage/${product.images[0]}`" 
                                                :alt="product.name" 
                                                class="w-full h-full object-cover" />
                                            <div v-else class="w-full h-full flex items-center justify-center">
                                                <span class="text-coffee-400">No image available</span>
                                            </div>
                                            
                                            <!-- Featured badge -->
                                            <div v-if="product.featured" class="absolute top-2 right-2">
                                                <span class="bg-coffee-200 text-coffee-800 px-2 py-1 rounded-full text-xs font-bold">Featured</span>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4">
                                            <h4 class="text-lg font-semibold text-coffee-800">{{ product.name }}</h4>
                                            <p class="text-coffee-600 text-sm mt-1 line-clamp-2">{{ product.description }}</p>
                                            <div class="mt-4 flex justify-between items-center">
                                                <span class="font-bold text-coffee-700">${{ product.price }}</span>
                                                <span class="inline-block px-3 py-1 bg-coffee-600 text-white text-xs rounded-full">
                                                    View Details
                                                </span>
                                            </div>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                            
                            <div v-else class="text-center py-8 bg-cream-50 rounded-lg">
                                <p class="text-coffee-600">No featured products available at the moment.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>