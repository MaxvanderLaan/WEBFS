<script setup lang="ts">
import { defineProps, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import BackOffice from "@/Layouts/AuthenticatedLayout.vue";
import Tablet from "@/Layouts/Tablet.vue";

interface Table {
  id: number;
  number: string;
}

const props = defineProps({
  tables: {
    type: Array as () => Table[],
    required: true,
  },
});

const form = useForm({
  tableId: null,
  customers: [{ name: "", birthday: "", deluxe: false }],
});

const addCustomer = () => {
  if (form.customers.length < 8) {
    form.customers.push({ name: "", birthday: "", deluxe: false });
  }
};

const removeCustomer = (index: number) => {
  form.customers.splice(index, 1);
};

const register = () => {
  form.post("/tablet/register", {
    onSuccess: () => {
      displaySuccessMessage();
      clearForm();
    },
    onError: () => {},
  });
};

const clearForm = () => {
  form.reset();
  form.customers = [{ name: "", birthday: "", deluxe: false }];
};
</script>

<template>
  <Tablet>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <div>
            <b>{{ $t("messages.Workflow explained") }}</b>
            <br />
            {{
              $t(
                "messages.This page should only be accesed though the tablet available at the tables in the restaurant. When the customer walks into the store a waiter should bring them to a empty table. A tablet is then produced, either the waiter will bring one or one is available on the table."
              )
            }}
            <br />
            {{
              $t(
                "messages.For the first step the waiter will register all customers participating in the dining experience"
              )
            }}
            <br />
            {{
              $t(
                "messages.After the registration is finished the tablet will be handed over to the customers, who can start placing orders"
              )
            }}
            <br />
            {{
              $t(
                "messages.When the customer has finished their dining experience they can request the bill via checkout"
              )
            }}
            <br />
            {{
              $t(
                "messages.The asigned waiter will then come over and finish up the payment manually"
              )
            }}
            <br /><br />
            <b>{{ $t("messages.Instructions for waiter") }}</b>
            <br />
            {{ $t("messages.Please select what table the guests are seated at.") }}
            <br /><br />
            <select v-model="form.tableId">
              <option v-for="table in tables" :key="table.id" :value="table.id">
                {{ table.number }}
              </option>
            </select>
            <div v-if="form.errors.tableId" class="text-red-500 mt-1">
              {{ form.errors.tableId }}
            </div>
            <br /><br />
            {{
              $t(
                "messages.Register all the customers that are seated around the table and will partake in the dining."
              )
            }}
            <br /><br />
            <form @submit.prevent="register">
              <div v-for="(customer, index) in form.customers" :key="index" class="mb-4">
                <label class="block mb-2">{{ $t("messages.Customer") }} {{ index + 1 }}</label>
                <input
                  v-model="customer.name"
                  type="text"
                  placeholder="Customer Name"
                  class="border rounded p-2 w-full mb-2"
                />
                <div
                  v-if="form.errors[`customers.${index}.name`]"
                  class="text-red-500 mt-1"
                >
                  {{ form.errors[`customers.${index}.name`] }}
                </div>
                <input
                  v-model="customer.birthday"
                  type="date"
                  placeholder="Customer Birthday"
                  class="border rounded p-2 w-full"
                />
                <div
                  v-if="form.errors[`customers.${index}.birthday`]"
                  class="text-red-500 mt-1"
                >
                  {{ form.errors[`customers.${index}.birthday`] }}
                </div>

                <label class="block mt-2">
                  <input type="checkbox" v-model="customer.deluxe" />
                  {{ $t("messages.Deluxe") }}
                </label>

                <button
                  type="button"
                  @click="removeCustomer(index)"
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded mt-2"
                >
                  {{ $t("messages.Remove") }}
                </button>
              </div>
              <button
                v-if="form.customers.length < 8"
                type="button"
                @click="addCustomer"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
              >
                {{ $t("messages.Add Customer") }}
              </button>
              <br /><br />
              <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                {{ $t("messages.Register") }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </Tablet>
</template>
