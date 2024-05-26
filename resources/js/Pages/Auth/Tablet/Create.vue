<script setup lang="ts">
import { ref, watch } from 'vue';
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const displaySuccessMessage = () => {
  successMessage.value = "Order created successfully!";
  errorMessage.value = null;

  setTimeout(() => {
    successMessage.value = null;
  }, 10000);
};

const displayErrorMessage = () => {
  successMessage.value = null;
  errorMessage.value = "Failed to create order!";

  setTimeout(() => {
    errorMessage.value = null;
  }, 10000);
};

const props = defineProps({
  menu: {
    type: Array,
    required: true,
  },
  meal_additions: {
    type: Array,
    required: true,
  },
});

const form = useForm({
  items: [],
  description: "",
  remarks: {},
  meal_additions: {},
  quantities: {},
});

const selectedItems = ref([]);

watch(selectedItems, (newSelectedItems) => {
  form.items = newSelectedItems;
  for (const key in form.remarks) {
    if (!newSelectedItems.includes(Number(key))) {
      delete form.remarks[key];
    }
  }
  for (const key in form.meal_additions) {
    if (!newSelectedItems.includes(Number(key))) {
      delete form.meal_additions[key];
    }
  }
  for (const key in form.quantities) {
    if (!newSelectedItems.includes(Number(key))) {
      delete form.quantities[key];
    }
  }
});

const clearForm = () => {
  selectedItems.value = [];
  form.description = "";
  form.remarks = {};
  form.meal_additions = {};
  form.quantities = {};
};

const addItemToOrder = (itemId) => {
  if (!selectedItems.value.includes(itemId)) {
    selectedItems.value.push(itemId);
    form.quantities[itemId] = 1;
  } else {
    form.quantities[itemId] += 1;
  }
};

const removeItemFromOrder = (itemId) => {
  if (selectedItems.value.includes(itemId)) {
    form.quantities[itemId] -= 1;
    if (form.quantities[itemId] <= 0) {
      selectedItems.value = selectedItems.value.filter(id => id !== itemId)
      delete form.quantities[itemId];
      delete form.remarks[itemId];
      delete form.meal_additions[itemId];
    }
  }
};

const submitForm = () => {
  form.items = selectedItems.value;
  form.submit('post', `/tablet/order/make`, {
    onSuccess: () => {
      displaySuccessMessage();
      clearForm();
    },
    onError: displayErrorMessage,
  });
};
</script>

<template>
  <div class="ml-3 mt-10">
    <h1 class="text-2xl font-bold mb-4">Menu Items</h1>
    <form @submit.prevent="submitForm">
      <div v-if="menu.length > 0">
        <table class="table-auto w-full border-collapse border border-gray-400">
          <thead>
            <tr>
              <th class="border border-gray-300 px-4 py-2">Number</th>
              <th class="border border-gray-300 px-4 py-2">Name</th>
              <th class="border border-gray-300 px-4 py-2">Price</th>
              <th class="border border-gray-300 px-4 py-2">Description</th>
              <th class="border border-gray-300 px-4 py-2">Add/Remove</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in menu" :key="item.id">
              <td class="border border-gray-300 px-4 py-2">{{ item.number }}</td>
              <td class="border border-gray-300 px-4 py-2">{{ item.name }}</td>
              <td class="border border-gray-300 px-4 py-2">{{ item.price }}</td>
              <td class="border border-gray-300 px-4 py-2">{{ item.description }}</td>
              <td class="border border-gray-300 px-4 py-2">
                <button
                  type="button"
                  class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded"
                  @click="addItemToOrder(item.id)"
                >
                  +
                </button>
                <button
                  type="button"
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded"
                  @click="removeItemFromOrder(item.id)"
                >
                  -
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else>
        <p>No menu items available.</p>
      </div>
      <div class="mt-4">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea
          id="description"
          v-model="form.description"
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          rows="4"
          placeholder="Enter description"
        ></textarea>
      </div>
      <div class="flex justify-between mt-4">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Create
        </button>
      </div>
      <div v-if="selectedItems.length > 0" class="mt-4">
        <h2 class="text-xl font-bold mb-2">Current Order:</h2>
        <ul>
          <li v-for="itemId in selectedItems" :key="itemId" class="mb-2">
            <strong>{{ menu.find(item => item.id === itemId).name }}</strong>
            <div>
              <label :for="'quantity-' + itemId" class="block text-sm font-medium text-gray-700">Quantity</label>
              <input
                type="number"
                :id="'quantity-' + itemId"
                :value="form.quantities[itemId]"
                readonly
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              />
            </div>
            <div>
              <label :for="'meal_addition-' + itemId" class="block text-sm font-medium text-gray-700">Meal Addition</label>
              <select
                :id="'meal_addition-' + itemId"
                v-model="form.meal_additions[itemId]"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              >
                <option disabled value="">Select a meal addition</option>
                <option
                  v-for="addition in props.meal_additions"
                  :key="addition.id"
                  :value="addition.id"
                >
                  {{ addition.name }}
                </option>
              </select>
            </div>
            <div>
              <label :for="'remark-' + itemId" class="block text-sm font-medium text-gray-700">Remark</label>
              <input
                type="text"
                :id="'remark-' + itemId"
                v-model="form.remarks[itemId]"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                placeholder="Enter remark"
              />
            </div>
          </li>
        </ul>
      </div>
    </form>
    <div v-if="successMessage" class="mt-4 text-green-500">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="mt-4 text-red-500">
      {{ errorMessage }}
    </div>
  </div>
</template>
