<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import BackOffice from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref } from "vue";

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
  return props.menuOffers.map((offer) => {
    let startDate = new Date(offer.start_date);
    let endDate = new Date(offer.end_date);

    return {
      ...offer,
      formatted_start_date: startDate.toLocaleDateString("en-GB", {
        day: "numeric",
        month: "long",
        year: "numeric",
      }),
      formatted_end_date: endDate.toLocaleDateString("en-GB", {
        day: "numeric",
        month: "long",
        year: "numeric",
      }),
    };
  });
});

const props = defineProps({
  menuOffers: {
    type: Array as () => MenuOffer[],
    default: () => [],
  },
});

let sortKey = ref<keyof Menu | "">("");
let filters = ref<{ [key: string]: string }>({
  id: "",
  name: "",
  number: "",
  addition: "",
  price: "",
  description: "",
  discount: "",
  start_date: "",
  end_date: "",
});

let sortedAndFilteredMenuOffers = computed(() => {
  let offers = formattedMenuOffers.value || [];

  if (sortKey.value) {
    offers = [...offers].sort((a, b) => {
      const key = sortKey.value as keyof Menu;
      return a.menu[key] > b.menu[key] ? 1 : -1;
    });
  }

  return offers.filter((offer) => {
    return Object.keys(filters.value).every((key) => {
      if (!filters.value[key]) return true;
      const value =
        key in offer.menu ? offer.menu[key as keyof Menu] : offer[key as keyof MenuOffer];
      return String(value).toLowerCase().includes(filters.value[key].toLowerCase());
    });
  });
});
</script>

<template>
  <BackOffice>
    <Head title="Menu Offers" />
    <div class="bg-gray-200 p-4">
      <a
        :href="`/register/menu/offer/create`"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded m-4"
      >
        {{ $t("messages.Update") }}
      </a>
      <div class="w-full mb-4 mt-8">
        <label for="sort" class="block text-sm font-medium text-gray-700">{{
          $t("messages.Short by")
        }}</label>
        <select
          id="sort"
          v-model="sortKey"
          class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">{{ $t("messages.Select a field") }}</option>
          <option value="id">{{ $t("messages.ID") }}</option>
          <option value="name">{{ $t("messages.Name") }}</option>
          <option value="number">{{ $t("messages.Number") }}</option>
          <option value="addition">{{ $t("messages.Addition") }}</option>
          <option value="price">{{ $t("messages.Price") }}</option>
          <option value="description">{{ $t("messages.Description") }}</option>
        </select>
      </div>
      <table class="w-full bg-white">
        <thead>
          <tr>
            <th class="w-1/12 py-2 px-4">{{ $t("messages.ID") }}</th>
            <th class="w-1/6 py-2 px-4">{{ $t("messages.Name") }}</th>
            <th class="w-1/12 py-2 px-4">{{ $t("messages.Number") }}</th>
            <th class="w-1/6 py-2 px-4">{{ $t("messages.Addition") }}</th>
            <th class="w-1/6 py-2 px-4">{{ $t("messages.Price") }}</th>
            <th class="w-1/6 py-2 px-4">{{ $t("messages.Discount") }}</th>
            <th class="w-1/6 py-2 px-4">{{ $t("messages.Start Date") }}</th>
            <th class="w-1/6 py-2 px-4">{{ $t("messages.End Date") }}</th>
          </tr>

          <tr>
            <th class="w-1/12 py-2 px-4">
              <input
                v-model="filters.id"
                :placeholder="$t('messages.Search ID')"
                class="border p-1 rounded"
              />
            </th>
            <th class="w-1/6 py-2 px-4">
              <input
                v-model="filters.name"
                :placeholder="$t('messages.Search Name')"
                class="border p-1 rounded"
              />
            </th>
            <th class="w-1/12 py-2 px-4">
              <input
                v-model="filters.number"
                :placeholder="$t('messages.Search Number')"
                class="border p-1 rounded"
              />
            </th>
            <th class="w-1/6 py-2 px-4">
              <input
                v-model="filters.addition"
                :placeholder="$t('messages.Search Addition')"
                class="border p-1 rounded"
              />
            </th>
            <th class="w-1/6 py-2 px-4">
              <input
                v-model="filters.price"
                :placeholder="$t('messages.Search Price')"
                class="border p-1 rounded"
              />
            </th>
            <th class="w-1/6 py-2 px-4">
              <input
                v-model="filters.discount"
                :placeholder="$t('messages.Search Discount')"
                class="border p-1 rounded"
              />
            </th>
            <th class="w-1/6 py-2 px-4">
              <input
                v-model="filters.start_date"
                :placeholder="$t('messages.Search Start Date')"
                class="border p-1 rounded"
              />
            </th>
            <th class="w-1/6 py-2 px-4">
              <input
                v-model="filters.end_date"
                :placeholder="$t('messages.Search End Date')"
                class="border p-1 rounded"
              />
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
