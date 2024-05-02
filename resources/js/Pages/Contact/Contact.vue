<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import Base from '@/Layouts/Base.vue';

const form = ref({
    name: '',
    email: '',
    message: '',
});

</script>

<template>
    <Base>
        <Head title="Contact" />
    </Base>
</template>