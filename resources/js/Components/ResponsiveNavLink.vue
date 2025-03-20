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
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-coffee-300 text-start text-base font-medium text-white bg-coffee-700 focus:outline-none focus:text-white focus:bg-coffee-600 focus:border-coffee-200 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-coffee-200 hover:text-white hover:bg-coffee-600 hover:border-coffee-400 focus:outline-none focus:text-white focus:bg-coffee-600 focus:border-coffee-400 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>