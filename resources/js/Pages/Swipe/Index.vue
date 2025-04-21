<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    items: Array,
    itemType: {
        type: String,
        default: 'product'
    }
});

const currentItemIndex = ref(0);
const isAnimating = ref(false);
const swipeDirection = ref(null);
const remainingItems = ref([...props.items]);
const swipeType = ref(props.itemType);

const toggleSwipeType = () => {
    const newType = swipeType.value === 'product' ? 'category' : 'product';
    router.get(route('swipe.index', { type: newType }));
};

// Function to handle swipe action
const swipe = async (direction) => {
    if (isAnimating.value || remainingItems.value.length === 0) return;
    
    isAnimating.value = true;
    swipeDirection.value = direction;
    
    const currentItem = remainingItems.value[0];
    
    try {
        // Record swipe in the database
        const payload = {
            direction: direction === 'right' ? 'right' : 'left',
            type: swipeType.value
        };
        
        // Add the appropriate ID based on type
        if (swipeType.value === 'product') {
            payload.product_id = currentItem.id;
        } else {
            payload.category_id = currentItem.id;
        }
        
        await axios.post(route('swipe.store'), payload);
        
        // Wait for animation to complete
        setTimeout(() => {
            // Remove the swiped item
            remainingItems.value.shift();
            
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

// Computed property for displaying the proper title
const swipeTypeTitle = computed(() => {
    return swipeType.value === 'product' ? 'Discover Products' : 'Discover Categories';
});

onMounted(() => {
    document.addEventListener('keydown', handleKeyDown);
});

// Clean up event listener when component is unmounted
onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <Head :title="swipeTypeTitle" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-coffee-800 leading-tight">{{ swipeTypeTitle }}</h2>
                <div>
                    <button 
                        @click="toggleSwipeType" 
                        class="px-4 py-2 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition"
                    >
                        Switch to {{ swipeType === 'product' ? 'Categories' : 'Products' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                    <div class="flex justify-center">
                        <div class="w-full max-w-md">
                            <!-- Swipe Cards -->
                            <div class="relative h-96">
                                <div v-if="remainingItems.length === 0" class="flex flex-col items-center justify-center h-full">
                                    <p class="text-xl font-medium text-coffee-800">No more {{ swipeType === 'product' ? 'products' : 'categories' }} to swipe!</p>
                                    <p class="text-coffee-600 mt-2">Check back later for new items or try swiping {{ swipeType === 'product' ? 'categories' : 'products' }} instead.</p>
                                    <button 
                                        @click="toggleSwipeType" 
                                        class="mt-4 px-4 py-2 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition"
                                    >
                                        Switch to {{ swipeType === 'product' ? 'Categories' : 'Products' }}
                                    </button>
                                </div>
                                
                                <div v-for="(item, index) in remainingItems" 
                                     :key="item.id" 
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
                                        <!-- Product/Category Image -->
                                        <div class="h-3/5 bg-cream-100 relative">
                                            <img v-if="swipeType === 'product' && item.images && item.images.length > 0" 
                                                 :src="`/storage/${item.images[0]}`" 
                                                 :alt="item.name" 
                                                 class="w-full h-full object-cover" />
                                            <img v-else-if="swipeType === 'category' && item.image" 
                                                 :src="`/storage/${item.image}`" 
                                                 :alt="item.name" 
                                                 class="w-full h-full object-cover" />
                                            <div v-else class="w-full h-full flex items-center justify-center">
                                                <!-- Show appropriate icon based on item type -->
                                                <span v-if="swipeType === 'category'" class="text-6xl">
                                                    <!-- Default icons based on category name -->
                                                    <span v-if="item.name.toLowerCase().includes('men')">👔</span>
                                                    <span v-else-if="item.name.toLowerCase().includes('women')">👗</span>
                                                    <span v-else-if="item.name.toLowerCase().includes('foot') || item.name.toLowerCase().includes('shoe')">👟</span>
                                                    <span v-else-if="item.name.toLowerCase().includes('access')">🧣</span>
                                                    <span v-else-if="item.name.toLowerCase().includes('electron')">📱</span>
                                                    <span v-else>🛍️</span>
                                                </span>
                                                <span v-else class="text-coffee-400">No image available</span>
                                            </div>
                                            
                                            <!-- Categories badges for products -->
                                            <div v-if="swipeType === 'product' && item.categories" class="absolute top-2 left-2 flex flex-wrap gap-1">
                                                <span v-for="category in item.categories" 
                                                      :key="category.id"
                                                      class="px-2 py-1 bg-coffee-600 text-white text-xs rounded-full">
                                                    {{ category.name }}
                                                </span>
                                            </div>
                                            
                                            <!-- Item Type Badge -->
                                            <div class="absolute top-2 right-2">
                                                <span class="px-2 py-1 bg-coffee-200 text-coffee-800 text-xs rounded-full">
                                                    {{ swipeType === 'product' ? 'Product' : 'Category' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Item Info -->
                                        <div class="p-4 flex-grow">
                                            <h3 class="text-xl font-bold text-coffee-800">{{ item.name }}</h3>
                                            <p v-if="item.description" class="text-coffee-600 mt-1">
                                                {{ item.description.substring(0, 100) }}{{ item.description.length > 100 ? '...' : '' }}
                                            </p>
                                            <div v-if="swipeType === 'product'" class="mt-4 flex justify-between items-center">
                                                <span class="text-2xl font-bold text-coffee-700">${{ item.price }}</span>
                                                <span class="text-sm text-coffee-500">{{ item.stock }} in stock</span>
                                            </div>
                                            <div v-else class="mt-4">
                                                <span class="text-sm text-coffee-500">Swipe right to see more products in this category</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Swipe Controls -->
                            <div class="flex justify-center mt-6 space-x-8">
                                <button @click="swipe('left')" 
                                        class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cream-50 to-white border border-coffee-300 rounded-full shadow-md text-coffee-600 hover:from-coffee-400 hover:to-coffee-600 hover:text-white transition-all duration-300"
                                        :disabled="isAnimating || remainingItems.length === 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <button @click="swipe('right')" 
                                        class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cream-50 to-white border border-coffee-300 rounded-full shadow-md text-coffee-600 hover:from-coffee-600 hover:to-coffee-400 hover:text-white transition-all duration-300"
                                        :disabled="isAnimating || remainingItems.length === 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Instructions -->
                            <div class="mt-6 text-center text-coffee-600 text-sm">
                                <p>Swipe right to like, swipe left to dislike.</p>
                                <p class="mt-1">You can also use keyboard arrows: ← →</p>
                                <p class="mt-3">Currently swiping: <strong>{{ swipeTypeTitle }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>