<script setup lang="ts">
import { ref } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const displaySuccessMessage = () => {
    successMessage.value = 'Table created successfully';
    errorMessage.value = null;

    setTimeout(() => {
        successMessage.value = null;
    }, 10000);
};

const displayErrorMessage = () => {
    successMessage.value = null;
    errorMessage.value = 'Failed to create table';

    setTimeout(() => {
        errorMessage.value = null;
    }, 10000);
};

const props = defineProps({
    errors: Object as () => Record<string, string[]>,
});

const form = useForm({
    number: null,
});

const clearForm = () => {
    form.number = null;
};
</script>

<template>
    <BackOffice>
        <a :href="`/admin/table`" class="text-blue-500 font-bold py-2">
            {{ $t("messages.Back") }}
        </a>
        <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative" role="alert">
            <p class="font-bold">{{ $t("messages.Success") }}</p>
            <p>{{ successMessage }}</p>
        </div>
        <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 relative" role="alert">
            <p class="font-bold">{{ $t("messages.Error") }}</p>
            <p>{{ errorMessage }}</p>
        </div>

        <form @submit.prevent="form.submit('post', `/admin/table/make`, {
                onSuccess: () => {
                    displaySuccessMessage();
                    clearForm();
                },
            onError: displayErrorMessage,
        })">
            <div class="flex flex-col space-y-4">
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700">{{ $t("messages.Number") }}</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.number">{{ props.errors?.number }}</p>
                    <input id="number" v-model="form.number" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
                <div class="flex justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">{{ $t("messages.Create") }}</button>
                </div>
            </div>
        </form>
    </BackOffice>
</template>
