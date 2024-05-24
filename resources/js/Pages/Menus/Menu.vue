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
        <div class="p-4 flex flex-wrap justify-center bg-yellow-200">
            <div v-for="(group, mealType) in groupedMenus" class="flex flex-col m-6">
                <h2 class="text-lg font-bold mb-2">{{ mealType }}</h2>
                <div class="flex flex-row space-x-4">
                    <div class="flex flex-col">
                        <div v-for="menu in group" :key="menu.id" class="flex justify-between text-black text-sm">
                            <div class="flex flex-col space-x-4 overflow-hidden">
                                <p>{{ menu.number }}. {{ menu.addition }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div v-for="menu in group" :key="menu.id" class="flex justify-between text-black text-sm">
                            <div class="flex flex-col space-x-4 overflow-hidden">
                                <p>{{ menu.name }}.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div v-for="menu in group" :key="menu.id" class="flex justify-between text-black text-sm">
                            <div class="flex flex-row space-x-1 overflow-hidden">
                                <p v-if="menu.menu_offers.length > 0"><span class="line-through text-red-500">€ {{ menu.price }}</span></p>
                                <p v-else>€ {{ menu.price }}</p>
                                <div v-for="offer in menu.menu_offers" :key="offer.id" class="flex justify-end">
                                    <p class="text-green-500">{{ (Math.round(menu.price * (1 - offer.discount / 100) * 100) / 100).toFixed(2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Base>
</template>