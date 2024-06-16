<script setup lang="ts">
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    helps: Array
});
</script>

<template>
    <Head title="Dashboard" />

    <BackOffice>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    You're logged in!
                    <div class="mt-6">
                        <h3 class="font-semibold text-lg text-gray-800 leading-tight">{{ $t('messages.Help requests')}}</h3>
                        <ul>
                            <li v-for="help in helps" :key="help.id" class="mt-2">
                                <div class="p-4 bg-gray-100 rounded-md">
                                    <p class="text-gray-800">{{ help.message }}</p>
                                    <p class="text-sm text-gray-600">{{ $t('messages.Completed: ')}} {{ help.completed ? 'Yes' : 'No' }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </BackOffice>
</template>
