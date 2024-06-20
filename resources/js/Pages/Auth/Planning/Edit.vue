<script setup lang="ts">
import { ref } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

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

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const today = new Date();
const oneWeekFromNow = new Date(today.getTime() + 6 * 24 * 60 * 60 * 1000);
const minDateTime = ref(today.toISOString().split('T')[0]);
const maxDateTime = ref(oneWeekFromNow.toISOString().split('T')[0]);

const displaySuccessMessage = () => {
    successMessage.value = 'Table updated successfully';
    errorMessage.value = null;

    setTimeout(() => {
        successMessage.value = null;
    }, 10000);
};

const displayErrorMessage = () => {
    successMessage.value = null;
    errorMessage.value = 'Failed to update table';

    setTimeout(() => {
        errorMessage.value = null;
    }, 10000);
};

const props = defineProps<{
    planning: Planning,
    tables: Table[],
    users: User[],
    errors: Record<string, string[]>,
}>();

const form = useForm({
    id: props.planning.id,
    start_date: props.planning.start_time.split('T')[0],
    start_time: new Date(props.planning.start_time).toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' }),
    end_date: props.planning.end_time.split('T')[0],
    end_time: new Date(props.planning.end_time).toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' }),
    user_id: props.planning.user.id,
    table_ids: props.planning.planning_tables.map(planningTable => planningTable.table.id),
});

const selectedTable = ref<number | null>(null);

const addTable = () => {
    if (selectedTable.value && !form.table_ids.includes(selectedTable.value)) {
        form.table_ids.push(selectedTable.value);
        selectedTable.value = null;
    }
};

const removeTable = (id: number) => {
    form.table_ids = form.table_ids.filter(tableId => tableId !== id);
};

const deleteForm = useForm({ id: props.planning.id });

</script>

<template>
    <BackOffice>
        <a :href="`/admin/planning`" class="text-blue-500 font-bold py-2">
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

        <form @submit.prevent="form.submit('put', `/admin/planning/update`, {
                onSuccess: () => {
                    displaySuccessMessage();
                },
            onError: displayErrorMessage,
        })">
            <input type="hidden" v-model="form.id" />
            <div class="flex flex-col space-y-4">

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">{{ $t("messages.Start Date") }}</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.start_date">{{ props.errors?.start_date }}</p>
                    <input id="start_date" type="date" v-model="form.start_date" :min="minDateTime" :max="maxDateTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">{{ $t("messages.Start Time") }}</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.start_time">{{ props.errors?.start_time }}</p>
                    <input id="start_time" type="time" v-model="form.start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">{{ $t("messages.End Date") }}</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.end_date">{{ props.errors?.end_date }}</p>
                    <input id="end_date" type="date" v-model="form.end_date" :min="minDateTime" :max="maxDateTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">{{ $t("messages.End Time") }}</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.end_time">{{ props.errors?.end_time }}</p>
                    <input id="end_time" type="time" v-model="form.end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="user" class="block text-sm font-medium text-gray-700">{{ $t("messages.User") }}</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.user_id">{{ props.errors?.user_id }}</p>
                    <select id="user" v-model="form.user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option :value="null">{{ $t("messages.Select a user") }}</option>
                        <option v-for="user in props.users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>

                <div>
                    <label for="table" class="block text-sm font-medium text-gray-700">{{ $t("messages.Table") }}</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.table_ids">{{ props.errors?.table_ids }}</p>
                    <select id="table" v-model="selectedTable" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option :value="null">{{ $t("messages.Select a table") }}</option>
                        <option v-for="table in props.tables" :key="table.id" :value="table.id">{{ table.number }}</option>
                    </select>
                    <button type="button" @click="addTable" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ $t("messages.Add Table") }}</button>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-blue-700">{{ $t("messages.Selected Tables:") }}</h2>
                    <ul>
                        <li v-for="id in form.table_ids" :key="id">
                            {{ $t("messages.Table") }} {{ props.tables.find(table => table.id === id)?.number }}
                            <button type="button" @click="removeTable(id)" class="ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 mt-1 rounded">{{ $t("messages.Remove") }}</button>
                        </li>
                        <li v-if="form.table_ids.length === 0">{{ $t("messages.No tables selected") }}</li>
                    </ul>
                </div>

                <div class="flex justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">{{ $t("messages.Update") }}</button>
                    <form @submit.prevent="deleteForm.submit('post', `/admin/planning/delete`)">
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4">{{ $t("messages.Delete") }}</button>
                    </form>
                </div>
            </div>
        </form>
    </BackOffice>
</template>
