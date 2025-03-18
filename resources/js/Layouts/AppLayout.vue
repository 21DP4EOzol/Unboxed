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

// Generate avatar initials from user name
const getUserInitials = computed(() => {
    if (!user.value) return 'G'; // Guest
    
    const nameParts = user.value.name.split(' ');
    if (nameParts.length >= 2) {
        return (nameParts[0][0] + nameParts[1][0]).toUpperCase();
    }
    return nameParts[0][0].toUpperCase();
});

// Get cart item count
const cartItemCount = computed(() => {
    const cart = page.props.cart || {};
    return Object.keys(cart).length;
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-cream-50">
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
                                
                                <NavLink :href="route('products.index')" :active="route().current('products.index')" class="text-cream-100 hover:text-white border-b-2 hover:border-cream-300">
                                    Shop
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

                        <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                            <!-- Cart Icon -->
                            <Link :href="route('cart.index')" class="flex items-center text-cream-100 hover:text-white relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span v-if="cartItemCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ cartItemCount }}
                                </span>
                            </Link>
                            
                            <!-- For logged-in users -->
                            <div v-if="isLoggedIn" class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex items-center text-sm font-medium text-cream-100 hover:text-white hover:border-cream-300 focus:outline-none transition duration-150 ease-in-out">
                                            <!-- User Avatar Circle -->
                                            <div class="w-8 h-8 rounded-full bg-coffee-600 text-white flex items-center justify-center mr-2 border border-cream-200">
                                                {{ getUserInitials }}
                                            </div>
                                            
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
                            <div v-else class="flex items-center space-x-4">
                                <Link :href="route('login')" class="text-cream-100 hover:text-white transition duration-150 ease-in-out">
                                    Log in
                                </Link>

                                <Link :href="route('register')" class="px-4 py-2 bg-coffee-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-coffee-700 focus:bg-coffee-700 active:bg-coffee-800 focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                        
                        <ResponsiveNavLink :href="route('products.index')" :active="route().current('products.index')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Shop
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
                        
                        <ResponsiveNavLink :href="route('cart.index')" :active="route().current('cart.index')" class="text-cream-100 hover:text-white hover:bg-coffee-700">
                            Cart
                            <span v-if="cartItemCount > 0" class="ml-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 inline-flex items-center justify-center">
                                {{ cartItemCount }}
                            </span>
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div v-if="isLoggedIn" class="pt-4 pb-1 border-t border-coffee-700">
                        <div class="px-4 flex items-center">
                            <!-- User Avatar Circle -->
                            <div class="w-8 h-8 rounded-full bg-coffee-600 text-white flex items-center justify-center mr-3 border border-cream-200">
                                {{ getUserInitials }}
                            </div>
                            <div>
                                <div class="font-medium text-base text-cream-100">{{ user.name }}</div>
                                <div class="font-medium text-sm text-cream-300">{{ user.email }}</div>
                            </div>
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

            <!-- Flash Messages -->
            <div v-if="$page.props.flash && ($page.props.flash.success || $page.props.flash.error)" 
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <div v-if="$page.props.flash.success" 
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" 
                    role="alert">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash.error" 
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" 
                    role="alert">
                    {{ $page.props.flash.error }}
                </div>
            </div>

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
            
            <!-- Footer -->
            <footer class="bg-coffee-900 text-cream-100 py-8 mt-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4">Unboxed</h3>
                            <p class="text-cream-300">Discover products you'll love with our innovative swipe-to-discover platform.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                            <ul class="space-y-2">
                                <li><Link href="/" class="text-cream-300 hover:text-white">Home</Link></li>
                                <li><Link :href="route('products.index')" class="text-cream-300 hover:text-white">Shop</Link></li>
                                <li><Link :href="route('swipe.index')" class="text-cream-300 hover:text-white">Discover</Link></li>
                                <li><Link :href="route('cart.index')" class="text-cream-300 hover:text-white">Cart</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                            <p class="text-cream-300">Email: support@unboxed.com</p>
                            <p class="text-cream-300">Phone: (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="mt-8 pt-8 border-t border-coffee-700 text-center text-cream-400 text-sm">
                        &copy; {{ new Date().getFullYear() }} Unboxed. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>