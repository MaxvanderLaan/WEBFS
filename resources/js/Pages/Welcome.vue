<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Base from '@/Layouts/Base.vue';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

</script>

<template>
    <Base>
        <Head title="Welcome" />
        <div class="text-center text-xl">
            <div>Chinees Indische Specialiteiten</div>
            <div>De Gouden Draak</div>
        </div>
    </Base>
</template>