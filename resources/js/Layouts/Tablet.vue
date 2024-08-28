<script setup lang="ts">
import { defineExpose } from 'vue';
import Base from './Base.vue';

const asset = (path: any) => `/storage/${path}`;

defineExpose({ asset });
</script>

<template>
	<Base>
        <slot />
	</Base>
</template>