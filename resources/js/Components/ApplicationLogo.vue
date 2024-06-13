<script setup lang="ts">
import { defineExpose } from 'vue';

const asset = (path: any) => `/storage/${path}`;

defineExpose({ asset });
</script>

<template>
    <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon">
</template>
