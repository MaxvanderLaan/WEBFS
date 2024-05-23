<script setup lang="ts">
import { defineProps, reactive, ref, computed, defineExpose, onMounted } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';

interface MealType {
    id: number;
    type: string;
}

interface Menu {
    id: number;
    name: string;
    number: number;
    addition: string;
    price: number;
    description: string;
    is_archived: boolean;
    meal_type: MealType;
}

const props = defineProps({
    menus: Array as () => Menu[],
});

const state = reactive({
    menus: props.menus || [],
    successMessage: '',
    errorMessage: '',
});

const groupedMenus = computed(() => {
    return state.menus.reduce((groups: Record<string, Menu[]>, menu) => {
        const key = menu.meal_type.type;
        if (!groups[key]) {
            groups[key] = [];
        }
        groups[key].push(menu);
        return groups;
    }, {} as Record<string, Menu[]>);
});

const query = ref('');
let controller = new AbortController();
let signal = controller.signal;
const isLoading = ref(false);

const search = () => {
    controller.abort();
    controller = new AbortController();
    signal = controller.signal;

    isLoading.value = true;

    fetch(`/change/menu/search?query=${query.value}`, { signal })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            state.menus = data;
            isLoading.value = false;
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                console.error('Error:', error);
            }
        })
};

defineExpose({ menus: state.menus, groupedMenus, query, search});
</script>

<template>
    <BackOffice>
        <div class="flex justify-center mt-10">
            <input v-model="query" @input="search" placeholder="Search..." class="p-2 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
        </div>
        <div class="flex justify-center mt-10" v-if="isLoading">Loading...</div>
        <a :href="`/change/menu/create`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create
        </a>
        <div class="flex mt-5">
            <div class="w-full">
                <div v-for="(menus, mealType) in groupedMenus" :key="mealType">
                    <h2 class="text-center text-xl font-bold mb-2">{{ mealType }}</h2>
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-2">Number</th>
                                <th class="text-left px-4 py-2">Name</th>
                                <th class="text-left px-4 py-2">Description</th>
                                <th class="text-left px-4 py-2">Addition</th>
                                <th class="text-left px-4 py-2">Price</th>
                                <th class="text-left px-4 py-2">Is Archived</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="menu in menus" :key="menu.id">
                                <td class="border w-1/12 px-4 py-2">{{ menu.number }}</td>
                                <td class="border w-1/12 px-4 py-2">{{ menu.name }}</td>
                                <td class="border w-3/6 px-4 py-2">{{ menu.description }}</td>
                                <td class="border w-1/12 px-4 py-2">{{ menu.addition }}</td>
                                <td class="border w-1/12 px-4 py-2">{{ menu.price }}</td>
                                <td class="border w-1/12 px-4 py-2">{{ menu.is_archived }}</td>
                                <td class="border w-1/12 px-4 py-2">
                                    <a :href="`/change/menu/edit/${menu.id}`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="state.menus.length === 0" class="text-center text-gray-500 text-xl">No menus</div>
            </div>
        </div>
    </BackOffice>
</template>
