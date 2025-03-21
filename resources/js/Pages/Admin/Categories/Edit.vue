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
    category: Object
});

const form = useForm({
    name: props.category.name,
    description: props.category.description || '',
    image: null,
    active: props.category.active,
    _method: 'PUT'
});

const preview = ref(null);
const hasExistingImage = ref(!!props.category.image);

const handleImageChange = (e) => {
    form.image = e.target.files[0];
    
    // Generate preview
    if (e.target.files.length > 0) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target.result;
        };
        reader.readAsDataURL(form.image);
        hasExistingImage.value = false;
    } else {
        preview.value = null;
    }
};

const removeCurrentImage = () => {
    hasExistingImage.value = false;
    form.image = null;
    preview.value = null;
};

const submit = () => {
    // If we're using the existing image and not uploading a new one, 
    // we don't need to include the image field in the request
    if (!form.image && hasExistingImage.value) {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('name', form.name);
        formData.append('description', form.description);
        formData.append('active', form.active ? '1' : '0');
        
        router.post(route('admin.categories.update', props.category.id), formData, {
            forceFormData: true,
            preserveScroll: true
        });
    } else {
        form.post(route('admin.categories.update', props.category.id), {
            forceFormData: true,
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Edit Category" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Edit Category</h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-coffee-200">
                        <form @submit.prevent="submit" class="space-y-6 max-w-xl">
                            <div>
                                <InputLabel for="name" value="Category Name" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />
                                <textarea id="description" class="mt-1 block w-full border-gray-300 focus:border-coffee-500 focus:ring-coffee-500 rounded-md shadow-sm" 
                                          rows="4" v-model="form.description"></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="image" value="Category Image" />
                                
                                <!-- Current Image -->
                                <div v-if="hasExistingImage && category.image" class="mt-2 mb-4">
                                    <p class="text-sm text-coffee-600 mb-2">Current Image:</p>
                                    <div class="relative inline-block">
                                        <img :src="`/storage/${category.image}`" alt="Current Image" class="h-32 w-32 object-cover rounded" />
                                        <button 
                                            type="button"
                                            @click="removeCurrentImage"
                                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                                        >
                                            &times;
                                        </button>
                                    </div>
                                </div>
                                
                                <input type="file" id="image" class="mt-1 block w-full" 
                                       @change="handleImageChange" accept="image/*" />
                                <InputError class="mt-2" :message="form.errors.image" />
                                
                                <!-- New Image Preview -->
                                <div v-if="preview" class="mt-4">
                                    <p class="text-sm text-coffee-600 mb-2">New Image:</p>
                                    <img :src="preview" alt="Preview" class="h-32 w-32 object-cover rounded" />
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <Checkbox id="active" v-model:checked="form.active" />
                                    <InputLabel for="active" value="Active" class="ml-2" />
                                </div>
                                <InputError class="mt-2" :message="form.errors.active" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Update Category
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>