<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
    categories: Array,
    selectedCategoryIds: Array
});

const form = useForm({
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    stock: props.product.stock,
    sku: props.product.sku,
    category_ids: props.selectedCategoryIds,
    featured: props.product.featured,
    active: props.product.active,
    new_images: [],
    remove_images: []
});

const preview = ref([]);
const imageRemoval = ref(props.product.images ? [...props.product.images] : []);

const handleImagesChange = (e) => {
    form.new_images = e.target.files;
    
    // Generate preview
    preview.value = [];
    for (let i = 0; i < e.target.files.length; i++) {
        const file = e.target.files[i];
        const reader = new FileReader();
        
        reader.onload = () => {
            preview.value.push(reader.result);
        };
        
        reader.readAsDataURL(file);
    }
};

const removeImage = (path) => {
    form.remove_images.push(path);
    imageRemoval.value = imageRemoval.value.filter(img => img !== path);
};

const submit = () => {
    // Convert form data for proper submission with files
    const formData = new FormData();
    formData.append('_method', 'PUT'); // Method spoofing for Laravel
    
    // Append all basic form fields
    formData.append('name', form.name);
    formData.append('description', form.description);
    formData.append('price', form.price);
    formData.append('stock', form.stock);
    formData.append('sku', form.sku);
    formData.append('featured', form.featured ? '1' : '0');
    formData.append('active', form.active ? '1' : '0');
    
    // Append category IDs as an array
    if (form.category_ids && form.category_ids.length) {
        form.category_ids.forEach(id => {
            formData.append('category_ids[]', id);
        });
    }
    
    // Append remove_images array
    if (form.remove_images && form.remove_images.length) {
        form.remove_images.forEach(path => {
            formData.append('remove_images[]', path);
        });
    }
    
    // Append new image files
    if (form.new_images && form.new_images.length) {
        for (let i = 0; i < form.new_images.length; i++) {
            formData.append(`new_images[${i}]`, form.new_images[i]);
        }
    }
    
    // Use router.post instead of Inertia.post
    router.post(route('admin.products.update', props.product.id), formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            // Clear any existing errors
            form.clearErrors();
        }
    });
};
</script>

<template>
    <Head title="Edit Product" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Product</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Product Name" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Rest of the form fields similar to Create.vue -->
                            <div>
    <InputLabel for="description" value="Description" />
    <textarea id="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
              rows="4" v-model="form.description" required></textarea>
    <InputError class="mt-2" :message="form.errors.description" />
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div>
        <InputLabel for="price" value="Price" />
        <TextInput id="price" type="number" step="0.01" class="mt-1 block w-full" v-model.number="form.price" required />
        <InputError class="mt-2" :message="form.errors.price" />
    </div>

    <div>
        <InputLabel for="stock" value="Stock" />
        <TextInput id="stock" type="number" class="mt-1 block w-full" v-model.number="form.stock" required />
        <InputError class="mt-2" :message="form.errors.stock" />
    </div>

    <div>
        <InputLabel for="sku" value="SKU" />
        <TextInput id="sku" type="text" class="mt-1 block w-full" v-model="form.sku" required />
        <InputError class="mt-2" :message="form.errors.sku" />
    </div>
</div>

<div>
    <InputLabel value="Categories" />
    <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div v-for="category in categories" :key="category.id" class="flex items-center">
            <Checkbox :id="`category-${category.id}`" v-model:checked="form.category_ids" :value="category.id" />
            <InputLabel :for="`category-${category.id}`" :value="category.name" class="ml-2" />
        </div>
    </div>
    <InputError class="mt-2" :message="form.errors.category_ids" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <InputLabel value="Status" />
        <div class="mt-2 flex items-center">
            <Checkbox id="active" v-model:checked="form.active" />
            <InputLabel for="active" value="Active" class="ml-2" />
        </div>
    </div>

    <div>
        <InputLabel value="Featured" />
        <div class="mt-2 flex items-center">
            <Checkbox id="featured" v-model:checked="form.featured" />
            <InputLabel for="featured" value="Featured on homepage" class="ml-2" />
        </div>
    </div>
</div>
                            
                            <!-- Current Images -->
                            <div v-if="imageRemoval.length > 0">
                                <InputLabel value="Current Images" />
                                <div class="mt-2 grid grid-cols-3 gap-4">
                                    <div v-for="(image, index) in imageRemoval" :key="index" class="relative">
                                        <img :src="`/storage/${image}`" alt="Product image" class="h-32 w-32 object-cover rounded" />
                                        <button type="button" @click="removeImage(image)" 
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                            &times;
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- New Images -->
                            <div>
                                <InputLabel for="new_images" value="Add New Images" />
                                <input type="file" id="new_images" class="mt-1 block w-full" 
                                       @change="handleImagesChange" multiple accept="image/*" />
                                <InputError class="mt-2" :message="form.errors.new_images" />
                                
                                <!-- New Image Preview -->
                                <div v-if="preview.length > 0" class="mt-4 grid grid-cols-3 gap-4">
                                    <div v-for="(image, index) in preview" :key="index" class="relative">
                                        <img :src="image" alt="Preview" class="h-32 w-32 object-cover rounded" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Update Product
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>