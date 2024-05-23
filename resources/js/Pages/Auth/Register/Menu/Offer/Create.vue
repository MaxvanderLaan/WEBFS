<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { Errors } from '@inertiajs/core';

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
    meal_type: MealType;
}

interface MenuOffer {
    id?: number;
    discount: number;
    start_date: string;
    end_date: string;
    number: number;
    menu_id: number;
}

let tempIdCounter = -1;

const addMenu = (menu: Menu) => {
    if (state.menuOffers.some(m => m.menu_id === menu.id)) {
        return;
    }

    const discount = 0;

    const now = new Date();
    const nextWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 7);
    const startDay = nextWeek.getDate() - nextWeek.getDay() + (nextWeek.getDay() === 0 ? -6 : 1);
    const endDay = startDay + 6;

    const startDate = new Date(nextWeek.getFullYear(), nextWeek.getMonth(), startDay + 1).toISOString().split('T')[0];
    const endDate = new Date(nextWeek.getFullYear(), nextWeek.getMonth(), endDay + 1).toISOString().split('T')[0];

    state.menuOffers.push({
        discount,
        start_date: startDate,
        end_date: endDate,
        number: state.menuOffers.length + 1,
        menu_id: menu.id
    });
}

const deleteMenu = (menuOffer: MenuOffer) => {
    const index = state.menuOffers.findIndex(s => s.menu_id === menuOffer.menu_id);
    if (index !== -1) {
        state.menuOffers.splice(index, 1);
    }
}

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
}

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const displaySuccessMessage = () => {
    successMessage.value = 'Offers for next week updated successfully!';
    errorMessage.value = null;

    setTimeout(() => {
        successMessage.value = null;
    }, 10000);
};

const displayErrorMessage = (errors: Errors) => {
    successMessage.value = null;

    let error = 'Failed to create offer!';
    let errorMessages = [];
    for (let key in errors) {
        if (key.startsWith('menuOffers.')) {
            const index = key.split('.')[1];
            const field = key.split('.')[2];
            const menuId = state.menuOffers[parseInt(index)].menu_id;
            let menuIndex = state.menus.findIndex(m => m.id === menuId);
            let menu = state.menus[menuIndex];
            const errorMessage = errors[key].replace(key, field);
            errorMessages.push(`Offer ${menu.number}: The ${field} field is invalid. ${errorMessage}`);
        }
    }

    if (errorMessages.length > 0) {
        error = errorMessages.join('<br>');
    }

    errorMessage.value = error;

    setTimeout(() => {
        errorMessage.value = null;
    }, 30000);
};

const props = defineProps({
    menus: Array as () => Menu[],
    menuOffers: Array as () => MenuOffer[],
});

const state = reactive({
    menus: props.menus || [],
    menuOffers: props.menuOffers || [],
});

const form = useForm({
    menuOffers: [] as MenuOffer[],
});

const sendOffer = () => {
    form.menuOffers = [...state.menuOffers];
    form.post('/register/menu/offer/make', {
        preserveScroll: true,
        onSuccess: () => {
            displaySuccessMessage();
        },
        onError: (errors) => {
            displayErrorMessage(errors);
            form.reset();
        },
    });
};

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

</script>

<template>
    <BackOffice>
        <a :href="`/register/menu/offer`" class="text-blue-500 font-bold py-2">
            Back
        </a>
        <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative" role="alert">
            <p class="font-bold">Success</p>
            <p>{{ successMessage }}</p>
        </div>
        <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 relative" role="alert">
            <p class="font-bold">Error</p>
            <div v-html="errorMessage"></div>
        </div>

        <div class="flex justify-center mt-10">
            <input v-model="query" @input="search" placeholder="Search..." class="p-2 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
        </div>
        <div class="flex justify-center mt-10" v-if="isLoading">Loading...</div>
        <div class="flex flex-col xl:flex-row mt-5">
            <div class="flex-1 order-2 xl:order-1">
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
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="menu in menus" :key="menu.id">
                                <td class="border w-1/12 px-4 py-2">{{ menu.number }}</td>
                                <td class="border w-1/12 px-4 py-2">{{ menu.name }}</td>
                                <td class="border w-3/6 px-4 py-2">{{ menu.description }}</td>
                                <td class="border w-1/12 px-4 py-2">{{ menu.addition }}</td>
                                <td class="border w-1/12 px-4 py-2">{{ menu.price }}</td>
                                <td class="border w-1/12 px-4 py-2">
                                    <button v-if="!state.menuOffers.some(offer => offer.menu_id === menu.id)" @click="addMenu(menu)" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">Add</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="state.menus.length === 0" class="text-center text-gray-500 text-xl">No menus</div>
            </div>
            <div class="flex-1 order-1 xl:order-2">
                <h2 class="text-center text-xl font-bold mb-2">Menu Offers</h2>
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="text-left px-4 py-2">Number</th>
                            <th class="text-left px-4 py-2">Menu Name</th>
                            <th class="text-left px-4 py-2">Discount Percentage</th>
                            <th class="text-left px-4 py-2">Start Date</th>
                            <th class="text-left px-4 py-2">End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="menuOffer in state.menuOffers" :key="menuOffer.id">
                            <td class="border w-1/12 px-4 py-2">{{ state.menus.find(menu => menu.id === menuOffer.menu_id)?.number }}</td>
                            <td class="border w-1/6 px-4 py-2">{{ state.menus.find(menu => menu.id === menuOffer.menu_id)?.name }}</td>
                            <td class="border w-1/6 px-4 py-2">
                                <input type="number" v-model="menuOffer.discount" class="p-1 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
                            </td>
                            <td class="border w-1/6 px-4 py-2">
                                <input type="text" :value="formatDate(menuOffer.start_date)" readonly class="p-1 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
                            </td>
                            <td class="border w-1/6 px-4 py-2">
                                <input type="text" :value="formatDate(menuOffer.end_date)" readonly class="p-1 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
                            </td>
                            <td class="border w-1/12 px-4 py-2">
                                <button @click="deleteMenu(menuOffer)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center mt-5">
                    <button @click="sendOffer()" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">Update Offer</button>
                </div>
            </div>
        </div>
    </BackOffice>
</template>