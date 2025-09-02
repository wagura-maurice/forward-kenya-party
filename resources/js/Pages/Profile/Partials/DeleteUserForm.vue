<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Party Membership
        </template>

        <template #description>
            Official process to leave Forward Kenya Party
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400 space-y-4">
                <p>If you wish to officially leave the Forward Kenya Party, you can initiate the process here. This action will:</p>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Remove your access to all party systems and platforms</li>
                    <li>Withdraw your party membership</li>
                    <li>Remove your personal information from our active member database</li>
                </ul>
                <p>Before proceeding, please ensure you have saved any important information you may need from your account.</p>
                <p class="text-sm text-gray-500 italic">Note: Certain information may be retained as required by law or for record-keeping purposes.</p>
            </div>

            <div class="mt-5 flex items-center justify-end">
                <DangerButton @click="confirmUserDeletion">
                    Party Membership
                </DangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Party Membership
                </template>

                <template #content>
                    You are about to initiate the process to leave Forward Kenya Party. This action will:
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li>Remove your access to all party systems and platforms</li>
                        <li>Withdraw your party membership</li>
                        <li>Remove your personal information from our active member database</li>
                    </ul>
                    <p class="mt-3">If you need any assistance or have questions, please contact us at <a href="mailto:forwardkenyaparty@gmail.com" class="text-green-600 hover:underline">forwardkenyaparty@gmail.com</a> before proceeding.</p>
                    <p class="mt-2">To confirm, please enter your password:</p>

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Party Membership
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
