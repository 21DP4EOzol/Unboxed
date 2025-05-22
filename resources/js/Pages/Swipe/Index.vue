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

// Variables for drag functionality
const startX = ref(0);
const currentX = ref(0);
const isDragging = ref(false);
const dragThreshold = 100; // Minimum drag distance to trigger swipe

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
            
            // Reset drag state
            isDragging.value = false;
            startX.value = 0;
            currentX.value = 0;
        }, 300);
    } catch (error) {
        console.error('Error recording swipe:', error);
        isAnimating.value = false;
        swipeDirection.value = null;
        isDragging.value = false;
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

// Mouse/Touch event handlers
const startDrag = (e) => {
    if (isAnimating.value || remainingItems.value.length === 0) return;
    
    isDragging.value = true;
    startX.value = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
    currentX.value = startX.value;
};

const onDrag = (e) => {
    if (!isDragging.value) return;
    
    currentX.value = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
    
    // Prevent default behavior for touch events to avoid scrolling
    if (e.type === 'touchmove') {
        e.preventDefault();
    }
};

const endDrag = () => {
    if (!isDragging.value) return;
    
    const dragDistance = currentX.value - startX.value;
    
    // If drag distance exceeds threshold, trigger swipe
    if (Math.abs(dragDistance) >= dragThreshold) {
        swipe(dragDistance > 0 ? 'right' : 'left');
    } else {
        // Reset position if threshold not met
        isDragging.value = false;
        startX.value = 0;
        currentX.value = 0;
    }
};

// Cancel drag if mouse leaves the element
const cancelDrag = () => {
    isDragging.value = false;
    startX.value = 0;
    currentX.value = 0;
};

// Computed property for card drag style
const cardDragStyle = computed(() => {
    if (!isDragging.value || remainingItems.value.length === 0) return {};
    
    const dragDistance = currentX.value - startX.value;
    const rotation = dragDistance * 0.05; // Add slight rotation effect
    
    return {
        transform: `translateX(${dragDistance}px) rotate(${rotation}deg)`,
        transition: 'none',
        cursor: 'grabbing'
    };
});

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
            <div>
                <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Discover</h2>
            </div>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                    <!-- Products/Categories Tab Navigation -->
                    <div class="mb-6 flex">
                        <button 
                            @click="swipeType = 'products'; router.get(route('swipe.index', { type: 'product' }))" 
                            class="px-6 py-2 rounded-l-lg font-medium text-sm focus:outline-none"
                            :class="swipeType === 'product' 
                                ? 'bg-coffee-600 text-white' 
                                : 'bg-coffee-200 text-coffee-700 hover:bg-coffee-300'"
                        >
                            Products
                        </button>
                        <button 
                            @click="swipeType = 'category'; router.get(route('swipe.index', { type: 'category' }))" 
                            class="px-6 py-2 rounded-r-lg font-medium text-sm focus:outline-none"
                            :class="swipeType === 'category' 
                                ? 'bg-coffee-600 text-white' 
                                : 'bg-coffee-200 text-coffee-700 hover:bg-coffee-300'"
                        >
                            Categories
                        </button>
                    </div>
                    
                    <div class="flex justify-center">
                        <div class="w-full max-w-md">
                            <!-- Swipe Cards -->
                            <div class="relative h-96">
                                <div v-if="remainingItems.length === 0" class="flex flex-col items-center justify-center h-full">
                                    <p class="text-xl font-medium text-coffee-800">No more {{ swipeType === 'product' ? 'products' : 'categories' }} to swipe!</p>
                                    <p class="text-coffee-600 mt-2">Check back later for new items or try swiping {{ swipeType === 'product' ? 'categories' : 'products' }} instead.</p>
                                    <div class="flex space-x-4 mt-4">
                                        <button 
                                            @click="swipeType = 'products'; router.get(route('swipe.index', { type: 'product' }))" 
                                            class="px-4 py-2 rounded-md font-medium text-sm"
                                            :class="swipeType !== 'product' 
                                                ? 'bg-coffee-600 text-white hover:bg-coffee-700' 
                                                : 'bg-coffee-200 text-coffee-700'"
                                        >
                                            Switch to Products
                                        </button>
                                        <button 
                                            @click="swipeType = 'category'; router.get(route('swipe.index', { type: 'category' }))" 
                                            class="px-4 py-2 rounded-md font-medium text-sm"
                                            :class="swipeType !== 'category' 
                                                ? 'bg-coffee-600 text-white hover:bg-coffee-700' 
                                                : 'bg-coffee-200 text-coffee-700'"
                                        >
                                            Switch to Categories
                                        </button>
                                    </div>
                                </div>
                                
                                <div v-for="(item, index) in remainingItems" 
                                     :key="item.id" 
                                     class="absolute top-0 left-0 w-full h-full transition-all duration-300 ease-out"
                                     :class="{
                                         'translate-x-full opacity-0': index === 0 && swipeDirection === 'right',
                                         '-translate-x-full opacity-0': index === 0 && swipeDirection === 'left',
                                         'z-10': index === 0,
                                         'z-0': index === 1,
                                         'hidden': index > 1,
                                         'cursor-grab': index === 0 && !isDragging && !isAnimating,
                                         'cursor-grabbing': index === 0 && isDragging
                                     }"
                                     :style="index === 0 && isDragging ? cardDragStyle : {
                                         transform: index > 0 ? `scale(${0.95 - (index - 1) * 0.05}) translateY(${(index - 1) * 10}px)` : ''
                                     }"
                                     @mousedown="index === 0 && !isAnimating ? startDrag($event) : null"
                                     @mousemove="index === 0 ? onDrag($event) : null"
                                     @mouseup="index === 0 ? endDrag() : null"
                                     @mouseleave="index === 0 ? cancelDrag() : null"
                                     @touchstart="index === 0 && !isAnimating ? startDrag($event) : null"
                                     @touchmove="index === 0 ? onDrag($event) : null"
                                     @touchend="index === 0 ? endDrag() : null"
                                     @touchcancel="index === 0 ? cancelDrag() : null">
                                    
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
                                                <span class="text-2xl font-bold text-coffee-700">€{{ item.price }}</span>
                                                <span class="text-sm text-coffee-500">{{ item.stock }} in stock</span>
                                            </div>
                                            <div v-else class="mt-4">
                                                <span class="text-sm text-coffee-500">Swipe right to see more products in this category</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- X and Checkmark buttons in the style from the image -->
                            <div class="flex justify-center mt-8 space-x-12">
                                <button @click="swipe('left')" 
                                        class="w-14 h-14 flex items-center justify-center rounded-full border border-amber-200"
                                        :disabled="isAnimating || remainingItems.length === 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <button @click="swipe('right')" 
                                        class="w-14 h-14 flex items-center justify-center rounded-full border border-amber-200"
                                        :disabled="isAnimating || remainingItems.length === 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Instructions -->
                            <div class="mt-6 text-center text-coffee-600 text-sm">
                                <p>Drag cards left to dislike or right to like</p>
                                <p class="mt-1">You can also use keyboard arrow keys</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>