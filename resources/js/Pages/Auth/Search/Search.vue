<script setup lang="ts">
import { defineProps, reactive, ref, computed, defineExpose } from 'vue';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface MealAddition {
    id: number;
    name: string;
}

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

interface MenuSale {
    menuId: number;
    amount: number;
    price: number;
    mealAdditionId: number;
    remark: string;
}

const props = defineProps({
    menus: Array as () => Menu[],
    mealAdditions: Array as () => MealAddition[],
});

const state = reactive({
    menus: props.menus || [],
    mealAdditions: props.mealAdditions || [],
    successMessage: '',
    errorMessage: '',
});

const query = ref('');

const amounts = new Map();
const updateAmount = (menuId: number, value: number) => {
    if (amounts.has(menuId)) {
        amounts.get(menuId).value = value;
    } else {
        amounts.set(menuId, ref(value));
    }
};

const menuSales = reactive<MenuSale[]>([]);
const addSale = (menu: Menu) => {
    const amount = amounts.get(menu.id)?.value || 1;
    const remark = '';
    const mealAdditionId = state.mealAdditions[0].id;
    menuSales.push({ menuId: menu.id, amount, price: menu.price, mealAdditionId, remark });
    amounts.delete(menu.id);
}
const deleteSale = (sale: MenuSale) => {
    const index = menuSales.findIndex(s => s.menuId === sale.menuId && s.amount === sale.amount);
    if (index !== -1) {
        menuSales.splice(index, 1);
    }
}

const search = () => {
    fetch(`/search/menu?query=${query.value}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            state.menus = data;
        })
        .catch(error => console.error('Error:', error));
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

const total = computed(() => {
    return menuSales.reduce((total, sale) => {
        return total + sale.amount * sale.price;
    }, 0);
});

function roundToNearestFiveCents(number: number) {
    const factor = Math.pow(10, 2);
    const tempNumber = number * factor * 10;
    const roundedTempNumber = Math.round(tempNumber);
    const hasPoint5 = roundedTempNumber % 10;
    const finalNumber = hasPoint5 >= 5 ? Math.ceil(tempNumber) : Math.floor(tempNumber);
    
    return finalNumber / (factor * 10);
}

// Fetch the CSRF token from the meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const sendOrder = () => {
    fetch(route('register.order'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken as string
        },
        body: JSON.stringify({ menuSales })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        state.successMessage = 'Order sent successfully';
        menuSales.splice(0, menuSales.length); // Clear the menuSales array
    })
    .catch(error => {
        state.errorMessage = 'Failed to send order';
        console.error('There was a problem with the fetch operation:', error);
    });
};

defineExpose({ menus: state.menus, query, amounts, search, groupedMenus, updateAmount, addSale, menuSales, total, deleteSale, sendOrder });
</script>

<template>
    <BackOffice>
        <div v-if="state.successMessage" class="alert alert-success">
            {{ state.successMessage }}
        </div>

        <div v-if="state.errorMessage" class="alert alert-danger">
            {{ state.errorMessage }}
        </div>

        <div class="flex justify-center mt-10">
            <input v-model="query" @input="search" placeholder="Search..." class="p-2 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
        </div>
        <div class="flex mt-5">
            <div class="w-1/2">
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
                                <th class="text-left px-4 py-2">Amount</th>
                                <th class="text-left px-4 py-2">Add</th>
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
                                    <input type="number" min="1" :value="amounts.get(menu.id)?.value" @input="updateAmount(menu.id, parseInt(($event.target as HTMLInputElement).value))" class="p-1 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
                                </td>
                                <td class="border w-1/12 px-4 py-2">
                                    <button @click="addSale(menu)" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">Add</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="state.menus.length === 0" class="text-center text-gray-500 text-xl">No menus</div>
            </div>
            <div class="w-1/2">
                <h2 class="text-center text-xl font-bold mb-2">Menu Sales</h2>
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="text-left px-4 py-2">Menu Number</th>
                            <th class="text-left px-4 py-2">Menu Name</th>
                            <th class="text-left px-4 py-2">Price</th>
                            <th class="text-left px-4 py-2">Remark</th>
                            <th class="text-left px-4 py-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in menuSales" :key="sale.menuId">
                            <td class="border w-1/6 px-4 py-2">{{ sale.menuId }}</td>
                            <td class="border w-1/6 px-4 py-2">{{ menus && menus.find(menu => menu.id === sale.menuId)?.name }}</td>
                            <td class="border w-1/6 px-4 py-2">{{ menus && menus.find(menu => menu.id === sale.menuId)?.price }}</td>
                            <td class="border w-1/6 px-4 py-2">{{ sale.remark }}</td>
                            <td class="border w-1/6 px-4 py-2">{{ sale.amount }}</td>
                            <td class="border w-1/6 px-4 py-2">
                                <button @click="deleteSale(sale)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center text-xl mt-5">
                    Total: €{{ roundToNearestFiveCents(total).toFixed(2) }}
                </div>
                <div class="text-center mt-5">
                    <button @click="sendOrder" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">Send Order</button>
                </div>
            </div>
        </div>
    </BackOffice>
</template>
