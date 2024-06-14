<script setup lang="ts">
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Tablet from '@/Layouts/Tablet.vue';

const navigateToCreateOrder = () => {
    window.location.href = route('tablet.order.create');
};

</script>

<template>
    <Tablet>
        <div>
            {{ $t('messages.Welcome to the Golden Dragon') }}
            <br>
            {{ $t('messages.Have a nice stay and enjoy your meal.') }}
            <br>
            {{ $t('messages.You are allowed to order up to 5 times with a period of 10 minutes in between') }}
            <br>
            {{ $t('messages.Order count: ') }}
            {{ $t('messages.Count down timer: ') }}
            <br>
            <br>
            <button type="button" @click="navigateToCreateOrder" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">>
                {{ $t('messages.Create Order') }}
            </button>
        </div>
    </Tablet>
</template>
