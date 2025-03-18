<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-cream-200 text-start text-base font-medium text-white bg-coffee-700 focus:outline-none focus:text-white focus:bg-coffee-700 focus:border-cream-100 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-cream-100 hover:text-white hover:bg-coffee-700 hover:border-cream-300 focus:outline-none focus:text-white focus:bg-coffee-700 focus:border-cream-300 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>