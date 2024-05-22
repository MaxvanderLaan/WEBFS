<script setup lang="ts">
import { defineProps, ref } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

interface Table {
    id: number;
    number: string;
    is_archived: boolean;
}

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const displaySuccessMessage = () => {
    successMessage.value = 'Table ' + props.table.number + ' updated successfully!';
    errorMessage.value = null;

    setTimeout(() => {
        successMessage.value = null;
    }, 10000);
};

const displayErrorMessage = () => {
    successMessage.value = null;
    errorMessage.value = 'Failed to update menu!';

    setTimeout(() => {
        errorMessage.value = null;
    }, 10000);
};

const props = defineProps({
    table: {
        type: Object as () => Table,
        required: true,
    },
    errors: Object as () => Record<string, string[]>,
});

const form = useForm(props.table);
const deleteForm = useForm({ id: props.table.id });

</script>

<template>
    <BackOffice>
        <a :href="`/admin/table`" class= text-blue-500 font-bold py-2>
            Back
        </a>
        <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative" role="alert">
            <p class="font-bold">Success</p>
            <p>{{ successMessage }}</p>
        </div>
        <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 relative" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ errorMessage }}</p>
        </div>
        <form @submit.prevent="form.submit('put', `/admin/table/update`, {
            onSuccess: displaySuccessMessage,
            onError: displayErrorMessage,
        })">
            <input type="hidden" v-model="form.id" />
            <div class="flex flex-col space-y-4">
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700">Number</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.number">{{ props.errors?.number }}</p>
                    <input id="number" v-model="form.number" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
            </div>

            <div class="flex flex-col">
                <label for="archive" class="block text-sm font-medium text-gray-700">Archive</label>
                <input id="archive" type="checkbox" v-model="form.is_archived" class="mt-1 h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" />
            </div>
        
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">Update</button>
                <form @submit.prevent="deleteForm.submit('post', `/admin/table/delete`)">
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4">
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </BackOffice>
</template>
