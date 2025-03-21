<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />

    <AppLayout>
        <div class="py-12 bg-cream-50">
            <div class="max-w-md mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-coffee-200">
                        <div class="mb-4 text-sm text-coffee-600">
                            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                        </div>

                        <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent">
                            A new verification link has been sent to the email address you provided during registration.
                        </div>

                        <form @submit.prevent="submit">
                            <div class="mt-4 flex items-center justify-between">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Resend Verification Email
                                </PrimaryButton>

                                <div>
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="underline text-sm text-coffee-600 hover:text-coffee-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coffee-500"
                                    >
                                        Log Out
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>