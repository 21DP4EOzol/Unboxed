<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    products: Array
});

const currentProductIndex = ref(0);
const isAnimating = ref(false);
const swipeDirection = ref(null);
const remainingProducts = ref([...props.products]);

// Function to handle swipe action
const swipe = async (direction) => {
    if (isAnimating.value || remainingProducts.value.length === 0) return;
    
    isAnimating.value = true;
    swipeDirection.value = direction;
    
    const currentProduct = remainingProducts.value[0];
    
    try {
        // Record swipe in the database
        await axios.post(route('swipe.store'), {
            product_id: currentProduct.id,
            direction: direction === 'right' ? 'right' : 'left'
        });
        
        // Wait for animation to complete
        setTimeout(() => {
            // Remove the swiped product
            remainingProducts.value.shift();
            
            // Reset animation state
            isAnimating.value = false;
            swipeDirection.value = null;
        }, 300);
    } catch (error) {
        console.error('Error recording swipe:', error);
        isAnimating.value = false;
        swipeDirection.value = null;
    }
};

// Handle keyboard navigation
const handleKeyDown = (e) => {
    if (e.key === 'ArrowLeft') {
        swipe('left');
    } else if (e.key === 'ArrowRight') {
        swipe('right');
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeyDown);
});

// Clean up event listener when component is unmounted
onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <Head title="Swipe Products" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Discover Products</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                    <div class="flex justify-center">
                        <div class="w-full max-w-md">
                            <!-- Swipe Cards -->
                            <div class="relative h-96">
                                <div v-if="remainingProducts.length === 0" class="flex flex-col items-center justify-center h-full">
                                    <p class="text-xl font-medium text-coffee-800">No more products to swipe!</p>
                                    <p class="text-coffee-600 mt-2">Check back later for new items.</p>
                                </div>
                                
                                <div v-for="(product, index) in remainingProducts" 
                                     :key="product.id" 
                                     class="absolute top-0 left-0 w-full h-full transition-transform duration-300 ease-out"
                                     :class="{
                                         'translate-x-full opacity-0': index === 0 && swipeDirection === 'right',
                                         '-translate-x-full opacity-0': index === 0 && swipeDirection === 'left',
                                         'z-10': index === 0,
                                         'z-0': index === 1,
                                         'hidden': index > 1
                                     }"
                                     :style="{
                                         transform: index > 0 ? `scale(${0.95 - (index - 1) * 0.05}) translateY(${(index - 1) * 10}px)` : ''
                                     }">
                                    
                                    <div class="bg-white rounded-xl shadow-lg overflow-hidden h-full flex flex-col border border-coffee-200">
                                        <!-- Product Image -->
                                        <div class="h-3/5 bg-cream-100 relative">
                                            <img v-if="product.images && product.images.length > 0" 
                                                 :src="`/storage/${product.images[0]}`" 
                                                 alt="Product Image" 
                                                 class="w-full h-full object-cover" />
                                            <div v-else class="w-full h-full flex items-center justify-center">
                                                <span class="text-coffee-400">No image available</span>
                                            </div>
                                            
                                            <!-- Categories badges -->
                                            <div class="absolute top-2 left-2 flex flex-wrap gap-1">
                                                <span v-for="category in product.categories" 
                                                      :key="category.id"
                                                      class="px-2 py-1 bg-coffee-600 text-white text-xs rounded-full">
                                                    {{ category.name }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Product Info -->
                                        <div class="p-4 flex-grow">
                                            <h3 class="text-xl font-bold text-coffee-800">{{ product.name }}</h3>
                                            <p class="text-coffee-600 mt-1">{{ product.description.substring(0, 100) }}{{ product.description.length > 100 ? '...' : '' }}</p>
                                            <div class="mt-4 flex justify-between items-center">
                                                <span class="text-2xl font-bold text-coffee-700">${{ product.price }}</span>
                                                <span class="text-sm text-coffee-500">{{ product.stock }} in stock</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Swipe Controls -->
                            <div class="flex justify-center mt-6 space-x-8">
                                <button @click="swipe('left')" 
                                        class="flex items-center justify-center w-16 h-16 bg-white border border-coffee-300 rounded-full shadow-md text-coffee-600 hover:bg-cream-100 transition"
                                        :disabled="isAnimating || remainingProducts.length === 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <button @click="swipe('right')" 
                                        class="flex items-center justify-center w-16 h-16 bg-white border border-coffee-300 rounded-full shadow-md text-coffee-600 hover:bg-cream-100 transition"
                                        :disabled="isAnimating || remainingProducts.length === 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Instructions -->
                            <div class="mt-6 text-center text-coffee-600 text-sm">
                                <p>Swipe right to like, swipe left to dislike.</p>
                                <p class="mt-1">You can also use keyboard arrows: ← →</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>