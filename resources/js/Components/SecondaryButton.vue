<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
         :type="type"
        class="inline-flex items-center rounded-md border border-coffee-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-coffee-700 shadow-sm transition duration-150 ease-in-out hover:bg-coffee-50 focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:ring-offset-2 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
