<script setup lang="ts">
import { defineProps, reactive, defineExpose, onMounted } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';

interface Planning {
    id: number
    start_time: string,
    end_time: string,
    user: User,
    planning_tables: PlanningTable[],
}

interface PlanningTable {
    id: number,
    table: Table,
}

interface Table {
    id: number;
    number: string;
    is_archived: boolean;
}

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

const props = defineProps({
    plannings: Array as () => Planning[],
});

const state = reactive({
    plannings: props.plannings || [],
});

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleString(undefined, { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

defineExpose({ plannings: state.plannings });
onMounted(() => {
    console.log(state.plannings);
});
</script>

<template>
    <BackOffice>
        <div class="mt-10">
            <a :href="`/admin/planning/create`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ $t("messages.Create") }}
            </a>
        </div>
        <div class="flex mt-5">
            <div class="w-full">
                <h2 class="text-center text-xl font-bold mb-2">{{ $t("messages.Planning") }}</h2>
                <div v-if="state.plannings.length === 0" class="text-center text-gray-500 text-xl">{{ $t("messages.No planning") }}</div>
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="text-left px-4 py-2">{{ $t("messages.Users") }}</th>
                            <th class="text-left px-4 py-2">{{ $t("messages.Tables") }}</th>
                            <th class="text-left px-4 py-2">{{ $t("messages.Start Time") }}</th>
                            <th class="text-left px-4 py-2">{{ $t("messages.End Time") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="planning in plannings" :key="planning.id">
                            <td class="border w-1/12 px-4 py-2">{{ planning.user.name }}</td>
                            <td class="border w-1/12 px-4 py-2">
                                <ul>
                                    <li v-for="planningTable in planning.planning_tables" :key="planningTable.id">
                                        {{ planningTable.table.number }}
                                    </li>
                                </ul>
                            </td>
                            <td class="border w-1/12 px-4 py-2">{{ formatDate(planning.start_time) }}</td>
                            <td class="border w-1/12 px-4 py-2">{{ formatDate(planning.end_time) }}</td>
                            <td class="border w-1/12 px-4 py-2">
                                <a :href="`/admin/planning/edit/${planning.id}`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ $t("messages.Edit") }}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </BackOffice>
</template>
