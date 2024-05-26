<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps({
    table: {
        type: Object,
        required: false,
        default: null,
    },
});
</script>

<template>
    <div class="ml-3 mt-10">
        <div v-if="table" class="mb-3">
            <span>Table Number: {{ table.number }}</span>
        </div>
        <a :href="`/tablet/order/create`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create
        </a>
    </div>
</template>
