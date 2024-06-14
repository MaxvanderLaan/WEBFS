<script setup lang="ts">
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BackOffice from '@/Layouts/AuthenticatedLayout.vue';
import Tablet from '@/Layouts/Tablet.vue';

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
    customers: [{ name: '', birthday: '', deluxe: false }]
});

const addCustomer = () => {
    if (form.customers.length < 8) {
        form.customers.push({ name: '', birthday: '', deluxe: false });
    }
};

const removeCustomer = (index: number) => {
    form.customers.splice(index, 1);
};

const register = () => {
    form.post('/tablet/order/register', {
        onSuccess: () => {
            displaySuccessMessage();
            clearForm();
        },
        onError: () => {
        }
    });
};

const clearForm = () => {
    form.reset();
    form.customers = [{ name: '', birthday: '', deluxe: false }];
};
</script>

<template>
    <Tablet>
        <div>
            <b>Instructions for waiter</b>
            <br>
            Please select what table the guests are seated at.
            <br><br>
            <select v-model="form.tableId">
                <option v-for="table in tables" :key="table.id" :value="table.id">
                    {{ table.number }}
                </option>
            </select>
            <div v-if="form.errors.tableId" class="text-red-500 mt-1">{{ form.errors.tableId }}</div>
            <br><br>
            Now register all the customers that are seated around the table and will partake in the dining.
            <br><br>
            <form @submit.prevent="register">
                <div v-for="(customer, index) in form.customers" :key="index" class="mb-4">
                    <label class="block mb-2">Customer {{ index + 1 }}</label>
                    <input v-model="customer.name" type="text" placeholder="Customer Name" class="border rounded p-2 w-full mb-2">
                    <div v-if="form.errors[`customers.${index}.name`]" class="text-red-500 mt-1">{{ form.errors[`customers.${index}.name`] }}</div>
                    <input v-model="customer.birthday" type="date" placeholder="Customer Birthday" class="border rounded p-2 w-full">
                    <div v-if="form.errors[`customers.${index}.birthday`]" class="text-red-500 mt-1">{{ form.errors[`customers.${index}.birthday`] }}</div>
                    
                    <label class="block mt-2">
                        <input type="checkbox" v-model="customer.deluxe">
                        Deluxe
                    </label>

                    <button type="button" @click="removeCustomer(index)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded mt-2">
                        Remove
                    </button>
                </div>
                <button 
                    v-if="form.customers.length < 8" 
                    type="button" 
                    @click="addCustomer" 
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Add Customer
                </button>
                <br><br>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Register
                </button>
            </form>
        </div>
    </Tablet>
</template>
