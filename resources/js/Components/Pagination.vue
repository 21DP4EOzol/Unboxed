<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array
});
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" 
                     class="mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-400 border rounded" 
                     v-html="link.label"></div>
                
                <Link v-else 
                      class="mr-1 mb-1 px-4 py-2 text-sm leading-4 border rounded hover:bg-coffee-50 focus:outline-none" 
                      :class="{ 'bg-coffee-600 text-white hover:bg-coffee-700': link.active }"
                      :href="link.url" 
                      v-html="link.label"></Link>
            </template>
        </div>
    </div>
</template>