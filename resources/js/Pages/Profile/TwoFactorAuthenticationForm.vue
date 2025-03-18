<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
    twoFactorEnabled: Boolean,
});

const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);
const showingConfirmation = ref(false);
const showingRecoveryCodes = ref(false);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => props.twoFactorEnabled || enabling.value
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    useForm({})
        .post(route('two-factor.enable'), {
            preserveScroll: true,
            onSuccess: () => Promise.all([
                showQrCode(),
                showSetupKey(),
                showRecoveryCodes(),
            ]).then(() => {
                showingConfirmation.value = true;
            }),
        });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
        showingRecoveryCodes.value = true;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirming.value = true;

    confirmationForm.post(route('two-factor.confirm'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            enabling.value = false;
            showingConfirmation.value = false;
        },
    });
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    useForm({})
        .delete(route('two-factor.disable'), {
            preserveScroll: true,
            onSuccess: () => {
                disabling.value = false;
                enabling.value = false;
            },
        });
};

const closeConfirmationModal = () => {
    showingConfirmation.value = false;
};

const closeRecoveryCodesModal = () => {
    showingRecoveryCodes.value = false;
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-coffee-800">
                Two Factor Authentication
            </h2>

            <p class="mt-1 text-sm text-coffee-600">
                Add additional security to your account using two factor authentication.
            </p>
        </header>

        <div class="mt-5 space-y-6">
            <div v-if="!twoFactorEnabled">
                <div class="mb-4 font-medium text-sm text-coffee-700">
                    When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your email.
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

            <div v-else>
                <div class="mb-4 font-medium text-sm text-green-600">
                    Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application or enter the setup key.
                </div>

                <div class="mt-4">
                    <div v-if="qrCode" class="p-4 bg-white rounded-lg border border-coffee-200 inline-block">
                        <div v-html="qrCode"></div>
                    </div>
                </div>

                <div v-if="setupKey" class="mt-4 max-w-xl text-sm text-coffee-700">
                    <div class="font-bold">Setup Key: <span class="font-mono text-coffee-500">{{ setupKey }}</span></div>
                </div>

                <div class="mt-5">
                    <div class="flex space-x-3">
                        <SecondaryButton 
                            @click="showRecoveryCodes" 
                            class="border-coffee-300 text-coffee-700 hover:bg-coffee-50"
                        >
                            Show Recovery Codes
                        </SecondaryButton>

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
        </div>

        <!-- Confirmation Modal -->
        <Modal :show="showingConfirmation" @close="closeConfirmationModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-coffee-800">
                    Finish enabling two factor authentication
                </h2>

                <p class="mt-1 text-sm text-coffee-600">
                    To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.
                </p>

                <div class="mt-4">
                    <div v-if="qrCode" class="p-4 bg-white rounded-lg border border-coffee-200 inline-block">
                        <div v-html="qrCode"></div>
                    </div>
                </div>

                <div v-if="setupKey" class="mt-4 text-sm font-medium text-coffee-700">
                    <div>Setup Key: <span class="font-mono">{{ setupKey }}</span></div>
                </div>

                <div class="mt-4">
                    <InputLabel for="code" value="Code" class="text-coffee-700" />

                    <TextInput
                        id="code"
                        v-model="confirmationForm.code"
                        type="text"
                        class="mt-1 block w-full border-coffee-300 focus:border-coffee-500 focus:ring-coffee-500"
                        inputmode="numeric"
                        autofocus
                        autocomplete="one-time-code"
                        @keyup.enter="confirmTwoFactorAuthentication"
                    />

                    <InputError :message="confirmationForm.errors.code" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeConfirmationModal" class="border-coffee-300 text-coffee-700 hover:bg-coffee-50">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ml-3 bg-coffee-600 hover:bg-coffee-700 focus:bg-coffee-700 active:bg-coffee-800 focus:ring-coffee-500"
                        :class="{ 'opacity-25': confirmationForm.processing }"
                        :disabled="confirmationForm.processing"
                        @click="confirmTwoFactorAuthentication"
                    >
                        Confirm
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Recovery Codes Modal -->
        <Modal :show="showingRecoveryCodes" @close="closeRecoveryCodesModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-coffee-800">
                    Recovery Codes
                </h2>

                <p class="mt-1 text-sm text-coffee-600">
                    Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                </p>

                <div class="mt-4 bg-cream-50 p-4 rounded-lg border border-coffee-200">
                    <div v-for="code in recoveryCodes" :key="code" class="text-coffee-800 font-mono text-sm">
                        {{ code }}
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeRecoveryCodesModal" class="border-coffee-300 text-coffee-700 hover:bg-coffee-50">
                        Close
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>