<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const activeTab = ref('profile');
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-coffee-800">
                My Account
            </h2>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Tab Navigation -->
                <div class="flex flex-wrap mb-6">
                    <button 
                        @click="activeTab = 'profile'" 
                        class="px-4 py-2 mr-2 mb-2 rounded-t-lg font-medium"
                        :class="activeTab === 'profile' ? 'bg-white text-coffee-800 border border-coffee-200 border-b-0' : 'bg-coffee-100 text-coffee-700 hover:bg-coffee-200'"
                    >
                        Profile
                    </button>
                    <button 
                        @click="activeTab = 'password'" 
                        class="px-4 py-2 mr-2 mb-2 rounded-t-lg font-medium"
                        :class="activeTab === 'password' ? 'bg-white text-coffee-800 border border-coffee-200 border-b-0' : 'bg-coffee-100 text-coffee-700 hover:bg-coffee-200'"
                    >
                        Password
                    </button>
                    <button 
                        @click="activeTab = 'delete'" 
                        class="px-4 py-2 mr-2 mb-2 rounded-t-lg font-medium"
                        :class="activeTab === 'delete' ? 'bg-white text-coffee-800 border border-coffee-200 border-b-0' : 'bg-coffee-100 text-coffee-700 hover:bg-coffee-200'"
                    >
                        Delete Account
                    </button>
                </div>

                <!-- Tab Content -->
                <div v-if="activeTab === 'profile'" class="bg-white p-6 shadow sm:rounded-lg border border-coffee-200">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div v-if="activeTab === 'password'" class="bg-white p-6 shadow sm:rounded-lg border border-coffee-200">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div v-if="activeTab === 'delete'" class="bg-white p-6 shadow sm:rounded-lg border border-coffee-200">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>