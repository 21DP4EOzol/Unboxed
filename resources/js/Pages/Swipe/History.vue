<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BackToTopButton from '@/Components/BackToTopButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    likedProducts: Array,
    dislikedProducts: Array,
    likedCategories: Array,
    dislikedCategories: Array,
});

// Active tab for switching between products and categories
const activeTab = ref('products');

// Selected products and categories state
const selectedLikedProducts = ref([]);
const selectedDislikedProducts = ref([]);
const selectedLikedCategories = ref([]);
const selectedDislikedCategories = ref([]);
const selectionMode = ref(false);

// Form for bulk removal
const form = useForm({
    productIds: [],
    categoryIds: []
});

// Toggle selection mode
const toggleSelectionMode = () => {
    selectionMode.value = !selectionMode.value;
    // Clear selections when toggling off
    if (!selectionMode.value) {
        selectedLikedProducts.value = [];
        selectedDislikedProducts.value = [];
        selectedLikedCategories.value = [];
        selectedDislikedCategories.value = [];
    }
};

// Toggle selection of a liked product
const toggleLikedProduct = (productId) => {
    const index = selectedLikedProducts.value.indexOf(productId);
    if (index === -1) {
        selectedLikedProducts.value.push(productId);
    } else {
        selectedLikedProducts.value.splice(index, 1);
    }
};

// Toggle selection of a disliked product
const toggleDislikedProduct = (productId) => {
    const index = selectedDislikedProducts.value.indexOf(productId);
    if (index === -1) {
        selectedDislikedProducts.value.push(productId);
    } else {
        selectedDislikedProducts.value.splice(index, 1);
    }
};

// Toggle selection of a liked category
const toggleLikedCategory = (categoryId) => {
    const index = selectedLikedCategories.value.indexOf(categoryId);
    if (index === -1) {
        selectedLikedCategories.value.push(categoryId);
    } else {
        selectedLikedCategories.value.splice(index, 1);
    }
};

// Toggle selection of a disliked category
const toggleDislikedCategory = (categoryId) => {
    const index = selectedDislikedCategories.value.indexOf(categoryId);
    if (index === -1) {
        selectedDislikedCategories.value.push(categoryId);
    } else {
        selectedDislikedCategories.value.splice(index, 1);
    }
};

// Select all liked products
const selectAllLikedProducts = () => {
    if (selectedLikedProducts.value.length === props.likedProducts.length) {
        // If all are already selected, deselect all
        selectedLikedProducts.value = [];
    } else {
        // Otherwise, select all
        selectedLikedProducts.value = props.likedProducts.map(product => product.id);
    }
};

// Select all disliked products
const selectAllDislikedProducts = () => {
    if (selectedDislikedProducts.value.length === props.dislikedProducts.length) {
        // If all are already selected, deselect all
        selectedDislikedProducts.value = [];
    } else {
        // Otherwise, select all
        selectedDislikedProducts.value = props.dislikedProducts.map(product => product.id);
    }
};

// Select all liked categories
const selectAllLikedCategories = () => {
    if (selectedLikedCategories.value.length === props.likedCategories.length) {
        // If all are already selected, deselect all
        selectedLikedCategories.value = [];
    } else {
        // Otherwise, select all
        selectedLikedCategories.value = props.likedCategories.map(category => category.id);
    }
};

// Select all disliked categories
const selectAllDislikedCategories = () => {
    if (selectedDislikedCategories.value.length === props.dislikedCategories.length) {
        // If all are already selected, deselect all
        selectedDislikedCategories.value = [];
    } else {
        // Otherwise, select all
        selectedDislikedCategories.value = props.dislikedCategories.map(category => category.id);
    }
};

// Compute total selected count
const selectedCount = computed(() => {
    return selectedLikedProducts.value.length + 
        selectedDislikedProducts.value.length + 
        selectedLikedCategories.value.length + 
        selectedDislikedCategories.value.length;
});

// Remove selected products and categories from history
const removeSelectedFromHistory = () => {
    if (selectedCount.value === 0) return;
    
    form.productIds = [...selectedLikedProducts.value, ...selectedDislikedProducts.value];
    form.categoryIds = [...selectedLikedCategories.value, ...selectedDislikedCategories.value];
    
    form.delete(route('swipe.remove-selected'), {
        onSuccess: () => {
            // Reset selections after successful removal
            selectedLikedProducts.value = [];
            selectedDislikedProducts.value = [];
            selectedLikedCategories.value = [];
            selectedDislikedCategories.value = [];
            selectionMode.value = false;
        }
    });
};
</script>

<template>
    <Head title="Swipe History" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Swipe History</h2>
                <div class="flex space-x-3">
                    <PrimaryButton v-if="!selectionMode" @click="toggleSelectionMode">
                        Select Items
                    </PrimaryButton>
                    <div v-else class="flex space-x-2">
                        <span class="py-2 px-3 bg-coffee-100 text-coffee-800 rounded-md">
                            {{ selectedCount }} selected
                        </span>
                        <SecondaryButton @click="toggleSelectionMode">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton 
                            @click="removeSelectedFromHistory"
                            :disabled="selectedCount === 0 || form.processing"
                            :class="{ 'opacity-50 cursor-not-allowed': selectedCount === 0 || form.processing }"
                        >
                            Remove Selected
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tab Navigation -->
                <div class="mb-6 flex">
                    <button 
                        @click="activeTab = 'products'" 
                        class="px-6 py-2 rounded-t-lg font-medium text-sm focus:outline-none"
                        :class="activeTab === 'products' 
                            ? 'bg-white text-coffee-800 border border-coffee-200 border-b-0' 
                            : 'bg-coffee-200 text-coffee-700 hover:bg-coffee-300'"
                    >
                        Products
                    </button>
                    <button 
                        @click="activeTab = 'categories'" 
                        class="px-6 py-2 rounded-t-lg font-medium text-sm focus:outline-none"
                        :class="activeTab === 'categories' 
                            ? 'bg-white text-coffee-800 border border-coffee-200 border-b-0' 
                            : 'bg-coffee-200 text-coffee-700 hover:bg-coffee-300'"
                    >
                        Categories
                    </button>
                </div>
                
                <!-- Products Tab Content -->
                <div v-if="activeTab === 'products'">
                    <!-- Liked Products -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border border-coffee-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-coffee-800">Products You Liked</h3>
                            <button 
                                v-if="selectionMode && likedProducts && likedProducts.length > 0" 
                                @click="selectAllLikedProducts"
                                class="text-coffee-600 hover:text-coffee-800 text-sm"
                            >
                                {{ selectedLikedProducts.length === likedProducts.length ? 'Deselect All' : 'Select All' }}
                            </button>
                        </div>
                        
                        <div v-if="!likedProducts || likedProducts.length === 0" class="text-coffee-600">
                            You haven't liked any products yet.
                        </div>
                        
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div 
                                v-for="product in likedProducts" 
                                :key="product.id" 
                                class="border border-coffee-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow relative"
                                :class="{ 'ring-2 ring-coffee-500': selectionMode && selectedLikedProducts.includes(product.id) }"
                            >
                                <!-- Selection Checkbox -->
                                <div v-if="selectionMode" class="absolute top-2 left-2 z-10">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedLikedProducts.includes(product.id)"
                                        @change="toggleLikedProduct(product.id)"
                                        class="h-5 w-5 rounded border-coffee-300 text-coffee-600 focus:ring-coffee-500"
                                    />
                                </div>
                                
                                <!-- Product Image -->
                                <div 
                                    class="h-48 bg-cream-100 cursor-pointer" 
                                    @click="selectionMode ? toggleLikedProduct(product.id) : null"
                                >
                                    <img v-if="product.images && product.images.length > 0" 
                                         :src="`/storage/${product.images[0]}`" 
                                         alt="Product Image" 
                                         class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <span class="text-coffee-400">No image available</span>
                                    </div>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="p-4">
                                    <h4 class="text-lg font-semibold text-coffee-800">{{ product.name }}</h4>
                                    <p class="text-coffee-600 text-sm mt-1">{{ product.description?.substring(0, 80) }}{{ product.description && product.description.length > 80 ? '...' : '' }}</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="font-bold text-coffee-700">${{ product.price }}</span>
                                        <Link v-if="!selectionMode" :href="`/products/${product.id}`" class="text-coffee-600 hover:text-coffee-800 hover:underline">
                                            View Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Disliked Products -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-coffee-800">Products You Disliked</h3>
                            <button 
                                v-if="selectionMode && dislikedProducts && dislikedProducts.length > 0" 
                                @click="selectAllDislikedProducts"
                                class="text-coffee-600 hover:text-coffee-800 text-sm"
                            >
                                {{ selectedDislikedProducts.length === dislikedProducts.length ? 'Deselect All' : 'Select All' }}
                            </button>
                        </div>
                        
                        <div v-if="!dislikedProducts || dislikedProducts.length === 0" class="text-coffee-600">
                            You haven't disliked any products yet.
                        </div>
                        
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div 
                                v-for="product in dislikedProducts" 
                                :key="product.id" 
                                class="border border-coffee-100 rounded-lg overflow-hidden relative"
                                :class="{ 
                                    'opacity-70': !selectionMode || !selectedDislikedProducts.includes(product.id),
                                    'opacity-100 ring-2 ring-coffee-500': selectionMode && selectedDislikedProducts.includes(product.id)
                                }"
                            >
                                <!-- Selection Checkbox -->
                                <div v-if="selectionMode" class="absolute top-2 left-2 z-10">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedDislikedProducts.includes(product.id)"
                                        @change="toggleDislikedProduct(product.id)"
                                        class="h-5 w-5 rounded border-coffee-300 text-coffee-600 focus:ring-coffee-500"
                                    />
                                </div>
                                
                                <!-- Product Image -->
                                <div 
                                    class="h-48 bg-cream-100 cursor-pointer"
                                    @click="selectionMode ? toggleDislikedProduct(product.id) : null"
                                >
                                    <img v-if="product.images && product.images.length > 0" 
                                         :src="`/storage/${product.images[0]}`" 
                                         alt="Product Image" 
                                         class="w-full h-full object-cover grayscale" 
                                         :class="{ 'grayscale-0': selectionMode && selectedDislikedProducts.includes(product.id) }" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <span class="text-coffee-400">No image available</span>
                                    </div>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="p-4">
                                    <h4 class="text-lg font-semibold text-coffee-700">{{ product.name }}</h4>
                                    <p class="text-coffee-500 text-sm mt-1">{{ product.description?.substring(0, 80) }}{{ product.description && product.description.length > 80 ? '...' : '' }}</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="font-bold text-coffee-600">${{ product.price }}</span>
                                        <Link v-if="!selectionMode" :href="`/products/${product.id}`" class="text-coffee-500 hover:text-coffee-700 hover:underline">
                                            View Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Categories Tab Content -->
                <div v-if="activeTab === 'categories'">
                    <!-- Liked Categories -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border border-coffee-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-coffee-800">Categories You Liked</h3>
                            <button 
                                v-if="selectionMode && likedCategories && likedCategories.length > 0" 
                                @click="selectAllLikedCategories"
                                class="text-coffee-600 hover:text-coffee-800 text-sm"
                            >
                                {{ selectedLikedCategories.length === likedCategories.length ? 'Deselect All' : 'Select All' }}
                            </button>
                        </div>
                        
                        <div v-if="!likedCategories || likedCategories.length === 0" class="text-coffee-600">
                            You haven't liked any categories yet.
                        </div>
                        
                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div 
                                v-for="category in likedCategories" 
                                :key="category.id" 
                                class="border border-coffee-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow relative"
                                :class="{ 'ring-2 ring-coffee-500': selectionMode && selectedLikedCategories.includes(category.id) }"
                            >
                                <!-- Selection Checkbox -->
                                <div v-if="selectionMode" class="absolute top-2 left-2 z-10">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedLikedCategories.includes(category.id)"
                                        @change="toggleLikedCategory(category.id)"
                                        class="h-5 w-5 rounded border-coffee-300 text-coffee-600 focus:ring-coffee-500"
                                    />
                                </div>
                                
                                <!-- Category Image -->
                                <div 
                                    class="h-36 bg-coffee-100 cursor-pointer" 
                                    @click="selectionMode ? toggleLikedCategory(category.id) : null"
                                >
                                    <img v-if="category.image" 
                                         :src="`/storage/${category.image}`" 
                                         :alt="category.name" 
                                         class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center text-4xl">
                                        <!-- Display icon based on category name -->
                                        <span v-if="category.name.toLowerCase().includes('men')">👔</span>
                                        <span v-else-if="category.name.toLowerCase().includes('women')">👗</span>
                                        <span v-else-if="category.name.toLowerCase().includes('foot') || category.name.toLowerCase().includes('shoe')">👟</span>
                                        <span v-else-if="category.name.toLowerCase().includes('access')">🧣</span>
                                        <span v-else-if="category.name.toLowerCase().includes('electron')">📱</span>
                                        <span v-else>🛍️</span>
                                    </div>
                                </div>
                                
                                <!-- Category Info -->
                                <div class="p-4">
                                    <h4 class="text-lg font-semibold text-coffee-800">{{ category.name }}</h4>
                                    <p v-if="category.description" class="text-coffee-600 text-sm mt-1">
                                        {{ category.description.substring(0, 60) }}{{ category.description && category.description.length > 60 ? '...' : '' }}
                                    </p>
                                    <div class="mt-2 flex justify-end">
                                        <Link v-if="!selectionMode" :href="route('products.index', { category: category.slug })" class="text-coffee-600 hover:text-coffee-800 hover:underline">
                                            Browse Products
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Disliked Categories -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-coffee-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-coffee-800">Categories You Disliked</h3>
                            <button 
                                v-if="selectionMode && dislikedCategories && dislikedCategories.length > 0" 
                                @click="selectAllDislikedCategories"
                                class="text-coffee-600 hover:text-coffee-800 text-sm"
                            >
                                {{ selectedDislikedCategories.length === dislikedCategories.length ? 'Deselect All' : 'Select All' }}
                            </button>
                        </div>
                        
                        <div v-if="!dislikedCategories || dislikedCategories.length === 0" class="text-coffee-600">
                            You haven't disliked any categories yet.
                        </div>
                        
                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div 
                                v-for="category in dislikedCategories" 
                                :key="category.id" 
                                class="border border-coffee-100 rounded-lg overflow-hidden relative"
                                :class="{ 
                                    'opacity-70': !selectionMode || !selectedDislikedCategories.includes(category.id),
                                    'opacity-100 ring-2 ring-coffee-500': selectionMode && selectedDislikedCategories.includes(category.id)
                                }"
                            >
                                <!-- Selection Checkbox -->
                                <div v-if="selectionMode" class="absolute top-2 left-2 z-10">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedDislikedCategories.includes(category.id)"
                                        @change="toggleDislikedCategory(category.id)"
                                        class="h-5 w-5 rounded border-coffee-300 text-coffee-600 focus:ring-coffee-500"
                                    />
                                </div>
                                
                                <!-- Category Image -->
                                <div 
                                    class="h-36 bg-coffee-100 cursor-pointer" 
                                    @click="selectionMode ? toggleDislikedCategory(category.id) : null"
                                >
                                    <img v-if="category.image" 
                                         :src="`/storage/${category.image}`" 
                                         :alt="category.name" 
                                         class="w-full h-full object-cover grayscale"
                                         :class="{ 'grayscale-0': selectionMode && selectedDislikedCategories.includes(category.id) }" />
                                    <div v-else class="w-full h-full flex items-center justify-center text-4xl grayscale">
                                        <!-- Display icon based on category name -->
                                        <span v-if="category.name.toLowerCase().includes('men')">👔</span>
                                        <span v-else-if="category.name.toLowerCase().includes('women')">👗</span>
                                        <span v-else-if="category.name.toLowerCase().includes('foot') || category.name.toLowerCase().includes('shoe')">👟</span>
                                        <span v-else-if="category.name.toLowerCase().includes('access')">🧣</span>
                                        <span v-else-if="category.name.toLowerCase().includes('electron')">📱</span>
                                        <span v-else>🛍️</span>
                                    </div>
                                </div>
                                
                                <!-- Category Info -->
                                <div class="p-4">
                                    <h4 class="text-lg font-semibold text-coffee-700">{{ category.name }}</h4>
                                    <p v-if="category.description" class="text-coffee-500 text-sm mt-1">
                                        {{ category.description.substring(0, 60) }}{{ category.description && category.description.length > 60 ? '...' : '' }}
                                    </p>
                                    <div class="mt-2 flex justify-end">
                                        <Link v-if="!selectionMode" :href="route('products.index', { category: category.slug })" class="text-coffee-500 hover:text-coffee-700 hover:underline">
                                            Browse Products
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <BackToTopButton />
    </AppLayout>
</template>