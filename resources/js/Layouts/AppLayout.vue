<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
const auth = computed(() => page.props.auth || {});
const user = computed(() => auth.value.user);
const isLoggedIn = computed(() => !!user.value);
</script>

<template>
    <div>
        <div class="min-h-screen bg-cream-100">
            <nav class="bg-coffee-800 border-b border-coffee-700 shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <div class="w-10 h-10 bg-cream-200 text-coffee-800 rounded-lg flex items-center justify-center text-xl font-bold">U</div>
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('home')" :active="route().current('home')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Home
                                </NavLink>
                                
                                <NavLink v-if="isLoggedIn" :href="route('swipe.index')" :active="route().current('swipe.index')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Discover Products
                                </NavLink>
                                <NavLink v-if="isLoggedIn" :href="route('recommendations.index')" :active="route().current('recommendations.index')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Recommendations
                                </NavLink>
                                <NavLink v-if="isLoggedIn" :href="route('swipe.history')" :active="route().current('swipe.history')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Swipe History
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- For logged-in users -->
                            <div v-if="isLoggedIn" class="ml-3 relative">
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
                                        <DropdownLink v-if="user && user.is_admin" :href="route('admin.dashboard')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-coffee-700 hover:bg-cream-100 focus:outline-none focus:bg-cream-100 transition duration-150 ease-in-out">
                                            Admin Dashboard
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="block w-full px-4 py-2 text-left text-sm leading-5 text-coffee-700 hover:bg-cream-100 focus:outline-none focus:bg-cream-100 transition duration-150 ease-in-out">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- For guests -->
                            <div v-else-if="!route().current('home')" class="ml-3 flex items-center space-x-4">
                                <Link :href="route('login')" class="text-cream-100 hover:text-white transition duration-150 ease-in-out">
                                    Log in
                                </Link>

                                <<Link :href="route('register')" class="ml-4 px-4 py-2 bg-coffee-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-coffee-700 focus:bg-coffee-700 active:bg-coffee-800 focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Register
                                </Link>
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
                        <ResponsiveNavLink :href="route('home')" :active="route().current('home')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Home
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink v-if="isLoggedIn" :href="route('swipe.index')" :active="route().current('swipe.index')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Discover Products
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="isLoggedIn" :href="route('recommendations.index')" :active="route().current('recommendations.index')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Recommendations
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="isLoggedIn" :href="route('swipe.history')" :active="route().current('swipe.history')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Swipe History
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div v-if="isLoggedIn" class="pt-4 pb-1 border-t border-coffee-700">
                        <div class="px-4">
                            <div class="font-medium text-base text-cream-100">{{ user.name }}</div>
                            <div class="font-medium text-sm text-cream-300">{{ user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="user && user.is_admin" :href="route('admin.dashboard')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Admin Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>

                    <!-- Guest Options (Mobile) -->
                    <div v-else class="pt-4 pb-1 border-t border-coffee-700">
                        <div class="mt-3 space-y-1 px-4">
                            <ResponsiveNavLink :href="route('login')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Log in
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('register')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                                Register
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