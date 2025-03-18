<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-coffee-100 p-4">
        <div class="w-full max-w-md">
            <div class="bg-coffee-600 p-6 rounded-t-lg shadow-lg">
                <h2 class="text-2xl font-bold text-white text-center flex items-center justify-center">
                    <div class="w-10 h-10 bg-cream-200 text-coffee-800 rounded-lg flex items-center justify-center text-xl font-bold mr-3">
                        U
                    </div>
                    Register
                </h2>
            </div>
            
            <div class="bg-white p-8 rounded-b-lg shadow-lg">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <InputLabel for="name" value="Name" class="sr-only" />
                        <TextInput
                            id="name"
                            type="text"
                            class="w-full p-3 border border-coffee-200 rounded focus:border-coffee-500 focus:ring focus:ring-coffee-200"
                            placeholder="Name"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="email" value="Email" class="sr-only" />
                        <TextInput
                            id="email"
                            type="email"
                            class="w-full p-3 border border-coffee-200 rounded focus:border-coffee-500 focus:ring focus:ring-coffee-200"
                            placeholder="Email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="password" value="Password" class="sr-only" />
                        <TextInput
                            id="password"
                            type="password"
                            class="w-full p-3 border border-coffee-200 rounded focus:border-coffee-500 focus:ring focus:ring-coffee-200"
                            placeholder="Password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mb-6">
                        <InputLabel for="password_confirmation" value="Confirm Password" class="sr-only" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="w-full p-3 border border-coffee-200 rounded focus:border-coffee-500 focus:ring focus:ring-coffee-200"
                            placeholder="Confirm Password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-coffee-600 hover:bg-coffee-700 text-white font-bold py-3 px-4 rounded transition-colors duration-200"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        CREATE ACCOUNT
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-coffee-700">
                        Already have an account?
                        <Link :href="route('login')" class="text-coffee-800 font-semibold hover:underline">
                            Sign in
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>