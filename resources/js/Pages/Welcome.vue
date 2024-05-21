<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Base from '@/Layouts/Base.vue';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

</script>

<template>
    <Base>
        <Head title="Welcome" />
    </Base>
</template>