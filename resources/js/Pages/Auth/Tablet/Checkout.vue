<script setup lang="ts">
import { defineProps, computed, onMounted } from 'vue';
import Tablet from '@/Layouts/Tablet.vue';

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
    id: number;
    menu_id: number;
    amount: number;
    price: number;
    meal_addition_id: number;
    remark: string;
}

const props = defineProps({
  menuSales: Array as () => MenuSale[],
  menus: Array as () => Menu[],
  mealAdditions: Array as () => MealAddition[],
});

const state = {
  menus: props.menus || [],
  mealAdditions: props.mealAdditions || [],
};

const total = computed(() => {
  return props.menuSales.reduce((total, sale) => {
    const menu = state.menus.find(menu => menu.id === sale.menu_id);
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
</script>

<template>
  <Tablet>
    <div class="flex-1 order-1 2xl:order-2">
      <h2 class="text-center text-xl font-bold mb-2">{{ $t('messages.Selected Items') }}</h2>
      <table class="table-auto w-full">
        <thead>
          <tr>
            <th class="text-left px-4 py-2">Menu Number</th>
            <th class="text-left px-4 py-2">Menu Name</th>
            <th class="text-left px-4 py-2">Price</th>
            <th class="text-left px-4 py-2">Remark</th>
            <th class="text-left px-4 py-2">Addition</th>
            <th class="text-left px-4 py-2">Amount</th>
            <th class="text-left px-4 py-2">Total Price</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sale in menuSales" :key="sale.id">
            <td class="border w-1/12 px-4 py-2">{{ state.menus.find(menu => menu.id === sale.menu_id)?.number }}</td>
            <td class="border w-1/6 px-4 py-2">{{ state.menus.find(menu => menu.id === sale.menu_id)?.name }}</td>
            <td class="border w-1/6 px-4 py-2">
              <p v-if="(state.menus.find(menu => menu.id === sale.menu_id)?.menu_offers.length ?? 0) > 0">
                <span class="line-through text-red-500">€ {{ state.menus.find(menu => menu.id === sale.menu_id)?.price }}</span>
                <div v-for="offer in state.menus.find(menu => menu.id === sale.menu_id)?.menu_offers" :key="offer.id" class="flex justify-end">
                  <p class="text-green-500">{{ roundToNearestFiveCents((Math.round((state.menus.find(menu => menu.id === sale.menu_id)?.price ?? 0) * (1 - offer.discount / 100) * 100) / 100)).toFixed(2) }}</p>
                </div>
              </p>
              <p v-else>€ {{ state.menus.find(menu => menu.id === sale.menu_id)?.price }}</p>
            </td>
            <td class="border w-1/6 px-4 py-2">{{ sale.remark }}</td>
            <td class="border w-1/6 px-4 py-2">
              {{ state.mealAdditions.find(addition => addition.id === sale.meal_addition_id)?.name }}
            </td>
            <td class="border w-1/6 px-4 py-2">{{ sale.amount }}</td>
            <td class="border w-1/6 px-4 py-2">
              <p v-if="(state.menus.find(menu => menu.id === sale.menu_id)?.menu_offers.length ?? 0) > 0">
                € {{ roundToNearestFiveCents((Math.round((state.menus.find(menu => menu.id === sale.menu_id)?.price ?? 0) * (1 - (state.menus.find(menu => menu.id === sale.menu_id)?.menu_offers[0]?.discount ?? 0) / 100) * sale.amount * 100) / 100)).toFixed(2) }}
              </p>
              <p v-else>
                € {{ roundToNearestFiveCents(((state.menus.find(menu => menu.id === sale.menu_id)?.price ?? 0) * sale.amount * 100 / 100)).toFixed(2) }}
              </p>
            </td>
          </tr>
        </tbody>
      </table>
        <br>
        {{ $t('messages.A waiter is on it\'s way with the bill') }}
    </div>
  </Tablet>
</template>
