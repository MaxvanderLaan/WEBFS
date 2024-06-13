<script setup lang="ts">
import { defineProps } from 'vue';
import Base from '@/Layouts/Base.vue';

const props = defineProps({
    qr_codes: Array as () => string[],
});

</script>

<template>
    <Base>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative" role="alert">
            <p class="font-bold">Success</p>
            <p>Order successfully created!</p>
        </div>
        <div class="bg-gray-200 p-4 flex flex-wrap items-center justify-center">
            <div v-for="(qr_code, index) in qr_codes" :key="index" class="m-4">
                <img :src="qr_code" alt="QR Code" class="min-w-64 min-h-64 max-w-xl max-h-xl">
            </div>
        </div>
    </Base>
</template>