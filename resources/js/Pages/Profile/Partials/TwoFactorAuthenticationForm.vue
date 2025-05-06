<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';

const props = defineProps({
    twoFactorEnabled: {
        type: Boolean,
        default: false
    },
});

const enabling = ref(false);
const disabling = ref(false);
const confirmationMode = ref(false);
const emailSent = ref(false);
const emailError = ref('');
const user = usePage().props.auth.user;

const form = useForm({
    code: ''
});

// Check if 2FA is already enabled
const isTwoFactorEnabled = computed(() => {
    return user.two_factor_confirmed_at !== null;
});

onMounted(() => {
    // If we get redirected back after successful confirmation,
    // make sure we're not still showing the confirmation form
    if (isTwoFactorEnabled.value) {
        confirmationMode.value = false;
    }
});

const enableTwoFactorAuthentication = async () => {
    enabling.value = true;
    emailError.value = '';
    emailSent.value = false;
    
    try {
        await axios.post(route('two-factor.enable'));
        emailSent.value = true;
        confirmationMode.value = true;
    } catch (error) {
        console.error('Error sending verification code:', error);
        emailError.value = 'Failed to send verification code. Please try again.';
    } finally {
        enabling.value = false;
    }
};

const resendVerificationCode = async () => {
    enabling.value = true;
    emailError.value = '';
    emailSent.value = false;
    
    try {
        await axios.post(route('two-factor.enable'));
        emailSent.value = true;
    } catch (error) {
        console.error('Error sending verification code:', error);
        emailError.value = 'Failed to send verification code. Please try again.';
    } finally {
        enabling.value = false;
    }
};

const confirmTwoFactorAuthentication = () => {
    form.post(route('two-factor.confirm'), {
        preserveScroll: true,
        onSuccess: () => {
            confirmationMode.value = false;
            window.location.reload();
        }
    });
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    useForm({}).delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            window.location.reload();
        },
        onError: () => {
            disabling.value = false;
        }
    });
};

const cancelConfirmation = () => {
    confirmationMode.value = false;
    form.reset();
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-coffee-800">
                Two Factor Authentication
            </h2>

            <p class="mt-1 text-sm text-coffee-600">
                Add additional security to your account using email-based two factor authentication.
            </p>
        </header>

        <div class="mt-5 space-y-6">
            <!-- Confirmation Form (shown after enabling) -->
            <div v-if="confirmationMode && !isTwoFactorEnabled" class="bg-cream-50 p-4 rounded-md border border-coffee-200">
                <h3 class="font-medium text-coffee-800 mb-2">Complete Two Factor Setup</h3>
                
                <div v-if="emailSent" class="mb-4 p-2 bg-green-100 text-green-800 rounded">
                    Verification code has been sent to your email address.
                </div>
                
                <div v-if="emailError" class="mb-4 p-2 bg-red-100 text-red-800 rounded">
                    {{ emailError }}
                </div>
                
                <p class="text-sm text-coffee-600 mb-4">
                    Please enter the 6-digit verification code sent to your email address to complete two-factor authentication setup.
                </p>
                
                <div class="mb-4">
                    <InputLabel for="code" value="Verification Code" class="text-coffee-700" />
                    <TextInput
                        id="code"
                        v-model="form.code"
                        type="text"
                        class="mt-1 block w-full border-coffee-300 focus:border-coffee-500 focus:ring-coffee-500"
                        required
                        autofocus
                    />
                    <InputError :message="form.errors.code" class="mt-2" />
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <PrimaryButton 
                        @click="confirmTwoFactorAuthentication" 
                        :disabled="form.processing"
                        class="bg-coffee-600 hover:bg-coffee-700 focus:bg-coffee-700 active:bg-coffee-800 focus:ring-coffee-500"
                    >
                        {{ form.processing ? 'Verifying...' : 'Verify' }}
                    </PrimaryButton>
                    
                    <button 
                        @click="resendVerificationCode"
                        type="button" 
                        class="px-4 py-2 bg-coffee-200 text-coffee-800 rounded focus:outline-none hover:bg-coffee-300"
                        :disabled="enabling"
                    >
                        {{ enabling ? 'Sending...' : 'Resend Code' }}
                    </button>
                    
                    <button 
                        @click="cancelConfirmation"
                        type="button" 
                        class="px-4 py-2 bg-coffee-100 text-coffee-800 rounded focus:outline-none hover:bg-coffee-200"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Initial State - Two Factor Not Enabled -->
            <div v-if="!isTwoFactorEnabled && !confirmationMode">
                <div class="mb-4 font-medium text-sm text-coffee-700">
                    When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You will receive this token via email to complete the login process.
                </div>

                <div class="mt-5">
                    <PrimaryButton 
                        @click="enableTwoFactorAuthentication" 
                        :disabled="enabling"
                        class="bg-coffee-600 hover:bg-coffee-700 focus:bg-coffee-700 active:bg-coffee-800 focus:ring-coffee-500"
                    >
                        {{ enabling ? 'Enabling...' : 'Enable' }}
                    </PrimaryButton>
                </div>
            </div>

            <!-- Two Factor Enabled State -->
            <div v-if="isTwoFactorEnabled && !confirmationMode">
                <div class="mb-4 font-medium text-sm text-green-600">
                    Two factor authentication is now enabled. When you log in, you will need to enter a verification code sent to your email address.
                </div>

                <div class="mt-5">
                    <DangerButton 
                        @click="disableTwoFactorAuthentication" 
                        :disabled="disabling"
                        class="bg-coffee-700 hover:bg-coffee-800"
                    >
                        {{ disabling ? 'Disabling...' : 'Disable' }}
                    </DangerButton>
                </div>
            </div>
        </div>
    </section>
</template>