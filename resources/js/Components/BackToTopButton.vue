<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isVisible = ref(false);

// Function to check scroll position and show/hide button
const checkScroll = () => {
    isVisible.value = window.scrollY > 300;
};

// Function to scroll back to top
const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

// Add scroll event listener when component is mounted
onMounted(() => {
    window.addEventListener('scroll', checkScroll);
    checkScroll(); // Check initial scroll position
});

// Remove scroll event listener when component is unmounted
onUnmounted(() => {
    window.removeEventListener('scroll', checkScroll);
});
</script>

<template>
    <button 
        v-show="isVisible" 
        @click="scrollToTop" 
        class="fixed bottom-6 right-6 bg-coffee-600 hover:bg-coffee-700 text-white p-3 rounded-full shadow-lg transition-all duration-300 z-50"
        aria-label="Back to top"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</template>