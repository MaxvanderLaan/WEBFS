<script setup lang="ts">
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Tablet from '@/Layouts/Tablet.vue';

const props = defineProps({
    saleId: Number,
});

const form = useForm({
    message: '',
    saleId: props.saleId,
});

const submit = () => {
    form.post(route('tablet.askHelpStore'), {
        onSuccess: () => {
            form.reset('message');
        },
    });
};
</script>

<template>
    <Tablet>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <input
                                type="text"
                                id="message"
                                v-model="form.message"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.message" class="text-red-600 mt-2">
                                {{ form.errors.message }}
                            </div>
                        </div>
                        <div>
                            <button
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            >
                                {{ $t('messages.Send message to staff') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Tablet>
</template>
