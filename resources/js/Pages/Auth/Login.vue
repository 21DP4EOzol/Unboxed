<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-coffee-100 p-4">
        <div class="w-full max-w-md">
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 p-6 rounded-t-lg shadow-lg">
                <h2 class="text-2xl font-bold text-white text-center flex items-center justify-center">
                    <div class="w-10 h-10 bg-coffee-300 text-coffee-800 rounded-lg flex items-center justify-center text-xl font-bold mr-3">
                        U
                    </div>
                    Login
                </h2>
            </div>
            
            <div class="bg-white p-8 rounded-b-lg shadow-lg">
                <div v-if="status" class="mb-4 text-sm font-medium text-coffee-600 bg-coffee-50 p-3 rounded">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <InputLabel for="email" value="Email" class="sr-only" />
                        <TextInput
                            id="email"
                            type="email"
                            class="w-full p-3 border border-coffee-200 rounded focus:border-coffee-500 focus:ring focus:ring-coffee-200"
                            placeholder="Email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mb-6">
                        <InputLabel for="password" value="Password" class="sr-only" />
                        <TextInput
                            id="password"
                            type="password"
                            class="w-full p-3 border border-coffee-200 rounded focus:border-coffee-500 focus:ring focus:ring-coffee-200"
                            placeholder="Password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" class="text-coffee-600 border-coffee-300 focus:ring-coffee-500" />
                            <span class="ml-2 text-sm text-coffee-700">Remember me</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-coffee-600 hover:text-coffee-800"
                        >
                            Forgot password?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-coffee-600 hover:bg-coffee-700 text-white font-bold py-3 px-4 rounded transition-colors duration-200"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        SIGN IN
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-coffee-700">
                        Don't have an account?
                        <Link :href="route('register')" class="text-coffee-800 font-semibold hover:underline">
                            Register
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>