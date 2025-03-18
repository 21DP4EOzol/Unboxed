<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;
</script>

<template>
    <div>
        <div class="min-h-screen bg-cream-100">
            <nav class="bg-coffee-800 border-b border-coffee-700 shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('admin.dashboard')">
                                    <div class="w-10 h-10 bg-cream-200 text-coffee-800 rounded-lg flex items-center justify-center text-xl font-bold">U</div>
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('admin.products.index')" :active="route().current('admin.products.*')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Products
                                </NavLink>
                                <NavLink :href="route('admin.categories.index')" :active="route().current('admin.categories.*')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Categories
                                </NavLink>
                                <NavLink :href="route('admin.orders.index')" :active="route().current('admin.orders.*')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Orders
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex items-center text-sm font-medium text-cream-100 hover:text-white hover:border-cream-300 focus:outline-none transition duration-150 ease-in-out">
                                            <div>{{ user.name }}</div>

                                            <div class="ml-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-coffee-700 hover:bg-cream-100 focus:outline-none focus:bg-cream-100 transition duration-150 ease-in-out">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('dashboard')" as="button" class="block w-full px-4 py-2 text-left text-sm leading-5 text-coffee-700 hover:bg-cream-100 focus:outline-none focus:bg-cream-100 transition duration-150 ease-in-out">
                                            Go to Store
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="block w-full px-4 py-2 text-left text-sm leading-5 text-coffee-700 hover:bg-cream-100 focus:outline-none focus:bg-cream-100 transition duration-150 ease-in-out">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-cream-200 hover:text-white hover:bg-coffee-700 focus:outline-none focus:bg-coffee-700 focus:text-white transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-coffee-800">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.products.index')" :active="route().current('admin.products.*')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Products
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.categories.index')" :active="route().current('admin.categories.*')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Categories
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.orders.index')" :active="route().current('admin.orders.*')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Orders
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-coffee-700">
                        <div class="px-4">
                            <div class="font-medium text-base text-cream-100">{{ user.name }}</div>
                            <div class="font-medium text-sm text-cream-300">{{ user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('home')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Go to Store
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>