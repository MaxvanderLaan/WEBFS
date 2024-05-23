<script setup lang="ts">
import { defineProps, ref } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Menu from '@/Pages/Menus/Menu.vue';

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

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const displaySuccessMessage = () => {
    successMessage.value = 'Menu ' + props.menu.name + ' updated successfully!';
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
    menu: {
        type: Object as () => Menu,
        required: true,
    },
    mealTypes: Array as () => MealType[],
    errors: Object as () => Record<string, string[]>,
});

const form = useForm(props.menu);
const deleteForm = useForm({ id: props.menu.id });

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
        <form @submit.prevent="form.submit('put', `/change/menu/update`, {
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

                <div class="flex flex-col">
                    <label for="archive" class="block text-sm font-medium text-gray-700">Archive</label>
                    <input id="archive" type="checkbox" v-model="form.is_archived" class="mt-1 h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" />
                </div>
        
                <div>
                    <label for="meal_type" class="block text-sm font-medium text-gray-700">Meal Type</label>
                    <p class="text-red-500 text-xs italic" v-if="props.errors?.meal_type_id">{{ props.errors?.meal_type_id }}</p>
                    <select id="meal_type" v-model="form.meal_type.id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option v-for="mealType in props.mealTypes" :key="mealType.id" :value="mealType.id">
                        {{ mealType.type }}
                    </option>
                    </select>
                </div>
            </div>
        
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">Update</button>
                <form @submit.prevent="deleteForm.submit('post', `/change/menu/delete`)">
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4">
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </BackOffice>
</template>
