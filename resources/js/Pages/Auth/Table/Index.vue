<script setup lang="ts">
import { defineProps, reactive, defineExpose } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';

interface Table {
    id: number;
    number: string;
    is_archived: boolean;
}

const props = defineProps({
    tables: Array as () => Table[],
});

const state = reactive({
    tables: props.tables || [],
});


defineExpose({ tables: state.tables });
</script>

<template>
    <BackOffice>
        <div class="mt-10">
            <a :href="`/admin/table/create`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create
            </a>
        </div>
        <div class="flex mt-5">
            <div class="w-full">
                <h2 class="text-center text-xl font-bold mb-2">Tables</h2>
                <div v-if="state.tables.length === 0" class="text-center text-gray-500 text-xl">No tables</div>
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="text-left px-4 py-2">Number</th>
                            <th class="text-left px-4 py-2">isArchived</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="table in tables" :key="table.id">
                            <td class="border w-1/12 px-4 py-2">{{ table.number }}</td>
                            <td class="border w-1/12 px-4 py-2">{{ table.is_archived }}</td>
                            <td class="border w-1/12 px-4 py-2">
                                <a :href="`/admin/table/edit/${table.id}`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </BackOffice>
</template>
