<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    categories: Object
});
</script>

<template>
    <Head title="Categories" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Categories</h2>
                <Link :href="route('admin.categories.create')" class="px-4 py-2 bg-coffee-600 text-white rounded hover:bg-coffee-700 transition">
                    Add New Category
                </Link>
            </div>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border border-coffee-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">ID</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Image</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Name</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Slug</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Status</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Products</th>
                                        <th class="py-2 px-4 border-b border-coffee-200 text-left text-coffee-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="category in categories.data" :key="category.id">
                                        <td class="py-2 px-4 border-b border-coffee-100">{{ category.id }}</td>
                                        <td class="py-2 px-4 border-b border-coffee-100">
                                            <img v-if="category.image" 
                                                 :src="`/storage/${category.image}`" 
                                                 alt="Category Image" 
                                                 class="h-12 w-12 object-cover rounded" />
                                            <div v-else class="h-12 w-12 bg-cream-200 flex items-center justify-center rounded">
                                                <span class="text-coffee-400 text-xs">No image</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 border-b border-coffee-100">{{ category.name }}</td>
                                        <td class="py-2 px-4 border-b border-coffee-100">{{ category.slug }}</td>
                                        <td class="py-2 px-4 border-b border-coffee-100">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-green-100 text-green-800': category.active,
                                                'bg-red-100 text-red-800': !category.active
                                            }">
                                                {{ category.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-coffee-100">
                                            {{ category.products_count || 0 }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-coffee-100">
                                            <div class="flex space-x-2">
                                                <Link :href="route('admin.categories.edit', category.id)" class="text-coffee-600 hover:text-coffee-800">
                                                    Edit
                                                </Link>
                                                
                                                <!-- Toggle activation button -->
                                                <Link 
                                                    :href="route('admin.categories.toggle-active', category.id)" 
                                                    method="patch" 
                                                    as="button" 
                                                    class="text-coffee-600 hover:text-coffee-800"
                                                >
                                                    {{ category.active ? 'Deactivate' : 'Activate' }}
                                                </Link>
                                                
                                                <Link :href="route('admin.categories.destroy', category.id)" method="delete" as="button" 
                                                      class="text-coffee-600 hover:text-coffee-800"
                                                      @click.prevent="
                                                          if (confirm('Are you sure you want to delete this category?')) {
                                                              $inertia.delete(route('admin.categories.destroy', category.id));
                                                          }
                                                      ">
                                                    Delete
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="categories.data.length === 0">
                                        <td colspan="7" class="py-4 text-center border-b border-coffee-100 text-coffee-600">No categories found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <Pagination class="mt-6" :links="categories.links" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>