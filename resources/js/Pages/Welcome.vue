<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Base from '@/Layouts/Base.vue';
import { computed } from 'vue';

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
    menu: Menu;
}

const props = defineProps({
    menus: Array as () => Menu[],
    menuOffers: Array as () => MenuOffer[],
});

const thisWeekMenuOffers = computed(() => {
    const now = new Date();
    const startOfWeek = now.setDate(now.getDate() - now.getDay());
    const endOfWeek = now.setDate(now.getDate() - now.getDay() + 6);

    return (props.menuOffers ?? []).filter(offer => {
        const offerDate = new Date(offer.start_date);
        return offerDate >= new Date(startOfWeek) && offerDate <= new Date(endOfWeek);
    });
});

const nextWeekMenuOffers = computed(() => {
    const now = new Date();
    const startOfNextWeek = now.setDate(now.getDate() - now.getDay() + 7);
    const endOfNextWeek = now.setDate(now.getDate() - now.getDay() + 13);

    return (props.menuOffers ?? []).filter(offer => {
        const offerDate = new Date(offer.start_date);
        return offerDate >= new Date(startOfNextWeek) && offerDate <= new Date(endOfNextWeek);
    });
});

</script>

<template>
    <Base>
        <Head title="Welcome" />
        <div class="bg-gray-200 p-4">
            <div v-if="thisWeekMenuOffers.length > 0">
                <h2 class="text-2xl font-bold mb-4">This Week's Offers</h2>
                <div v-for="offer in thisWeekMenuOffers" :key="offer.id" class="p-4 rounded-lg mb-4">
                    <p class="font-semibold text-2xl mb-2">{{ offer.menu.meal_type.type }}</p>
                    <p class="font-semibold text-lg mb-2">Menu: {{ offer.menu.name }}</p>
                    <p class="mb-2">Original Price: <span class="line-through text-red-500">{{ offer.menu.price }}</span></p>
                    <p class="text-green-500 font-bold">Discounted Price: {{ (Math.round(offer.menu.price * (1 - offer.discount / 100) * 100) / 100).toFixed(2) }}</p>
                </div>
            </div>
            <div v-if="nextWeekMenuOffers.length > 0">
                <h2 class="text-2xl font-bold mb-4">Next Week's Offers</h2>
                <div v-for="offer in nextWeekMenuOffers" :key="offer.id" class= "p-4 rounded-lg mb-4">
                    <p class="font-semibold text-2xl mb-2">{{ offer.menu.meal_type.type }}</p>
                    <p class="font-semibold text-lg mb-2">Menu: {{ offer.menu.name }}</p>
                    <p class="mb-2">Original Price: <span class="line-through text-red-500">{{ offer.menu.price }}</span></p>
                    <p class="text-green-500 font-bold">Discounted Price: {{ (Math.round(offer.menu.price * (1 - offer.discount / 100) * 100) / 100).toFixed(2) }}</p>
                </div>
            </div>
        </div>
    </Base>
</template>