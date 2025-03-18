<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    products: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const selectedSort = ref(props.filters.sort || 'newest');

// Debounce search input
let searchTimeout;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters({ search: value });
    }, 500);
});

const applyFilters = (newFilters) => {
    router.get(route('products.index'), {
        ...props.filters,
        ...newFilters
    }, {
        preserveState: true,
        replace: true
    });
};

const changeSort = (sort) => {
    selectedSort.value = sort;
    applyFilters({ sort });
};
</script>

<template>
    <Head title="Shop Products" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Shop Products</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <!-- Search Bar -->
                    <div class="w-full md:w-1/3">
                        <div class="relative">
                            <input 
                                type="text" 
                                v-model="search"
                                placeholder="Search products..."
                                class="w-full pl-10 pr-4 py-2 border border-coffee-300 rounded-md focus:ring-coffee-500 focus:border-coffee-500"
                            />
                            <div class="absolute left-3 top-2.5 text-coffee-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="flex items-center">
                        <span class="text-coffee-700 mr-2">Sort by:</span>
                        <div class="relative">
                            <select 
                                v-model="selectedSort" 
                                @change="changeSort(selectedSort)"
                                class="pl-3 pr-8 py-2 border border-coffee-300 rounded-md appearance-none focus:ring-coffee-500 focus:border-coffee-500 bg-white"
                            >
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                                <option value="price_low_high">Price: Low to High</option>
                                <option value="price_high_low">Price: High to Low</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-coffee-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Display -->
                <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    <div v-for="product in products.data" :key="product.id" class="bg-white rounded-lg shadow-md overflow-hidden border border-coffee-200 hover:shadow-lg transition">
                        <Link :href="route('products.show', product.id)">
                            <div class="h-48 bg-cream-100 relative">
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
                            
                            <!-- Categories -->
                            <div class="px-4 pt-3 flex flex-wrap gap-1">
                                <span v-for="category in product.categories" 
                                      :key="category.id" 
                                      class="bg-coffee-100 text-coffee-800 px-2 py-0.5 rounded-full text-xs">
                                    {{ category.name }}
                                </span>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-coffee-800">{{ product.name }}</h3>
                                <p class="text-coffee-600 text-sm mt-1 line-clamp-2">{{ product.description }}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="font-bold text-coffee-700">${{ product.price }}</span>
                                    <div class="text-coffee-600 text-xs">{{ product.stock }} in stock</div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
                
                <div v-else class="bg-white p-8 rounded-lg shadow-md text-center border border-coffee-200">
                    <p class="text-coffee-600">No products found matching your criteria.</p>
                    
                    <div v-if="filters.search || filters.category" class="mt-4">
                        <Link :href="route('products.index')" class="text-coffee-700 hover:text-coffee-900 underline">
                            Clear all filters
                        </Link>
                    </div>
                </div>
                
                <!-- Pagination -->
                <Pagination :links="products.links" />
            </div>
        </div>
    </AppLayout>
</template>