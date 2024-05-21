<script setup lang="ts">
import { defineProps, ref, onMounted } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

interface MealType {
    id: number;
    type: string;
}

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const displaySuccessMessage = () => {
    successMessage.value = 'Menu ' + form.name + ' created successfully!';
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
    mealTypes: Array as () => MealType[],
    errors: Object as () => Record<string, string[]>,
});

const form = useForm({
    id: null,
    name: '',
    number: null,
    addition: '',
    price: null,
    description: '',
    meal_type_id: 0,
});

const clearForm = () => {
    form.id = null;
    form.name = '';
    form.number = null;
    form.addition = '';
    form.price = null;
    form.description = '';
    form.meal_type_id = props.mealTypes && props.mealTypes.length > 0 ? props.mealTypes[0].id : 0;
};

onMounted(() => {
    if (props.mealTypes && props.mealTypes.length > 0) {
        form.meal_type_id = props.mealTypes[0].id;
    }
});

</script>

<template>
    <BackOffice>
        <a :href="`/change/menu`" class= text-blue-500 font-bold py-2>
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
        <form @submit.prevent="form.submit('post', `/change/menu/make`, {
                onSuccess: () => {
                    displaySuccessMessage();
                    clearForm();
                },
            onError: displayErrorMessage,
        })">
            <div class="flex flex-col space-y-4">
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700">Number</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.number">{{ props.errors?.number }}</p>
                    <input id="number" v-model="form.number" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
        
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.name">{{ props.errors?.name }}</p>
                    <input id="name" v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
        
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.description">{{ props.errors?.description }}</p>
                    <input id="description" v-model="form.description" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
        
                <div>
                    <label for="addition" class="block text-sm font-medium text-gray-700">Addition</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.addition">{{ props.errors?.addition }}</p>
                    <input id="addition" v-model="form.addition" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
        
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.price">{{ props.errors?.price }}</p>
                    <input id="price" v-model="form.price" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
        
                <div>
                    <label for="meal_type" class="block text-sm font-medium text-gray-700">Meal Type</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.meal_type_id">{{ props.errors?.meal_type_id }}</p>
                    <select id="meal_type" v-model="form.meal_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option v-for="mealType in props.mealTypes" :key="mealType.id" :value="mealType.id">
                        {{ mealType.type }}
                    </option>
                    </select>
                </div>
            </div>
        
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">Create</button>
            </div>
        </form>
    </BackOffice>
</template>
