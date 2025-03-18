<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    product: Object,
    relatedProducts: Array
});

const quantity = ref(1);
const currentImageIndex = ref(0);

const addToCartForm = useForm({
    product_id: props.product.id,
    quantity: 1
});

const incrementQuantity = () => {
    quantity.value++;
    addToCartForm.quantity = quantity.value;
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
        addToCartForm.quantity = quantity.value;
    }
};

const setQuantity = (value) => {
    const parsedValue = parseInt(value);
    quantity.value = isNaN(parsedValue) || parsedValue < 1 ? 1 : parsedValue;
    addToCartForm.quantity = quantity.value;
};

const addToCart = () => {
    addToCartForm.post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            quantity.value = 1;
            addToCartForm.quantity = 1;
        }
    });
};

const setCurrentImage = (index) => {
    currentImageIndex.value = index;
};
</script>

<template>
    <Head :title="product.name" />

    <AppLayout>
        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <div class="mb-6 text-sm">
                    <Link :href="route('home')" class="text-coffee-600 hover:text-coffee-800">Home</Link>
                    <span class="mx-2 text-coffee-400">/</span>
                    <span v-if="product.categories && product.categories.length > 0">
                        <Link 
                            :href="route('products.index', { category: product.categories[0].slug })" 
                            class="text-coffee-600 hover:text-coffee-800"
                        >
                            {{ product.categories[0].name }}
                        </Link>
                        <span class="mx-2 text-coffee-400">/</span>
                    </span>
                    <span class="text-coffee-800">{{ product.name }}</span>
                </div>
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-coffee-200">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Product Images -->
                            <div>
                                <div class="mb-4 bg-cream-50 rounded-md overflow-hidden">
                                    <img 
                                        v-if="product.images && product.images.length > 0" 
                                        :src="`/storage/${product.images[currentImageIndex]}`"
                                        :alt="product.name"
                                        class="w-full h-96 object-contain"
                                    />
                                    <div v-else class="w-full h-96 flex items-center justify-center bg-cream-100">
                                        <span class="text-coffee-400">No image available</span>
                                    </div>
                                </div>
                                
                                <!-- Thumbnails -->
                                <div v-if="product.images && product.images.length > 1" class="flex space-x-2">
                                    <div 
                                        v-for="(image, index) in product.images" 
                                        :key="index" 
                                        class="w-16 h-16 cursor-pointer rounded-md overflow-hidden border-2"
                                        :class="index === currentImageIndex ? 'border-coffee-600' : 'border-transparent'"
                                        @click="setCurrentImage(index)"
                                    >
                                        <img :src="`/storage/${image}`" :alt="`${product.name} thumbnail ${index + 1}`" class="w-full h-full object-cover" />
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Product Details -->
                            <div>
                                <!-- Categories -->
                                <div class="mb-2">
                                    <div v-if="product.categories && product.categories.length > 0" class="flex flex-wrap gap-2">
                                        <span 
                                            v-for="category in product.categories" 
                                            :key="category.id"
                                            class="inline-block bg-coffee-100 text-coffee-800 rounded-full px-3 py-1 text-xs"
                                        >
                                            {{ category.name }}
                                        </span>
                                    </div>
                                </div>
                                
                                <h1 class="text-3xl font-bold text-coffee-800 mb-2">{{ product.name }}</h1>
                                
                                <div class="text-2xl font-bold text-coffee-700 mb-4">${{ product.price }}</div>
                                
                                <div class="mb-6">
                                    <p class="text-coffee-600">{{ product.description }}</p>
                                </div>
                                
                                <!-- Stock Status -->
                                <div class="mb-6">
                                    <div v-if="product.stock > 0" class="text-green-600 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        In Stock ({{ product.stock }} available)
                                    </div>
                                    <div v-else class="text-red-600 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Out of Stock
                                    </div>
                                </div>
                                
                                <!-- Quantity Selector -->
                                <div v-if="product.stock > 0" class="mb-6">
                                    <div class="text-coffee-700 mb-2">Quantity:</div>
                                    <div class="flex max-w-[140px]">
                                        <button 
                                            @click="decrementQuantity" 
                                            class="w-10 h-10 flex items-center justify-center border border-coffee-300 rounded-l bg-cream-50 text-coffee-800 hover:bg-cream-100"
                                        >
                                            -
                                        </button>
                                        <input 
                                            type="number" 
                                            v-model="quantity" 
                                            min="1" 
                                            :max="product.stock" 
                                            @change="setQuantity($event.target.value)"
                                            class="w-20 h-10 border-t border-b border-coffee-300 text-center text-coffee-800"
                                        />
                                        <button 
                                            @click="incrementQuantity" 
                                            class="w-10 h-10 flex items-center justify-center border border-coffee-300 rounded-r bg-cream-50 text-coffee-800 hover:bg-cream-100"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Add to Cart Button -->
                                <div v-if="product.stock > 0" class="mb-6">
                                    <button 
                                        @click="addToCart" 
                                        class="w-full sm:w-auto px-6 py-3 bg-coffee-600 text-white rounded-md hover:bg-coffee-700 transition flex items-center justify-center"
                                        :disabled="addToCartForm.processing"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                        </svg>
                                        Add to Cart
                                    </button>
                                </div>
                                
                                <!-- SKU -->
                                <div class="text-coffee-500 text-sm">
                                    SKU: {{ product.sku }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Products -->
                <div v-if="relatedProducts && relatedProducts.length > 0" class="mt-12">
                    <h2 class="text-2xl font-bold text-coffee-800 mb-6">Related Products</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="product in relatedProducts" :key="product.id" class="bg-white rounded-lg shadow-md overflow-hidden border border-coffee-200 hover:shadow-lg transition">
                            <Link :href="route('products.show', product.id)">
                                <div class="h-48 bg-cream-50">
                                    <img v-if="product.images && product.images.length > 0" 
                                         :src="`/storage/${product.images[0]}`" 
                                         :alt="product.name" 
                                         class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <span class="text-coffee-400">No image available</span>
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-coffee-800 mb-1">{{ product.name }}</h3>
                                    <div class="text-coffee-700 font-bold">${{ product.price }}</div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>