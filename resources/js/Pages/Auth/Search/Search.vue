<script setup lang="ts">
import { defineProps, defineExpose, ref, reactive } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';

interface MealType {
    type: string;
}

interface Menu {
    id: number;
    number: number;
    addition: string;
    name: string;
    price: number;
    description: string;
    mealTypes: MealType;
}

const props = defineProps({
    menus: Array as () => Menu[]
});

const state = reactive({
    menus: props.menus || []
});

const query = ref('');

const search = () => {
    fetch(`/search/menu?query=${query.value}`)
        .then(response => response.json())
        .then(data => {
        state.menus = data;
        });
};

defineExpose({ menus: state.menus, query, search });
</script>

<template>
    <BackOffice>
        <div class="flex justify-center mt-10">
        <input v-model="query" @input="search" placeholder="Search..." class="p-2 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
        </div>
        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="menu in state.menus" :key="menu.id" class="p-5 border-2 border-gray-300 rounded-md">
            <h2 class="text-lg font-bold mb-2">{{ menu.name }}</h2>
            <p class="text-gray-700">{{ menu.description }}</p>
            <p class="mt-2 text-blue-500">{{ menu.mealTypes.type }}</p>
            <p class="mt-2 text-gray-500">Number: {{ menu.number }}</p>
            <p class="mt-2 text-gray-500">Addition: {{ menu.addition }}</p>
            <p class="mt-2 text-gray-500">Price: {{ menu.price }}</p>
        </div>
        <div v-if="state.menus.length === 0" class="text-center text-gray-500 text-xl">No menus</div>
        </div>
    </BackOffice>
</template>