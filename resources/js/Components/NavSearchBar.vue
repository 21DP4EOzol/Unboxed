<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const search = ref('');
const suggestions = ref([]);
const showSuggestions = ref(false);
const searchInput = ref(null);
const loading = ref(false);

// Debounce search input for suggestions
let searchTimeout;
watch(search, (value) => {
    if (!value) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchSuggestions(value);
    }, 300);
});

// Fetch product suggestions from the server
const fetchSuggestions = async (query) => {
    if (query.length < 2) return;

    loading.value = true;
    try {
        const response = await axios.get(route('api.search.suggestions'), {
            params: { query }
        });
        suggestions.value = response.data;
        showSuggestions.value = true;
    } catch (error) {
        console.error('Error fetching suggestions:', error);
    } finally {
        loading.value = false;
    }
};

const submitSearch = () => {
    if (!search.value.trim()) return;
    
    router.get(route('products.index'), {
        search: search.value
    });
    showSuggestions.value = false;
};

const selectSuggestion = (suggestion) => {
    search.value = suggestion.name;
    submitSearch();
};

const handleClickOutside = (event) => {
    if (searchInput.value && !searchInput.value.contains(event.target)) {
        showSuggestions.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative w-full max-w-lg" ref="searchInput">
        <div class="relative">
            <input 
                type="text" 
                v-model="search"
                placeholder="Search products..."
                class="w-full pl-10 pr-4 py-2 border border-coffee-300 rounded-md focus:ring-coffee-500 focus:border-coffee-500 bg-cream-50 text-coffee-800"
                @keyup.enter="submitSearch"
            />
            <div class="absolute left-3 top-2.5 text-coffee-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <div v-if="loading" class="absolute right-3 top-2.5 text-coffee-400">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Suggestions dropdown -->
        <div v-show="showSuggestions && suggestions.length > 0" 
            class="absolute z-10 w-full mt-1 bg-white border border-coffee-200 rounded-md shadow-lg">
            <ul class="py-1">
                <li v-for="suggestion in suggestions" :key="suggestion.id" 
                    class="px-4 py-2 hover:bg-coffee-100 cursor-pointer text-coffee-800"
                    @click="selectSuggestion(suggestion)">
                    {{ suggestion.name }}
                </li>
            </ul>
        </div>
    </div>
</template>