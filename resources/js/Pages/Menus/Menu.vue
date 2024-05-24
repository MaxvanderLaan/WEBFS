<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import Base from '@/Layouts/Base.vue';

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

const groupedMenus = computed(() => {
    if (props.menus) {
        return props.menus.reduce((groups: Record<string, Menu[]>, menu) => {
            const key = menu.meal_type.type;
            if (!groups[key]) {
                groups[key] = [];
            }
            groups[key].push(menu);
            return groups;
        }, {} as Record<string, Menu[]>);
    } else {
        return {};
    }
});
const props = defineProps({
    menus: Array as () => Menu[],
});

</script>

<template>
    <Base>
        <Head title="Menu" />
        <div class="p-4 flex flex-col bg-yellow-200">
            <div v-for="(group, mealType) in groupedMenus" class="flex flex-col mb-4">
                <h2 class="text-lg font-bold mb-2">{{ mealType }}</h2>
                <div v-for="menu in group" :key="menu.id" class="flex justify-center text-black text-sm">
                    <div class="mr-4 flex flex-row space-x-1">
                        <p>Menu Number: {{ menu.number }} </p>
                        <p>Addition: {{ menu.addition }}</p>
                        <p>Price: {{ menu.price }}</p>
                        <div v-for="offer in menu.menu_offers" :key="offer.id">
                            <p class="text-green-500">Discounted Price: {{ (Math.round(menu.price * (1 - offer.discount / 100) * 100) / 100).toFixed(2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Base>
</template>