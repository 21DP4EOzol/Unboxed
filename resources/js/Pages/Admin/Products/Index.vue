<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    products: Object
});
</script>

<template>
    <Head title="Products" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
                <Link :href="route('admin.products.create')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Add New Product
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">ID</th>
                                        <th class="py-2 px-4 border-b text-left">Image</th>
                                        <th class="py-2 px-4 border-b text-left">Name</th>
                                        <th class="py-2 px-4 border-b text-left">Price</th>
                                        <th class="py-2 px-4 border-b text-left">Stock</th>
                                        <th class="py-2 px-4 border-b text-left">Status</th>
                                        <th class="py-2 px-4 border-b text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in products.data" :key="product.id">
                                        <td class="py-2 px-4 border-b">{{ product.id }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <img v-if="product.images && product.images.length > 0" 
                                                 :src="`/storage/${product.images[0]}`" 
                                                 alt="Product Image" 
                                                 class="h-12 w-12 object-cover rounded" />
                                            <div v-else class="h-12 w-12 bg-gray-200 flex items-center justify-center rounded">
                                                <span class="text-gray-400 text-xs">No image</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ product.name }}</td>
                                        <td class="py-2 px-4 border-b">${{ product.price }}</td>
                                        <td class="py-2 px-4 border-b">{{ product.stock }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-green-100 text-green-800': product.active,
                                                'bg-red-100 text-red-800': !product.active
                                            }">
                                                {{ product.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex space-x-2">
                                                <Link :href="route('admin.products.edit', product.id)" class="text-blue-600 hover:text-blue-900">
                                                    Edit
                                                </Link>
                                                <Link :href="route('admin.products.destroy', product.id)" method="delete" as="button" 
                                                      class="text-red-600 hover:text-red-900"
                                                      @click.prevent="
                                                          if (confirm('Are you sure you want to delete this product?')) {
                                                              $inertia.delete(route('admin.products.destroy', product.id));
                                                          }
                                                      ">
                                                    Delete
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="products.data.length === 0">
                                        <td colspan="7" class="py-4 text-center border-b">No products found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <Pagination class="mt-6" :links="products.links" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>