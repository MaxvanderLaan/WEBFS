<script setup lang="ts">
import { defineProps, reactive, ref, computed, defineExpose } from 'vue';
import Base from '@/Layouts/Base.vue';
import { useForm } from '@inertiajs/vue3';

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
    menu_offers: MenuOffer[];
}

interface MenuOffer {
    id: number;
    discount: number;
    start_date: string;
    end_date: string;
    number: number;
    menu: Menu;
}

interface MenuSale {
    menuId: number;
    amount: number;
    price: number;
    mealAdditionId: number;
    remark: string;
}

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const displaySuccessMessage = () => {
    successMessage.value = $t('messages.Order successfully created!');
    errorMessage.value = null;

    setTimeout(() => {
        successMessage.value = null;
    }, 10000);
};

const displayErrorMessage = () => {
    successMessage.value = null;
    errorMessage.value = $t('messages.Failed to create order!');

    setTimeout(() => {
        errorMessage.value = null;
    }, 10000);
};

const props = defineProps({
    menus: Array as () => Menu[],
    mealAdditions: Array as () => MealAddition[],
});

const state = reactive({
    menus: props.menus || [],
    mealAdditions: props.mealAdditions || [],
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

let controller = new AbortController();
let signal = controller.signal;
const isLoading = ref(false);

const search = () => {
    controller.abort();
    controller = new AbortController();
    signal = controller.signal;

    isLoading.value = true;

    fetch(`/takeaway/search?query=${query.value}`, { signal })
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
        const menu = state.menus.find(menu => menu.id === sale.menuId);
        if (!menu) return total;
        const discount = menu.menu_offers.length > 0 ? menu.menu_offers[0].discount : 0;
        const price = discount ? menu.price * (1 - discount / 100) : menu.price;
        const saleTotal = sale.amount * price;
        return total + roundToNearestFiveCents(saleTotal);
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

const form = useForm({
    menuSales: [] as MenuSale[],
});

const sendOrder = () => {
    form.menuSales = [...menuSales];
    form.post('/takeaway/order', {
        preserveScroll: true,
        onSuccess: () => {
            displaySuccessMessage();
            menuSales.splice(0);
        },
        onError: () => {
            displayErrorMessage();
        }
    });
};

const qrCode = ref<string | null>(null);

defineExpose({ menus: state.menus, query, amounts, search, groupedMenus, updateAmount, addSale, menuSales, total, deleteSale, sendOrder, displaySuccessMessage, displayErrorMessage });
</script>

<template>
    <Base>
        <div class="bg-gray-200 p-4">
            <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative" role="alert">
                <p class="font-bold">{{ $t("messages.Success") }}</p>
                <p>{{ successMessage }}</p>
            </div>
            <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 relative" role="alert">
                <p class="font-bold">{{ $t("messages.Error") }}</p>
                <p>{{ errorMessage }}</p>
            </div>
            <img v-if="qrCode" :src="'data:image/png;base64,' + qrCode" alt="QR Code">
            <div class="flex justify-center mt-10">
                <input v-model="query" @input="search" :placeholder="$t('messages.Search...')" class="p-2 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
            </div>
            <div class="flex justify-center mt-10" v-if="isLoading">{{ $t("messages.Loading...") }}</div>
            <div class="flex flex-col 2xl:flex-row mt-5">
                <div class="flex-1 order-2 2xl:order-1">
                    <div v-for="(menus, mealType) in groupedMenus" :key="mealType">
                        <h2 class="text-center text-xl font-bold mb-2">{{ mealType }}</h2>
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="text-left px-4 py-2">{{ $t("messages.Number") }}</th>
                                    <th class="text-left px-4 py-2">{{ $t("messages.Name") }}</th>
                                    <th class="text-left px-4 py-2">{{ $t("messages.Description") }}</th>
                                    <th class="text-left px-4 py-2">{{ $t("messages.Addition") }}</th>
                                    <th class="text-left px-4 py-2">{{ $t("messages.Price") }}</th>
                                    <th class="text-left px-4 py-2">{{ $t("messages.Amount") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="menu in menus" :key="menu.id">
                                    <td class="border w-1/12 px-4 py-2">{{ menu.number }}</td>
                                    <td class="border w-1/12 px-4 py-2">{{ menu.name }}</td>
                                    <td class="border w-3/6 px-4 py-2">{{ menu.description }}</td>
                                    <td class="border w-1/12 px-4 py-2">{{ menu.addition }}</td>
                                    <td class="border w-1/12 px-4 py-2">
                                        <p v-if="menu.menu_offers.length > 0"><span class="line-through text-red-500">€ {{ menu.price }}</span></p>
                                        <p v-else>€ {{ menu.price }}</p>
                                        <div v-for="offer in menu.menu_offers" :key="offer.id" class="flex justify-end">
                                            <p class="text-green-500">{{ roundToNearestFiveCents((Math.round(menu.price * (1 - offer.discount / 100) * 100) / 100)).toFixed(2) }}</p>
                                        </div>
                                    </td>
                                    <td class="border w-1/12 px-4 py-2">
                                        <input type="number" min="1" :value="amounts.get(menu.id)?.value" @input="updateAmount(menu.id, parseInt(($event.target as HTMLInputElement).value))" class="p-1 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
                                    </td>
                                    <td class="border w-1/12 px-4 py-2">
                                        <button @click="addSale(menu)" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">{{ $t("messages.Add") }}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="state.menus.length === 0" class="text-center text-gray-500 text-xl">{{ $t("messages.No menus") }}</div>
                </div>
                <div class="flex-1 order-1 2xl:order-2">
                    <h2 class="text-center text-xl font-bold mb-2">{{ $t("messages.Menu Sales") }}</h2>
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-2">{{ $t("messages.Menu Number") }}</th>
                                <th class="text-left px-4 py-2">{{ $t("messages.Menu Name") }}</th>
                                <th class="text-left px-4 py-2">{{ $t("messages.Price") }}</th>
                                <th class="text-left px-4 py-2">{{ $t("messages.Remark") }}</th>
                                <th class="text-left px-4 py-2">{{ $t("messages.Addition") }}</th>
                                <th class="text-left px-4 py-2">{{ $t("messages.Amount") }}</th>
                                <th class="text-left px-4 py-2">{{ $t("messages.Total Price") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sale in menuSales" :key="sale.menuId">
                                <td class="border w-1/12 px-4 py-2">{{ sale.menuId }}</td>
                                <td class="border w-1/6 px-4 py-2">{{ menus?.find(menu => menu.id === sale.menuId)?.name }}</td>
                                <td class="border w-1/6 px-4 py-2">
                                    <p v-if="(menus?.find(menu => menu.id === sale.menuId)?.menu_offers?.length ?? 0) > 0">
                                        <span class="line-through text-red-500">€ {{ menus?.find(menu => menu.id === sale.menuId)?.price }}</span>
                                        <div v-for="offer in menus?.find(menu => menu.id === sale.menuId)?.menu_offers" :key="offer.id" class="flex justify-end">
                                            <p class="text-green-500">{{ roundToNearestFiveCents((Math.round((menus?.find(menu => menu.id === sale.menuId)?.price ?? 0) * (1 - offer.discount / 100) * 100) / 100)).toFixed(2) }}</p>
                                        </div>
                                    </p>
                                    <p v-else>€ {{ menus?.find(menu => menu.id === sale.menuId)?.price }}</p>
                                </td>
                                <td class="border w-1/6 px-4 py-2">
                                    <input type="text" v-model="sale.remark" :placeholder="$t('messages.Remark')" class="p-1 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500" />
                                </td>
                                <td class="border w-1/6 px-4 py-2">
                                    <select v-model="sale.mealAdditionId" class="w-full p-1 border-2 border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                                        <option v-for="mealAddition in state.mealAdditions" :key="mealAddition.id" :value="mealAddition.id">
                                            {{ mealAddition.name }}
                                        </option>
                                    </select>
                                </td>
                                <td class="border w-1/6 px-4 py-2">{{ sale.amount }}</td>
                                <td class="border w-1/6 px-4 py-2">
                                    <p v-if="(menus?.find(menu => menu.id === sale.menuId)?.menu_offers?.length ?? 0) > 0">
                                        € {{ roundToNearestFiveCents((Math.round((menus?.find(menu => menu.id === sale.menuId)?.price ?? 0) * (1 - (menus?.find(menu => menu.id === sale.menuId)?.menu_offers[0]?.discount ?? 0) / 100) * sale.amount * 100) / 100)).toFixed(2) }}
                                    </p>
                                    <p v-else>
                                        € {{ roundToNearestFiveCents(((menus?.find(menu => menu.id === sale.menuId)?.price ?? 0) * sale.amount * 100 / 100)).toFixed(2) }}
                                    </p>
                                </td>
                                <td class="border w-1/12 px-4 py-2">
                                    <button @click="deleteSale(sale)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">{{ $t("messages.Remove") }}</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center text-xl mt-5">
                        {{ $t("messages.Total") }}: €{{ roundToNearestFiveCents(total).toFixed(2) }}
                    </div>
                    <div class="text-center mt-5">
                        <button @click="sendOrder" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">{{ $t("messages.Send Order") }}</button>
                    </div>
                </div>
            </div>
        </div>
    </Base>
</template>
