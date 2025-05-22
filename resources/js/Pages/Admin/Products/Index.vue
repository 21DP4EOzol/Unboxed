<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

const props = defineProps({
    products: Object,
    filter: {
        type: String,
        default: 'all'
    }
});

const activeFilter = ref(props.filter);

const filterProducts = () => {
    router.get(route('admin.products.index'), {
        filter: activeFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};
</script>

<template>
    <Head title="Products" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Products</h2>
                <Link :href="route('admin.products.create')" class="px-4 py-2 bg-coffee-600 text-white rounded hover:bg-coffee-700 transition">
                    Add New Product
                </Link>
            </div>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border border-coffee-200">
                        <!-- Filter Controls -->
                        <div class="mb-4 flex justify-end">
                            <select v-model="activeFilter" @change="filterProducts" class="border border-coffee-300 rounded p-">
                                <option value="all">All Products</option>
                                <option value="active">Active Only</option>
                                <option value="inactive">Inactive Only</option>
                            </select>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">ID</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Image</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Name</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Price</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Stock</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Status</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in products.data" :key="product.id" 
                                        :class="{'bg-gray-100': !product.active}">
                                        <td class="py-2 px-4 border-b border-coffee-100">{{ product.id }}</td>
                                        <td class="py-2 px-4 border-b border-coffee-100">
                                            <img v-if="product.images && product.images.length > 0" 
                                                 :src="`/storage/${product.images[0]}`" 
                                                 alt="Product Image" 
                                                 class="h-12 w-12 object-cover rounded" />
                                            <div v-else class="h-12 w-12 bg-cream-200 flex items-center justify-center rounded">
                                                <span class="text-coffee-400 text-xs">No image</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 border-b border-coffee-100">{{ product.name }}</td>
                                        <td class="py-2 px-4 border-b border-coffee-100">€{{ product.price }}</td>
                                        <td class="py-2 px-4 border-b border-coffee-100">{{ product.stock }}</td>
                                        <td class="py-2 px-4 border-b border-coffee-100">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-green-100 text-green-800': product.active,
                                                'bg-red-100 text-red-800': !product.active
                                            }">
                                                {{ product.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-coffee-100">
                                            <div class="flex space-x-2">
                                                <Link :href="route('admin.products.edit', product.id)" class="text-coffee-600 hover:text-coffee-800">
                                                    Edit
                                                </Link>
                                                
                                                <!-- Toggle activation button -->
                                                <Link 
                                                    :href="route('admin.products.toggle-active', product.id)" 
                                                    method="patch" 
                                                    as="button" 
                                                    class="text-coffee-600 hover:text-coffee-800"
                                                >
                                                    {{ product.active ? 'Deactivate' : 'Activate' }}
                                                </Link>
                                                
                                                <Link :href="route('admin.products.destroy', product.id)" method="delete" as="button" 
                                                      class="text-coffee-600 hover:text-coffee-800"
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
                                        <td colspan="7" class="py-4 text-center border-b border-coffee-100 text-coffee-600">No products found</td>
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