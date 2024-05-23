<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref } from 'vue';

interface Menu {
    id: number;
    name: string;
    number: number;
    addition: string;
    price: number;
    description: string;
}

interface MenuOffer {
    id: number;
    discount: number;
    start_date: string;
    end_date: string;
    number: number;
    menu: Menu;
}

let formattedMenuOffers = computed(() => {
    return props.menuOffers.map(offer => {
        let startDate = new Date(offer.start_date);
        let endDate = new Date(offer.end_date);

        return {
            ...offer,
            formatted_start_date: startDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }),
            formatted_end_date: endDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }),
        };
    });
});

const props = defineProps({
    menuOffers: {
        type: Array as () => MenuOffer[],
        default: () => [],
    },
});

let sortKey = ref<keyof Menu | ''>('');
let filters = ref<{ [key: string]: string }>({
    id: '',
    name: '',
    number: '',
    addition: '',
    price: '',
    description: '',
    discount: '',
    start_date: '',
    end_date: ''
});

let sortedAndFilteredMenuOffers = computed(() => {
    let offers = formattedMenuOffers.value || [];

    if (sortKey.value) {
        offers = [...offers].sort((a, b) => {
            const key = sortKey.value as keyof Menu;
            return a.menu[key] > b.menu[key] ? 1 : -1;
        });
    }

    return offers.filter(offer => {
        return Object.keys(filters.value).every(key => {
            if (!filters.value[key]) return true;
            const value = key in offer.menu ? offer.menu[key as keyof Menu] : offer[key as keyof MenuOffer];
            return String(value).toLowerCase().includes(filters.value[key].toLowerCase());
        });
    });
});
</script>

<template>
    <BackOffice>
        <Head title="Menu Offers" />
        <div class="bg-gray-200 p-4">
            <div class="w-full mb-4">
                <label for="sort" class="block text-sm font-medium text-gray-700">Sort by</label>
                <select id="sort" v-model="sortKey" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Select a field</option>
                    <option value="id">ID</option>
                    <option value="name">Name</option>
                    <option value="number">Number</option>
                    <option value="addition">Addition</option>
                    <option value="price">Price</option>
                    <option value="description">Description</option>
                </select>
            </div>
            <table class="w-full bg-white">
                <thead>
                    <tr>
                        <th class="w-1/12 py-2 px-4">ID</th>
                        <th class="w-1/6 py-2 px-4">Name</th>
                        <th class="w-1/12 py-2 px-4">Number</th>
                        <th class="w-1/6 py-2 px-4">Addition</th>
                        <th class="w-1/6 py-2 px-4">Price</th>
                        <th class="w-1/6 py-2 px-4">Discount</th>
                        <th class="w-1/6 py-2 px-4">Start Date</th>
                        <th class="w-1/6 py-2 px-4">End Date</th>
                    </tr>
                    <tr>
                        <th class="w-1/12 py-2 px-4">
                            <input v-model="filters.id" placeholder="Search ID" class="border p-1 rounded" />
                        </th>
                        <th class="w-1/6 py-2 px-4">
                            <input v-model="filters.name" placeholder="Search Name" class="border p-1 rounded" />
                        </th>
                        <th class="w-1/12 py-2 px-4">
                            <input v-model="filters.number" placeholder="Search Number" class="border p-1 rounded" />
                        </th>
                        <th class="w-1/6 py-2 px-4">
                            <input v-model="filters.addition" placeholder="Search Addition" class="border p-1 rounded" />
                        </th>
                        <th class="w-1/6 py-2 px-4">
                            <input v-model="filters.price" placeholder="Search Price" class="border p-1 rounded" />
                        </th>
                        <th class="w-1/6 py-2 px-4">
                            <input v-model="filters.discount" placeholder="Search Discount" class="border p-1 rounded" />
                        </th>
                        <th class="w-1/6 py-2 px-4">
                            <input v-model="filters.start_date" placeholder="Search Start Date" class="border p-1 rounded" />
                        </th>
                        <th class="w-1/6 py-2 px-4">
                            <input v-model="filters.end_date" placeholder="Search End Date" class="border p-1 rounded" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="offer in sortedAndFilteredMenuOffers" :key="offer.id">
                        <td class="w-1/12 border px-4 py-2">{{ offer.menu.id }}</td>
                        <td class="w-1/6 border px-4 py-2">{{ offer.menu.name }}</td>
                        <td class="w-1/12 border px-4 py-2">{{ offer.menu.number }}</td>
                        <td class="w-1/6 border px-4 py-2">{{ offer.menu.addition }}</td>
                        <td class="w-1/6 border px-4 py-2">{{ offer.menu.price }}</td>
                        <td class="w-1/6 border px-4 py-2">{{ offer.discount }}%</td>
                        <td class="w-1/6 border px-4 py-2">{{ offer.formatted_start_date }}</td>
                        <td class="w-1/6 border px-4 py-2">{{ offer.formatted_end_date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </BackOffice>
</template>