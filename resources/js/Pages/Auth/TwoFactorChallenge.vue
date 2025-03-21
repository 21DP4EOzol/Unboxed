<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    status: String,
    remainingTime: {
        type: Number,
        default: 600 // 10 minutes in seconds
    }
});

const form = useForm({
    code: ''
});

const resendForm = useForm({});

const timer = ref(props.remainingTime);
const interval = ref(null);

// Format the remaining time as mm:ss
const formattedTime = computed(() => {
    const minutes = Math.floor(timer.value / 60);
    const seconds = timer.value % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

// Start the countdown timer
const startTimer = () => {
    interval.value = setInterval(() => {
        if (timer.value > 0) {
            timer.value--;
        } else {
            clearInterval(interval.value);
        }
    }, 1000);
};

// Resend the verification code
const resendCode = () => {
    resendForm.post(route('two-factor.resend'), {
        onSuccess: () => {
            form.reset();
            timer.value = 600; // Reset timer to 10 minutes
            startTimer();
        }
    });
};

// Submit the verification code
const submitCode = () => {
    form.post(route('two-factor.verify'));
};

// Clean up the timer when component is unmounted
onUnmounted(() => {
    if (interval.value) {
        clearInterval(interval.value);
    }
});

// Start the timer when component is mounted
onMounted(() => {
    startTimer();
});
</script>

<template>
    <Head title="Two-Factor Authentication" />

    <AppLayout>
        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-coffee-200">
                        <h2 class="text-2xl font-semibold text-coffee-800 mb-6">Two-Factor Authentication</h2>
                        
                        <div class="mb-8 text-coffee-600">
                            <p>An authentication code has been sent to your email address. Please enter the code to verify your identity.</p>
                            <p class="mt-2 text-coffee-500">The code will expire in {{ formattedTime }}.</p>
                        </div>
                        
                        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                            {{ status }}
                        </div>
                        
                        <form @submit.prevent="submitCode">
                            <div>
                                <InputLabel for="code" value="Authentication Code" />
                                <TextInput
                                    id="code"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.code"
                                    placeholder="Enter 6-digit code"
                                    autofocus
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.code" />
                            </div>
                            
                            <div class="flex items-center justify-between mt-6">
                                <button
                                    type="button"
                                    @click="resendCode"
                                    class="text-sm text-coffee-600 hover:text-coffee-900 underline"
                                    :disabled="resendForm.processing"
                                >
                                    Resend Code
                                </button>
                                
                                <PrimaryButton :disabled="form.processing">
                                    Verify
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>