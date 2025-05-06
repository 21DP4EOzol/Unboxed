<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    _method: 'PATCH', // This is important for method spoofing
    name: user.name,
    email: user.email,
    profile_picture: null,
});

const preview = ref(null);

// Handle file selection
const handleFileChange = (e) => {
    if (e.target.files.length > 0) {
        form.profile_picture = e.target.files[0];
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target.result;
        };
        reader.readAsDataURL(form.profile_picture);
    }
};

const submit = () => {
    // Use post with the proper method
    form.post(route('profile.update'), {
        forceFormData: true, // Force using FormData
        preserveScroll: true,
        onSuccess: () => {
            form.profile_picture = null;
        },
        onError: (errors) => {
            console.error('Update failed:', errors);
        }
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-coffee-800">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-coffee-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="submit"
            class="mt-6 space-y-6"
            enctype="multipart/form-data"
        >
            <input type="hidden" name="_method" value="PATCH"> <!-- Method spoofing -->
            
            <div>
                <InputLabel for="name" value="Name" class="text-coffee-700" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full border-coffee-300 focus:border-coffee-500 focus:ring-coffee-500"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" class="text-coffee-700" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-coffee-300 focus:border-coffee-500 focus:ring-coffee-500"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-coffee-800 bg-cream-100 p-3 rounded">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-coffee-600 underline hover:text-coffee-900 focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="mt-6">
                <InputLabel for="profile_picture" value="Profile Picture" class="text-coffee-700" />
                
                <div class="mt-2 flex items-center">
                    <!-- Show current profile picture if it exists -->
                    <div v-if="user.profile_picture && !preview" class="mr-3">
                        <img :src="`/storage/${user.profile_picture}`" alt="Profile Picture" class="w-16 h-16 rounded-full object-cover" />
                    </div>
                    <!-- Show preview if available -->
                    <div v-else-if="preview" class="mr-3">
                        <img :src="preview" alt="Preview" class="w-16 h-16 rounded-full object-cover" />
                    </div>
                    <!-- Default avatar -->
                    <div v-else class="mr-3 w-16 h-16 rounded-full bg-coffee-200 flex items-center justify-center text-coffee-700 font-bold text-xl">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    
                    <input 
                        type="file" 
                        id="profile_picture" 
                        @change="handleFileChange"
                        class="hidden" 
                        accept="image/*"
                    />
                    <label for="profile_picture" class="bg-coffee-600 hover:bg-coffee-700 text-white px-3 py-1 rounded cursor-pointer">
                        Choose Image
                    </label>
                    <span v-if="form.profile_picture" class="ml-3 text-coffee-600">
                        File selected: {{ form.profile_picture.name }}
                    </span>
                </div>
                
                <InputError class="mt-2" :message="form.errors.profile_picture" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="bg-coffee-600 hover:bg-coffee-700 focus:bg-coffee-700 active:bg-coffee-800 focus:ring-coffee-500"
                >
                    Save
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-green-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>